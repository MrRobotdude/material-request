@extends('adminlte::page')

@section('title', 'Role Management')

@section('content_header')
    <div class="row justify-content-between">
        <h1>Role Management</h1>
        @if (auth()->user()->hasPermission('add_roles'))
            <a href="{{ route('role-management.create') }}" class="btn btn-primary mb-3">Tambah Role</a>
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

    {{-- Role Management --}}
    @php
        $heads = ['ID', 'Nama Role', 'Permissions', ['label' => 'Aksi', 'no-export' => true, 'width' => 10]];

        $data = [];
        foreach ($roles as $role) {
            $permissions = $role->permissions->pluck('name')->implode(', ');

            $actions = '<nobr>';

            if (auth()->user()->hasPermission('edit_roles')) {
                $actions .=
                    '<a href="' . route('role-management.edit', $role->id) . '" class="btn btn-warning btn-sm">Edit</a> ';
            }

            if (auth()->user()->hasPermission('delete_roles')) {
                $actions .=
                    '<button class="btn btn-danger btn-sm" onclick="confirmDelete(event, \'' .
                    route('role-management.destroy', $role->id) .
                    '\')">Hapus</button>';
            }

            $actions .= '</nobr>';

            $data[] = [$role->id, $role->name, $permissions, $actions];
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

    <x-adminlte-card title="Role Management" theme="primary" collapsible>
        <x-adminlte-datatable id="roleTable" :heads="$heads" :config="$config" striped hoverable with-buttons bordered />
    </x-adminlte-card>
@stop

@section('js')
    <script>
        function confirmDelete(event, url) {
            event.preventDefault();
            Swal.fire({
                title: 'Konfirmasi',
                text: "Apakah Anda yakin ingin menghapus role ini?",
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

                    // Tambahkan CSRF token
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

                    // Tambahkan metode DELETE
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
