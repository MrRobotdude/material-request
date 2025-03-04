@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@php( $password_username_url = View::getSection('password_username_url') ?? config('adminlte.password_username_url', 'password/username') )

@if (config('adminlte.use_route_url', false))
    @php( $password_username_url = $password_username_url ? route($password_username_url) : '' )
@else
    @php( $password_username_url = $password_username_url ? url($password_username_url) : '' )
@endif

@section('auth_header', __('adminlte::adminlte.password_reset_message'))

@section('auth_body')

    @if(session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <form action="{{ $password_username_url }}" method="post">
        @csrf

        {{-- username field --}}
        <div class="input-group mb-3">
            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                   value="{{ old('username') }}" placeholder="Username" autofocus>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('username')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Send reset link button --}}
        <button type="submit" class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
            <span class="fas fa-share-square"></span>
            {{ __('adminlte::adminlte.send_password_reset_link') }}
        </button>

    </form>

@stop
