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
                                <h6 id="suhu">{{ $suhu }} °C</h6>
                                <span class="text-muted small pt-1">sebelumnya </span> <span
                                    class="text-primary small pt-2 ps-1 fw-bold" id="suhu-sebelumnya">{{ $suhuSebelumnya }}
                                    °C</span>
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
                                <h6 id="kelembaban">{{ $kelembaban }} %</h6>
                                <span class="text-muted small pt-1">sebelumnya </span> <span
                                    class="text-success small pt-2 ps-1 fw-bold"
                                    id="kelembaban-sebelumnya">{{ $kelembabanSebelumnya }} %</span>
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
                                <button type="button" class="btn btn-sm btn-link" data-bs-toggle="modal"
                                    data-bs-target="#modalLink">Perbarui Link Camera
                                </button>
                            </div>
                        </h5>
                        <div class="container mb-4">
                            <div class="bg-secondary text-white p-3" style="border-radius: 10px;">
                                <p class="mb-0">
                                    <i class="ri-information-line"></i> Perubahan kondisi terkadang
                                    mengalami keterlambatan karena jaringan !
                                </p>
                            </div>
                        </div>
                        <div class="container mb-4" style="height: 50vh">
                            <div class="bg-dark h-100 text-center text-white live-preview-container"
                                style="border-radius: 25px;">
                                <img id="livePreviewImage" src="{{ $link2 }}" width="100%" height="100%"
                                    scrolling="no" style="border: none; border-radius: 25px; object-fit: cover;">
                                <button id="fullscreenButton" class="btn btn-lg text-white p-0" onclick="openFullscreen()"
                                    style="position: absolute; top: 35%; left: 91%; transform: translate(-50%, -50%); font-size: 30px;">
                                    <i class="ri-fullscreen-fill"></i>
                                </button>
                                <p id="connectionStatus" class="pt-5"
                                    style="display: none; position: absolute; top: 55%; left: 50%; transform: translate(-50%, -50%);">
                                    <span><i class="ri-information-line"></i></span>Kamera tidak tersambung
                                </p>
                                @if (session('status'))
                                    <div class="alert alert-info bg-primary text-light border-0 alert-dismissible fade show"
                                        role="alert"
                                        style="position: absolute; z-index: 9999; top: 35%; left: 50%; transform: translate(-50%, -50%);">
                                        <i class="ri-information-line"></i>
                                        <b>Status ! </b> {{ session('status') }}
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @elseif (session('error'))
                                    <div class="alert alert-info bg-danger text-light border-0 alert-dismissible fade show"
                                        role="alert"
                                        style="position: absolute; z-index: 9999; top: 30%; left: 50%; transform: translate(-50%, -50%); width: 60%;">
                                        <i class="ri-information-line"></i>
                                        <b>Error ! </b> {{ session('error') }}
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @elseif (session('loading'))
                                    <div class="alert alert-info bg-warning text-dark border-0 alert-dismissible fade show"
                                        role="alert"
                                        style="position: absolute; z-index: 9999; top: 30%; left: 50%; transform: translate(-50%, -50%); width: 70%;">
                                        <i class="ri-information-line"></i>
                                        <b>Status ! </b> {{ session('loading') }}
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif
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
                                Kontrol Alat <span class="small text-muted">- alur scan telur</span>
                            </div>
                        </h5>
                        <div class="container" style="height:66vh">
                            <p class="small text-muted">Berikut adalah langkah - langkah sebelum melakukan proses scanning
                                pada telur ayam :</p>
                            {{-- 1 Button --}}
                            <form method="POST" action="{{ url('/kontrolRelay') }}">
                                @csrf
                                <div class="py-2">
                                    <p class="small">Lampu Bohlam
                                        <span class="ms-2">
                                            @if ($relay1 === 'On')
                                                <span class="badge rounded-pill bg-primary">On</span>
                                            @elseif ($relay1 === 'Off')
                                                <span class="badge rounded-pill bg-danger">Off</span>
                                            @else
                                                <span class="badge rounded-pill bg-secondary">Unknown</span>
                                            @endif
                                        </span>
                                    </p>
                                    <div class="d-grid gap-2">
                                        @if ($relay1 === 'On')
                                            <button class="btn btn-danger btn-lg" type="submit" name="relay"
                                                value="relay1_off">OFF</button>
                                        @elseif ($relay1 === 'Off')
                                            <button class="btn btn-primary btn-lg" type="submit" name="relay"
                                                value="relay1_on">ON</button>
                                        @else
                                            <button class="btn btn-secondary btn-lg" type="button"
                                                onclick="window.location.reload()">RELOAD</button>
                                        @endif
                                    </div>
                                    <p class="small mt-3 text-muted"><i class="ri-information-line"></i> Step 1: Matikan
                                        lampu
                                        bohlam untuk memaksimalkan hasil scan telur</p>
                                </div>
                                <div class="py-2">
                                    <p class="small">Lampu LED
                                        <span class="ms-2">
                                            @if ($relay2 === 'On')
                                                <span class="badge rounded-pill bg-primary">On</span>
                                            @elseif ($relay2 === 'Off')
                                                <span class="badge rounded-pill bg-danger">Off</span>
                                            @else
                                                <span class="badge rounded-pill bg-secondary">Unknown</span>
                                            @endif
                                        </span>
                                    </p>
                                    <div class="d-grid gap-2">
                                        @if ($relay2 === 'On')
                                            <button class="btn btn-danger btn-lg" type="submit" name="relay"
                                                value="relay2_off">OFF</button>
                                        @elseif ($relay2 === 'Off')
                                            <button class="btn btn-primary btn-lg" type="submit" name="relay"
                                                value="relay2_on">ON</button>
                                        @else
                                            <button class="btn btn-secondary btn-lg" type="button"
                                                onclick="window.location.reload()">RELOAD</button>
                                        @endif
                                    </div>
                                    <p class="small mt-3 text-muted">
                                        <i class="ri-information-line"></i> Step 2: Hidupkan lampu led untuk melakukan
                                        proses
                                        candling telur
                                    </p>
                                </div>
                            </form>

                            <p class="text-muted" style="font-size: 11px"><i class="ri-information-line"></i> Jika status
                                button sudah
                                berubah
                                namun kondisi lampu belum berubah, maka reload halaman atau tekan kembali button</p>

                            {{-- 2 Button --}}
                            {{-- <form method="POST" action="{{ url('/kontrolRelay') }}">
                                @csrf
                                <div class="py-2">
                                    <p class="small">Lampu LED
                                        <span class="ms-2">
                                            @if ($relay1 === 'On')
                                                <span class="badge rounded-pill bg-primary">On</span>
                                            @elseif ($relay1 === 'Off')
                                                <span class="badge rounded-pill bg-danger">Off</span>
                                            @else
                                                <span class="badge rounded-pill bg-secondary">Unknown</span>
                                            @endif
                                        </span>
                                    </p>
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-primary btn-lg" type="submit" name="relay"
                                            value="relay1_on">ON</button>
                                        <button class="btn btn-danger btn-lg" type="submit" name="relay"
                                            value="relay1_off">OFF</button>
                                    </div>
                                </div>
                                <div class="py-2">
                                    <p class="small">Lampu Bohlam
                                        <span class="ms-2">
                                            @if ($relay2 === 'On')
                                                <span class="badge rounded-pill bg-primary">On</span>
                                            @elseif ($relay2 === 'Off')
                                                <span class="badge rounded-pill bg-danger">Off</span>
                                            @else
                                                <span class="badge rounded-pill bg-secondary">Unknown</span>
                                            @endif
                                        </span>
                                    </p>
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-primary btn-lg" type="submit" name="relay"
                                            value="relay2_on">ON</button>
                                        <button class="btn btn-danger btn-lg" type="submit" name="relay"
                                            value="relay2_off">OFF</button>
                                    </div>
                                </div>
                            </form> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Grafik --}}
        <div class="row">
            <div class="col-lg-6 col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Grafik Suhu <span>| Minggu ini</span></h5>
                        <div id="temperatureChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Grafik Kelembaban <span>| Minggu ini</span></h5>
                        <div id="humidityChart"></div>
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

    {{-- Modal Link --}}
    <div class="modal fade" id="modalLink" tabindex="-1" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Link Camera ESP32Cam</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ url('/linkESPCAM') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="link1" class="form-label">Link Camera 1 <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="link1" name="link1"
                                placeholder="exp. http://192.168.1.150" value="{{ $link1 }}" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="link2" class="form-label">Link Camera 2 <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="link2" name="link2"
                                placeholder="exp. http://192.168.1.150" value="{{ $link2 }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
    {{-- Full Screen --}}
    <script>
        function openFullscreen() {
            const elem = document.getElementById('livePreviewImage');
            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            } else if (elem.mozRequestFullScreen) { // Firefox
                elem.mozRequestFullScreen();
            } else if (elem.webkitRequestFullscreen) { // Chrome, Safari and Opera
                elem.webkitRequestFullscreen();
            } else if (elem.msRequestFullscreen) { // IE/Edge
                elem.msRequestFullscreen();
            }
        }
    </script>

    {{-- EspCam --}}
    <script>
        // Fungsi untuk memeriksa apakah gambar di URL tertentu dapat diakses
        function checkImage(url, callback) {
            var img = new Image();
            img.onload = function() {
                callback(true);
            };
            img.onerror = function() {
                callback(false);
            };
            img.src = url;
        }

        var link = '{{ $link2 }}';

        // Periksa apakah gambar di URL dapat diakses
        checkImage(link, function(isAccessible) {
            var livePreviewContainer = document.querySelector('.live-preview-container');
            var livePreviewImage = document.getElementById('livePreviewImage');
            var connectionStatus = document.getElementById('connectionStatus');
            var fullscreenButton = document.getElementById('fullscreenButton');

            if (isAccessible) {
                // Jika gambar diakses, tampilkan gambar
                livePreviewContainer.style.background = 'none'; // Hapus latar belakang hitam
                livePreviewImage.style.display = 'block'; // Tampilkan gambar
                connectionStatus.style.display = 'none'; // Sembunyikan pesan kamera tidak tersambung
                fullscreenButton.style.display = 'block';
            } else {
                // Jika gambar tidak dapat diakses, tampilkan latar belakang hitam dan pesan kamera tidak tersambung
                livePreviewContainer.style.background = '#000'; // Latar belakang hitam
                livePreviewImage.style.display = 'none'; // Sembunyikan gambar
                connectionStatus.style.display = 'block'; // Tampilkan pesan kamera tidak tersambung
                fullscreenButton.style.display = 'none';
            }
        });
    </script>

    {{-- Realtime Card --}}
    <script>
        var channel = pusher.subscribe('thingspeak-channel');
        channel.bind('thingspeak-event', function(data) {
            updateSuhu(data.newData.suhu_monitor);
            updateKelembaban(data.newData.kelembaban_monitor);
        });

        var cardChannel = pusher.subscribe('card-channel');
        cardChannel.bind('card-event', function(data) {
            updateSuhuSebelumnya(data.lastData.suhu_sebelumnya);
            updateKelembabanSebelumnya(data.lastData.kelembaban_sebelumnya);
        });

        function updateSuhu(suhu) {
            document.getElementById('suhu').innerHTML = suhu + ' °C';
        }

        function updateSuhuSebelumnya(suhuSebelumnya) {
            document.getElementById('suhu-sebelumnya').innerHTML = suhuSebelumnya + ' °C';
        }

        function updateKelembaban(kelembaban) {
            document.getElementById('kelembaban').innerHTML = kelembaban + ' %';
        }

        function updateKelembabanSebelumnya(kelembabanSebelumnya) {
            document.getElementById('kelembaban-sebelumnya').innerHTML = kelembabanSebelumnya + ' %';
        }
    </script>

    {{-- Grafik --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Mendapatkan data dari endpoint menggunakan Ajax
            fetch('{{ route('kontrolalat.grafik') }}')
                .then(response => response.json())
                .then(data => {
                    // Memanggil fungsi untuk menggambar grafik dengan data yang diperoleh
                    drawTemperatureChart(data);
                    drawHumidityChart(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                });

            // Fungsi untuk menggambar grafik suhu dengan data yang diperoleh
            function drawTemperatureChart(data) {
                var options = {
                    series: [{
                        name: 'Suhu',
                        data: data.suhu,
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
                        categories: data.categories,
                        labels: {
                            datetimeUTC: false,
                        }
                    },
                    tooltip: {
                        x: {
                            format: 'dd/MM/yy HH:mm'
                        },
                    }
                };

                var temperatureChart = new ApexCharts(document.querySelector("#temperatureChart"), options);
                temperatureChart.render();
            }

            // Fungsi untuk menggambar grafik kelembaban dengan data yang diperoleh
            function drawHumidityChart(data) {
                var options = {
                    series: [{
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
                        colors: '#00e396',
                        size: 4
                    },
                    fill: {
                        colors: ['#00e396'],
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
                        width: 2,
                        colors: ['#00e396']
                    },
                    xaxis: {
                        type: 'datetime',
                        categories: data.categories,
                        labels: {
                            datetimeUTC: false,
                        }
                    },
                    tooltip: {
                        x: {
                            format: 'dd/MM/yy HH:mm'
                        },
                        marker: {
                            fillColors: ['#00e396']
                        }
                    }
                };

                var humidityChart = new ApexCharts(document.querySelector("#humidityChart"), options);
                humidityChart.render();
            }
        });
    </script>

    {{-- Datatables --}}
    <script>
        $(document).ready(function() {
            var table = $('#tableSuhuKelembaban').DataTable({
                processing: false,
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

            // Menambahkan event listener untuk event dari Pusher
            var channel = pusher.subscribe('thingspeak-channel');
            channel.bind('thingspeak-event', function(data) {
                // Menambahkan data baru ke dalam DataTables
                var newData = {
                    waktu_monitor: data.waktu_monitor,
                    suhu_monitor: data.suhu_monitor,
                    kelembaban_monitor: data.kelembaban_monitor
                };
                table.row.add(newData).draw();
            });
        });
    </script>
@endsection
