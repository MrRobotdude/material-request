@extends('adminlte::page')

@section('title', isset($user) ? 'Edit Akun' : 'Tambah Akun')

@section('content_header')
    <h1>{{ isset($user) ? 'Edit Akun' : 'Tambah Akun' }}</h1>
@stop

@section('plugins.SweetAlert2', true)
@section('plugins.Select2', true)

@section('content')
    <x-adminlte-card>
        {{-- Tombol Back --}}
        <a href="{{ route('account-management.index') }}" class="btn btn-secondary mb-3">Kembali</a>

        <form id="accountForm"
            action="{{ isset($user) ? route('account-management.update', $user->user_id) : route('account-management.store') }}"
            method="POST">
            @csrf
            @if (isset($user))
                @method('PUT')
            @endif

            {{-- Nama --}}
            <div class="form-group">
                <label for="name">Nama</label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $user->name ?? '') }}" placeholder="Nama">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Username --}}
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username"
                    class="form-control @error('username') is-invalid @enderror"
                    value="{{ old('username', $user->username ?? '') }}" placeholder="Username">
                @error('username')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Role --}}
            <div class="form-group">
                <label for="role">Role</label>
                <x-adminlte-select2 name="role" id="role" class="form-control">
                    <option value="">-- Pilih Role --</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}"
                            {{ old('role') == $role->id || (isset($user) && $user->roles->isNotEmpty() && $user->roles->first()->id == $role->id) ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </x-adminlte-select2>
                @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Password fields (only for creating new user) --}}
            @if (!isset($user))
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password"
                        class="form-control @error('password') is-invalid @enderror" placeholder="Password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="form-control @error('password_confirmation') is-invalid @enderror"
                        placeholder="Konfirmasi Password">
                    @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            @endif

            <button type="submit" class="btn btn-primary">{{ isset($user) ? 'Update' : 'Simpan' }}</button>
        </form>
    </x-adminlte-card>
@stop

@section('js')
    <script>
        document.getElementById('accountForm').addEventListener('submit', function(event) {
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

    @if ($errors->has('role'))
        $('#role').next('.select2-container').find('.select2-selection').addClass('is-invalid');
    @endif

@stop
