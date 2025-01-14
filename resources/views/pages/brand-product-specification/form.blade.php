@extends('adminlte::page')

@section('title', isset($relation) ? 'Edit Brand Product Specification' : 'Tambah Brand Product Specification')

@section('content_header')
    <h1>{{ isset($relation) ? 'Edit Brand Product Specification' : 'Tambah Brand Product Specification' }}</h1>
@stop

@section('plugins.Select2', true)
@section('plugins.SweetAlert2', true)

@section('content')
    <x-adminlte-card>
        {{-- Tombol Back --}}
        <a href="{{ route('brand-product-specification.index') }}" class="btn btn-secondary mb-3">Kembali</a>

        <form id="brandProductSpecForm"
            action="{{ isset($relation) ? route('brand-product-specification.update', $relation->id) : route('brand-product-specification.store') }}"
            method="POST">
            @csrf
            @if (isset($relation))
                @method('PUT')
            @endif

            {{-- Dropdown Brand --}}
            <div class="form-group">
                <label for="brand_code">Brand</label>
                <x-adminlte-select2 name="brand_code" id="brand_code" class="form-control">
                    <option value="" disabled {{ isset($relation) ? '' : 'selected' }}>-- Pilih Brand --</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->brand_code }}"
                            {{ old('brand_code', $relation->brand_code ?? '') == $brand->brand_code ? 'selected' : '' }}>
                            {{ $brand->brand_name }}
                        </option>
                    @endforeach
                </x-adminlte-select2>
            </div>

            {{-- Dropdown Product --}}
            <div class="form-group">
                <label for="product_code">Product</label>
                <x-adminlte-select2 name="product_code" id="product_code" class="form-control">
                    <option value="" disabled {{ isset($relation) ? '' : 'selected' }}>-- Pilih Product --</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->product_code }}"
                            {{ old('product_code', $relation->product_code ?? '') == $product->product_code ? 'selected' : '' }}>
                            {{ $product->product_name }}
                        </option>
                    @endforeach
                </x-adminlte-select2>
            </div>

            {{-- Dropdown Multiple Specification --}}
            @php
                $config = [
                    'placeholder' => 'Pilih specifications...',
                    'allowClear' => true,
                ];
            @endphp
            <div class="form-group">
                <label for="specifications">Specifications</label>
                <x-adminlte-select2 id="specifications" name="specifications[]" :config="$config" multiple
                    class="@error('specifications') is-invalid @enderror">
                    @foreach ($specifications as $specification)
                        <option value="{{ $specification->specification_id }}"
                            {{ isset($selectedSpecifications) && in_array($specification->specification_id, $selectedSpecifications) ? 'selected' : '' }}>
                            {{ $specification->specification_name }}
                        </option>
                    @endforeach
                </x-adminlte-select2>
                @error('specifications')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Tombol Submit --}}
            <button type="submit" class="btn btn-primary mt-3">
                {{ isset($relation) ? 'Update' : 'Simpan' }}
            </button>
        </form>
    </x-adminlte-card>
@stop

@section('js')
    <script>
        document.getElementById('brandProductSpecForm').addEventListener('submit', function(event) {
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

        // Highlight validation error for Select2 fields
        @if ($errors->has('brand_code'))
            $('#brand_code').next('.select2-container').find('.select2-selection').addClass('is-invalid');
        @endif

        @if ($errors->has('product_code'))
            $('#product_code').next('.select2-container').find('.select2-selection').addClass('is-invalid');
        @endif

        @if ($errors->has('specifications'))
            $('#specifications').next('.select2-container').find('.select2-selection').addClass('is-invalid');
        @endif
    </script>
@stop
