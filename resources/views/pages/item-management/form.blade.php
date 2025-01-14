@extends('adminlte::page')

@section('title', isset($item) ? 'Edit Item' : 'Tambah Item')

@section('content_header')
    <h1>{{ isset($item) ? 'Edit Item' : 'Tambah Item' }}</h1>
@stop

@section('plugins.Select2', true)
@section('plugins.SweetAlert2', true)

@section('content')
    <x-adminlte-card>
        {{-- Tombol Back --}}
        <a href="{{ route('item-management.index') }}" class="btn btn-secondary mb-3">Kembali</a>

        <form id="itemForm"
            action="{{ isset($item) ? route('item-management.update', $item->item_id) : route('item-management.store') }}"
            method="POST">
            @csrf
            @if (isset($item))
                @method('PUT')
            @endif

            {{-- Dropdown Brand --}}
            <div class="form-group">
                <label for="brand_code">Brand</label>
                <x-adminlte-select2 name="brand_code" id="brand_code" class="form-control">
                    <option value="" disabled {{ isset($item) ? '' : 'selected' }}>-- Pilih Brand --</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->brand_code }}"
                            {{ old('brand_code', $item->brand_code ?? '') == $brand->brand_code ? 'selected' : '' }}>
                            {{ $brand->brand_name }}
                        </option>
                    @endforeach
                </x-adminlte-select2>
            </div>

            {{-- Dropdown Product --}}
            <div class="form-group">
                <label for="product_code">Product</label>
                <x-adminlte-select2 name="product_code" id="product_code" class="form-control">
                    <option value="" disabled {{ isset($item) ? '' : 'selected' }}>-- Pilih Product --</option>
                </x-adminlte-select2>
            </div>

            {{-- Dropdown Type --}}
            <div class="form-group">
                <label for="type_code">Type</label>
                <x-adminlte-select2 name="type_code" id="type_code" class="form-control">
                    <option value="" disabled {{ isset($item) ? '' : 'selected' }}>-- Pilih Type --</option>
                    @foreach ($types as $type)
                        <option value="{{ $type->type_code }}"
                            {{ old('type_code', $item->type_code ?? '') == $type->type_code ? 'selected' : '' }}>
                            {{ $type->type_name }}
                        </option>
                    @endforeach
                </x-adminlte-select2>
            </div>

            {{-- Dropdown Sub Type --}}
            <div class="form-group">
                <label for="sub_type_code">Sub Type</label>
                <x-adminlte-select2 name="sub_type_code" id="sub_type_code" class="form-control">
                    <option value="" disabled {{ isset($item) ? '' : 'selected' }}>-- Pilih Sub Type --</option>
                </x-adminlte-select2>
            </div>

            {{-- Dinamis Input untuk Spesifikasi --}}
            <div class="mt-3">
                <x-adminlte-card title="Specifications" theme="info">
                    <div id="specifications-container">
                        <p class="text-muted">Specifications akan muncul berdasarkan pasangan Brand dan Product yang
                            dipilih.</p>
                    </div>
                </x-adminlte-card>
            </div>


            {{-- Dropdown Unit --}}
            <div class="form-group">
                <label for="unit">Unit</label>
                <x-adminlte-select2 name="unit" id="unit" class="form-control text-uppercase" required>
                    <option value="" disabled {{ isset($item) && !$item->unit ? 'selected' : '' }}>-- PILIH UNIT --
                    </option>
                    @php
                        $units = [
                            'RIM',
                            'ROL',
                            'PCS',
                            'UNIT',
                            'BOX',
                            'PACK',
                            'METER',
                            'LITRE',
                            'KG',
                            'SET',
                            'BAG',
                            'ROLL',
                            'SHEET',
                            'TUBE',
                            'DOZEN',
                            'PAIR',
                        ];
                    @endphp
                    @foreach ($units as $unit)
                        <option value="{{ $unit }}"
                            {{ old('unit', strtoupper($item->unit ?? '')) == $unit ? 'selected' : '' }}>
                            {{ $unit }}
                        </option>
                    @endforeach
                </x-adminlte-select2>
            </div>


            {{-- Tombol Submit --}}
            <button type="submit" class="btn btn-primary mt-3">
                {{ isset($item) ? 'Update' : 'Simpan' }}
            </button>
        </form>
    </x-adminlte-card>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            const brandProductRelations = @json($brandProductRelations);
            const specifications = @json($specifications);
            const products = @json($products);
            const subTypes = @json($subTypes);
            const itemDescription = "{{ old('description', $item->description ?? '') }}";

            const brandSelect = $('#brand_code');
            const productSelect = $('#product_code');
            const typeSelect = $('#type_code');
            const subTypeSelect = $('#sub_type_code');
            const specificationsContainer = $('#specifications-container');

            // Function to extract specification values from description
            function extractSpecifications(description) {
                const specs = {};
                specifications.forEach(spec => {
                    const regex = new RegExp(`${spec.specification_name}(\\s?\\((.*?)\\))?:\\s?(.*?)(,|$)`);
                    const match = description.match(regex);
                    if (match) {
                        specs[spec.specification_id] = match[3].trim();
                    }
                });
                return specs;
            }

            function updateSpecifications(brandCode, productCode, description) {
                specificationsContainer.empty();
                const relatedSpecifications = brandProductRelations
                    .filter(relation => relation.brand_code === brandCode && relation.product_code === productCode)
                    .map(relation => relation.specification_id);

                const filteredSpecifications = specifications.filter(spec =>
                    relatedSpecifications.includes(spec.specification_id)
                );

                const specValues = extractSpecifications(description || '');

                if (filteredSpecifications.length === 0) {
                    specificationsContainer.append(
                        '<p class="text-danger">Tidak ada spesifikasi terkait untuk pasangan Brand dan Product ini.</p>'
                        );
                    return;
                }

                filteredSpecifications.forEach(spec => {
                    const unitLabel = spec.unit ? ` (${spec.unit})` : '';
                    const value = specValues[spec.specification_id] || '';
                    const specField = `
            <div class="form-group">
                <label for="specifications[${spec.specification_id}]" class="font-weight-bold">${spec.specification_name}${unitLabel}</label>
                <input type="text" name="specifications[${spec.specification_id}]" class="form-control"
                    value="${value}" required>
            </div>
        `;
                    specificationsContainer.append(specField);
                });
            }


            function prefillProducts() {
                const selectedBrand = brandSelect.val();
                const selectedProduct = "{{ old('product_code', $item->product_code ?? '') }}";

                const relatedProductCodes = brandProductRelations
                    .filter(relation => relation.brand_code === selectedBrand)
                    .map(relation => relation.product_code);

                const filteredProducts = products.filter(product =>
                    relatedProductCodes.includes(product.product_code)
                );

                productSelect.empty().append('<option value="" disabled>-- Pilih Product --</option>');
                filteredProducts.forEach(product => {
                    productSelect.append(new Option(product.product_name, product.product_code));
                });
                productSelect.val(selectedProduct).trigger('change.select2');
            }

            function prefillSubTypes() {
                const selectedType = typeSelect.val();
                const selectedSubType = "{{ old('sub_type_code', $item->sub_type_code ?? '') }}";

                const filteredSubTypes = subTypes.filter(subType => subType.type_code === selectedType);

                subTypeSelect.empty().append('<option value="" disabled>-- Pilih Sub Type --</option>');
                filteredSubTypes.forEach(subType => {
                    subTypeSelect.append(new Option(subType.sub_type_name, subType.sub_type_code));
                });
                subTypeSelect.val(selectedSubType).trigger('change.select2');
            }

            function prefillUnit() {
                const selectedUnit = "{{ old('unit', $item->unit ?? '') }}";
                $('#unit').val(selectedUnit).trigger('change.select2');
            }

            // Prefill semua elemen saat penyuntingan
            prefillProducts();
            prefillSubTypes();
            prefillUnit();
            if (brandSelect.val() && productSelect.val()) {
                updateSpecifications(brandSelect.val(), productSelect.val(), itemDescription);
            }

            // Handle Brand change
            brandSelect.on('change.select2', function() {
                const brandCode = $(this).val();
                prefillProducts();
                productSelect.val('').trigger('change.select2'); // Reset Product
                specificationsContainer.empty(); // Clear specifications
            });

            // Handle Product change
            productSelect.on('change.select2', function() {
                const brandCode = brandSelect.val();
                const productCode = $(this).val();
                updateSpecifications(brandCode, productCode, '');
            });

            // Handle Type change
            typeSelect.on('change.select2', function() {
                prefillSubTypes();
            });

            // Tambahkan SweetAlert2 untuk Konfirmasi Submit
            document.getElementById('itemForm').addEventListener('submit', function(event) {
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
