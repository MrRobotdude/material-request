@extends('adminlte::page')

@section('title', 'Brand Product Specification Management')

@section('content_header')
    <div class="row justify-content-between">
        <h1>Brand Product Specification Management</h1>
        @if (auth()->user()->hasPermission('add_brand_product_specification'))
            <a href="{{ route('brand-product-specification.create') }}" class="btn btn-primary mb-3">Tambah Hubungan</a>
        @endif
    </div>
@stop

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.SweetAlert2', true)

@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

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
            'Brand',
            'Product',
            'Specifications',
            'Status',
            ['label' => 'Aksi', 'no-export' => true, 'width' => 10],
        ];

        $data = [];
        foreach ($relations as $relation) {
            $status = $relation->is_active
                ? '<span class="badge badge-success">Aktif</span>'
                : '<span class="badge badge-danger">Nonaktif</span>';

            $actions = '<nobr>';
            if (auth()->user()->hasPermission('edit_brand_product_specification')) {
                $actions .=
                    '<a href="' .
                    route('brand-product-specification.edit', $relation->id) .
                    '" class="btn btn-warning btn-sm">Edit</a> ';
            }
            if (auth()->user()->hasPermission('manage_brand_product_specification_status')) {
                $actions .=
                    '<button class="btn btn-secondary btn-sm" onclick="confirmToggleStatus(event, \'' .
                    route('brand-product-specification.toggle-status', $relation->id) .
                    '\')">' .
                    ($relation->is_active ? 'Nonaktifkan' : 'Aktifkan') .
                    '</button>';
            }
            $actions .= '</nobr>';

            $data[] = [
                $relation->id,
                $relation->brand_name,
                $relation->product_name,
                $relation->specifications, // Menampilkan teks spesifikasi sepenuhnya
                $status,
                $actions,
            ];
        }

        $config = [
            'data' => $data,
            'order' => [[0, 'asc']],
            'columns' => [null, null, null, null, null, ['orderable' => false]],
            'paging' => true,
            'lengthMenu' => [5, 10, 25, 50, 100],
            'searching' => true,
            'dom' =>
                '<"row justify-content-between" <"col-sm-6"B> <"col-sm-6"f>>' .
                '<"row" <"col-12"tr>>' .
                '<"row justify-content-between" <"col-sm-6"i> <"col-sm-6"p>>',
        ];
    @endphp

    <x-adminlte-card title="Brand Product Specification Management" theme="primary" collapsible>
        <x-adminlte-datatable id="brandProductSpecTable" :heads="$heads" :config="$config" striped hoverable with-buttons
            bordered />
    </x-adminlte-card>
@stop

@section('js')
    <script>
        function confirmToggleStatus(event, url) {
            event.preventDefault();

            Swal.fire({
                title: 'Konfirmasi',
                text: "Apakah Anda yakin ingin mengubah status hubungan ini?",
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

                    const csrfToken = '{{ csrf_token() }}';
                    if (csrfToken) {
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = csrfToken;
                        form.appendChild(csrfInput);
                    }

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
