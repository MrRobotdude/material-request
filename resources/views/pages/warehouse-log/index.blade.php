@extends('adminlte::page')

@section('title', 'Warehouse Logs')

@section('content_header')
    <div class="row justify-content-between">
        <h1>Warehouse Logs</h1>
    </div>
@stop

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.SweetAlert2', true)

@section('content')
    @php
        $heads = ['Kode MR', 'Kode Item', 'Deskripsi Item', 'Jumlah Release', 'Sisa', 'Tanggal Dibuat'];

        $data = [];
        foreach ($logs as $log) {
            $data[] = [
                $log->materialRequestItem->materialRequest->mr_code ?? '-',
                $log->materialRequestItem->item->item_code ?? '-',
                $log->materialRequestItem->item->description ?? '-',
                $log->fulfilled_quantity,
                $log->remaining_quantity,
                $log->created_at->format('Y-m-d H:i:s'),
            ];
        }

        $config = [
            'data' => $data,
            'order' => [[5, 'desc']],
            'columns' => [null, null, null, null, null, null],
            'paging' => true,
            'lengthMenu' => [5, 10, 25, 50, 100],
            'dom' =>
                '<"row justify-content-between" <"col-sm-6"B> <"col-sm-6"f>>' .
                '<"row" <"col-12"tr>>' .
                '<"row justify-content-between" <"col-sm-6"i> <"col-sm-6"p>>',
        ];
    @endphp

    <x-adminlte-card title="Warehouse Logs" theme="primary" collapsible>
        <x-adminlte-datatable id="warehouseLogTable" :heads="$heads" :config="$config" striped hoverable with-buttons
            bordered />
    </x-adminlte-card>
@stop

@section('js')
@stop
