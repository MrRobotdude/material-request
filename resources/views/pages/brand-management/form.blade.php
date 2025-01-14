@extends('adminlte::page')

@section('title', isset($brand) ? 'Edit Brand' : 'Tambah Brand')

@section('content_header')
    <h1>{{ isset($brand) ? 'Edit Brand' : 'Tambah Brand' }}</h1>
@stop

@section('plugins.SweetAlert2', true)
@section('plugins.Select2', true)

@section('content')
    <x-adminlte-card>
        {{-- Tombol Back --}}
        <a href="{{ route('brand-management.index') }}" class="btn btn-secondary mb-3">Kembali</a>

        <form id="brandForm"
            action="{{ isset($brand) ? route('brand-management.update', $brand->brand_code) : route('brand-management.store') }}"
            method="POST">
            @csrf
            @if (isset($brand))
                @method('PUT')
            @endif

            {{-- Kode Brand --}}
            @if (isset($brand))
                <div class="form-group">
                    <label for="brand_code">Kode Brand</label>
                    <input type="text" name="brand_code" id="brand_code" class="form-control"
                        value="{{ $brand->brand_code }}" readonly>
                </div>
            @endif

            {{-- Nama Brand --}}
            <div class="form-group">
                <label for="brand_name">Nama Brand</label>
                <input type="text" name="brand_name" id="brand_name"
                    class="form-control @error('brand_name') is-invalid @enderror"
                    value="{{ old('brand_name', $brand->brand_name ?? '') }}" placeholder="Nama Brand"
                    {{ isset($brand) ? 'disabled' : '' }}>
                @error('brand_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Initial Brand --}}
            <div class="form-group">
                <label for="brand_initial">Initial Brand</label>
                <input type="text" name="brand_initial" id="brand_initial"
                    class="form-control @error('brand_initial') is-invalid @enderror"
                    value="{{ old('brand_initial', $brand->brand_initial ?? '') }}" placeholder="Initial Brand">
                @error('brand_initial')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">{{ isset($brand) ? 'Update' : 'Simpan' }}</button>
        </form>
    </x-adminlte-card>
@stop

@section('js')
    <script>
        document.getElementById('brandForm').addEventListener('submit', function(event) {
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
