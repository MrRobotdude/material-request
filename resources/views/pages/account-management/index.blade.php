@extends('adminlte::page')

@section('title', 'Account Management')

@section('content_header')
    <div class="row justify-content-between">
        <h1>Account Management</h1>
        @if (auth()->user()->hasPermission('add_account'))
            <a href="{{ route('account-management.create') }}" class="btn btn-primary mb-3">Tambah Akun</a>
        @endif
    </div>
@stop

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.SweetAlert2', true)

@section('content')
    {{-- Tampilkan pesan sukses --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
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
        $heads = [
            'ID',
            'Nama',
            'Username',
            'Role',
            ['label' => 'Status', 'width' => 5],
            ['label' => 'Aksi', 'no-export' => true, 'width' => 10],
        ];

        $data = [];
        foreach ($users as $user) {
            $statusBadge = $user->is_active
                ? '<span class="badge badge-success">Aktif</span>'
                : '<span class="badge badge-danger">Nonaktif</span>';

            $roles = $user->roles->pluck('name')->implode(', ');

            $actions = '<nobr>';

            // Cek permission untuk edit akun
            if (auth()->user()->hasPermission('edit_account')) {
                $actions .=
                    '<a href="' .
                    route('account-management.edit', $user) .
                    '" class="btn btn-warning btn-sm">Edit</a> ';
            }

            // Cek permission untuk ganti password
            if (auth()->user()->hasPermission('change_password')) {
                $actions .=
                    '<a href="' .
                    route('account-management.change-password', $user) .
                    '" class="btn btn-info btn-sm">Ganti Password</a> ';
            }

            // Cek permission untuk mengubah status
            if (auth()->user()->hasPermission('manage_account_status')) {
                $actions .=
                    '<button class="btn btn-' .
                    ($user->is_active ? 'danger' : 'success') .
                    ' btn-sm" onclick="confirmToggleStatus(event, \'' .
                    route('account-management.toggle-status', $user) .
                    '\')">' .
                    ($user->is_active ? 'Nonaktifkan' : 'Aktifkan') .
                    '</button>';
            }

            $actions .= '</nobr>';

            $data[] = [$user->user_id, $user->name, $user->username, $roles, $statusBadge, $actions];
        }

        $config = [
            'data' => $data,
            'order' => [[0, 'asc']],
            'columns' => [null, null, null, null, null, ['orderable' => false]],
            'paging' => true,
            'lengthMenu' => [5, 10, 25, 50, 100],
            'dom' =>
                '<"row justify-content-between" <"col-sm-6"B> <"col-sm-6"f>>' .
                '<"row" <"col-12"tr>>' .
                '<"row justify-content-between" <"col-sm-6"i> <"col-sm-6"p>>',
        ];
    @endphp

    <x-adminlte-card title="Account Management" theme="primary" collapsible>
        <x-adminlte-datatable id="accountTable" :heads="$heads" :config="$config" striped hoverable with-buttons bordered
            responsive />
    </x-adminlte-card>
@stop

@section('js')
    <script>
        function confirmToggleStatus(event, url) {
            event.preventDefault();
            Swal.fire({
                title: 'Konfirmasi',
                text: "Apakah Anda yakin ingin mengubah status akun ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Ubah!',
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

                    // Tambahkan metode PUT
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'PUT';
                    form.appendChild(methodField);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@stop
