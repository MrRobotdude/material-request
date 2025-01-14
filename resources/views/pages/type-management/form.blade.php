@extends('adminlte::page')

@section('title', isset($type) ? 'Edit Type' : 'Tambah Type')

@section('content_header')
    <h1>{{ isset($type) ? 'Edit Type' : 'Tambah Type' }}</h1>
@stop

@section('plugins.SweetAlert2', true)
@section('plugins.Select2', true)

@section('content')
    <x-adminlte-card>
        {{-- Tombol Back --}}
        <a href="{{ route('type-management.index') }}" class="btn btn-secondary mb-3">Kembali</a>

        <form id="typeForm"
            action="{{ isset($type) ? route('type-management.update', $type->type_code) : route('type-management.store') }}"
            method="POST">
            @csrf
            @if (isset($type))
                @method('PUT')
            @endif

            {{-- Nama Type --}}
            <div class="form-group">
                <label for="type_name">Nama Type</label>
                <input type="text" name="type_name" id="type_name"
                    class="form-control @error('type_name') is-invalid @enderror"
                    value="{{ old('type_name', $type->type_name ?? '') }}" placeholder="Nama Type" required
                    {{ isset($type) ? 'disabled' : '' }}>
                @error('type_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Initial --}}
            <div class="form-group">
                <label for="initial">Initial</label>
                <input type="text" name="initial" id="initial"
                    class="form-control @error('initial') is-invalid @enderror"
                    value="{{ old('initial', $type->initial ?? '') }}" maxlength="4" placeholder="Initial" required>
                @error('initial')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">{{ isset($type) ? 'Update' : 'Simpan' }}</button>
        </form>
    </x-adminlte-card>
@stop

@section('js')
    <script>
        document.getElementById('typeForm').addEventListener('submit', function(event) {
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
