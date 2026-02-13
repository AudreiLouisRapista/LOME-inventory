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
        <div class="col-lg-3 col-6">
            <div class="dash-stat-card"
                style="background: white; padding: 20px; border-radius: 20px; border: 1px solid #f0f0f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); height: 155px; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #64748b; font-size: 14px; font-weight: 500;">Total Products</span>

                    </div>
                </div>
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                        <h3 style="font-size: 32px; font-weight: 700; margin: 0; color: #1e293b;">{{ $totalProducts }}</h3>
                        <div
                            style="background: #f0fdf4; color: #16a34a; padding: 4px 8px; border-radius: 10px; font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                            ↗ 0.53%
                        </div>
                    </div>
                    <small style="color: #94a3b8; font-size: 11px;">From last week</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="dash-stat-card"
                style="background: white; padding: 20px; border-radius: 20px; border: 1px solid #f0f0f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); height: 155px; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #64748b; font-size: 14px; font-weight: 500;">Available Stock</span>

                    </div>
                </div>
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                        <h3 style="font-size: 32px; font-weight: 700; margin: 0; color: #1e293b;">{{ $totalQuantity }}</h3>
                        <div
                            style="background: #fef2f2; color: #dc2626; padding: 4px 8px; border-radius: 10px; font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                            ↘ 1.24%
                        </div>
                    </div>
                    <small style="color: #94a3b8; font-size: 11px;">From last week</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="dash-stat-card"
                style="background: white; padding: 20px; border-radius: 20px; border: 1px solid #f0f0f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); height: 155px; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #64748b; font-size: 14px; font-weight: 500;">Low Stock</span>

                    </div>
                </div>
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                        <h3 style="font-size: 32px; font-weight: 700; margin: 0; color: #1e293b;">{{ $lowStockProducts }}
                        </h3>
                        <div
                            style="background: #f0fdf4; color: #16a34a; padding: 4px 8px; border-radius: 10px; font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                            ↗ 1.52%
                        </div>
                    </div>
                    <small style="color: #94a3b8; font-size: 11px;">From last week</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="dash-stat-card"
                style="background: white; padding: 20px; border-radius: 20px; border: 1px solid #f0f0f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); height: 155px; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #64748b; font-size: 14px; font-weight: 500;">Out of Stock</span>

                    </div>
                </div>
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                        <h3 style="font-size: 32px; font-weight: 700; margin: 0; color: #1e293b;">{{ $outOfStock }}</h3>
                        <div
                            style="background: #fef2f2; color: #dc2626; padding: 4px 8px; border-radius: 10px; font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                            ↘ 1.55%
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
                        <div class="card-header" style="background:#fff;">
                            <h5 class="card-title fs-4 fw-bold m-0">Inventory List</h5>
                            <!-- Button trigger modal -->


                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#exampleModal"
                                style="float: right; border-radius: 20px; background-color: #4CAF50; border: none; padding: 10px 20px; font-size: 16px; cursor: pointer; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); transition: background-color 0.3s ease;">
                                Inventory
                            </button>
                            <form action="{{ route('import_pos_sales') }}" method="POST" enctype="multipart/form-data"
                                class="d-flex align-items-right" style="float: right; margin-right: 10px;">
                                @csrf
                                <input type="file" name="inventory_file" class="form-control me-2" required>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-file-earmark-excel"></i> Import
                                </button>
                            </form>
                            <!-- Modal -->
                            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="addScheduleModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content" style="border-radius: 20px;">
                                        <div class="modal-header bg-primary">
                                            <h5 class="modal-title" id="exampleModal">Add Inventory</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            @include('layout.partials.alerts')

                                            <form method="POST" action="{{ route('save_inventory') }}"
                                                enctype="multipart/form-data">
                                                @csrf

                                                <h4 class="mb-4 text-center">Inventory Information</h4>

                                                <div class="form-group">
                                                    <label>Category</label>
                                                    <select id="category_ID" name="category_ID" class="form-control"
                                                        required>
                                                        <option value="">-- Select Category --</option>
                                                        @foreach ($categories as $cat)
                                                            <option value="{{ $cat->category_ID }}">
                                                                {{ $cat->category_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label for="product_ID">Product</label>
                                                    <select id="product_ID" name="product_ID" class="form-control" required>
                                                        <option value="">-- Select Product --</option>
                                                    </select>
                                                </div>




                                                <div class="form-group">
                                                    <label>Quantity</label>
                                                    <input type="number" name="quantity" class="form-control"
                                                        value="{{ old('quantity') }}" required>
                                                </div>



                                                <div class="text-center mt-4">
                                                    <button type="submit" class="btn btn-primary w-100">Register</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- /.card-header -->
                        <div class="card-body" style="text-align: center;">
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
                                <tbody>

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
                // "dom": '<"d-flex align-items-end justify-content-between mb-4"Bf>rtip',
                // "buttons": ["excel", "pdf", "print"]


            });

            $('#example2 tbody').on('click', '.edit-btn', function() {

                var id = $(this).data('id');
                var prodID = $(this).data('product-id');
                var prodName = $(this).data('product-name');
                var catID = $(this).data('category-id');
                var qnty = $(this).data('quantity');
                var rStock = $(this).data('remainingstock');

                $('#edit_id').val(id);
                $('#edit_product_name').val(prodID);
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

        // 2. Handle Category Change
        $(document).on('change', '#category_ID', function() {
            var categoryId = $(this).val();
            var productSelect = $('#product_ID');

            productSelect.empty().append('<option value="">-- Loading Products... --</option>');

            if (categoryId) {
                $.ajax({
                    // Ensure this route exists in web.php
                    url: "/admin/get-products-by-category/" + categoryId, // USED FOR GET METHOD
                    type: 'GET',
                    dataType: 'json',

                    success: function(data) {
                        console.log("Products loaded:", data); // Debugging line
                        productSelect.empty().append(
                            '<option value="">-- Select Product --</option>');
                        $.each(data, function(key, value) {
                            // CHECK: If your DB uses 'product_id' lowercase, change this!
                            productSelect.append('<option value="' + value
                                .product_ID + '">' + value.product_name +
                                '</option>');
                        });
                        table.draw(); // Refresh the background table
                    },
                    error: function(xhr) {
                        // This will print the actual PHP error in your F12 Console
                        console.error("The Server says: " + xhr.responseText);
                        productSelect.empty().append(
                            '<option value="">Error loading products</option>');
                    }
                });
            } else {
                productSelect.empty().append('<option value="">-- Select Product --</option>');
                table.draw();
            }
        });
    </script>

    <style>
        .custom-badge {
            padding: 5px 12px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            border-radius: 5px;
        }

        .status-in-stock {
            background-color: #a5ffb6;
            color: #234e52;
            border: 1px solid #02c702;
            padding: 5px 12px;
            border-radius: 55px;
        }

        .status-low-stock {
            background-color: #ffedca;
            color: #a14022;
            border: 1px solid #ffbc3f;
            padding: 5px 12px;
            border-radius: 55px;
        }

        .status-out-of-stock {
            background-color: #ffdada;
            color: #a41919;
            border: 1px solid #ff1515;
            padding: 3px 10px;
            border-radius: 55px;
        }

        /* .fileImport {
                                                                                                                                                                                                                                                                                                                                                                            margin-top: 10px;
                                                                                                                                                                                                                                                                                                                                                                            display: flex;
                                                                                                                                                                                                                                                                                                                                                                            flex-direction: column;
                                                                                                                                                                                                                                                                                                                                                                            align-items: flex-end;
                                                                                                                                                                                                                                                                                                                                                                        } */

        /* input[type="file"]::file-selector-button {
                                                                                                                                                                                                                                                                                                                                                                                background-color: #4CAF50;
                                                                                                                                                                                                                                                                                                                                                                                color: white;
                                                                                                                                                                                                                                                                                                                                                                                border-radius: 20px;
                                                                                                                                                                                                                                                                                                                                                                                border: none;
                                                                                                                                                                                                                                                                                                                                                                                padding: 10px 20px;
                                                                                                                                                                                                                                                                                                                                                                                box-shadow: #1e293b;
                                                                                                                                                                                                                                                                                                                                                                                cursor: pointer;
                                                                                                                                                                                                                                                                                                                                                                                transition: background-color 0.3s ease;

                                                                                                                                                                                                                                                                                                                                                                            }

                                                                                                                                                                                                                                                                                                                                                                            input[type="file"]::file-selector-button:hover {
                                                                                                                                                                                                                                                                                                                                                                                background-color: #45a049;
                                                                                                                                                                                                                                                                                                                                                                            } */
    </style>

@endsection
