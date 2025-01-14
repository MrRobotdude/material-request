@extends('adminlte::page')

@section('title', isset($role) ? 'Edit Role' : 'Tambah Role')

@section('content_header')
    <h1>{{ isset($role) ? 'Edit Role' : 'Tambah Role' }}</h1>
@stop

@section('plugins.Select2', true)
@section('plugins.SweetAlert2', true)

@section('content')
    <x-adminlte-card>
        {{-- Tombol Back --}}
        <a href="{{ route('role-management.index') }}" class="btn btn-secondary mb-3">Kembali</a>

        <form id="roleForm"
            action="{{ isset($role) ? route('role-management.update', $role->id) : route('role-management.store') }}"
            method="POST">
            @csrf
            @if (isset($role))
                @method('PUT')
            @endif

            {{-- Nama Role --}}
            <div class="form-group">
                <label for="name">Nama Role</label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $role->name ?? '') }}" placeholder="Nama Role">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Permissions --}}
            @php
                $config = [
                    'placeholder' => 'Pilih permissions...',
                    'allowClear' => true,
                ];
            @endphp
            <div class="form-group">
                <label for="permissions" class="text-primary">Permissions</label>
                <x-adminlte-select2 id="permissions" name="permissions[]" class="@error('permissions') is-invalid @enderror"
                    :config="$config" multiple>
                    @foreach ($permissions as $permission)
                        <option value="{{ $permission->id }}"
                            {{ isset($role) && $role->permissions->pluck('id')->contains($permission->id) ? 'selected' : '' }}>
                            {{ $permission->name }}
                        </option>
                    @endforeach
                </x-adminlte-select2>
                @error('permissions')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary mt-3">{{ isset($role) ? 'Update' : 'Simpan' }}</button>
        </form>
    </x-adminlte-card>
@stop

@section('js')
    <script>
        document.getElementById('roleForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Mencegah form disubmit langsung
            const form = this; // Simpan referensi ke form
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
