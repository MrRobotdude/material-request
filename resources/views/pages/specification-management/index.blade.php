@extends('adminlte::page')

@section('title', 'Specification Management')

@section('content_header')
    <div class="row justify-content-between">
        <h1>Specification Management</h1>
        @if (auth()->user()->hasPermission('add_specification'))
            <a href="{{ route('specification-management.create') }}" class="btn btn-primary mb-3">Tambah Spesifikasi</a>
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
        $heads = [
            'ID',
            'Nama Spesifikasi',
            'Satuan',
            'Status',
            ['label' => 'Aksi', 'no-export' => true, 'width' => 10],
        ];

        $data = [];
        foreach ($specifications as $specification) {
            $status = $specification->is_active
                ? '<span class="badge badge-success">Aktif</span>'
                : '<span class="badge badge-danger">Nonaktif</span>';

            $actions = '<nobr>';
            if (auth()->user()->hasPermission('edit_specification')) {
                $actions .=
                    '<a href="' .
                    route('specification-management.edit', $specification->specification_id) .
                    '" class="btn btn-warning btn-sm">Edit</a> ';
            }

            if (auth()->user()->hasPermission('manage_specification_status')) {
                $actions .=
                    '<button class="btn btn-secondary btn-sm" onclick="confirmToggleStatus(event, \'' .
                    route('specification-management.toggleStatus', $specification->specification_id) .
                    '\')">' .
                    ($specification->is_active ? 'Nonaktifkan' : 'Aktifkan') .
                    '</button>';
            }

            $actions .= '</nobr>';

            $data[] = [
                $specification->specification_id,
                $specification->specification_name,
                $specification->unit ?? '-',
                $status,
                $actions,
            ];
        }

        $config = [
            'data' => $data,
            'order' => [[0, 'asc']],
            'columns' => [null, null, null, null, ['orderable' => false]],
            'paging' => true,
            'lengthMenu' => [5, 10, 25, 50, 100],
            'dom' =>
                '<"row justify-content-between" <"col-sm-6"B> <"col-sm-6"f>>' .
                '<"row" <"col-12"tr>>' .
                '<"row justify-content-between" <"col-sm-6"i> <"col-sm-6"p>>',
        ];
    @endphp

    <x-adminlte-card title="Specification Management" theme="primary" collapsible>
        <x-adminlte-datatable id="specificationTable" :heads="$heads" :config="$config" striped hoverable with-buttons
            bordered />
    </x-adminlte-card>
@stop

@section('js')
    <script>
        function confirmToggleStatus(event, url) {
            event.preventDefault(); // Mencegah aksi default form

            Swal.fire({
                title: 'Konfirmasi',
                text: "Apakah Anda yakin ingin mengubah status spesifikasi ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Ubah!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Membuat dan mengirimkan form untuk toggle status
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
