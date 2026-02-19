@extends('themes.main')

{{-- 1. DEFINE PAGE TITLE --}}
@section('title', 'User Profile')

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
                        <h3 style="font-size: 28px; font-weight: 700; margin: 0; color: #1e293b;">{{ $totalProducts }}</h3>
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
                        <h3 style="font-size: 28px; font-weight: 700; margin: 0; color: #1e293b;">{{ $totalQuantity }}</h3>
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
                        <h3 style="font-size: 28px; font-weight: 700; margin: 0; color: #1e293b;">{{ $lowStockProducts }}
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
                        <h3 style="font-size: 28px; font-weight: 700; margin: 0; color: #1e293b;">{{ $outOfStock }}</h3>
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
                        <h3 style="font-size: 28px; font-weight: 700; margin: 0; color: #1e293b;">{{ $totalSold }}</h3>
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
                                                            <input id="product_quantity" type="number"
                                                                name="product_quantity" class="form-control bg-light"
                                                                placeholder="0" style="border-radius: 10px; height: 45px;"
                                                                value="" required>
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
                        <div class="table-container">
                            <table id="example2" class="table table-bordered table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Inventory ID</th>
                                        <th>Product Name</th>
                                        <th>Product Category</th>
                                        <th>Cost</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
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
                                    <form id="updateProductForm" action="{{ route('update_inventory') }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <input type="hidden" name="inventory_ID" id="edit_id">
                                            <div class="mb-3">
                                                <label class="form-label">Product Name</label>
                                                <input type="text" id="edit_product_name" name="product_ID"
                                                    class="form-control" required>
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
                                                <label class="form-label">Quantity</label>
                                                <input type="number" id="edit_quantity" name="quantity"
                                                    class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Remaining Stock</label>
                                                <input type="number" id="edit_remainingstock" name="remainingstock"
                                                    class="form-control">
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
            var table = $('#example2').DataTable({
                destroy: true, // use this to reinitialize the table if it already exists. use ths if you are using AJAX to load data and want to refresh the table with new data
                processing: true, // This shows a loading indicator while the data is being fetched, enhancing user experience.
                serverSide: true, // This enables the fast loading for 4k rows. instead of loading all data at once, it loads only the data needed for the current page.
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Processing...</span></div>'
                },
                ajax: "{{ route('view_inventory') }}", // Ensure this matches your route name

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
                        data: 'invt_quantity',
                        name: 'inventory.invt_quantity'
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
                        render: function(data, type, row) {
                            if (data == 1) {
                                return '<span class="status-in-stock">In Stock</span>';
                            } else if (data == 2) {
                                return '<span class="status-low-stock">Low Stock</span>';
                            } else if (data == 3) {
                                return '<span class="status-out-of-stock">Out of Stock</span>';
                            } else {
                                return '<span class="custom-badge" style="background-color: #e0e0e0; color: #333;">Unknown</span>';
                            }
                        }
                    },


                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                "dom": '<"d-flex align-items-end justify-content-between mb-4"Bf>rtip',
                "buttons": ["excel", "pdf", "print"]


            });

            $('#example2 tbody').on('click', '.edit-btn', function() {

                var id = $(this).data('id');
                var prodID = $(this).data('product-id');
                var prodName = $(this).data('product-name');
                var catID = $(this).data('category-id');
                var qnty = $(this).data('quantity');
                var rStock = $(this).data('remainingstock');

                $('#edit_id').val(id);
                $('#edit_product_name').val(prodName);
                $('#edit_category').val(catID);
                $('#edit_quantity').val(qnty);
                $('#edit_remainingstock').val(rStock);



                $('#UpdateProductModal').modal('show');
            });

            $('#updateProductForm').on('submit', function(e) {
                e.preventDefault();

                var actionUrl = $(this).attr(
                    'action'); // BEST OPTION IF THERE IS ACTION CRUD IN THE FUTURE. USUALLY USED FOR POST
                var formdata = $(this).serialize();

                $.ajax({
                    url: actionUrl,
                    method: 'POST',
                    data: formdata,
                    success: function(response) {
                        $('#UpdateProductModal').modal('hide');
                        table.ajax.reload(null,
                            false); // Reload the DataTable without resetting the pagination
                        alert('Product updated successfully!');
                    },
                    error: function(xhr) {
                        alert('Something went wrong!');
                    }
                });
            });
        });

        $(document).ready(function() {
            $(document).on('change', '#category_ID', function() {
                var categoryId = $(this).val();
                var productSelect = $('#product_ID');

                console.log("Category changed to: " + categoryId); // DEBUG 1

                // Clear everything first
                productSelect.empty().append('<option value="">-- Loading Products... --</option>');
                $('#product_cost, #product_price, #product_quantity').val('');

                if (categoryId) {
                    $.ajax({
                        url: "/admin/get-products-by-category/" + categoryId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            console.log("Server returned data:", data); // DEBUG 2

                            productSelect.empty().append(
                                '<option value=""> Select Product-</option>');

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
                                    'data-qty="' + value.current_stock + '">' +
                                    value.product_name +
                                    '</option>');
                            });
                        },
                        error: function(xhr) {
                            console.error("AJAX Error: ", xhr.status, xhr
                                .responseText); // DEBUG 3
                            productSelect.empty().append(
                                '<option value="">Error loading</option>');
                        }
                    });
                } else {
                    productSelect.empty().append('<option value="">-- Select Product --</option>');
                }
            });

            // This handles the second step: clicking the product
            $(document).on('change', '#product_ID', function() {
                var selected = $(this).find('option:selected');
                console.log("Product selected. Data values:", selected.data()); // DEBUG 4

                $('#product_cost').val(selected.data('cost'));
                $('#product_price').val(selected.data('price'));
                $('#product_quantity').val(selected.data('qty'));
            });
        });
        // This prevents double-clicking and shows the user something is happening
        $('form').on('submit', function() {
            $(this).find('#importBtn').prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm"></span> Importing...');
        });




        var inventoryChart; // Global variable

        $(document).ready(function() {
            // 1. Initialize DataTable
            var table = $('#inventoryTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('view_inventory') }}",
                    data: function(d) {
                        d.category_id = $('#chartCategoryFilter').val(); // Sync table with filter
                    }
                },

            });

            // 2. Initialize Chart (Empty at start)
            var ctx = document.getElementById('inventoryStatusChart').getContext('2d');
            inventoryChart = new Chart(ctx, {
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

            // 3. The "Refresh" function
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
                        inventoryChart.update(); // This is like table.draw() but for Chart.js
                    }
                });
            }

            // 4. Trigger both when filter changes
            $('#chartCategoryFilter').on('change', function() {
                table.draw(); // Refresh Table
                refreshChartOnly(); // Refresh Chart
            });

            // Initial load
            refreshChartOnly();
        });
    </script>

    <style>
        .dataTables_filter input {
            width: 250px !important;
            height: 38px !important;
            border: 1.5px solid #e2e8f0 !important;
            border-radius: 12px !important;
            /* Modern rounded look */
            background-color: #f8fafc !important;
            padding-left: 35px !important;
            /* Space for an icon */
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2394a3b8' class='bi bi-search' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: 12px center;
            transition: all 0.2s ease;
        }

        .dataTables_filter input:focus {
            border-color: #3b82f6 !important;
            background-color: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
            outline: none;
        }

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
