@extends('adminlte::page')

@section('title', isset($specification) ? 'Edit Spesifikasi' : 'Tambah Spesifikasi')

@section('content_header')
    <h1>{{ isset($specification) ? 'Edit Spesifikasi' : 'Tambah Spesifikasi' }}</h1>
@stop

@section('plugins.SweetAlert2', true)

@section('content')
    <x-adminlte-card>
        {{-- Tombol Back --}}
        <a href="{{ route('specification-management.index') }}" class="btn btn-secondary mb-3">Kembali</a>

        <form id="specificationForm"
            action="{{ isset($specification) ? route('specification-management.update', $specification->specification_id) : route('specification-management.store') }}"
            method="POST">
            @csrf
            @if (isset($specification))
                @method('PUT')
            @endif

            {{-- ID Spesifikasi --}}
            @if (isset($specification))
                <div class="form-group">
                    <label for="specification_id">ID Spesifikasi</label>
                    <input type="text" name="specification_id" id="specification_id" class="form-control"
                        value="{{ $specification->specification_id }}" readonly>
                </div>
            @endif

            {{-- Nama Spesifikasi --}}
            <div class="form-group">
                <label for="specification_name">Nama Spesifikasi</label>
                <input type="text" name="specification_name" id="specification_name"
                    class="form-control @error('specification_name') is-invalid @enderror"
                    value="{{ old('specification_name', $specification->specification_name ?? '') }}"
                    placeholder="Nama Spesifikasi" {{ isset($specification) ? 'disabled' : '' }}>
                @error('specification_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Satuan --}}
            <div class="form-group">
                <label for="unit">Satuan (Opsional)</label>
                <input type="text" name="unit" id="unit" class="form-control @error('unit') is-invalid @enderror"
                    value="{{ old('unit', $specification->unit ?? '') }}" placeholder="Contoh: cm, kg, pcs">
                @error('unit')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary mt-3">{{ isset($specification) ? 'Update' : 'Simpan' }}</button>
        </form>
    </x-adminlte-card>
@stop

@section('js')
    <script>
        document.getElementById('specificationForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const form = this;
            Swal.fire({
                title: 'Konfirmasi',
                text: "Apakah Anda yakin data sudah sesuai?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    </script>
@stop
