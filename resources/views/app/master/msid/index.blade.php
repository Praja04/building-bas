@extends('layouts.component.main')
@section('title', 'Data MSID')
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
        <div class="card shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold">Data MSID</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle" id="table">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th>NIK</th>
                                <th>Nama</th>
                                <th>Card ID</th>
                                <th>Departemen</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    {{-- MODAL ADD / EDIT EVENT --}}
    <div class="modal fade" id="modalMsid" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleMsid"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form id="formMsid">
                        <input type="hidden" id="msid_id" name="msid_id">
                        <div class="mb-3">
                            <label class="form-label">NIK <span class="text-danger">*</span></label>
                            <input type="text" id="nik" name="nik" class="form-control" placeholder="NIK">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" id="nama" name="nama" class="form-control" placeholder="Nama">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Card ID <span class="text-danger">*</span></label>
                            <input type="text" id="card_id" name="card_id" class="form-control" placeholder="Card ID">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Departemen <span class="text-danger">*</span></label>
                            <input type="text" id="departemen" name="departemen" class="form-control"
                                placeholder="Departemen">
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button class="btn btn-light" data-bs-dismiss="modal" type="button">Batal</button>
                            <button class="btn btn-primary" id="btnSaveMsid" type="submit">Simpan</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // DATATABLE
            let table = $('#table').DataTable({
                ajax: "{{ route('msid.index') }}",
                processing: true,
                serverSide: true,
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'nik',
                        name: 'nik',
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'card_id',
                        name: 'card_id',
                    },

                    {
                        data: 'departemen',
                        name: 'departemen'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `
                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-sm btn-primary btnEdit" data-id="${data.id}">Edit</button>
                            </div>                        
                            `;
                        }
                    }
                ]
            });

            $('body').on('click', '.btnEdit', function() {
                let id = $(this).data('id');
                $('#modalTitleMsid').text('Edit MSID');

                $.ajax({
                    url: "/master/msid/" + id,
                    type: "GET",
                    success: function(res) {
                        $('#msid_id').val(res.id);
                        $('#nik').val(res.nik);
                        $('#nama').val(res.nama);
                        $('#card_id').val(res.card_id);
                        $('#departemen').val(res.departemen);
                        $('#modalMsid').modal('show');
                    },
                    error: function(xhr) {
                        let msg = "Terjadi kesalahan saat mengambil data peserta";

                        if (xhr.status === 404) {
                            msg = 'Data tidak ditemukan.';
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: msg,
                        });
                    },
                });
            });

            $('#formMsid').on('submit', function(e) {
                e.preventDefault();

                let id = $('#msid_id').val();;

                $.ajax({
                    url: "/master/msid/" + id,
                    type: "POST",
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        $('#modalMsid').modal('hide');
                        $('#formMsid')[0].reset();
                        table.ajax.reload();

                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses!',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        let msg = "Terjadi kesalahan saat menyimpan data";

                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorList = '';

                            $.each(errors, function(key, value) {
                                errorList += '• ' + value[0] + '<br>';
                            });

                            Swal.fire({
                                icon: 'warning',
                                title: 'Validasi Gagal',
                                html: errorList,
                                confirmButtonText: 'OK'
                            });
                            return;
                        }

                        if (xhr.status === 404) {
                            msg = 'Data tidak ditemukan atau sudah dihapus.';
                        }

                        if (xhr.status === 500 && xhr.responseJSON && xhr.responseJSON
                            .message) {
                            msg = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: msg,
                        });
                    },
                });
            });
        });
    </script>
@endsection
