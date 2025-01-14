@extends('adminlte::page')

@section('title', isset($subType) ? 'Edit SubType' : 'Tambah SubType')

@section('content_header')
    <h1>{{ isset($subType) ? 'Edit SubType' : 'Tambah SubType' }}</h1>
@stop

@section('plugins.Select2', true)
@section('plugins.SweetAlert2', true)

@section('content')
    <x-adminlte-card>
        {{-- Tombol Back --}}
        <a href="{{ route('subtype-management.index') }}" class="btn btn-secondary mb-3">Kembali</a>

        <form id="subTypeForm"
            action="{{ isset($subType) ? route('subtype-management.update', $subType->sub_type_code) : route('subtype-management.store') }}"
            method="POST">
            @csrf
            @if (isset($subType))
                @method('PUT')
            @endif

            {{-- Dropdown Type --}}
            <div class="form-group">
                <label for="type_code">Type</label>
                <x-adminlte-select2 name="type_code" id="type_code" class="form-control">
                    <option value="" disabled {{ isset($subType) ? '' : 'selected' }}>-- Pilih Type --</option>
                    @foreach ($types as $type)
                        <option value="{{ $type->type_code }}"
                            {{ old('type_code', $subType->type_code ?? '') == $type->type_code ? 'selected' : '' }}>
                            {{ $type->type_name }}
                        </option>
                    @endforeach
                </x-adminlte-select2>
            </div>

            {{-- Input Nama SubType --}}
            <div class="form-group">
                <label for="sub_type_name">Nama SubType</label>
                <input type="text" name="sub_type_name" id="sub_type_name"
                    class="form-control @error('sub_type_name') is-invalid @enderror"
                    value="{{ old('sub_type_name', $subType->sub_type_name ?? '') }}" placeholder="Nama SubType" required {{ isset($subType) ? 'disabled' : '' }}>
                @error('sub_type_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Input Initial --}}
            <div class="form-group">
                <label for="initial">Initial</label>
                <input type="text" name="initial" id="initial"
                    class="form-control @error('initial') is-invalid @enderror"
                    value="{{ old('initial', $subType->initial ?? '') }}" maxlength="4" placeholder="Initial" required>
                @error('initial')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Tombol Submit --}}
            <button type="submit" class="btn btn-primary mt-3">
                {{ isset($subType) ? 'Update' : 'Simpan' }}
            </button>
        </form>
    </x-adminlte-card>
@stop

@section('js')
    <script>
        document.getElementById('subTypeForm').addEventListener('submit', function(event) {
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

        @if ($errors->has('type_code'))
            $('#type_code').next('.select2-container').find('.select2-selection').addClass('is-invalid');
        @endif
    </script>
@stop
