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
    <section class="section">
        {{-- Grafik --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Grafik Penetasan</h5>
                        <div id="grafikPenetasan"></div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Notifikasi --}}
        @if (session('success'))
            <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-1"></i>
                <b>Sukses ! </b> {{ session('success') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                    aria-label="Close"></button>
            </div>
        @endif
        @if ($errors->any())
            @foreach ($errors->all() as $err)
                <div class="alert alert-danger bg-danger text-light border-0 alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-octagon me-1"></i>
                    <b>Gagal ! </b> {{ $err }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                        aria-label="Close"></button>
                </div>
            @endforeach
        @endif
        {{-- DataTables --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title row">
                            <div class="col">Data Penetasan Telur</div>
                            <div class="col text-end">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#tambahData"><i class="bi bi-plus-lg me-1"></i> Tambah
                                    Penetasan</button>
                            </div>
                        </h5>
                        <div class="datatable-wrapper datatable-loading sortable searchable fixed-columns p-3">
                            <table id="tablePenetasan" class="table" role="grid" style="min-height: 500px;">
                                <thead>
                                    <tr>
                                        <th class="col">#</th>
                                        <th class="col">Tanggal Mulai</th>
                                        <th class="col">Tanggal Selesai</th>
                                        <th class="col">Jumlah Telur</th>
                                        <th class="col">Prediksi Menetas</th>
                                        <th class="col">Total Menetas</th>
                                        <th class="col">Cek Harian</th>
                                        <th class="col"></th>
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

    {{-- Modal Create --}}
    <div class="modal fade" id="tambahData" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="color: #012970;">Tambah Data Penetasan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ url('/penetasan/create') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="inputDate" class="form-label">Tanggal Mulai <span
                                    class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="tanggal_mulai" name="tanggal_mulai"
                                required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="inputDate" class="form-label">Jumlah Telur <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="jumlah_telur" name="jumlah_telur" required>
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

    {{-- Modal Info --}}
    @foreach ($penetasans as $penetasan)
        <div class="modal fade" id="infoData{{ $penetasan->id_penetasan }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" style="color: #012970;">Data Penetasan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="fw-semibold">Tanggal Mulai</td>
                                    <td>:</td>
                                    <td>{{ \Carbon\Carbon::parse($penetasan->tanggal_mulai)->locale('id')->translatedFormat('l, j F Y (H:i)') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Tanggal Selesai</td>
                                    <td>:</td>
                                    <td>{{ \Carbon\Carbon::parse($penetasan->tanggal_selesai)->locale('id')->translatedFormat('l, j F Y (H:i)') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Jumlah Telur</td>
                                    <td>:</td>
                                    <td>{{ $penetasan->jumlah_telur }} butir</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Prediksi Menetas</td>
                                    <td>:</td>
                                    <td>{{ $penetasan->prediksi_menetas }} butir</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Total Menetas</td>
                                    <td>:</td>
                                    <td>{{ $penetasan->total_menetas }} butir</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Rata-rata Suhu</td>
                                    <td>:</td>
                                    <td>{{ $penetasan->rata_rata_suhu }} °C</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Rata-rata Kelembaban</td>
                                    <td>:</td>
                                    <td>{{ $penetasan->rata_rata_kelembaban }} %</td>
                                </tr>
                                @if ($penetasan->harian->isNotEmpty())
                                    @php $latestHarian = $penetasan->harian->last(); @endphp
                                    <tr style="border: transparent">
                                        <td colspan="3">
                                            <p class="fw-semibold">Kondisi Terakhir</p>
                                            <div class="container mb-1" style="height: 50vh">
                                                <div class="bg-dark h-100 text-center text-white"
                                                    style="border-radius: 25px;">
                                                    <p style="padding-top: 23vh">
                                                        <i class="bi bi-exclamation-circle-fill"></i>
                                                        <span>{{ $latestHarian->deskripsi }}</span>
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @else
                                    <tr style="border: transparent">
                                        <td colspan="3">
                                            <p class="fw-semibold">Kondisi Terakhir</p>
                                            <div class="container mb-1" style="height: 50vh">
                                                <div class="bg-secondary h-100 text-center text-white"
                                                    style="border-radius: 25px;">
                                                    <p style="padding-top: 23vh">
                                                        <i class="bi bi-exclamation-circle-fill"></i>
                                                        <span>Belum ada gambar</span>
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Modal Edit --}}
    @foreach ($penetasans as $penetasan)
        <div class="modal fade" id="editData{{ $penetasan->id_penetasan }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" style="color: #012970;">Edit Data Penetasan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ url('/penetasan/' . $penetasan->id_penetasan . '/edit') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="form-group mb-3">
                                <label for="inputDate" class="form-label">Tanggal Mulai <span
                                        class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="tanggal_mulai"
                                    name="tanggal_mulai" value="{{ $penetasan->tanggal_mulai }}" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="inputDate" class="form-label">Jumlah Telur <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="jumlah_telur" name="jumlah_telur"
                                    value="{{ $penetasan->jumlah_telur }}" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Modal Delete --}}
    @foreach ($penetasans as $penetasan)
        <div class="modal fade" id="deleteData{{ $penetasan->id_penetasan }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ url('/penetasan/' . $penetasan->id_penetasan . '/delete') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="container text-center p-3">
                                <img src="{{ asset('assets/img/local/danger.png') }}" width="80px" alt="">
                                <h3 class="mt-4 fw-bold">Anda yakin ingin hapus data ini ?</h3>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endsection
@section('js')
    {{-- Grafik --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Mendapatkan data dari endpoint menggunakan Ajax
            fetch('{{ route('penetasan.grafik') }}')
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

    {{-- Datatables --}}
    <script>
        $(document).ready(function() {
            var table = $('#tablePenetasan').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('penetasan') }}",
                    data: function(d) {
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
                        data: 'tanggal_mulai',
                        searchable: true,
                        render: function(data, type, full, meta) {
                            var tanggal_mulai = moment(full.tanggal_mulai);
                            var formattedDate = tanggal_mulai.format('dddd, D MMMM YYYY');
                            var formattedTime = tanggal_mulai.format('HH:mm');
                            return '<p class="mb-0">' +
                                formattedDate +
                                '</p>' +
                                '<p class="mb-0">Jam: ' +
                                formattedTime +
                                '</p>';
                        }
                    },
                    {
                        data: 'tanggal_selesai',
                        searchable: true,
                        render: function(data, type, full, meta) {
                            var tanggal_selesai = moment(full.tanggal_selesai);
                            var formattedDate = tanggal_selesai.format('dddd, D MMMM YYYY');
                            var formattedTime = tanggal_selesai.format('HH:mm');
                            return '<p class="mb-0">' +
                                formattedDate +
                                '</p>' +
                                '<p class="mb-0">Jam: ' +
                                formattedTime +
                                '</p>';
                        }
                    },
                    {
                        data: 'jumlah_telur',
                        searchable: true,
                        render: function(data, type, full, meta) {
                            return '<p class="mb-0">' + data + ' butir</p>';
                        }
                    },
                    {
                        data: 'prediksi_menetas',
                        searchable: true,
                        render: function(data, type, full, meta) {
                            return '<p class="mb-0">' + data + ' butir</p>';
                        }
                    },
                    {
                        data: 'total_menetas',
                        searchable: true,
                        render: function(data, type, full, meta) {
                            return '<p class="mb-0">' + data + ' butir</p>';
                        }
                    },
                    {
                        data: 'id_penetasan',
                        searchable: true,
                        render: function(data, type, full, meta) {
                            var url = "/penetasan/" + data + "/harian";
                            return '<a type="button" class="btn btn-primary btn-sm" href="' + url +
                                '"><i class="bi bi-info-circle me-1"></i> Cek Harian</a>';
                        }
                    },
                    {
                        data: 'id_penetasan',
                        searchable: false,
                        orderable: false,
                        render: function(data, type, full, meta) {
                            return '<div class="dropdown">' +
                                '<a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></a>' +
                                '<ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">' +
                                '<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#infoData' +
                                data +
                                '"><span><i class="bi bi-info-circle"></i></span>Info</a></li>' +
                                '<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editData' +
                                data +
                                '"><span><i class="bi bi-pencil-square"></i></span>Edit</a></li>' +
                                '<li><a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#deleteData' +
                                data + '"><span><i class="bi bi-trash"></i></span>Delete</a></li>' +
                                '</ul>' +
                                '</div>';
                        }
                    }
                ],
                lengthMenu: [
                    [10, 25, 50, 100, -1], // Jumlah entries per halaman, -1 untuk Tampilkan Semua Data
                    ['10', '25', '50', '100', 'Semua']
                ]
            });

            $(
                    '<span class="ms-2"><label>Filter: <input type="month" id="filterBulan" class="form-control"></label></span>'
                )
                .appendTo('.dataTables_wrapper .dataTables_filter');

            $('#filterBulan').on('change', function() {
                table.ajax.reload();
            });

        });
    </script>
@endsection
