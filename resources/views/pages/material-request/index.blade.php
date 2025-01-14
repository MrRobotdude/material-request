@extends('adminlte::page')

@section('title', 'Material Request Management')

@section('content_header')
    <div class="row justify-content-between">
        <h1>Material Request Management</h1>
        @if (auth()->user()->hasPermission('add_material_request'))
            <a href="{{ route('material-request.create') }}" class="btn btn-primary mb-3">Tambah Material Request</a>
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
            'Kode MR',
            'Project',
            'Note',
            'Nama Requestor',
            'Status',
            'Tanggal Dibuat',
            ['label' => 'Aksi', 'no-export' => true, 'width' => 10],
        ];

        $data = [];
        foreach ($materialRequests as $mr) {
            $actions = '<nobr>';

            if (auth()->user()->hasPermission('edit_material_request') && in_array($mr->status, ['created'])) {
                $actions .=
                    '<a href="' .
                    route('material-request.edit', $mr->mr_code) .
                    '" class="btn btn-warning btn-sm">Edit</a> ';
            }
            if (auth()->user()->hasPermission('view_material_request_items')) {
                $actions .=
                    '<button class="btn btn-info btn-sm" onclick="showItems(\'' .
                    $mr->mr_code .
                    '\')">Lihat Items</button> ';
            }

            if (
                auth()->user()->hasPermission('cancel_material_request') &&
                !in_array($mr->status, ['completed', 'cancelled'])
            ) {
                $actions .=
                    '<button class="btn btn-danger btn-sm" onclick="cancelMR(\'' .
                    $mr->mr_code .
                    '\')">Cancel</button> ';
            }
            if (auth()->user()->hasPermission('approve_material_request') && $mr->status === 'created') {
                $actions .=
                    '<button class="btn btn-success btn-sm" onclick="approveMR(\'' .
                    $mr->mr_code .
                    '\')">Approve</button> ';
            }

            $actions .= '</nobr>';

            $data[] = [
                'kode' => $mr->mr_code,
                'project' => $mr->project->project_name,
                'note' => $mr->note,
                'requestor' => $mr->requestor->name ?? '-',
                'status' => $mr->status,
                'actions' => $actions,
                'created_at' => $mr->created_at,
                'attributes' => ['data-id' => $mr->mr_code], // Tambahkan data-id
            ];
        }

        $config = [
            // 'data' => $data,
            'order' => [[5, 'desc']],
            'columns' => [null, null, null, null, null, null, ['orderable' => false]],
            'paging' => true,
            'lengthMenu' => [5, 10, 25, 50, 100],
            'dom' =>
                '<"row justify-content-between" <"col-sm-6"B> <"col-sm-6"f>>' .
                '<"row" <"col-12"tr>>' .
                '<"row justify-content-between" <"col-sm-6"i> <"col-sm-6"p>>',
        ];
    @endphp

    <x-adminlte-card title="Material Request Management" theme="primary" collapsible>
        <x-adminlte-datatable id="materialRequestTable" :heads="$heads" :config="$config" striped hoverable with-buttons
            bordered>
            @foreach ($data as $row)
                <tr data-id="{{ $row['kode'] }}">
                    <td>{{ $row['kode'] }}</td>
                    <td>{{ $row['project'] }}</td>
                    <td>{{ $row['note'] }}</td>
                    <td>{{ $row['requestor'] }}</td>
                    <td>{{ $row['status'] }}</td>
                    <td>{{ $row['created_at'] }}</td>
                    <td>{!! $row['actions'] !!}</td>
                </tr>
            @endforeach
        </x-adminlte-datatable>

    </x-adminlte-card>

    {{-- Material Request Item Table --}}
    {{-- Material Request Item Table --}}
    <x-adminlte-card id="materialRequestItemCard" title="Material Request Items" theme="info" collapsible>
        <div id="materialRequestItemContainer" style="display: none;">
            <h4 id="materialRequestItemTitle">Items untuk MR: </h4>
            <x-adminlte-datatable id="materialRequestItemTable" :heads="[
                'Kode Item',
                'Deskripsi',
                'Jumlah',
                'Terpenuhi',
                'Status',
                ['label' => 'Action', 'no-export' => true, 'width' => 10],
            ]" striped hoverable with-buttons bordered>
                <tbody>
                    {{-- Data akan diisi menggunakan JavaScript --}}
                </tbody>
            </x-adminlte-datatable>
        </div>
    </x-adminlte-card>

@stop

@section('js')
    <script>
        let materialRequestItemTable;

        function approveMR(materialRequestId) {
            Swal.fire({
                title: 'Konfirmasi',
                text: "Apakah Anda yakin ingin menyetujui Material Request ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Setujui!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/material-requests/${materialRequestId}/approve`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                        })
                        .then(response => response.json())
                        .then((data) => {
                            if (data.status) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: data.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                });
                            }
                        })
                        .catch((error) => {
                            console.error('Error:', error);
                            Swal.fire({
                                title: 'Error!',
                                text: 'Gagal menyetujui Material Request.',
                                icon: 'error',
                                confirmButtonText: 'OK',
                            });
                        });
                }
            });
        }

        function showItems(materialRequestId) {
            const container = document.getElementById('materialRequestItemContainer');
            const title = document.getElementById('materialRequestItemTitle');
            const tableBody = document.querySelector('#materialRequestItemTable tbody');

            fetch(`/material-requests/${materialRequestId}/items`)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error(`Error: ${response.status}`);
                    }
                    return response.json();
                })
                .then((data) => {
                    const {
                        items,
                        material_request_code,
                        material_request_status
                    } = data;

                    // Tampilkan container
                    container.style.display = 'block';
                    title.textContent = `Items untuk MR: ${material_request_code}`;

                    // Hancurkan DataTable jika sudah ada
                    if ($.fn.DataTable.isDataTable('#materialRequestItemTable')) {
                        $('#materialRequestItemTable').DataTable().clear().destroy();
                    }

                    // Isi ulang tabel dengan data baru
                    tableBody.innerHTML = '';
                    items.forEach((item) => {
                        const cancelButton =
                            item.status.toLowerCase() !== 'fulfilled' &&
                            item.status.toLowerCase() !== 'cancelled' &&
                            item.status.toLowerCase() !== 'completed' ?
                            `<button class="btn btn-danger btn-sm" onclick="cancelItem('${item.item_id}', '${materialRequestId}')">Cancel</button>` :
                            '';

                        const releaseButton =
                            material_request_status !== 'created' &&
                            item.status.toLowerCase() !== 'fulfilled' &&
                            item.status.toLowerCase() !== 'cancelled' ?
                            `<button class="btn btn-success btn-sm" onclick="releaseItem('${item.item_id}', '${materialRequestId}', ${item.quantity}, ${item.fulfilled_quantity})">Release</button>` :
                            '';

                        const row = `
                    <tr>
                        <td>${item.item_code}</td>
                        <td>${item.description}</td>
                        <td>${item.quantity}</td>
                        <td>${item.fulfilled_quantity}</td>
                        <td>${item.status}</td>
                        <td class="d-flex justify-content-between">${cancelButton} ${releaseButton}</td>
                    </tr>`;
                        tableBody.insertAdjacentHTML('beforeend', row);
                    });

                    // Reinitialisasi DataTable dengan konfigurasi baru
                    $('#materialRequestItemTable').DataTable({
                        dom: '<"row justify-content-between" <"col-sm-6"B> <"col-sm-6"f>>' +
                            '<"row" <"col-12"tr>>' +
                            '<"row justify-content-between" <"col-sm-6"i> <"col-sm-6"p>>',
                        // buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                        paging: true,
                        lengthMenu: [5, 10, 25, 50, 100],
                        responsive: true,
                    });
                })
                .catch((error) => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'Gagal memuat data items.',
                        icon: 'error',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK',
                    });
                });
        }

        function cancelMR(materialRequestId) {
            Swal.fire({
                title: 'Konfirmasi',
                text: "Apakah Anda yakin ingin membatalkan Material Request ini? Semua item yang belum fulfilled juga akan dibatalkan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Batalkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/material-requests/${materialRequestId}/cancel`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({})
                        })
                        .then((response) => {
                            if (!response.ok) {
                                throw new Error(`Error: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then((data) => {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        })
                        .catch((error) => {
                            console.error('Error:', error);
                            Swal.fire({
                                title: 'Error',
                                text: 'Gagal membatalkan Material Request.',
                                icon: 'error',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK',
                            });
                        });
                }
            });
        }

        function releaseItem(itemId, materialRequestId, totalQuantity, fulfilledQuantity) {
            Swal.fire({
                title: 'Release Item',
                html: `
            <div>
                <label for="releaseQuantity">Jumlah yang diberikan (Maksimal ${totalQuantity - fulfilledQuantity}):</label>
                <input type="number" id="releaseQuantity" class="swal2-input"
                    min="1" max="${totalQuantity - fulfilledQuantity}">
            </div>
        `,
                confirmButtonText: 'Submit',
                showCancelButton: true,
                preConfirm: () => {
                    const releaseQuantity = Swal.getPopup().querySelector('#releaseQuantity').value;
                    const maxQuantity = parseInt(Swal.getPopup().querySelector('#releaseQuantity').getAttribute(
                        'max'), 10);

                    if (!releaseQuantity || releaseQuantity <= 0) {
                        Swal.showValidationMessage('Jumlah harus lebih besar dari 0');
                        return false;
                    }

                    if (releaseQuantity > maxQuantity) {
                        Swal.showValidationMessage(
                            `Jumlah tidak boleh lebih dari sisa permintaan (${maxQuantity})`);
                        return false;
                    }

                    return releaseQuantity;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/material-requests/${materialRequestId}/items/${itemId}/release`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                fulfilled_quantity: result.value
                            })
                        })
                        .then(response => response.json())
                        .then((data) => {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        })
                        .catch((error) => {
                            console.error('Error:', error);
                            Swal.fire({
                                title: 'Error',
                                text: 'Gagal melakukan release item.',
                                icon: 'error',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK',
                            });
                        });
                }
            });
        }

        function cancelItem(itemCode, materialRequestId) {
            Swal.fire({
                title: 'Konfirmasi',
                text: "Apakah Anda yakin ingin membatalkan item ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Batalkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {

                if (result.isConfirmed) {
                    fetch(`/material-requests/${materialRequestId}/items/${itemCode}/cancel`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({})
                        })
                        .then((response) => {

                            if (!response.ok) {
                                throw new Error(`Error: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(() => {
                            location.reload();
                        })
                        .catch((error) => {
                            console.error('Error:', error);
                            Swal.fire({
                                title: 'Error',
                                text: 'Gagal membatalkan item.',
                                icon: 'error',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK',
                            });
                        });
                }
            });
        }
    </script>
@stop
