<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Mata Pelajaran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f9fafb;
            font-weight: 600;
            border-bottom: 2px solid #3b82f6;
        }
        .jadwal-item {
            position: relative; 
            width: 100%;
            min-height: 56px;
            padding-left: 15px;
            align-items: center;  
        }

        .color-strip {
            position: absolute;  
            left: 0;          
            top: 0;           
            width: 10px;
            height: 42px;     
        }

        .jadwal-content {
            width: 100%;
            padding: 5px 5px 5px 5px;
        }

        .jadwal-content strong {
            display: block;    /* Stack the elements vertically */
        }

        .jadwal-content small {
            display: block;    /* Stack the elements vertically */
        }
        .text-center {
            text-align: center;
        }
        .font-bold {
            font-weight: 700;
        }
        .text-xl {
            font-size: 1.25rem;
        }
        .text-2xl {
            font-size: 1.5rem;
        }
        .text-green-600 {
            color: #16a34a;
        }
        .bg-white {
            background-color: white;
        }
        .p-4 {
            padding: 1rem;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body class="bg-white p-4">
    <div class="container">
        <h1 class="text-center text-2xl font-bold mb-4">Jadwal Mata Pelajaran Semua Tingkatan</h1>
        <h2 class="text-center text-xl mb-2">Tahun Ajaran: {{ $selectedPeriod }}</h2>
        <h2 class="text-center text-xl mb-4">MIN 2 Pontianak</h2>

        @foreach($tingkatanKelas as $tingkatan)
            <div class="{{ !$loop->first ? 'page-break' : '' }}">

                <h2 class="text-xl font-bold mb-4">Tingkatan Kelas {{ $tingkatan->nama_tingkatan }}</h2>

                @for($hari = 1; $hari <= 5; $hari++)
                    <h3 class="text-xl text-center mb-2">
                        @switch($hari)
                            @case(1) Senin @break
                            @case(2) Selasa @break
                            @case(3) Rabu @break
                            @case(4) Kamis @break
                            @case(5) Jumat @break
                        @endswitch
                    </h3>

                    <table class="min-w-full border-collapse mb-4">
                        <thead>
                            <tr>
                                <th class="border-b-2 border-gray-200 border-r-4 border-r-primaryColor px-4 py-2 w-40 text-left">Waktu/Kelas</th>
                                @foreach($jadwalPerTingkatan[$tingkatan->id][$hari]['kelas'] as $kelas)
                                    <th class="border-b-2 border-gray-200 px-4 py-2 text-center">{{ $tingkatan->nama_tingkatan }}{{ $kelas->nama_kelas }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jadwalPerTingkatan[$tingkatan->id][$hari]['matrix'] as $waktu => $data)
                                <tr class="border-b border-gray-100">
                                    <td class="px-4 py-2 text-sm border-r-4 border-r-primaryColor">
                                        {{ $waktu }}
                                    </td>
                                    @if($data['is_khusus'])
                                        <td colspan="{{ count($jadwalPerTingkatan[$tingkatan->id][$hari]['kelas']) }}" class="px-4 py-3 border-y-primaryColor border-y-2">
                                            <div class="text-center text-green-600 h-10 justify-center flex items-center font-bold text-2xl">
                                                {{ $data['nama_kegiatan'] ?? 'SLOT KHUSUS' }}
                                            </div>
                                        </td>
                                    @elseif($data['is_istirahat'])
                                        <td colspan="{{ count($jadwalPerTingkatan[$tingkatan->id][$hari]['kelas']) }}" class="px-4 py-3 border-y-primaryColor border-y-2">
                                            <div class="text-center text-green-600 h-10 justify-center flex items-center font-bold text-2xl">
                                                ISTIRAHAT
                                            </div>
                                        </td>
                                    @else
                                        @foreach($jadwalPerTingkatan[$tingkatan->id][$hari]['kelas'] as $kelas)
                                            <td class="px-2 py-2">
                                                @if($data['kelas'][$kelas->id]['mata_pelajaran'] != '-')
                                                <div class="jadwal-item">
                                                    <div class="color-strip" style="background-color: {{ $data['kelas'][$kelas->id]['warna'] }};"></div>
                                                    <div class="jadwal-content">
                                                        <strong style="font-size: 0.875rem;">{{ $data['kelas'][$kelas->id]['mata_pelajaran'] }}</strong>
                                                        <small style="font-size: 0.75rem;">{{ $data['kelas'][$kelas->id]['guru'] }}</small>
                                                    </div>
                                                </div>
                                                @endif
                                            </td>
                                        @endforeach
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endfor
            </div>
        @endforeach
    </div>
</body>
</html>
