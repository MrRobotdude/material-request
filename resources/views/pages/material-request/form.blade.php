@extends('adminlte::page')

@section('title', isset($materialRequest) ? 'Edit Material Request' : 'Tambah Material Request')

@section('content_header')
    <h1>{{ isset($materialRequest) ? 'Edit Material Request' : 'Tambah Material Request' }}</h1>
@stop

@section('plugins.Select2', true)
@section('plugins.SweetAlert2', true)

@section('content')
    <x-adminlte-card>
        {{-- Tombol Back --}}
        <a href="{{ route('material-request.index') }}" class="btn btn-secondary mb-3">Kembali</a>

        <form id="materialRequestForm"
            action="{{ isset($materialRequest) ? route('material-request.update', $materialRequest->mr_code) : route('material-request.store') }}"
            method="POST">
            @csrf
            @if (isset($materialRequest))
                @method('PUT')
            @endif

            {{-- Nomor MR --}}
            <div class="form-group">
                <label for="mr_code">Nomor MR</label>
                <input type="text" name="mr_code" id="mr_code" class="form-control"
                    value="{{ old('mr_code', $materialRequest->mr_code ?? 'MR-' . str_pad($lastMrNumber + 1, 6, '0', STR_PAD_LEFT)) }}"
                    readonly>
                @error('mr_code')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            {{-- Pilih Proyek --}}
            <div class="form-group">
                <label for="project_id">Proyek</label>
                <select name="project_id" id="project_id" class="form-control select2"
                    {{ isset($materialRequest) ? 'disabled' : '' }}>
                    <option value="" disabled selected>-- Pilih Proyek --</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->project_id }}"
                            {{ old('project_id', $materialRequest->project_id ?? '') == $project->project_id ? 'selected' : '' }}>
                            {{ $project->project_name }}
                        </option>
                    @endforeach
                </select>
                @error('project_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>


            {{-- Catatan --}}
            <div class="form-group">
                <label for="note">Catatan</label>
                <textarea name="note" id="note" class="form-control" rows="3"
                    {{ isset($materialRequest) ? 'disabled' : '' }}>{{ old('note', $materialRequest->note ?? '') }}</textarea>
                @error('note')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            {{-- Pilih User --}}
            <div class="form-group">
                <label for="created_by">User</label>
                <select name="created_by" id="created_by" class="form-control select2"
                    {{ isset($materialRequest) ? 'disabled' : '' }}>
                    <option value="" disabled selected>-- Pilih User --</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->user_id }}"
                            {{ old('created_by', $materialRequest->created_by ?? '') == $user->user_id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
                @error('created_by')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            {{-- Tambah Item --}}
            <div class="mt-3">
                <x-adminlte-card title="Daftar Item" theme="info">
                    <div id="items-container">
                        @if (isset($materialRequest) && $materialRequest->items->count() > 0)
                            {{-- Tampilkan item yang sudah ada --}}
                            @foreach ($materialRequest->items as $index => $item)
                                <div class="row item-row mb-2">
                                    <div class="col-md-6">
                                        <select name="items[{{ $index }}][item_id]" class="form-control select2">
                                            <option value="" disabled>-- Pilih Item --</option>
                                            @foreach ($availableItems as $availableItem)
                                                <option value="{{ $availableItem->item_id }}"
                                                    {{ $item->item_id == $availableItem->item_id ? 'selected' : '' }}>
                                                    {{ $availableItem->description }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error("items.{$index}.item_id")
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" name="items[{{ $index }}][quantity]"
                                            class="form-control" placeholder="Kuantitas"
                                            value="{{ old('items.' . $index . '.quantity', $item->quantity ?? '') }}">
                                        @error("items.{$index}.quantity")
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger btn-sm remove-item">Hapus</button>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    @error('items')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <button type="button" class="btn btn-success btn-sm mt-2" id="add-item">Tambah Item</button>
                </x-adminlte-card>
            </div>

            {{-- Tombol Submit --}}
            <button type="submit" class="btn btn-primary mt-3">
                {{ isset($materialRequest) ? 'Update' : 'Simpan' }}
            </button>
        </form>
    </x-adminlte-card>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            const availableItems = @json($availableItems);
            let itemIndex = {{ isset($materialRequest) ? count($materialRequest->items) : 0 }};

            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%',
                matcher: function(params, data) {
                    // Jika tidak ada teks pencarian, tampilkan semua data
                    if ($.trim(params.term) === '') {
                        return data;
                    }

                    // Pisahkan teks pencarian menjadi array kata kunci
                    const keywords = params.term.toLowerCase().split(' ');

                    // Gabungkan semua teks dalam opsi ke satu string untuk dicari
                    const text = data.text.toLowerCase();

                    // Cek apakah semua kata kunci ada dalam teks
                    const matches = keywords.every(keyword => text.includes(keyword));

                    if (matches) {
                        return data;
                    }

                    // Jika tidak cocok, jangan tampilkan opsi ini
                    return null;
                }
            });

            $('#add-item').on('click', function() {
                const itemRow = `
                    <div class="row item-row mb-2">
                        <div class="col-md-6">
                            <select name="items[${itemIndex}][item_id]" class="form-control select2">
                                <option value="" disabled selected>-- Pilih Item --</option>
                                ${availableItems.map(item => `<option value="${item.item_id}">${item.description}</option>`).join('')}
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="number" name="items[${itemIndex}][quantity]" class="form-control"
                                placeholder="Kuantitas">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-sm remove-item">Hapus</button>
                        </div>
                    </div>
                `;
                $('#items-container').append(itemRow);

                // Inisialisasi Select2 menggunakan AdminLTE
                $(`[name="items[${itemIndex}][item_id]"]`).select2({
                    theme: 'bootstrap4',
                    width: '100%',
                    matcher: function(params, data) {
                        // Jika tidak ada teks pencarian, tampilkan semua data
                        if ($.trim(params.term) === '') {
                            return data;
                        }

                        // Pisahkan teks pencarian menjadi array kata kunci
                        const keywords = params.term.toLowerCase().split(' ');

                        // Gabungkan semua teks dalam opsi ke satu string untuk dicari
                        const text = data.text.toLowerCase();

                        // Cek apakah semua kata kunci ada dalam teks
                        const matches = keywords.every(keyword => text.includes(keyword));

                        if (matches) {
                            return data;
                        }

                        // Jika tidak cocok, jangan tampilkan opsi ini
                        return null;
                    }
                });

                itemIndex++;
            });

            $(document).on('click', '.remove-item', function() {
                $(this).closest('.item-row').remove();
            });

            document.getElementById('materialRequestForm').addEventListener('submit', function(event) {
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
        });
    </script>
@stop
