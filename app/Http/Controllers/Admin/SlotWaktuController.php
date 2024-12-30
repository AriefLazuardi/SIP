<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\SlotWaktu;
use App\Models\TingkatanKelas;
use App\Models\Hari;
use App\Http\Controllers\Controller;
use App\Models\SesiBelajar;
use App\Models\SlotWaktuMapping;
use App\Models\SlotWaktuTingkatanKelas;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SlotWaktuController extends Controller
{
    public function createSlotWaktu()
    {
        $slotWaktu = SlotWaktu::all();
        $tingkatanKelas = TingkatanKelas::all()->pluck('nama_tingkatan', 'id');
        $hari = Hari::all()->pluck('nama_hari', 'id');
        $sesiBelajar = SesiBelajar::all()->pluck('nama', 'id');

        return view('admin.slotwaktu.create', compact('slotWaktu','tingkatanKelas', 'hari', 'sesiBelajar'));
    }

    public function showSlotWaktu(Request $request)
    {
        $search = $request->input('search');
        $perPage = 10;
    
        $slotWaktus = SlotWaktu::with([
            'slotWaktuTingkatanKelas.tingkatanKelas',
            'slotWaktuTingkatanKelas.sesiBelajar',
            'slotWaktuTingkatanKelas.slotWaktuMapping.hari'
        ])
        ->when($search, function ($query) use ($search) {
            return $query->where(function($q) use ($search) {
                $q->whereHas('slotWaktuTingkatanKelas.tingkatanKelas', function ($query) use ($search) {
                    $query->where('nama_tingkatan', 'like', '%' . $search . '%');
                })
                ->orWhereHas('slotWaktuTingkatanKelas.slotWaktuMapping.hari', function ($query) use ($search) {
                    $query->where('nama_hari', 'like', '%' . $search . '%');
                })
                ->orWhereHas('slotWaktuTingkatanKelas.sesiBelajar', function ($query) use ($search) {
                    $query->where('nama', 'like', '%' . $search . '%');
                })
                ->orWhereRaw("TIME_FORMAT(mulai, '%H:%i') LIKE ?", ['%' . $search . '%'])
                ->orWhereRaw("TIME_FORMAT(selesai, '%H:%i') LIKE ?", ['%' . $search . '%']);
            });
        });
    
        $slotWaktuCount = (clone $slotWaktus)->count();
    
        $slotWaktus = $slotWaktus->paginate($perPage)
            ->through(function ($slotWaktu) {
                return [
                    'id' => $slotWaktu->id,
                    'jam_mulai' => $slotWaktu->mulai->format('H:i'),
                    'jam_selesai' => $slotWaktu->selesai->format('H:i'),
                    'is_istirahat' => $slotWaktu->is_istirahat,
                    'tingkatan_kelas' => $slotWaktu->slotWaktuTingkatanKelas->map(function($tk) {
                        return [
                            'nama' => $tk->tingkatanKelas->nama_tingkatan,
                            'sesi_belajar' => $tk->sesiBelajar->nama,
                            'hari' => $tk->slotWaktuMapping->map(function($mapping) {
                                return $mapping->hari->nama_hari;
                            })->implode(', ')
                        ];
                    })->toArray()
                ];
            });
    
        return view('admin.slotwaktu.index', compact('slotWaktus', 'slotWaktuCount'));
    }

    public function storeSlotWaktu(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'mulai' => 'required|date_format:H:i',
                'selesai' => 'required|date_format:H:i|after:mulai',
                'is_istirahat' => 'boolean',
                'tingkatan_kelas' => 'required|array',
                'tingkatan_kelas.*' => 'exists:tingkatan_kelas,id',
                'sesi_belajar_id' => 'required|exists:sesi_belajar,id',
                'hari' => 'required|array',
                'hari.*' => 'exists:hari,id'
            ]);
    
            // Check for conflicting slots
            $isConflicting = SlotWaktu::where('selesai', '>', $request->mulai)
                ->where('mulai', '<', $request->selesai)
                ->whereHas('slotWaktuTingkatanKelas', function ($query) use ($request) {
                    $query->whereIn('tingkatan_kelas_id', $request->tingkatan_kelas)
                          ->where('sesi_belajar_id', $request->sesi_belajar_id)
                          ->whereHas('slotWaktuMapping', function ($subQuery) use ($request) {
                              $subQuery->whereIn('hari_id', $request->hari);
                          });
                })
                ->exists();
    
            if ($isConflicting) {
                return redirect()->back()
                    ->with('status', 'error')
                    ->withErrors(['message' => 'Waktu slot bentrok dengan jadwal yang ada. Silakan pilih waktu yang tidak bertumpang tindih.'])
                    ->withInput();
            }
    
            DB::beginTransaction();
            $slotWaktu = SlotWaktu::create([
                'mulai' => $request->mulai,
                'selesai' => $request->selesai,
                'is_istirahat' => $request->boolean('is_istirahat', false)
            ]);
    
            foreach ($request->tingkatan_kelas as $tingkatanKelasId) {
                $slotWaktuTingkatanKelas = SlotWaktuTingkatanKelas::create([
                    'slot_waktu_id' => $slotWaktu->id,
                    'tingkatan_kelas_id' => $tingkatanKelasId,
                    'sesi_belajar_id' => $request->sesi_belajar_id
                ]);
    
                foreach ($request->hari as $hariId) {
                    SlotWaktuMapping::create([
                        'slot_waktu_tingkatan_kelas_id' => $slotWaktuTingkatanKelas->id,
                        'hari_id' => $hariId
                    ]);
                }
            }
    
            DB::commit();
            return redirect()
                ->route('admin.slotwaktu.index')
                ->with('status', 'slot-waktu-created')
                ->with('message', 'Slot waktu berhasil ditambah');
    
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Terjadi error saat menambahkan data data: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('status', 'error')
                ->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function editSlotWaktu($id)
    {
        $slotWaktu = SlotWaktu::with([
            'slotWaktuTingkatanKelas.tingkatanKelas',
            'slotWaktuTingkatanKelas.sesiBelajar',
            'slotWaktuTingkatanKelas.slotWaktuMapping.hari'
        ])->findOrFail($id);
        
        // Get all master data
        $tingkatanKelas = TingkatanKelas::all();
        $haris = Hari::all();
        $sesiBelajar = SesiBelajar::all()->pluck('nama', 'id');
    
        // Get selected data
        $selectedTingkatanIds = $slotWaktu->slotWaktuTingkatanKelas->pluck('tingkatan_kelas_id')->toArray();
        $selected_sesi_belajar = $slotWaktu->slotWaktuTingkatanKelas->first()->sesi_belajar_id ?? null;
        $selectedHariIds = $slotWaktu->slotWaktuTingkatanKelas->first()->slotWaktuMapping->pluck('hari_id')->toArray();
        $hariMapping = $haris->pluck('nama_hari', 'id')->toArray();
        $tingkatanKelasMapping = $tingkatanKelas->pluck('nama_tingkatan', 'id')->toArray();
        // dd($selectedHari);
        return view('admin.slotwaktu.edit', compact(
            'slotWaktu',
            'tingkatanKelas',
            'haris',
            'sesiBelajar',
            'selectedTingkatanIds',
            'selected_sesi_belajar',
            'selectedHariIds',
            'hariMapping',
            'tingkatanKelasMapping',
        ));
    }

    public function updateSlotWaktu(Request $request, $id)
    {
        try {
            DB::beginTransaction(); // Mulai transaksi database
            
            $slotWaktu = SlotWaktu::findOrFail($id);
    
            $validatedData = $request->validate([
                'tingkatan_kelas_id' => 'required|array',
                'hari_id' => 'required|array',
                'sesi_belajar_id' => 'required',
                'mulai' => 'required|date_format:H:i',
                'selesai' => 'required|date_format:H:i',
                'is_istirahat' => 'nullable|boolean',
            ]);
    
            // Update slot waktu
            $slotWaktu->mulai = $validatedData['mulai'];
            $slotWaktu->selesai = $validatedData['selesai'];
            $slotWaktu->is_istirahat = $request->has('is_istirahat') ? 1 : 0;
            $slotWaktu->save();
    
            // Hapus relasi yang ada
            $slotWaktu->slotWaktuTingkatanKelas()->delete();
    
            // Buat relasi baru untuk setiap tingkatan kelas
            foreach ($validatedData['tingkatan_kelas_id'] as $tingkatanKelasId) {
                $slotWaktuTingkatanKelas = $slotWaktu->slotWaktuTingkatanKelas()->create([
                    'tingkatan_kelas_id' => $tingkatanKelasId,
                    'sesi_belajar_id' => $validatedData['sesi_belajar_id']
                ]);
    
                // Buat slot waktu mapping untuk setiap hari
                foreach ($validatedData['hari_id'] as $hariId) {
                    $slotWaktuTingkatanKelas->slotWaktuMapping()->create([
                        'hari_id' => $hariId
                    ]);
                }
            }
    
            DB::commit(); // Commit transaksi jika semua berhasil
    
            return redirect()->route('admin.slotwaktu.index')
                ->with('status', 'slot-waktu-updated')
                ->with('message', 'Slot waktu berhasil diperbarui');
    
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback jika terjadi error
            Log::error('Terjadi error saat memperbarui data: ' . $e->getMessage());
    
            return redirect()->back()
                ->with('status', 'error')
                ->withErrors(['message' => 'Gagal memperbarui slot waktu'])
                ->withInput();
        }
    }

    public function destroySlotWaktu($id)
    {
        try {
            $slotWaktu = SlotWaktu::findOrFail($id);
            $slotWaktu->delete();

            return redirect()->back()->with('status', 'slotwaktu-deleted');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['message' => 'Terjadi kesalahan saat menghapus slot waktu.']);
        }
    }

    public function getAvailableSlots()
    {
        // Menggunakan scope
        return SlotWaktu::bukanIstirahat()->get();
    }

    public function getIstirahatSlots()
    {
        // Menggunakan scope
        return SlotWaktu::istirahat()->get();
    }
}
