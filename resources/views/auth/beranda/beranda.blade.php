@extends('auth/app')
@section('pagetittle')
    <div class="pagetitle">
        <h1>@yield('title', $title)</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item active">@yield('title', $title)</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
@endsection
@section('content')
    <section class="section dashboard">
        <div class="row">
            <!-- Suhu Card -->
            <div class="col-xxl-4 col-md-6">
                <div class="card info-card sales-card">
                    <div class="card-body">
                        <h5 class="card-title row">
                            <div class="col">
                                Suhu
                            </div>
                            <div class="col text-end">
                                <span>
                                    Hari ini
                                </span>
                            </div>
                        </h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-thermometer-half"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ $suhu }} °C</h6>
                                <span class="text-muted small pt-1">sebelumnya </span> <span
                                    class="text-primary small pt-2 ps-1 fw-bold">{{ $suhuSebelumnya }} °C</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- End Suhu Card -->

            <!-- Kelembaban Card -->
            <div class="col-xxl-4 col-md-6">
                <div class="card info-card revenue-card">
                    <div class="card-body">
                        <h5 class="card-title row">
                            <div class="col">
                                Kelembaban
                            </div>
                            <div class="col text-end">
                                <span>
                                    Hari ini
                                </span>
                            </div>
                        </h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-droplet-half"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ $kelembaban }} %</h6>
                                <span class="text-muted small pt-1">sebelumnya </span> <span
                                    class="text-success small pt-2 ps-1 fw-bold">{{ $kelembabanSebelumnya }} %</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- End Kelembaban Card -->

            <!-- Waktu Card -->
            <div class="col-xxl-4 col-md-6">
                <div class="card info-card">
                    <div class="card-body">
                        <h5 class="card-title row">
                            <div class="col">
                                Penetasan
                            </div>
                        </h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center"
                                style="background-color: #ffc10747; color: #e29a00;">
                                <i class="bi bi-egg"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ $telur }} menetas</h6>
                                <span class="text-muted small pt-1">dari </span>
                                <span class="small pt-2 ps-1 fw-bold" style="color: #012970">
                                    {{ $penetasan }} penetasan
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- End Waktu Card -->
        </div>
        <div class="row">
            <div class="col-lg-6 col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Grafik Suhu dan Kelembaban <span>| Minggu ini</span></h5>
                        <div id="grafikSuhuKelembaban"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Grafik Penetasan</h5>
                        <div id="grafikPenetasan"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('js')
    {{-- Suhu --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Mendapatkan data dari endpoint menggunakan Ajax
            fetch('{{ route('beranda.suhu.grafik') }}')
                .then(response => response.json())
                .then(data => {
                    // Memanggil fungsi untuk menggambar grafik dengan data yang diperoleh
                    drawChart(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                });

            // Fungsi untuk menggambar grafik dengan data yang diperoleh
            function drawChart(data) {
                new ApexCharts(document.querySelector("#grafikSuhuKelembaban"), {
                    series: [{
                        name: 'Suhu',
                        data: data.suhu,
                    }, {
                        name: 'Kelembaban',
                        data: data.kelembaban
                    }],
                    chart: {
                        height: 350,
                        type: 'area',
                        toolbar: {
                            show: true,
                            offsetX: 0,
                            offsetY: 0,
                            tools: {
                                download: true,
                                selection: false,
                                zoom: false,
                                zoomin: false,
                                zoomout: false,
                                pan: false,
                                reset: false | '<img src="/static/icons/reset.png" width="20">',
                                customIcons: []
                            },
                        },
                    },
                    markers: {
                        size: 4
                    },
                    fill: {
                        type: "gradient",
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.3,
                            opacityTo: 0.4,
                            stops: [0, 90, 100]
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 2
                    },
                    xaxis: {
                        type: 'datetime',
                        categories: data.categories
                    },
                    tooltip: {
                        x: {
                            format: 'dd/MM/yy HH:mm'
                        },
                    }
                }).render();
            }
        });
    </script>
    
    {{-- Penetasan --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Mendapatkan data dari endpoint menggunakan Ajax
            fetch('{{ route('beranda.penetasan.grafik') }}')
                .then(response => response.json())
                .then(data => {
                    // Memanggil fungsi untuk menggambar grafik dengan data yang diperoleh
                    drawChart(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                });

            // Fungsi untuk menggambar grafik dengan data yang diperoleh
            function drawChart(data) {
                var options = {
                    series: [{
                        name: 'Jumlah Telur',
                        type: 'column',
                        data: data.jumlahTelur
                    }, {
                        name: 'Menetas',
                        type: 'line',
                        data: data.menetas
                    }],
                    chart: {
                        height: 350,
                        type: 'line',
                        toolbar: {
                            show: true,
                            offsetX: 0,
                            offsetY: 0,
                            tools: {
                                download: true,
                                selection: false,
                                zoom: false,
                                zoomin: false,
                                zoomout: false,
                                pan: false,
                                reset: false | '<img src="/static/icons/reset.png" width="20">',
                                customIcons: []
                            },
                        },
                    },
                    markers: {
                        size: 4
                    },
                    stroke: {
                        curve: 'smooth',
                        width: [0, 4]
                    },
                    plotOptions: {
                        bar: {
                            columnWidth: '10%',
                            borderRadius: 5
                        }
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    xaxis: {
                        type: 'datetime',
                        categories: data.categories
                    },
                };

                var chart = new ApexCharts(document.querySelector("#grafikPenetasan"), options);
                chart.render();
            }
        });
    </script>
@endsection
