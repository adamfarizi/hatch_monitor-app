@extends('auth/app')
@section('pagetittle')
    <div class="pagetitle">
        <h1>@yield('title', $title)</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="">Home</a></li>
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
                                <h6>38 °C</h6>
                                <span class="text-muted small pt-1">sebelumnya </span> <span
                                    class="text-primary small pt-2 ps-1 fw-bold">41 °C</span>
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
                                <h6>50 %</h6>
                                <span class="text-muted small pt-1">sebelumnya </span> <span
                                    class="text-success small pt-2 ps-1 fw-bold">50 %</span>
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
                                Waktu
                            </div>
                        </h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center"
                                style="background-color: #ffc10747; color: #e29a00;">
                                <i class="bi bi-clock"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ \Carbon\Carbon::now()->format('h:i A') }}</h6>
                                <span class="text-muted small pt-1">hari </span>
                                <span class="small pt-2 ps-1 fw-bold" style="color: #012970">
                                    {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- End Waktu Card -->
        </div>
        <div class="row">
            {{-- Live Preview --}}
            <div class="col-xxl-8 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title row">
                            <div class="col">
                                Live Preview Alat
                            </div>
                            <div class="col text-end">
                                <span>
                                    Hari ini
                                </span>
                            </div>
                        </h5>
                        <div class="container mb-1" style="height: 50vh">
                            <div class="bg-dark h-100 text-center text-white" style="  border-radius: 25px;">
                                <p style="padding-top: 23vh">
                                    <i class="bi bi-exclamation-circle-fill"></i>
                                    <span>Kamera tidak tersambung !</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Button --}}
            <div class="col-xxl-4 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title row">
                            <div class="col">
                                Kontrol Alat
                            </div>
                        </h5>
                        <div class="container">
                            <div class="py-1">
                                <p class="small text-muted">Lampu 1</p>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-primary btn-lg" type="button">ON</button>
                                </div>
                            </div>
                            <div class="py-1">
                                <p class="small text-muted">Lampu 2</p>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-primary btn-lg" type="button">ON</button>
                                </div>
                            </div>
                            <div class="py-1">
                                <p class="small text-muted">Lampu LED</p>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-primary btn-lg" type="button">ON</button>
                                </div>
                            </div>
                            <div class="py-1">
                                <p class="small text-muted">Kipas</p>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-primary btn-lg" type="button">ON</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Grafik --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Grafik Suhu dan Kelembaban <span>| Minggu ini</span></h5>
                        <div id="grafikSuhuKelembaban"></div>
                    </div>
                </div>
            </div>
        </div>
        {{-- DataTables --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Data Suhu dan Kelembaban</h5>
                        <div class="datatable-wrapper datatable-loading sortable searchable fixed-columns p-3">
                            <table id="tableSuhuKelembaban" class="table" role="grid" style="min-height: 500px;">
                                <thead>
                                    <tr>
                                        <th class="col">#</th>
                                        <th class="col">Tanggal</th>
                                        <th class="col">Jam</th>
                                        <th class="col">Suhu</th>
                                        <th class="col-2">Kelembaban</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('js')
    {{-- Grafik --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Mendapatkan data dari endpoint menggunakan Ajax
            fetch('{{ route('kontrolalat.grafik') }}')
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
                            show: false
                        },
                    },
                    markers: {
                        size: 4
                    },
                    colors: ['#4154f1', '#2eca6a'],
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

    {{-- Datatables --}}
    <script>
        $(document).ready(function() {
            var table = $('#tableSuhuKelembaban').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('kontrolalat') }}",
                    data: function(d) {
                        // Mengambil nilai bulan dari input tanggal
                        var filterBulan = $('#filterBulan').val();
                        d.filterBulan = filterBulan;
                    }
                },
                columns: [{
                        data: null,
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'waktu_monitor',
                        searchable: true,
                        render: function(data, type, full, meta) {
                            var waktu_monitor = moment(full.waktu_monitor);
                            var formattedDate = waktu_monitor.format('dddd, D MMMM YYYY');
                            return formattedDate;
                        }
                    },
                    {
                        data: 'waktu_monitor',
                        searchable: true,
                        render: function(data, type, full, meta) {
                            var waktu_monitor = moment(full.waktu_monitor);
                            var formattedTime = waktu_monitor.format('HH:mm');
                            return formattedTime;
                        }
                    },
                    {
                        data: 'suhu_monitor',
                        searchable: true,
                        render: function(data, type, full, meta) {
                            return '<p class="mb-0">' + data + ' &deg;C</p>';
                        }
                    },
                    {
                        data: 'kelembaban_monitor',
                        searchable: true,
                        render: function(data, type, full, meta) {
                            return '<p class="mb-0">' + data + ' %</p>';
                        }
                    }
                ],
                lengthMenu: [
                    [10, 25, 50, 100, -1], // Jumlah entries per halaman, -1 untuk Tampilkan Semua Data
                    ['10', '25', '50', '100', 'Semua']
                ]
            });

            // Membuat input bulan di samping kotak pencarian
            $('<span class="ms-2"><label>Filter: <input type="month" id="filterBulan" class="form-control"></label></span>')
                .appendTo('.dataTables_wrapper .dataTables_filter');

            // Menambahkan event listener untuk filter per bulan
            $('#filterBulan').on('change', function() {
                table.ajax.reload();
            });

        });
    </script>
@endsection
