@extends('layouts.component.main')
@section('title', 'Pengambilan Makan')
@section('content')
    <div class="page-title-head d-flex align-items-center mb-4">
        <div class="flex-grow-1">
            <h4 class="fs-xl fw-bold m-0">@yield('title')</h4>
        </div>
        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript:void(0)">{{ env('APP_NAME') }}</a></li>
                <li class="breadcrumb-item active">@yield('title')</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <ul class="nav nav-tabs" id="pengambilanMakanTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="form-tab" data-bs-toggle="tab" data-bs-target="#formTab" type="button"
                    role="tab">Pengambilan Makan</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="data-tab" data-bs-toggle="tab" data-bs-target="#dataTab" type="button"
                    role="tab">Data Pengambilan Makan</button>
            </li>
        </ul>

        <div class="tab-content mt-3" id="pengambilanMakanTabContent">
            {{-- TAB FORM --}}
            <div class="tab-pane fade show active" id="formTab" role="tabpanel">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-semibold">Form Pengambilan Makan</h6>
                    </div>
                    <div class="card-body">
                        <form id="formPengambilanMakan">
                            <div class="row g-3">
                                <!-- Kolom Kiri: Foto -->
                                <div class="col-md-4 d-flex justify-content-center align-items-center">
                                    <div class="text-center">
                                        <img id="fotoPegawai" src="{{ asset('assets/images/no-image.jpg') }}"
                                            alt="Foto Pegawai" class="rounded border"
                                            style="width: 250px; height: 300px; object-fit: cover;">
                                        <div class="mt-2">
                                            <small class="text-muted">Foto Pegawai</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kolom Kanan: Form Scan & Data -->
                                <div class="col-md-8">
                                    <div class="row g-3">
                                        <!-- Scan Kartu -->
                                        <div class="col-12">
                                            <div class="row g-2 align-items-end">
                                                <div class="col">
                                                    <label class="form-label fw-semibold">Scan Kartu RFID</label>
                                                    <input type="text" class="form-control" id="cardnodevice"
                                                        name="cardnodevice" placeholder="Tempelkan kartu..." autofocus>
                                                </div>
                                                <div class="col-auto">
                                                    <button type="button" class="btn btn-outline-secondary"
                                                        id="btnResetRFID">
                                                        Reset RFID
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- NIK -->
                                        <div class="col-md-12">
                                            <label class="form-label fw-semibold">
                                                NIK <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" name="nik" id="nik"
                                                required readonly>
                                        </div>

                                        <!-- Nama Pegawai -->
                                        <div class="col-md-12">
                                            <label class="form-label fw-semibold">
                                                Nama Pegawai <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" name="nama_pegawai" id="nama_pegawai"
                                                required readonly>
                                        </div>

                                        <!-- Departemen -->
                                        <div class="col-md-12">
                                            <label class="form-label fw-semibold">
                                                Departemen <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" name="departemen" id="departemen"
                                                required readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- TAB DATA --}}
            <div class="tab-pane fade" id="dataTab" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-semibold">Daftar Barang Dititipkan</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle" id="tablePengambilan">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th>ID Card</th>
                                        <th>NIK</th>
                                        <th>Nama</th>
                                        <th>Departemen</th>
                                        <th>Waktu Ambil</th>
                                        <th width="10%">Status</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function playSound(soundFile) {
            try {
                const audio = new Audio(soundFile);
                audio.volume = 1.0;
                audio.play().catch(err => console.log('Audio play error:', err));
            } catch (error) {
                console.log('Sound error:', error);
            }
        }

        $('#btnResetRFID').on('click', function() {
            $('#cardnodevice, #nik, #nama_pegawai, #departemen').val('');
            $('#fotoPegawai').attr('src', "{{ asset('assets/images/no-image.jpg') }}");
            $('#cardnodevice').focus();
        });

        $('#cardnodevice').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                let cardValue = $(this).val().trim();

                if (cardValue !== '') {
                    $.ajax({
                        url: '/pengambilan-makan/get-by-card/' + cardValue,
                        type: 'GET',
                        dataType: 'json',
                        beforeSend: function() {
                            $('#cardnodevice').prop('disabled', true);
                        },
                        success: function(res) {
                            if (res.status === 'success') {
                                playSound("{{ asset('assets/sound/success.mp3') }}");

                                $('#nik').val(res.data.nik);
                                $('#nama_pegawai').val(res.data.nama);
                                $('#departemen').val(res.data.departemen);

                                if (res.data.foto) {
                                    $('#fotoPegawai').attr('src', res.data.foto);
                                } else {
                                    $('#fotoPegawai').attr('src',
                                        "{{ asset('assets/images/no-image.jpg') }}");
                                }

                                Swal.fire({
                                    icon: 'success',
                                    title: '✓ BERHASIL!',
                                    html: `
                                <div class="text-center">
                                    <h5 class="mb-2">${res.data.nama}</h5>
                                    <p class="text-muted mb-1">${res.data.nik}</p>
                                    <p class="text-muted mb-3">${res.data.departemen}</p>
                                    <div class="alert alert-success mb-0">
                                        <i class="mdi mdi-check-circle"></i>
                                        <strong>Pengambilan Makan Tercatat</strong><br>
                                        <small>Tanggal: ${res.data.tanggal}</small><br>
                                        <small>Waktu: ${res.data.waktu_ambil} WIB</small>
                                    </div>
                                </div>
                            `,
                                    timer: 4000,
                                    timerProgressBar: true,
                                    showConfirmButton: false,
                                    allowOutsideClick: false
                                }).then(() => {
                                    $('#btnResetRFID').click();
                                });

                            }
                        },
                        error: function(xhr) {
                            let res = xhr.responseJSON;

                            if (xhr.status === 403 && res.status === 'rejected') {
                                // ❌ DITOLAK - Sudah ambil hari ini
                                playSound("{{ asset('assets/sound/failed.mp3') }}");
                                $('#nik').val(res.data.nik);
                                $('#nama_pegawai').val(res.data.nama);
                                $('#departemen').val(res.data.departemen || '');

                                if (res.data.foto) {
                                    $('#fotoPegawai').attr('src', res.data.foto);
                                }

                                Swal.fire({
                                    icon: 'error',
                                    title: '⚠️ DITOLAK!',
                                    html: `
                                <div class="text-center">
                                    <h5 class="mb-2">${res.data.nama}</h5>
                                    <p class="text-muted mb-1">${res.data.nik}</p>
                                    <div class="alert alert-danger mb-0">
                                        <i class="mdi mdi-close-circle"></i>
                                        <strong>${res.message}</strong><br>
                                        <small>Waktu pengambilan: ${res.data.waktu_ambil_sebelumnya} WIB</small><br>
                                    </div>
                                    <p class="mt-3 mb-0 text-danger">
                                        <strong>Batas pengambilan: 1x per hari</strong>
                                    </p>
                                </div>
                            `,
                                    timer: 5000,
                                    timerProgressBar: true,
                                    showConfirmButton: false,
                                    allowOutsideClick: false,
                                    customClass: {
                                        popup: 'animated shake'
                                    }
                                }).then(() => {
                                    $('#btnResetRFID').click();
                                    $('#tablePengambilan').DataTable().ajax
                                        .reload()
                                });

                            } else if (xhr.status === 404) {
                                // ❌ Kartu tidak ditemukan
                                playSound("{{ asset('assets/sound/failed.mp3') }}");
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Kartu Tidak Terdaftar',
                                    text: res.message || 'Kartu tidak ditemukan di sistem',
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                                $('#btnResetRFID').click();
                                $('#tablePengambilan').DataTable().ajax
                                    .reload()
                            } else {
                                // ❌ Error lainnya
                                playSound("{{ asset('assets/sound/failed.mp3') }}");
                                console.error('Error:', xhr);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi Kesalahan',
                                    text: res.message ||
                                        'Terjadi kesalahan saat memproses data',
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                                $('#btnResetRFID').click();
                                $('#tablePengambilan').DataTable().ajax
                                    .reload()
                            }
                        },
                        complete: function() {
                            $('#cardnodevice').prop('disabled', false);
                        }
                    });
                }
            }
        });

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // DataTable
            $('#tablePengambilan').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('pengambilan-makan.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'cardnodevice',
                        name: 'cardnodevice'
                    },
                    {
                        data: 'nik',
                        name: 'nik'
                    },
                    {
                        data: 'nama_pegawai',
                        name: 'nama_pegawai'
                    },
                    {
                        data: 'departemen',
                        name: 'departemen'
                    },
                    {
                        data: 'waktu_ambil_formatted',
                        name: 'waktu_ambil_formatted'
                    },
                    {
                        data: 'status_badge',
                        name: 'status',
                        orderable: false
                    },
                ],
            });

            $('#data-tab').on('click', function() {
                $('#tablePengambilan').DataTable().ajax
                    .reload()
            });

            $('#form-tab').on('click', function() {
                $('#cardnodevice').focus();
            });
        });
    </script>
@endsection
