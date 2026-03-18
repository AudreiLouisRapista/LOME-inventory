@extends('themes.main')

{{-- 1. DEFINE PAGE TITLE --}}
@section('title', 'Inventory Management')

{{-- 2. DEFINE CONTENT HEADER (Breadcrumbs) --}}
@section('content_header')
    <div class="header">
        <h1 class="title" style="margin-left: 50px; ">Inventory Management</h1>
        <p style="margin-left: 50px;">Manage Inventory for the Products</p>
    </div>
@endsection

@section('content')
    <link rel="stylesheet" href="{{ asset('css/pages/inventory_style.css') }}">
    {{-- ==========================================
         3. DASHBOARD STATISTICS CARDS
         ========================================== --}}
    <div class="row mb-4 g-3" style="font-family: 'Inter', sans-serif;">
        <div class="col-lg-3 col-md-4 col-6 col-5-custom">
            <div class="dash-stat-card">
                <div>
                    <span style="color: #64748b; font-size: 14px; font-weight: 500;">Total Products</span>
                </div>
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                        <h3 style="font-size: 28px; font-weight: 700; margin: 0; color: #1e293b;">
                            {{ number_format($totalProducts) }}</h3>
                        @php($delta = $cardDeltas['totalProducts'] ?? ['pct' => 0, 'dir' => 'up'])
                        <div
                            style="background: {{ $delta['dir'] === 'up' ? '#f0fdf4' : '#fef2f2' }}; color: {{ $delta['dir'] === 'up' ? '#16a34a' : '#dc2626' }}; padding: 4px 8px; border-radius: 10px; font-size: 11px; font-weight: 600;">
                            {{ $delta['dir'] === 'up' ? '↗' : '↘' }} {{ number_format(abs($delta['pct'] ?? 0), 1) }}%
                        </div>
                    </div>
                    <small style="color: #94a3b8; font-size: 11px;">From last week</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-4 col-6 col-5-custom">
            <div class="dash-stat-card">
                <div>
                    <span style="color: #64748b; font-size: 14px; font-weight: 500;">Available Stock</span>
                </div>
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                        <h3 style="font-size: 28px; font-weight: 700; margin: 0; color: #1e293b;">
                            {{ number_format($totalQuantity) }}</h3>
                        @php($delta = $cardDeltas['availableStock'] ?? ['pct' => 0, 'dir' => 'up'])
                        <div
                            style="background: {{ $delta['dir'] === 'up' ? '#f0fdf4' : '#fef2f2' }}; color: {{ $delta['dir'] === 'up' ? '#16a34a' : '#dc2626' }}; padding: 4px 8px; border-radius: 10px; font-size: 11px; font-weight: 600;">
                            {{ $delta['dir'] === 'up' ? '↗' : '↘' }} {{ number_format(abs($delta['pct'] ?? 0), 1) }}%
                        </div>
                    </div>
                    <small style="color: #94a3b8; font-size: 11px;">From last week</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-4 col-6 col-5-custom">
            <div class="dash-stat-card">
                <div>
                    <span style="color: #64748b; font-size: 14px; font-weight: 500;">Low Stock</span>
                </div>
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                        <h3 style="font-size: 28px; font-weight: 700; margin: 0; color: #1e293b;">
                            {{ number_format($lowStockProducts) }}
                        </h3>
                        @php($delta = $cardDeltas['lowStock'] ?? ['pct' => 0, 'dir' => 'up'])
                        <div
                            style="background: {{ $delta['dir'] === 'up' ? '#f0fdf4' : '#fef2f2' }}; color: {{ $delta['dir'] === 'up' ? '#16a34a' : '#dc2626' }}; padding: 4px 8px; border-radius: 10px; font-size: 11px; font-weight: 600;">
                            {{ $delta['dir'] === 'up' ? '↗' : '↘' }} {{ number_format(abs($delta['pct'] ?? 0), 1) }}%
                        </div>
                    </div>
                    <small style="color: #94a3b8; font-size: 11px;">From last week</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-4 col-6 col-5-custom">
            <div class="dash-stat-card">
                <div>
                    <span style="color: #64748b; font-size: 14px; font-weight: 500;">Out of Stock</span>
                </div>
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                        <h3 style="font-size: 28px; font-weight: 700; margin: 0; color: #1e293b;">
                            {{ number_format($outOfStock) }}</h3>
                        @php($delta = $cardDeltas['outOfStock'] ?? ['pct' => 0, 'dir' => 'up'])
                        <div
                            style="background: {{ $delta['dir'] === 'up' ? '#f0fdf4' : '#fef2f2' }}; color: {{ $delta['dir'] === 'up' ? '#16a34a' : '#dc2626' }}; padding: 4px 8px; border-radius: 10px; font-size: 11px; font-weight: 600;">
                            {{ $delta['dir'] === 'up' ? '↗' : '↘' }} {{ number_format(abs($delta['pct'] ?? 0), 1) }}%
                        </div>
                    </div>
                    <small style="color: #94a3b8; font-size: 11px;">From last week</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-4 col-6 col-5-custom">
            <div class="dash-stat-card">
                <div>
                    <span style="color: #64748b; font-size: 14px; font-weight: 500;">Total Sold</span>
                </div>
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                        <h3 style="font-size: 28px; font-weight: 700; margin: 0; color: #1e293b;">
                            {{ number_format($totalSold) }}</h3>
                        @php($delta = $cardDeltas['totalSold'] ?? ['pct' => 0, 'dir' => 'up'])
                        <div
                            style="background: {{ $delta['dir'] === 'up' ? '#f0fdf4' : '#fef2f2' }}; color: {{ $delta['dir'] === 'up' ? '#16a34a' : '#dc2626' }}; padding: 4px 8px; border-radius: 10px; font-size: 11px; font-weight: 600;">
                            {{ $delta['dir'] === 'up' ? '↗' : '↘' }} {{ number_format(abs($delta['pct'] ?? 0), 1) }}%
                        </div>
                    </div>
                    <small style="color: #94a3b8; font-size: 11px;">From last week</small>
                </div>
            </div>
        </div>
    </div>

    {{-- ==========================================
         4. MAIN INVENTORY MANAGEMENT SECTION
         ========================================== --}}
    <section class="content">
        <div class="container-fluid"
            style="max-width: 100%; display: block; margin-left: 5px; margin-right: 5px; margin-top: 20px;">
            <div class="row">
                <div class="col-12">
                    <div class="card" style=" display: block; background:#fff; padding:20px">
                        {{-- ==========================================
                             4.1 INVENTORY LIST HEADER WITH IMPORT AND ADD BUTTONS
                             ========================================== --}}
                        <div class="d-flex justify-content-between align-items-center bg-white p-3  rounded shadow-sm mb-4">
                            <h5 class="card-title fs-4 fw-bold m-0">Inventory List</h5>
                            {{-- POS Sale Import Form --}}
                            <div class="import-container">
                                <form action="{{ route('import_pos_sales') }}" method="POST" enctype="multipart/form-data"
                                    class="m-0">
                                    @csrf
                                    <div class="input-group pos-input-group" style="max-width: 330px;">
                                        <span class="input-group-text bg-light border-0 px-3">
                                            <i class="bi bi-receipt text-success"></i>
                                        </span>
                                        <input type="file" name="pos_import" class="form-control border-0 bg-light"
                                            id="inputGroupFile04" required>
                                        <button class="btn btn-success px-4" id="importBtn" type="submit">
                                            <i class="bi bi-cloud-arrow-up-fill me-1"></i> POS SALE
                                        </button>
                                    </div>
                                </form>
                            </div>

                            {{-- Add Inventory Button --}}
                            <div class="d-flex gap-2">
                                <button type="button" class="btn-inventory" data-bs-toggle="modal"
                                    data-bs-target="#addInventoryModal">
                                    <i class="bi bi-plus-lg"></i>
                                    Add Inventory
                                </button>
                            </div>
                        </div>

                        {{-- ==========================================
                             4.2 ADD INVENTORY MODAL
                             ========================================== --}}
                        <div class="modal fade" id="addInventoryModal" tabindex="-1" aria-labelledby="addInventoryModal"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content border-0"
                                    style="border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">

                                    <div class="modal-header bg-dark text-white py-3">
                                        <h5 class="modal-title fw-bold" id="addTeacherModalLabel">
                                            <i class="fas fa-box-open me-2"></i> Inventory
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>


                                    <div class="modal-body px-4 pb-4">
                                        @include('layout.partials.alerts')

                                        <form id="addInventoryForm" method="POST" action="{{ route('add_new_inventory') }}"
                                            enctype="multipart/form-data">
                                            @csrf

                                            <div class="mb-4">
                                                <label class="text-uppercase text-muted fw-bold mb-3"
                                                    style="font-size: 11px; letter-spacing: 1px;">Basic
                                                    Information</label>
                                                <hr class="mt-0 mb-4" style="opacity: 0.1;">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold"
                                                            style="color: #475569;">Category</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-light border-end-0"
                                                                style="border-radius: 10px 0 0 10px;">
                                                                <i class="bi bi-tag text-muted"></i>
                                                            </span>
                                                            <select id="category_ID_add" name="category_ID"
                                                                class="form-select bg-light border-start-0 js-category-select"
                                                                style="border-radius: 0 10px 10px 0; height: 45px;"
                                                                required>
                                                                <option value="">Select Category</option>
                                                                @foreach ($categories as $cat)
                                                                    <option value="{{ $cat->category_ID }}">
                                                                        {{ $cat->category_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold"
                                                            style="color: #475569;">Product</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-light border-end-0"
                                                                style="border-radius: 10px 0 0 10px;">
                                                                <i class="bi bi-tag text-muted"></i>
                                                            </span>
                                                            <select id="product_ID_add" name="product_ID"
                                                                class="form-select bg-light border-start-0 js-product-select"
                                                                style="border-radius: 0 10px 10px 0; height: 45px;"
                                                                required>
                                                                <option value="">Select Product</option>

                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="mb-4">
                                                <label class="text-uppercase text-muted fw-bold mb-3"
                                                    style="font-size: 11px; letter-spacing: 1px;">Stock
                                                    Management</label>
                                                <hr class="mt-0 mb-4" style="opacity: 0.1;">

                                                <div class="row g-3">

                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Cost Price</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">$</span>
                                                            <input id="product_cost_add" type="number"
                                                                name="product_cost" class="form-control js-product-cost"
                                                                step="0.01" placeholder="0.00">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Selling Price</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">$</span>
                                                            <input id="product_price_add" type="number"
                                                                name="product_price" class="form-control js-product-price"
                                                                step="0.01" placeholder="0.00" value=""
                                                                required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold"
                                                            style="color: #475569;">Available Quantity</label>
                                                        <input type="number" name="batch_quantity"
                                                            class="js-product-qty form-control" readonly>
                                                    </div>

                                                </div>

                                            </div>

                                            {{-- Modal Footer --}}
                                            <div class="d-flex justify-content-end gap-2 mt-5">
                                                <button type="button" class="btn btn-light px-4 fw-semibold"
                                                    data-bs-dismiss="modal"
                                                    style="border-radius: 10px; color: #64748b;">Cancel</button>
                                                <button type="submit" class="btn btn-primary px-5 fw-semibold"
                                                    style="border-radius: 10px; background: #007bff; border: none;">Save
                                                    Inventory</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ==========================================
                             4.3 INVENTORY TABLE WITH FILTERS
                             ========================================== --}}
                        <div class="table-container" style="overflow-x: auto; padding: 0 10px;">
                            {{-- Table Controls --}}
                            <div class="row mb-3 mt-3 align-items-center justify-content-between">
                                <div class="col-auto">
                                    <button type="button" id="btn-close-period" class="btn btn-danger btn-sm">
                                        <i class="bi bi-calendar-check-fill"></i> Close Month Period
                                    </button>
                                </div>

                                <div class="col-md-7 d-flex justify-content-end gap-2">
                                    <div class="col-md-5">
                                        <div class="input-group shadow-sm">
                                            <span class="input-group-text bg-white">
                                                <i class="bi bi-filter-right"></i>
                                            </span>
                                            <select id="tableCategoryFilter" class="form-select">
                                                <option value="all"> - Choose Category - </option>
                                                @foreach ($categories as $cat)
                                                    <option value="{{ $cat->category_ID }}">
                                                        {{ strtoupper($cat->category_name) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-5">
                                        <div class="input-group shadow-sm">
                                            <span class="input-group-text bg-white">
                                                <i class="bi bi-filter-left"></i>
                                            </span>
                                            <select id="tableProductFilter" class="form-select">
                                                <option value="all"> - Choose Product - </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Inventory DataTable --}}
                            <table id="example2" class="table table-bordered table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Inventory ID</th>
                                        <th>Product Name</th>
                                        <th>Product Category</th>
                                        <th>Cost</th>
                                        <th>Price</th>
                                        <th>Starting Quantity</th>
                                        <th>New Quantity</th>
                                        <th>Total Sold</th>
                                        <th>Remaining Stock</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody style="font-family: 'Inter', sans-serif; text-align: center;">
                                </tbody>
                            </table>
                        </div>

                        {{-- ==========================================
                             4.4 UPDATE PRODUCT MODAL
                             ========================================== --}}
                        <div class="modal fade" id="UpdateProductModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title">Update Product</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form id="updateProductForm" action="{{ route('update_inventory') }}"
                                        method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <input type="hidden" name="inventory_ID" id="edit_inventory_ID">
                                            <input type="hidden" name="product_ID" id="edit_product_ID">

                                            <div class="mb-3">
                                                <label class="form-label">Product Name</label>
                                                <input type="text" id="edit_product_name" name="product_name"
                                                    class="form-control" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Category</label>
                                                <select name="category_ID" id="edit_category" class="form-control"
                                                    required>
                                                    <option value="">-- Select Category --</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->category_ID }}">
                                                            {{ strtoupper($category->category_name) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">New Quantity</label>
                                                <input type="number" id="edit_NewQuantity" name="update_NewQuantity"
                                                    class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Remaining Stock</label>
                                                <input type="number" id="edit_remainingstock"
                                                    name="update_remainingstock" class="form-control" readonly>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary w-100">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- /.card-body --}}
                    </div>

                    {{-- ==========================================
                         4.5 PRODUCT PERFORMANCE CHART
                         ========================================== --}}
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                                <div
                                    class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center">
                                    <h5 class="fw-bold mb-0 text-black">Product Performance</h5>

                                    <select id="chartCategoryFilter" class="form-select form-select-sm w-auto">
                                        <option value="all">All Categories</option>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->category_ID }}"
                                                {{ isset($selectedCategory) && $selectedCategory == $cat->category_ID ? 'selected' : '' }}>
                                                {{ $cat->category_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="card-body">
                                    <div style="height: 300px;">
                                        <canvas id="inventoryStatusChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
    </section>

@endsection

@section('scripts src')
    <script>
        $(document).ready(function() {
            // ==========================================
            // 1. DATATABLE INITIALIZATION
            // ==========================================
            var table = $('#example2').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                searching: true,
                lengthChange: false,
                ajax: {
                    url: "{{ route('view_inventory') }}",
                    data: function(d) {
                        d.category_id_table = $('#tableCategoryFilter').val();
                        d.product_id_table = $('#tableProductFilter').val();
                    }
                },
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Processing...</span></div>'
                },
                columns: [{
                        data: 'inventory_ID',
                        name: 'inventory.inventory_ID',
                        render: function(data, type, row) {
                            // This displays "PRDCT-11" instead of just "11"
                            return '<span class="fw-bold text-secondary">INVT-' + data + '</span>';
                        }
                    },
                    {
                        data: 'product_name',
                        name: 'products.product_name'
                    },
                    {
                        data: 'name',
                        name: 'category.category_name'
                    },
                    {
                        data: 'unit_price',
                        name: 'purchase_items.unit_price'
                    },
                    {
                        data: 'invt_sellingPrice',
                        name: 'inventory.invt_sellingPrice'
                    },
                    {
                        data: 'invt_StartingQuantity',
                        name: 'inventory.invt_StartingQuantity'
                    },
                    {
                        data: 'invt_NewQuantity',
                        name: 'inventory.invt_NewQuantity'
                    },
                    {
                        data: 'invt_totalSold',
                        name: 'inventory.invt_totalSold'
                    },
                    {
                        data: 'invt_remainingStock',
                        name: 'inventory.invt_remainingStock'
                    },
                    {
                        data: 'status_ID',
                        name: 'inventory.status_ID',
                        render: function(data) {
                            if (data == 1)
                                return '<span class="status-badge status-in-stock">In Stock</span>';
                            if (data == 2)
                                return '<span class="status-badge status-low-stock">Low Stock</span>';
                            if (data == 3)
                                return '<span class="status-badge status-out-of-stock">Out of Stock</span>';
                            return '<span class="status-badge bg-secondary">Unknown</span>';
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
            });

            // ==========================================
            // 1.1 DELETE ACTION HANDLER
            // ==========================================
            $('#example2 tbody').on('click', '.delete-btn', function() {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Move to Trash?',
                    text: "Please Double Check before moving to trash.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#198754', // Matches your success/green theme
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, trash it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // If user clicks "Yes", run the AJAX
                        $.ajax({
                            url: "/admin/InventorysoftDelete/" + id,
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                _method: 'POST'
                            },
                            success: function(response) {
                                table.ajax.reload(null, false);

                                // Show a success SweetAlert after deletion
                                Swal.fire(
                                    'Deleted!',
                                    'Inventory has been moved to trash.',
                                    'success'
                                );
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error!',
                                    'Could not delete the Inventory.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });

            // ==========================================
            // 2. CHART.JS LOGIC
            // ==========================================
            var ctx = document.getElementById('inventoryStatusChart').getContext('2d');
            var inventoryChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: []
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            function refreshChartOnly() {
                $.ajax({
                    url: "{{ route('view_inventory') }}",
                    data: {
                        get_chart: true,
                        category_id: $('#chartCategoryFilter').val()
                    },
                    success: function(data) {
                        inventoryChart.data.labels = data.map(item => item.name);
                        inventoryChart.data.datasets = [{
                                label: 'Sold',
                                data: data.map(item => item.sold),
                                backgroundColor: '#f87171'
                            },
                            {
                                label: 'Remaining',
                                data: data.map(item => item.remaining),
                                backgroundColor: '#4CAF50'
                            }
                        ];
                        inventoryChart.update();
                    }
                });
            }

            // Trigger Table & Chart refresh
            $('#chartCategoryFilter').on('change', refreshChartOnly);
            $('#tableCategoryFilter').on('change', function() {
                table.draw();
            });

            // ==========================================
            // 3. EDIT MODAL LOGIC
            // ==========================================
            $('#example2 tbody').on('click', '.edit-btn', function() {
                var el = $(this);
                $('#edit_inventory_ID').val(el.attr('data-inventory-id'));
                $('#edit_product_ID').val(el.attr('data-product-id'));
                $('#edit_product_name').val(el.attr('data-product-name'));
                $('#edit_category').val(el.attr('data-category-ID'));
                $('#edit_NewQuantity').val(el.attr('data-update_NewQuantity'));
                $('#edit_remainingstock').val(el.attr('data-update_remainingstock'));
                $('#UpdateProductModal').modal('show');
            });

            $('#updateProductForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        // 1. Close the modal first
                        $('#UpdateProductModal').modal('hide');

                        // 2. Trigger professional Success Alert
                        Swal.fire({
                            title: 'Updated!',
                            text: response.save,
                            icon: 'success',
                            confirmButtonColor: '#3085d6',
                            timer: 2000 // Optional: automatically closes after 2 seconds
                        });

                        // 3. Refresh live UI components
                        table.ajax.reload(null, false);
                        refreshChartOnly();
                    },
                    error: function(xhr) {
                        // Trigger Error Alert
                        Swal.fire({
                            title: 'Update Failed',
                            text: xhr.responseJSON?.message ||
                                'Something went wrong while updating the inventory.',
                            icon: 'error',
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            });

            // ==========================================
            // 4. DYNAMIC DROPDOWNS (Filtered Products with Batch Qty)
            // ==========================================
            function populateProductsIntoSelect(productSelect, data, isFilter) {
                var defaultText = isFilter ? ' - All Products - ' : '--Select Product--';
                var defaultValue = isFilter ? 'all' : '';

                productSelect.empty().append('<option value="' + defaultValue + '">' + defaultText + '</option>');

                $.each(data, function(key, value) {
                    var qty = parseInt(value.batch_quantity) || 0;

                    // Only show the quantity label if it is NOT being used as a filter
                    var statusLabel = "";
                    if (!isFilter) {
                        statusLabel = (qty > 0) ? ` (${qty} available)` : ` (No available product)`;
                    }

                    // Disable selection in the Add Modal if qty is 0
                    // We keep it enabled for the filter so you can still search for out-of-stock items
                    var isDisabled = (!isFilter && qty <= 0) ? 'disabled style="color: #adb5bd;"' : '';

                    productSelect.append(
                        `<option value="${value.product_ID}" 
                data-unit-cost="${value.unit_cost}" 
                data-qty="${qty}" 
                ${isDisabled}>
                ${value.product_name}${statusLabel}
            </option>`
                    );
                });
            }

            $(document).on('change', '.js-category-select, #tableCategoryFilter', function() {
                var categoryId = $(this).val();
                var isFilter = ($(this).attr('id') === 'tableCategoryFilter');

                var productSelect = isFilter ? $('#tableProductFilter') : $(this).closest('form').find(
                    '.js-product-select');

                // Reset dependent fields
                if (!isFilter) {

                    var form = $(this).closest('form');
                    form.find('.js-product-cost').val('');
                    form.find('.js-product-qty').val(''); // Clear quantity too

                }

                productSelect.empty().append('<option value="">Loading...</option>');

                if (categoryId && categoryId !== 'all') {
                    $.ajax({
                        url: "/admin/getProductsByCategory/" + categoryId,
                        type: 'GET',
                        success: function(data) {
                            populateProductsIntoSelect(productSelect, data, isFilter);
                        },
                        error: function() {
                            productSelect.empty().append(
                                '<option value="">Failed to load products</option>');
                        }
                    });
                } else {

                    $('#tableProductFilter').val('all');
                    table.draw();
                    productSelect.empty().append('<option value="' + (isFilter ? 'all' : '') + '">' + (
                        isFilter ? ' - All Products - ' : 'Select Product') + '</option>');
                }
            });

            $(document).on('change', '.js-product-select, #tableProductFilter', function() {
                if ($(this).attr('id') === 'tableProductFilter') {
                    table.draw();
                    return;
                }

                var selected = $(this).find('option:selected');
                var form = $(this).closest('form');

                var cost = selected.data('unit-cost');
                var batchQty = selected.data('qty'); // Get quantity from the new data attribute

                form.find('.js-product-cost').val(cost === undefined ? '' : cost);

                // This is the specific part you wanted: Auto-filling the quantity from the batch
                form.find('.js-product-qty').val(batchQty === undefined ? '' : batchQty);
            });

            // ==========================================
            // 5. POS SALE IMPORT (AJAX File Upload)
            // ==========================================
            $(document).on('submit', '.import-container form', function(e) {
                e.preventDefault(); // Prevent page refresh

                var formData = new FormData(this); // Required to send the physical file
                var btn = $('#importBtn');

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false, // Tell jQuery not to process data as string
                    contentType: false, // Tell jQuery not to set contentType
                    beforeSend: function() {
                        btn.prop('disabled', true).html(
                            '<span class="spinner-border spinner-border-sm"></span> Processing...'
                        );
                    },
                    success: function(response) {
                        // 1. Success Notification using SweetAlert2
                        Swal.fire({
                            title: 'Success!',
                            text: response.save,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#3085d6'
                        });

                        // 2. Clear the file input
                        $('.import-container form')[0].reset();

                        // 3. REFRESH LIVE DATA
                        table.ajax.reload(null, false); // Refresh DataTable
                        refreshChartOnly(); // Refresh Chart.js
                    },
                    error: function(xhr) {
                        // Display the specific error from your Controller (e.g., duplicate hash)
                        var msg = xhr.responseJSON ? xhr.responseJSON.error : 'Import failed';
                        alert('Error: ' + msg);
                    },
                    complete: function() {
                        // Reset button state
                        btn.prop('disabled', false).html(
                            '<i class="bi bi-cloud-arrow-up-fill me-1"></i> POS SALE'
                        );
                    }
                });
            });
            // ==========================================
            // ADD INVENTORY (With Review Confirmation)
            // ==========================================
            $('#addInventoryForm').on('submit', function(e) {
                e.preventDefault();

                var form = $(this);
                var actionUrl = form.attr('action');

                // Capture values for the Review Modal
                // Change this line:
                var productName = $('#product_ID_add').find(' option:selected').text().split('(')[0].trim();
                var categoryName = $('.js-category-select option:selected').text();
                var cost = form.find('.js-product-cost').val();
                var qty = form.find('.js-product-qty').val();

                // 1. Show the Professional Review Modal
                Swal.fire({
                    title: 'Confirm Stock Entry',
                    html: `
            <div style="text-align: left; font-size: 0.9rem; line-height: 1.6; overflow-x: hidden;">
                <div class="mb-2"><strong>Category:</strong> <span class="text-primary">${categoryName}</span></div>
                <div class="mb-2"><strong>Product:</strong> ${productName}</div>
                <hr>
                <div class="row mx-0">
                    <div class="col-6 px-0"><strong>Unit Cost:</strong> ₱${cost}</div>
                    <div class="col-6 px-0 text-end"><strong>Quantity:</strong> ${qty}</div>
                </div>
            </div>
        `,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#0d6efd',
                    confirmButtonText: 'Confirm and Save',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {

                        // 2. Perform the AJAX request
                        $.ajax({
                            url: actionUrl,
                            method: 'POST',
                            data: form.serialize(),
                            beforeSend: function() {
                                Swal.fire({
                                    title: 'Saving Entry...',
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });
                            },
                            success: function(response) {
                                // Hide your Add Inventory Modal
                                $('#AddInventoryModal').modal('hide');
                                form[0].reset();

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Stock Added!',
                                    text: response.save,
                                    timer: 2000,
                                    showConfirmButton: false
                                });

                                // Refresh Table and Chart
                                if ($.fn.DataTable.isDataTable('#example2')) {
                                    $('#example2').DataTable().ajax.reload(null, false);
                                }
                                refreshChartOnly();
                            },
                            error: function(xhr) {
                                var errorMsg = xhr.responseJSON?.error ||
                                    'Something went wrong.';
                                Swal.fire('Error', errorMsg, 'error');
                            }
                        });
                    }
                });
            });

            // ==========================================
            // 6. ADD INVENTORY (With Review Confirmation)
            // ==========================================
            $('#addInventoryForm').on('submit', function(e) {
                e.preventDefault();

                var form = $(this);
                var actionUrl = form.attr('action');

                // Capture values for the Review Modal
                // Change this line:
                var productName = $('#product_ID_add').find(' option:selected').text().split('(')[0].trim();
                var categoryName = $('.js-category-select option:selected').text();
                var cost = form.find('.js-product-cost').val();
                var qty = form.find('.js-product-qty').val();

                // 1. Show the Professional Review Modal
                Swal.fire({
                    title: 'Confirm Stock Entry',
                    html: `
            <div style="text-align: left; font-size: 0.9rem; line-height: 1.6; overflow-x: hidden;">
                <div class="mb-2"><strong>Category:</strong> <span class="text-primary">${categoryName}</span></div>
                <div class="mb-2"><strong>Product:</strong> ${productName}</div>
                <hr>
                <div class="row mx-0">
                    <div class="col-6 px-0"><strong>Unit Cost:</strong> ₱${cost}</div>
                    <div class="col-6 px-0 text-end"><strong>Quantity:</strong> ${qty}</div>
                </div>
            </div>
        `,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#0d6efd',
                    confirmButtonText: 'Confirm and Save',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {

                        // 2. Perform the AJAX request
                        $.ajax({
                            url: actionUrl,
                            method: 'POST',
                            data: form.serialize(),
                            beforeSend: function() {
                                Swal.fire({
                                    title: 'Saving Entry...',
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });
                            },
                            success: function(response) {
                                // Hide your Add Inventory Modal
                                $('#AddInventoryModal').modal('hide');
                                form[0].reset();

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Stock Added!',
                                    text: response.save,
                                    timer: 2000,
                                    showConfirmButton: false
                                });

                                // Refresh Table and Chart
                                if ($.fn.DataTable.isDataTable('#example2')) {
                                    $('#example2').DataTable().ajax.reload(null, false);
                                }
                                refreshChartOnly();
                            },
                            error: function(xhr) {
                                var errorMsg = xhr.responseJSON?.error ||
                                    'Something went wrong.';
                                Swal.fire('Error', errorMsg, 'error');
                            }
                        });
                    }
                });
            });

            // ==========================================
            // 7. MONTHLY ROLLOVER (Security Logic)
            // ==========================================
            $(document).on('click', '#btn-close-period', function(e) {
                e.preventDefault();

                // 1. Initial Confirmation Alert
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will lock current remaining stock as the 'Starting Quantity' for the new month.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, proceed!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {

                        // 2. The "Type CLOSE" Confirmation
                        Swal.fire({
                            title: 'Type CLOSE to confirm:',
                            input: 'text',
                            inputAttributes: {
                                autocapitalize: 'off'
                            },
                            showCancelButton: true,
                            confirmButtonText: 'Submit',
                            preConfirm: (value) => {
                                if (value !== 'CLOSE') {
                                    Swal.showValidationMessage(
                                        'You must type "CLOSE" exactly');
                                }
                                return value;
                            }
                        }).then((inputResult) => {
                            if (inputResult.isConfirmed && inputResult.value === 'CLOSE') {

                                // 3. The Actual AJAX Call
                                $.ajax({
                                    url: "{{ route('inventory_rollover') }}",
                                    type: "POST",
                                    data: {
                                        _token: "{{ csrf_token() }}"
                                    },
                                    beforeSend: function() {
                                        // Show a "Processing" alert that doesn't close
                                        Swal.fire({
                                            title: 'Processing Rollover...',
                                            text: 'Please wait while we update the inventory.',
                                            allowOutsideClick: false,
                                            didOpen: () => {
                                                Swal.showLoading();
                                            }
                                        });
                                    },
                                    success: function(response) {
                                        // Show final success message from your Backend 'save' key
                                        Swal.fire({
                                            title: 'Completed!',
                                            text: response.save,
                                            icon: 'success'
                                        }).then(() => {
                                            location
                                                .reload(); // Reload after user clicks OK
                                        });
                                    },
                                    error: function(xhr) {
                                        // Show error message from Backend 'error' key
                                        Swal.fire({
                                            title: 'Error!',
                                            text: xhr.responseJSON
                                                .error ||
                                                "System error",
                                            icon: 'error'
                                        });
                                    }
                                });
                            }
                        });
                    }
                });
            });

            refreshChartOnly(); // Initial load
        });
    </script>

@endsection
