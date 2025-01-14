@extends('adminlte::page')

@section('title', 'Change Password')

@section('content_header')
    <h2>Ganti Password {{ $targetUser->user_id === auth()->id() ? 'Saya' : $targetUser->name }}</h2>

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
@stop

@section('content')


    <x-adminlte-card theme-mode="outline">
        <form method="POST" action="{{ route('account-management.update-password', ['user' => $targetUser->user_id]) }}">
            @csrf

            {{-- Current Password field --}}
            @if ($targetUser->user_id === auth()->id())
                <div class="input-group mb-3">
                    <input type="password" name="current_password"
                        class="form-control @error('current_password') is-invalid @enderror" placeholder="Password Saat Ini"
                        autofocus>

                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                        </div>
                    </div>

                    @error('current_password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            @endif

            {{-- New Password field --}}
            <div class="input-group mb-3">
                <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror"
                    placeholder="Password Baru">

                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                    </div>
                </div>

                @error('new_password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            {{-- New Password confirmation field --}}
            <div class="input-group mb-3">
                <input type="password" name="new_password_confirmation"
                    class="form-control @error('new_password_confirmation') is-invalid @enderror"
                    placeholder="Konfirmasi Password Baru">

                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                    </div>
                </div>

                @error('new_password_confirmation')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            {{-- Change password button --}}
            <button type="submit" class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
                <span class="fas fa-save"></span>
                {{ __('Ubah Password') }}
            </button>

        </form>

    </x-adminlte-card>
@stop
