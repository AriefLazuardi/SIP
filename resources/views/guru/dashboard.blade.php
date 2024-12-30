@extends('layouts.utama')

@section('content')

<div class="bg-primaryColor text-white p-4 rounded-lg flex items-center justify-between">
  <div>
    <h1 class="text-2xl font-bold">Selamat Datang</h1>
    <div class="mt-4 w-80">
        <p class="flex">
            <span class="w-4/12">Nama</span>
            <span class="text-center">:</span>
            <span class="font-medium ml-1">{{ $guru->name }}</span>
        </p>
        <p class="flex">
            <span class="w-4/12">NIP</span>
            <span class="text-center">:</span>
            <span class="font-medium ml-1">{{ $guru->nip }}</span>
        </p>
        @if ($waliKelas)
            <p class="flex">
                <span class="w-4/12">Wali Kelas</span>
                <span class="text-center">:</span>
                <span class="font-medium ml-1">{{ $waliKelas->kelas->tingkatanKelas->nama_tingkatan . $waliKelas->kelas->nama_kelas }}</span>
            </p>
        @endif
    </div>
  </div>
  <span class="material-icons text-9xl flex text-left text-whiteColor">school</span>
</div>

<div class="grid grid-cols-2 gap-4 mt-4 ">
    <div class="bg-white rounded-lg shadow-md p-4">
        <h2 class="text-lg font-bold mb-4">Mata Pelajaran yang Diajar</h2>
        <ul class="space-y-2">
        @foreach ($mataPelajaran as $index => $mataPelajaranItem)
            <li class="flex items-center">
                <div class="w-8 h-8 rounded-lg" 
                    style="background-color: {{ $mataPelajaranItem->warna->kode_hex }};">
                    <span class="text-white font-bold flex items-center justify-center w-full h-full">{{ $index + 1 }}</span>
                </div>
                <span class="ml-3">{{ $mataPelajaranItem->nama }}</span>
            </li>
        @endforeach
        </ul>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-4">
        <h2 class="text-lg font-bold mb-4">Kelas Yang Diajar</h2>
        <div class="flex flex-col gap-2">
            @foreach ($kelas as $tingkatan => $kelasGroup)
                <div class="flex flex-wrap gap-2">
                    @foreach ($kelasGroup as $kelasItem)
                        <div class="w-8 h-8 rounded-lg" 
                            style="background-color: {{ $kelasItem->color}};">
                            <span class="text-white font-bold flex items-center justify-center w-full h-full">
                                {{ $kelasItem->nama_tingkatan }} {{ $kelasItem->nama_kelas }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-4 mt-4">
        <h2 class="text-lg font-bold mb-4">Kepadatan Jam Mengajar</h2>
        <div class="w-full h-full flex justify-center items-center -mt-5 ">
            <canvas id="barChart" style="max-width: 80%; max-height: 80%;"></canvas>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-4 mt-4 w-full h-96 ">
        <h2 class="text-lg font-bold mb-4">Total Jam Mengajar</h2>
        <div class="w-full h-full flex justify-center items-center -mt-10 ">
            <canvas id="pieChart" style="max-width: 80%; max-height: 80%;"></canvas>
        </div>
    </div>
    
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    if (typeof Chart !== 'undefined') {
        var barChartCtx = document.getElementById('barChart').getContext('2d');
        var barChart = new Chart(barChartCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartData['labels']) !!},
                datasets: [{
                    label: '',  // Menghapus label nama guru
                    data: {!! json_encode($chartData['datasets'][0]['data']) !!},
                    backgroundColor: [
                        '#00CC99',
                        '#66648B',
                        '#FF6600',
                        '#E60026',
                        '#8AB5BD',
                        '#FF9F40',
                        '#FF6384'
                    ],
                    borderWidth: 0,
                    borderColor: '#2b2b2b',
                    borderDash: [5, 5]
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max : 8,
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                return Math.floor(value);
                            }
                        },
                        grid: {
                            borderDash: [5, 5],
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false 
                    }
                }
            }
        });


    var pieCtx = document.getElementById('pieChart').getContext('2d');
    var pieChart = new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: ['Total Jam Mengajar', 'Maksimal Jam Mengajar'],
            datasets: [{
                data: [{{ $pieChartData['totalJamMengajar'] }}, {{ $pieChartData['sisaJam'] }}],
                backgroundColor: ['#00C620', '#F1F1F1'],
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 10,
                        pointStyle: 'circle',
                        usePointStyle: true,
                        padding: 15
                    },
                },
                tooltip: {
                    enabled: false
                }
            },
            layout: {
                padding: {
                    top: 50
                }
            }
        },
        plugins: [{
            beforeDraw: (chart) => {
                const ctx = chart.ctx;
                const legendLeft = chart.legend.left;
                const legendCenterX = legendLeft + (chart.legend.width / 3.3);
                ctx.save();
                ctx.font = '16px sans-serif';
                ctx.fillStyle = '#000';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText('Keterangan', legendCenterX, 120);
                ctx.restore();

                const chartArea = chart.chartArea;
                const centerX = (chartArea.left + chartArea.right) / 2;
                const centerY = (chartArea.top + chartArea.bottom) / 2;
                const fontSize = 26;

                ctx.save();
                ctx.font = `${fontSize}px sans-serif`;
                ctx.fillStyle = '#2B2B2B';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(`{{ $pieChartData['totalJamMengajar'] }} jam`, centerX, centerY - fontSize * 0.3);

                ctx.font = `${fontSize * 0.5}px sans-serif`;
                ctx.fillText('per minggu', centerX, centerY + fontSize * 0.5);
                ctx.restore();
            }
        }]
    });        
    } else {
        console.error('Chart.js is not loaded');
    }
</script>

@endsection

