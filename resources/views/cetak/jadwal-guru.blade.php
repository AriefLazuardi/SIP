<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Mata Pelajaran</title>
    <style>
        /* Global Styles */
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        h2, h3, h4 {
            margin-bottom: 10px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: center;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .khusus, .istirahat {
            text-align: center;
            font-weight: bold;
            padding: 10px;
            background-color: #f0fdf4;
        }
        .khusus {
            color: #f97316;
        }
        .istirahat {
            color: #22c55e;
        }
        .jadwal-item {
            display: flex;
            align-items: center;
            width: 100%;
            min-height: 40px;
        }
        .color-strip {
            width: 10px;
            height: 40px;
            margin-right: 10px;
        }
        .jadwal-content {
            flex: 1;
            text-align: left;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        td {
            padding: 5px;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <h1 style="font-size: 30px; text-align: center;">Jadwal Mata Pelajaran Pribadi</h1>
    <p style="font-size: 18px; text-align: center;"><strong>Nama Guru:</strong> {{ $namaGuru }}</p>
    <h2 style="text-align: center;">Tahun Ajaran: {{ $selectedPeriod }}</h2>
    <h2 style=" text-align: center;">MIN 2 Pontianak</h2>

    @foreach($tingkatanKelas as $tingkatan)
        @if($tingkatanKelasIds->contains($tingkatan->id) && isset($jadwalPerTingkatan[$tingkatan->id]))
            <h2 style="font-size: 24px;">Tingkatan Kelas {{ $tingkatan->nama_tingkatan }}</h2>

            @for($hari = 1; $hari <= 5; $hari++)
                @if(isset($jadwalPerTingkatan[$tingkatan->id][$hari]))
                    <h1 style="font-size: 24px">
                        @switch($hari)
                            @case(1) Senin @break
                            @case(2) Selasa @break
                            @case(3) Rabu @break
                            @case(4) Kamis @break
                            @case(5) Jumat @break
                        @endswitch
                    </h1>

                    <table>
                        <thead>
                            <tr>
                                <th>Waktu/Kelas</th>
                                @foreach($jadwalPerTingkatan[$tingkatan->id][$hari]['kelas'] as $kelas)
                                    <th>{{ $tingkatan->nama_tingkatan }}{{ $kelas->nama_kelas }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jadwalPerTingkatan[$tingkatan->id][$hari]['matrix'] as $waktu => $data)
                                <tr>
                                    <td>{{ $waktu }}</td>
                                    @if($data['is_khusus'])
                                        <td colspan="{{ count($jadwalPerTingkatan[$tingkatan->id][$hari]['kelas']) }}" class="khusus">
                                            {{ $data['nama_kegiatan'] ?? 'SLOT KHUSUS' }}
                                        </td>
                                    @elseif($data['is_istirahat'])
                                        <td colspan="{{ count($jadwalPerTingkatan[$tingkatan->id][$hari]['kelas']) }}" class="istirahat">
                                            ISTIRAHAT
                                        </td>
                                    @else
                                        @foreach($jadwalPerTingkatan[$tingkatan->id][$hari]['kelas'] as $kelas)
                                            <td>
                                                @if(isset($data['kelas'][$kelas->id]) && isset($data['kelas'][$kelas->id]['guru']) && $data['kelas'][$kelas->id]['guru'] == $namaGuru)
                                                    <div class="jadwal-item">
                                                        <div class="color-strip" style="background-color: {{ $data['kelas'][$kelas->id]['warna'] ?? '#CCCCCC' }};"></div>
                                                        <div class="jadwal-content">
                                                            <strong>{{ $data['kelas'][$kelas->id]['mata_pelajaran'] ?? '-' }}</strong>
                                                            <br>
                                                            <small>{{ $data['kelas'][$kelas->id]['guru'] }}</small>
                                                        </div>
                                                    </div>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        @endforeach
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            @endfor
        @endif
    @endforeach
</body>
</html>
