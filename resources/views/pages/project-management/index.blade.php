@extends('adminlte::page')

@section('title', 'Project Management')

@section('content_header')
    <div class="row justify-content-between">
        <h1>Project Management</h1>
        @if (auth()->user()->hasPermission('add_project'))
            <a href="{{ route('project-management.create') }}" class="btn btn-primary mb-3">Tambah Project</a>
        @endif
    </div>
@stop

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.SweetAlert2', true)

@section('content')
    {{-- Tampilkan pesan sukses --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tampilkan pesan error --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @php
        $heads = ['Kode Project', 'Nama Project', 'Deskripsi', ['label' => 'Aksi', 'no-export' => true, 'width' => 10]];

        $data = [];
        foreach ($projects as $project) {
            $actions = '<nobr>';
            if (auth()->user()->hasPermission('edit_project')) {
                $actions .=
                    '<a href="' .
                    route('project-management.edit', $project->project_id) .
                    '" class="btn btn-warning btn-sm">Edit</a> ';
            }
            if (auth()->user()->hasPermission('delete_project')) {
                $actions .=
                    '<button class="btn btn-danger btn-sm" onclick="confirmDelete(event, \'' .
                    route('project-management.destroy', $project->project_id) .
                    '\')">Hapus</button>';
            }
            $actions .= '</nobr>';

            $data[] = [$project->project_id, $project->project_name, $project->description, $actions];
        }

        $config = [
            'data' => $data,
            'order' => [[0, 'asc']],
            'columns' => [null, null, null, ['orderable' => false]],
            'paging' => true,
            'lengthMenu' => [5, 10, 25, 50, 100],
            'dom' =>
                '<"row justify-content-between" <"col-sm-6"B> <"col-sm-6"f>>' .
                '<"row" <"col-12"tr>>' .
                '<"row justify-content-between" <"col-sm-6"i> <"col-sm-6"p>>',
        ];
    @endphp

    <x-adminlte-card title="Project Management" theme="primary" collapsible>
        <x-adminlte-datatable id="projectTable" :heads="$heads" :config="$config" striped hoverable with-buttons
            bordered />
    </x-adminlte-card>
@stop

@section('js')
    <script>
        function confirmDelete(event, url) {
            event.preventDefault();

            Swal.fire({
                title: 'Konfirmasi',
                text: "Apakah Anda yakin ingin menghapus project ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.action = url;
                    form.method = 'POST';

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';
                    form.appendChild(methodField);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@stop
