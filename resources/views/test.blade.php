@extends('layouts.component.main')
@section('title', 'RFID')

@section('content')

    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center">
            <div class="flex-grow-1">
                <h4 class="fs-xl fw-bold m-0">Test</h4>
            </div>
        </div>

        <form method="POST" action="/download-excel">
            @csrf
            <label>Masukkan NIK (satu per baris):</label><br>
            <textarea name="nik_list" rows="15" style="width:300px;"></textarea>
            <br><br>
            <button type="submit">Download Excel</button>
        </form>

    </div>

@endsection
