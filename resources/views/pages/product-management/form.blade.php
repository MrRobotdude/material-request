@extends('adminlte::page')

@section('title', isset($product) ? 'Edit Produk' : 'Tambah Produk')

@section('content_header')
    <h1>{{ isset($product) ? 'Edit Produk' : 'Tambah Produk' }}</h1>
@stop

@section('plugins.SweetAlert2', true)
@section('plugins.Select2', true)

@section('content')
    <x-adminlte-card>
        {{-- Tombol Back --}}
        <a href="{{ route('product-management.index') }}" class="btn btn-secondary mb-3">Kembali</a>

        <form id="productForm"
            action="{{ isset($product) ? route('product-management.update', $product->product_code) : route('product-management.store') }}"
            method="POST">
            @csrf
            @if (isset($product))
                @method('PUT')
            @endif

            {{-- Kode Produk --}}
            @if (isset($product))
                <div class="form-group">
                    <label for="product_code">Kode Produk</label>
                    <input type="text" name="product_code" id="product_code" class="form-control"
                        value="{{ $product->product_code }}" readonly>
                </div>
            @endif

            {{-- Nama Produk --}}
            <div class="form-group">
                <label for="product_name">Nama Produk</label>
                <input type="text" name="product_name" id="product_name"
                    class="form-control @error('product_name') is-invalid @enderror"
                    value="{{ old('product_name', $product->product_name ?? '') }}" placeholder="Nama Produk"
                    {{ isset($product) ? 'disabled' : '' }}>
                @error('product_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Initial Produk --}}
            <div class="form-group">
                <label for="product_initial">Initial Produk</label>
                <input type="text" name="product_initial" id="product_initial"
                    class="form-control @error('product_initial') is-invalid @enderror"
                    value="{{ old('product_initial', $product->product_initial ?? '') }}" placeholder="Initial Produk">
                @error('product_initial')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary mt-3">{{ isset($product) ? 'Update' : 'Simpan' }}</button>
        </form>
    </x-adminlte-card>
@stop

@section('js')
    <script>
        document.getElementById('productForm').addEventListener('submit', function(event) {
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
