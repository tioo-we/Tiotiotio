@extends('layouts.main', ['title' => 'Stok'])
@section('title-content')
    <i class="fas fa-pallet mr-2"></i>
    Stok
@endsection

@section('content')
<div class="row">
    <div class="col-xl-8 col-lg-10">
        <form method="POST" action="{{ route('stok.store-multiple') }}" class="card card-orange card-outline">
            <div class="card-header">
                <h3 class="card-title">Tambah Stok Barang (Multiple)</h3>
            </div>

            <div class="card-body">
                @csrf
                
                <!-- Supplier Selection -->
                <div class="form-group">
                    <label>Nama Supplier</label>
                    <div class="input-group">
                        <input type="text" id="namaSuplier" name="nama_suplier" 
                            class="form-control @error('nama_suplier') is-invalid @enderror" 
                            placeholder="Ketik nama supplier..." autocomplete="off"
                            value="{{ old('nama_suplier') }}">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary" id="btnCariProduk">
                                <i class="fas fa-search"></i> Cari Produk
                            </button>
                        </div>
                    </div>
                    @error('nama_suplier')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <div id="supplierSuggestions" class="list-group position-absolute" style="z-index: 1000; width: 100%; max-height: 200px; overflow-y: auto; display: none;"></div>
                </div>

                <!-- Products Selection Area -->
                <div id="produkArea" style="display: none;">
                    <hr>
                    <h5>Pilih Produk dari Supplier</h5>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        Pilih produk yang ingin ditambah stoknya, lalu isi jumlahnya.
                    </div>
                    
                    <div id="produkList">
                        <!-- Products will be loaded here -->
                    </div>
                </div>

                @error('products')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="card-footer form-inline">
                <button type="submit" class="btn btn-primary" id="btnSimpan" disabled>
                    <i class="fas fa-save"></i> Simpan Stok
                </button>
                <a href="{{ route('stok.index') }}" class="btn btn-secondary ml-auto">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>

    <!-- Keep the old single product form as alternative -->
    <div class="col-xl-4 col-lg-6 mt-4 mt-xl-0">
        <form method="POST" action="{{ route('stok.store') }}" class="card card-secondary card-outline">
            <div class="card-header">
                <h3 class="card-title">Tambah Stok Tunggal</h3>
            </div>

            <div class="card-body">
                @csrf
                <div class="form-group">
                    <label>Nama Produk</label>
                    <div class="input-group">
                        <input type="text" id="namaProdukSingle" name="nama_produk"
                            class="form-control @error('produk_id') is-invalid @enderror" disabled>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                data-target="#modalCari">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    @error('produk_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <input type="hidden" name="produk_id" id="produkIdSingle">
                </div>

                <div class="form-group">
                    <label>Jumlah</label>
                    <input type="number" name="jumlah" class="form-control @error('jumlah') is-invalid @enderror" 
                        value="{{ old('jumlah') }}" min="1">
                    @error('jumlah')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Nama Supplier</label>
                    <input type="text" name="nama_suplier" class="form-control @error('nama_suplier') is-invalid @enderror" 
                        value="{{ old('nama_suplier') }}">
                    @error('nama_suplier')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="card-footer form-inline">
                <button type="submit" class="btn btn-secondary">Simpan Stok Tunggal</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('modals')
<div class="modal fade" id="modalCari" data-backdrop="static" data-keyboard="false" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cari Produk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formSearch" action="" method="get" class="input-group">
                    <input type="text" class="form-control" id="search">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                <table class="table table-sm table-striped table-hover mt-3">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Produk</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="resultProduk"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endpush

@push('styles')
<style>
    .product-item {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 1rem;
        margin-bottom: 0.75rem;
        transition: all 0.2s;
    }
    .product-item:hover {
        background-color: #f8f9fa;
    }
    .product-item.selected {
        border-color: #007bff;
        background-color: #e3f2fd;
    }
    .list-group-item:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }
</style>
@endpush

@push('scripts')
<script>
$(function () {
    let supplierTimeout;
    
    // Supplier autocomplete
    $('#namaSuplier').on('input', function() {
        let query = $(this).val();
        
        clearTimeout(supplierTimeout);
        
        if (query.length >= 2) {
            supplierTimeout = setTimeout(() => {
                fetchSuppliers(query);
            }, 300);
        } else {
            $('#supplierSuggestions').hide();
        }
    });

    // Hide supplier suggestions when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#namaSuplier, #supplierSuggestions').length) {
            $('#supplierSuggestions').hide();
        }
    });

    // Search products by supplier
    $('#btnCariProduk').click(function() {
        let supplier = $('#namaSuplier').val().trim();
        if (supplier.length >= 2) {
            fetchProdukBySuplier(supplier);
        } else {
            alert('Masukkan nama supplier minimal 2 karakter');
        }
    });

    // Check selected products and enable/disable submit button
    $(document).on('change', '.product-checkbox', function() {
        updateSubmitButton();
        toggleProductItem($(this));
    });

    // Handle quantity input
    $(document).on('input', '.jumlah-input', function() {
        updateSubmitButton();
    });

    // Single product search (existing functionality)
    $('#formSearch').submit(function (e) {
        e.preventDefault();
        let search = $(this).find('#search').val();
        if (search.length >= 3) {
            fetchProdukSingle(search);
        }
    });
});

function fetchSuppliers(query) {
    $.getJSON("{{ route('stok.suppliers') }}", {search: query})
        .done(function(suppliers) {
            let html = '';
            suppliers.forEach(supplier => {
                html += `<a href="#" class="list-group-item list-group-item-action supplier-item" 
                            data-supplier="${supplier.nama_suplier}">
                            ${supplier.nama_suplier}
                         </a>`;
            });
            $('#supplierSuggestions').html(html).show();
        });
}

function fetchProdukBySuplier(supplier) {
    $.getJSON("{{ route('stok.produk-by-suplier') }}", {nama_suplier: supplier})
        .done(function(products) {
            if (products.length === 0) {
                $('#produkList').html(`
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Tidak ada produk yang pernah disupply oleh supplier ini.
                    </div>
                `);
            } else {
                let html = '';
                products.forEach((product, index) => {
                    html += `
                        <div class="product-item" id="product-${product.id}">
                            <div class="row align-items-center">
                                <div class="col-md-1">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input product-checkbox" 
                                               id="check-${product.id}" value="${product.id}" 
                                               name="products[${index}][produk_id]">
                                        <label class="custom-control-label" for="check-${product.id}"></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <strong>${product.nama_produk}</strong><br>
                                    <small class="text-muted">Stok saat ini: ${product.stok}</small>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-0">
                                        <label class="sr-only">Jumlah</label>
                                        <input type="number" class="form-control jumlah-input" 
                                               name="products[${index}][jumlah]" 
                                               placeholder="Jumlah" min="1" disabled>
                                    </div>
                                </div>
                                <div class="col-md-2 text-right">
                                    <span class="badge badge-info">ID: ${product.id}</span>
                                </div>
                            </div>
                        </div>
                    `;
                });
                $('#produkList').html(html);
            }
            $('#produkArea').show();
            $('#supplierSuggestions').hide();
        })
        .fail(function() {
            alert('Gagal mengambil data produk');
        });
}

function toggleProductItem(checkbox) {
    let productItem = checkbox.closest('.product-item');
    let jumlahInput = productItem.find('.jumlah-input');
    
    if (checkbox.is(':checked')) {
        productItem.addClass('selected');
        jumlahInput.prop('disabled', false).focus();
    } else {
        productItem.removeClass('selected');
        jumlahInput.prop('disabled', true).val('');
    }
}

function updateSubmitButton() {
    let hasSelected = $('.product-checkbox:checked').length > 0;
    let allHaveQuantity = true;
    
    $('.product-checkbox:checked').each(function() {
        let jumlahInput = $(this).closest('.product-item').find('.jumlah-input');
        if (!jumlahInput.val() || jumlahInput.val() <= 0) {
            allHaveQuantity = false;
            return false;
        }
    });
    
    $('#btnSimpan').prop('disabled', !(hasSelected && allHaveQuantity));
}

// Supplier selection from dropdown
$(document).on('click', '.supplier-item', function(e) {
    e.preventDefault();
    let supplier = $(this).data('supplier');
    $('#namaSuplier').val(supplier);
    $('#supplierSuggestions').hide();
});

// Single product search (existing functionality)
function fetchProdukSingle(search) {
    let url = "{{ route('stok.produk') }}?search=" + search;
    $.getJSON(url, function (result) {
        $('#resultProduk').html('');
        result.forEach((produk, index) => {
            let row = `<tr>`;
            row += `<td>${index + 1}</td>`;
            row += `<td>${produk.nama_produk}</td>`;
            row += `<td class="text-right">`;
            row += `<button type="button" class="btn btn-xs btn-success"
                        onclick="addProdukSingle(${produk.id}, '${produk.nama_produk}')">Add</button>`;
            row += `</td>`;
            row += `</tr>`;
            $('#resultProduk').append(row);
        });
    });
}

function addProdukSingle(id, nama_produk) {
    $('#namaProdukSingle').val(nama_produk);
    $('#produkIdSingle').val(id);
    $('#modalCari').modal('hide');
}
</script>
@endpush