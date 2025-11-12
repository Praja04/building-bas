@extends('layouts.component.main')
@section('title', 'Penitipan Barang')
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
        <ul class="nav nav-tabs" id="penitipanTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="form-tab" data-bs-toggle="tab" data-bs-target="#formTab" type="button"
                    role="tab">Form Penitipan</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="data-tab" data-bs-toggle="tab" data-bs-target="#dataTab" type="button"
                    role="tab">Data Penitipan</button>
            </li>
        </ul>

        <div class="tab-content mt-3" id="penitipanTabContent">
            {{-- TAB FORM --}}
            <div class="tab-pane fade show active" id="formTab" role="tabpanel">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-semibold">Form Penitipan Barang</h6>
                    </div>
                    <div class="card-body">
                        <form id="formPenitipan">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="row g-2 align-items-end">
                                        <div class="col">
                                            <label class="form-label fw-semibold">Scan Kartu RFID</label>
                                            <input type="text" class="form-control" id="cardnodevice" name="cardnodevice"
                                                placeholder="Tempelkan kartu..." autofocus>
                                        </div>
                                        <div class="col-auto">
                                            <button type="button" class="btn btn-outline-secondary" id="btnResetRFID">Reset
                                                RFID</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">NIK <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nik" id="nik" required
                                        readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nama Penitip <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nama_penitip" id="nama_penitip"
                                        required readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Departemen <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="departemen" id="departemen" required
                                        readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">No Penitipan <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="no_penitipan" id="no_penitipan"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nomor Telepon</label>
                                    <input type="number" class="form-control" name="telepon" id="telepon">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Jenis Barang <span
                                            class="text-danger">*</span></label>
                                    <select name="jenis_barang" class="form-select" required>
                                        <option value="" selected disabled>Pilih jenis barang</option>
                                        <option value="Tas">Tas</option>
                                        <option value="Elektronik">Elektronik</option>
                                        <option value="Dokumen">Dokumen</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>

                                {{-- Kamera Penitip --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Foto Penitip <span
                                            class="text-danger">*</span></label>
                                    <div class="border rounded p-2 text-center position-relative">
                                        <video id="videoPenitip" width="100%" height="240" autoplay muted></video>
                                        <canvas id="canvasPenitip" class="d-none"></canvas>
                                        <img id="previewPenitip" class="img-fluid d-none rounded" />
                                        <input type="hidden" name="foto_penitip" id="foto_penitip">
                                        <div class="mt-2">
                                            <button type="button" id="capturePenitip" class="btn btn-sm btn-primary">📸
                                                Ambil
                                                Foto</button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Kamera Barang --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Foto Barang <span
                                            class="text-danger">*</span></label>
                                    <div class="border rounded p-2 text-center position-relative">
                                        <video id="videoBarang" width="100%" height="240" autoplay muted></video>
                                        <canvas id="canvasBarang" class="d-none"></canvas>
                                        <img id="previewBarang" class="img-fluid d-none rounded" />
                                        <input type="hidden" name="foto_barang" id="foto_barang">
                                        <div class="mt-2">
                                            <button type="button" id="captureBarang" class="btn btn-sm btn-primary">📸
                                                Ambil
                                                Foto</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Waktu Titip <span
                                            class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" name="waktu_titip"
                                        value="{{ now()->format('Y-m-d\TH:i') }}" required>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Keterangan</label>
                                    <textarea name="keterangan" class="form-control" rows="2" placeholder="Opsional"></textarea>
                                </div>
                            </div>

                            <div class="mt-4 text-end">
                                <button type="reset" class="btn btn-light">Batal</button>
                                <button type="submit" class="btn btn-primary" id="savePenitipan">Simpan</button>
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
                            <table class="table table-striped table-hover align-middle" id="tablePenitipan">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th>ID Card</th>
                                        <th>NIK</th>
                                        <th>Nama Penitip</th>
                                        <th>No Penitipan</th>
                                        <th>Waktu Titip</th>
                                        <th>Waktu Ambil</th>
                                        <th width="10%">Status</th>
                                        <th width="15%">Aksi</th>
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

    {{-- Modal Detail --}}
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Penitipan Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="fw-semibold">NIK:</label>
                            <p id="detail-nik" class="mb-0">-</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-semibold">Nama Penitip:</label>
                            <p id="detail-nama" class="mb-0">-</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-semibold">Departemen:</label>
                            <p id="detail-departemen" class="mb-0">-</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-semibold">Telepon:</label>
                            <p id="detail-telepon" class="mb-0">-</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-semibold">No Penitipan:</label>
                            <p id="detail-no-penitipan" class="mb-0">-</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-semibold">Jenis Barang:</label>
                            <p id="detail-jenis" class="mb-0">-</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-semibold">Waktu Titip:</label>
                            <p id="detail-waktu-titip" class="mb-0">-</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-semibold">Waktu Ambil:</label>
                            <p id="detail-waktu-ambil" class="mb-0">-</p>
                        </div>
                        <div class="col-12">
                            <label class="fw-semibold">Status:</label>
                            <p id="detail-status" class="mb-0">-</p>
                        </div>
                        <div class="col-12">
                            <label class="fw-semibold">Keterangan:</label>
                            <p id="detail-keterangan" class="mb-0">-</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-semibold">Foto Penitip:</label>
                            <div class="border rounded p-2 text-center">
                                <img id="detail-foto-penitip" src="" alt="Foto Penitip" class="img-fluid"
                                    style="max-height: 300px;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-semibold">Foto Barang:</label>
                            <div class="border rounded p-2 text-center">
                                <img id="detail-foto-barang" src="" alt="Foto Barang" class="img-fluid"
                                    style="max-height: 300px;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // Kamera
        $(function() {
            function setupCamera(videoSel, canvasSel, captureBtnSel, hiddenInputSel, previewImgSel) {
                let stream = null;
                const $video = $(videoSel);
                const $canvas = $(canvasSel);
                const $captureBtn = $(captureBtnSel);
                const $hiddenInput = $(hiddenInputSel);
                const $previewImg = $(previewImgSel);

                navigator.mediaDevices.getUserMedia({
                        video: true
                    })
                    .then(s => {
                        stream = s;
                        $video.each(function() {
                            this.srcObject = stream;
                        });
                    })
                    .catch(err => console.error("Kamera tidak dapat diakses:", err));

                $captureBtn.on('click', function() {
                    const canvas = $canvas[0];
                    const video = $video[0];
                    const ctx = canvas.getContext('2d');
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                    const data = canvas.toDataURL('image/png');
                    $hiddenInput.val(data);

                    if (stream) {
                        stream.getTracks().forEach(track => track.stop());
                    }

                    $video.addClass('d-none');
                    $previewImg.attr('src', data).removeClass('d-none');
                    $captureBtn.prop('disabled', true).text('✅ Foto Diambil');
                });
            }

            setupCamera(
                '#videoPenitip',
                '#canvasPenitip',
                '#capturePenitip',
                '#foto_penitip',
                '#previewPenitip'
            );

            setupCamera(
                '#videoBarang',
                '#canvasBarang',
                '#captureBarang',
                '#foto_barang',
                '#previewBarang'
            );
        });

        $('#btnResetRFID').on('click', function() {
            $('#cardnodevice, #nik, #nama_penitip, #departemen').val('');
            $('#cardnodevice').focus();
        });

        $('#cardnodevice').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                let cardValue = $(this).val().trim();

                if (cardValue !== '') {
                    $.ajax({
                        url: '/penitipan-barang/get-by-card/' + cardValue,
                        type: 'GET',
                        dataType: 'json',
                        beforeSend: function() {
                            $('#cardnodevice').prop('disabled', true);
                        },
                        success: function(res) {
                            if (res.status === 'success') {
                                $('#nik').val(res.data.nik);
                                $('#nama_penitip').val(res.data.nama);
                                $('#departemen').val(res.data.departemen);
                            } else {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Peringatan',
                                    text: res.message || 'Kartu tidak ditemukan!'
                                });
                                $('#btnResetRFID').click();
                            }
                        },
                        error: function(err) {
                            console.error(err);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan saat mengambil data.'
                            });
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
            $('#tablePenitipan').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('penitipan-barang.index') }}",
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
                        data: 'nama_penitip',
                        name: 'nama_penitip'
                    },
                    {
                        data: 'no_penitipan',
                        name: 'no_penitipan'
                    },
                    {
                        data: 'waktu_titip_formatted',
                        name: 'waktu_titip_formatted'
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
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
            });

            $('body').on('click', '.btn-detail', function() {
                let id = $(this).data('id');

                $.ajax({
                    url: "{{ url('/penitipan-barang/detail') }}/" + id,
                    type: 'GET',
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Loading...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        Swal.close();
                        if (response.status === 'success') {
                            let data = response.data;
                            $('#detail-nik').text(data.nik);
                            $('#detail-nama').text(data.nama_penitip);
                            $('#detail-departemen').text(data.departemen);
                            $('#detail-telepon').text(data.telepon);
                            $('#detail-no-penitipan').text(data.no_penitipan);
                            $('#detail-jenis').text(data.jenis_barang);
                            $('#detail-waktu-titip').text(data.waktu_titip);
                            $('#detail-waktu-ambil').text(data.waktu_ambil);
                            $('#detail-keterangan').text(data.keterangan);

                            // Status badge
                            if (data.status === 'belum_diambil') {
                                $('#detail-status').html(
                                    '<span class="badge bg-warning text-dark">Belum Diambil</span>'
                                );
                            } else {
                                $('#detail-status').html(
                                    '<span class="badge bg-success">Sudah Diambil</span>');
                            }

                            // Foto
                            if (data.foto_penitip) {
                                $('#detail-foto-penitip').attr('src', data.foto_penitip).show();
                            } else {
                                $('#detail-foto-penitip').hide();
                            }

                            if (data.foto_barang) {
                                $('#detail-foto-barang').attr('src', data.foto_barang).show();
                            } else {
                                $('#detail-foto-barang').hide();
                            }

                            $('#modalDetail').modal('show');
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal mengambil data detail'
                        });
                    }
                });
            });

            $('body').on('click', '.btn-ambil', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah barang sudah diambil oleh penitip?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Sudah Diambil',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/penitipan-barang/ambil/' + id,
                            type: 'POST',
                            success: function(response) {
                                if (response.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: response.message
                                    }).then(() => {
                                        $('#tablePenitipan').DataTable().ajax
                                            .reload()
                                    });
                                }
                            },
                            error: function(xhr) {
                                let response = xhr.responseJSON;
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message ||
                                        'Terjadi kesalahan'
                                });
                            }
                        });
                    }
                });
            });

            $('#data-tab').on('click', function() {
                $('#tablePenitipan').DataTable().ajax
                    .reload()
            });

            $('#formPenitipan').submit(function(e) {
                e.preventDefault();

                if (!$('#foto_penitip').val()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Foto penitip belum diambil!',
                    });
                    return false;
                }

                if (!$('#foto_barang').val()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Foto barang belum diambil!',
                    });
                    return false;
                }

                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('penitipan-barang.store') }}",
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function() {
                        $('#savePenitipan').prop('disabled', true).html(
                            '<i class="mdi mdi-loading mdi-spin me-2"></i> Proses...'
                        );

                        $('.form-control').removeClass('is-invalid');
                        $('.text-danger').html('');
                    },
                    complete: function() {
                        $('#savePenitipan').prop('disabled', false).text('Simpan');
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: response.message,
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        let response = xhr.responseJSON;
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan',
                            text: 'Terjadi kesalahan, silakan coba lagi.',
                        });
                    }
                });
            });
        });
    </script>
@endsection
