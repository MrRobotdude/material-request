@extends('adminlte::page')

@section('title', isset($project) ? 'Edit Project' : 'Tambah Project')

@section('content_header')
    <h1>{{ isset($project) ? 'Edit Project' : 'Tambah Project' }}</h1>
@stop

@section('plugins.SweetAlert2', true)

@section('content')
    <x-adminlte-card>
        {{-- Tombol Back --}}
        <a href="{{ route('project-management.index') }}" class="btn btn-secondary mb-3">Kembali</a>

        <form id="projectForm"
            action="{{ isset($project) ? route('project-management.update', $project->project_id) : route('project-management.store') }}"
            method="POST">
            @csrf
            @if (isset($project))
                @method('PUT')
            @endif

            {{-- Input Project Name --}}
            <div class="form-group">
                <label for="project_name">Nama Proyek</label>
                <input type="text" name="project_name" id="project_name"
                    class="form-control @error('project_name') is-invalid @enderror"
                    value="{{ old('project_name', $project->project_name ?? '') }}">
                @error('project_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Input Project Description --}}
            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea name="description" id="description" rows="4"
                    class="form-control @error('description') is-invalid @enderror">{{ old('description', $project->description ?? '') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Tombol Submit --}}
            <button type="submit" class="btn btn-primary mt-3">
                {{ isset($project) ? 'Update' : 'Simpan' }}
            </button>
        </form>
    </x-adminlte-card>
@stop

@section('js')
    <script>
        document.getElementById('projectForm').addEventListener('submit', function(event) {
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
