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
                        <div
                            style="background: #f0fdf4; color: #16a34a; padding: 4px 8px; border-radius: 10px; font-size: 11px; font-weight: 600;">
                            ↗ 0.5%
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
                        <div
                            style="background: #fef2f2; color: #dc2626; padding: 4px 8px; border-radius: 10px; font-size: 11px; font-weight: 600;">
                            ↘ 1.2%
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
                        <div
                            style="background: #f0fdf4; color: #16a34a; padding: 4px 8px; border-radius: 10px; font-size: 11px; font-weight: 600;">
                            ↗ 1.5%
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
                        <div
                            style="background: #fef2f2; color: #dc2626; padding: 4px 8px; border-radius: 10px; font-size: 11px; font-weight: 600;">
                            ↘ 1.5%
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
                        <div
                            style="background: #fef2f2; color: #dc2626; padding: 4px 8px; border-radius: 10px; font-size: 11px; font-weight: 600;">
                            ↘ 1.5%
                        </div>
                    </div>
                    <small style="color: #94a3b8; font-size: 11px;">From last week</small>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid"
            style="max-width: 100%; display: block; margin-left: 5px; margin-right: 5px; margin-top: 20px;">
            <div class="row">
                <div class="col-12">
                    <div class="card" style=" display: block; background:#fff; padding:20px">
                        <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded shadow-sm mb-4">
                            <h5 class="card-title fs-4 fw-bold m-0">Inventory List</h5>
                            <!-- Button trigger modal -->





                            <div class="import-container">
                                <form action="{{ route('import_pos_sales') }}" method="POST" enctype="multipart/form-data"
                                    class="m-0">
                                    @csrf
                                    <div class="input-group pos-input-group" style="max-width: 330px;">
                                        <span class="input-group-text bg-light border-0 px-3">
                                            <i class="bi bi-receipt text-success"></i>
                                        </span>
                                        <input type="file" name="inventory_file" class="form-control border-0 bg-light"
                                            id="inputGroupFile04" required>
                                        <button class="btn btn-success px-4" id="importBtn" type="submit">
                                            <i class="bi bi-cloud-arrow-up-fill me-1"></i> POS SALE
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <button type="button" class="btn-inventory" data-bs-toggle="modal"
                                data-bs-target="#exampleModal">
                                <i class="bi bi-plus-lg"></i>
                                New Product
                            </button>
                            <!-- Modal -->
                            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="addScheduleModalLabel"
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

                                            <form method="POST" action="{{ route('save_inventory') }}"
                                                enctype="multipart/form-data">
                                                @csrf

                                                <div class="mb-4">
                                                    <label class="text-uppercase text-muted fw-bold mb-3"
                                                        style="font-size: 11px; letter-spacing: 1px;">Basic
                                                        Information</label>
                                                    <hr class="mt-0 mb-4" style="opacity: 0.1;">
                                                    <input type="hidden" name="inventory_ID" id="edit_id">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-semibold"
                                                                style="color: #475569;">Category</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text bg-light border-end-0"
                                                                    style="border-radius: 10px 0 0 10px;">
                                                                    <i class="bi bi-tag text-muted"></i>
                                                                </span>
                                                                <select id="category_ID" name="category_ID"
                                                                    class="form-select bg-light border-start-0"
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
                                                                <select id="product_ID" name="product_ID"
                                                                    class="form-select bg-light border-start-0"
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
                                                                <input id="product_cost" type="number"
                                                                    name="product_cost" class="form-control"
                                                                    step="0.01" placeholder="0.00" value=""
                                                                    required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-semibold">Selling Price</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text">$</span>
                                                                <input id="product_price" type="number"
                                                                    name="product_price" class="form-control"
                                                                    step="0.01" placeholder="0.00" value=""
                                                                    required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-semibold"
                                                                style="color: #475569;">Quantity</label>
                                                            <input id="product_StartingQuantity" type="number"
                                                                name="product_StartingQuantity"
                                                                class="form-control bg-light" placeholder="0"
                                                                style="border-radius: 10px; height: 45px;" value=""
                                                                required>
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="d-flex justify-content-end gap-2 mt-5">
                                                    <button type="button" class="btn btn-light px-4 fw-semibold"
                                                        data-bs-dismiss="modal"
                                                        style="border-radius: 10px; color: #64748b;">Cancel</button>
                                                    <button type="submit" class="btn btn-primary px-5 fw-semibold"
                                                        style="border-radius: 10px; background: #007bff; border: none;">Save
                                                        Product</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- /.card-header -->
                        <div class="table-container" style="overflow-x: auto; padding: 0 10px;">
                            <div class="row mb-3 mt-3 justify-content-end">
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white shadow-sm"><i
                                                class="bi bi-filter-right"></i></span>
                                        <select id="tableCategoryFilter" class="form-select shadow-sm">
                                            <option value="all"> - Choose Category - </option>
                                            @foreach ($categories as $cat)
                                                <option value="{{ $cat->category_ID }}">
                                                    {{ strtoupper($cat->category_name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white shadow-sm"><i
                                                class="bi bi-filter-left"></i></span>
                                        <select id="tableProductFilter" class="form-select shadow-sm">
                                            <option value="all"> - Choose Product - </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
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


                                        <!-- <th>ACtion</th> -->
                                    </tr>
                                </thead>
                                <tbody style="font-family: 'Inter', sans-serif; text-align: center;">

                                </tbody>
                            </table>


                        </div>

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
                        <!-- /.card-body -->
                    </div>
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

@section('tables')

    <script>
        $(document).ready(function() {
            // ==========================================
            // 1. DATATABLE INITIALIZATION (Inventory Table)
            // ==========================================
            var table = $('#example2').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                searching: false,
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
                        name: 'inventory.inventory_ID'
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
                        data: 'product_cost',
                        name: 'products.product_cost'
                    },
                    {
                        data: 'product_price',
                        name: 'products.product_price'
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
                            if (data == 1) return '<span class="status-in-stock">In Stock</span>';
                            if (data == 2) return '<span class="status-low-stock">Low Stock</span>';
                            if (data == 3)
                                return '<span class="status-out-of-stock">Out of Stock</span>';
                            return '<span class="badge bg-secondary">Unknown</span>';
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

            // Trigger Table & Chart refresh when Filter changes
            $('#chartCategoryFilter').on('change', function() {
                table.draw();
                refreshChartOnly();
            });

            $('#tableCategoryFilter').on('change', function() {
                table.draw();
            });

            // ==========================================
            // 3. EDIT MODAL LOGIC (Inventory Update)
            // ==========================================
            $('#example2 tbody').on('click', '.edit-btn', function() {
                // jQuery camelCase conversion for data-attributes
                var id = $(this).attr('data-inventory-id');
                var prodID = $(this).attr('data-product-id');
                var prodName = $(this).attr('data-product-name');
                var catID = $(this).attr('data-category-ID');
                var qnty = $(this).attr('data-update_NewQuantity');
                var rStock = $(this).attr('data-update_remainingstock');

                $('#edit_inventory_ID').val(id);
                $('#edit_product_ID').val(prodID);

                $('#edit_product_name').val(prodName);
                $('#edit_category').val(catID);
                $('#edit_NewQuantity').val(qnty);
                $('#edit_remainingstock').val(rStock);

                $('#UpdateProductModal').modal('show');
            });

            $('#updateProductForm').on('submit', function(e) {
                e.preventDefault();
                var actionUrl = $(this).attr('action');
                var formdata = $(this).serialize();

                $.ajax({
                    url: actionUrl,
                    method: 'POST',
                    data: formdata,
                    success: function(response) {
                        $('#UpdateProductModal').modal('hide');
                        table.ajax.reload(null, false);
                        refreshChartOnly();
                        alert('Inventory updated successfully!');
                    },
                    error: function() {
                        alert('Something went wrong with the update!');
                    }
                });
            });

            // ==========================================
            // 4. SAVE NEW PRODUCT LOGIC (Dropdown mapping)
            // ==========================================
            $(document).on('change', '#category_ID, #tableCategoryFilter', function() {
                var categoryId = $(this).val();
                var productSelect = ($(this).attr('id') === 'tableCategoryFilter') ? $(
                    '#tableProductFilter') : $('#product_ID');

                productSelect.empty().append('<option value="">-- Loading Products... --</option>');
                $('#product_cost, #product_price, #product_StartingQuantity').val('');

                if ($(this).attr('id') === 'category_ID') {
                    $('#product_cost, #product_price, #product_StartingQuantity').val('');
                }

                if (categoryId && categoryId !== 'all') {
                    $.ajax({
                        url: "/admin/get-products-by-category/" + categoryId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            // Determine the default text based on which dropdown it is
                            var defaultText = ($(this).attr('id') === 'tableCategoryFilter') ?
                                ' - All Products - ' : '--Select Product--';
                            var defaultValue = ($(this).attr('id') === 'tableCategoryFilter') ?
                                'all' : '';

                            productSelect.empty().append('<option value="' + defaultValue +
                                '">' + defaultText + '</option>');

                            if (data.length === 0) {
                                productSelect.append(
                                    '<option value="">No products found</option>');
                                return;
                            }

                            $.each(data, function(key, value) {
                                productSelect.append('<option value="' + value
                                    .product_ID + '" ' +
                                    'data-cost="' + value.product_cost + '" ' +
                                    'data-price="' + value.product_price + '" ' +
                                    'data-qty="' + (value.current_stock || 0) +
                                    '">' +
                                    value.product_name + '</option>');
                            });
                        }.bind(this) // Bind 'this' so we can check the ID inside success
                    });
                } else {
                    productSelect.empty().append('<option value="all"> - All Products - </option>');
                }
            });

            $(document).on('change', '#product_ID, #tableProductFilter', function() {
                var selected = $(this).find('option:selected');
                $('#product_cost').val(selected.data('cost'));
                $('#product_price').val(selected.data('price'));
                $('#product_StartingQuantity').val(selected.data('qty'));

                if ($(this).attr('id') === 'tableProductFilter') {
                    table.draw();
                }
            });

            // ==========================================
            // 5. MISC (Import Button & Initial Load)
            // ==========================================
            $('form').on('submit', function() {
                $(this).find('#importBtn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm"></span> Importing...');
            });

            refreshChartOnly(); // Load chart data on page open
        });
    </script>

    <style>
        .status-in-stock,
        .status-low-stock,
        .status-out-of-stock {
            display: inline-block;
            white-space: nowrap;
            min-width: 100px;
            text-align: center;
            padding: 5px 10px;
            font-weight: 500;
            border-radius: 50px;

        }

        .status-in-stock {
            background-color: #a5ffb6;
            color: #234e52;
            border: 1px solid #02c702;
            border-radius: 55px;
            font-size: 0.9rem;

        }

        .status-low-stock {
            background-color: #ffedca;
            color: #a14022;
            border: 1px solid #ffbc3f;
            border-radius: 55px;
            font-size: 0.9rem;
        }

        .status-out-of-stock {
            background-color: #ffdada;
            color: #a41919;
            border: 1px solid #ff1515;
            border-radius: 55px;
            font-size: 0.9rem;
        }

        /* Add this to your <style> */
        .table-container {
            width: 100%;
            overflow-x: auto;
            /* Enables horizontal scroll */
            -webkit-overflow-scrolling: touch;
            /* Smooth scrolling on iOS */
            margin-bottom: 1rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
            /* Forces the table to maintain width so it scrolls instead of squishing */
        }

        td {
            align-items: center;
        }

        /* Custom Styling to hide the default text but keep functionality */
        .custom-file-upload {
            position: relative;
            display: center;
            width: 100%;
        }

        /* Professional Import Styling */
        .import-wrapper {
            flex-grow: 1;
            max-width: 500px;
        }

        .pos-input-group {
            border-radius: 50px !important;
            border: 1px solid #e0e0e0;
            background: #fdfdfd;
            overflow: hidden;

        }

        .pos-input-group input[type=file]::file-selector-button {
            display: none;
            /* Hides annoying browser button */
        }

        /* Inventory Button Styling */
        .btn-inventory {
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 50px;
            padding: 10px 24px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(76, 175, 80, 0.2);
            white-space: nowrap;
        }

        .btn-inventory:hover {
            background-color: #3d8b40;
            transform: translateY(-1px);
            box-shadow: 0 6px 12px rgba(76, 175, 80, 0.3);
            color: white;
        }

        /* Custom 5-column grid for the summary cards */
        @media (min-width: 992px) {
            .col-5-custom {
                flex: 0 0 auto;
                width: 20%;
                /* 100% divided by 5 cards */
            }
        }

        .dash-stat-card {
            background: white;
            padding: 20px;
            border-radius: 20px;
            border: 1px solid #f0f0f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            height: 155px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.2s ease;
        }

        .dash-stat-card:hover {
            transform: translateY(-5px);
        }
    </style>

@endsection
