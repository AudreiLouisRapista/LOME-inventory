@extends('themes.main')

{{-- 1. DEFINE PAGE TITLE --}}
@section('title', 'User Profile')

{{-- 2. DEFINE CONTENT HEADER (Breadcrumbs) --}}
@section('content_header')
    <div class="header">
        <h1 class="title" style="margin-left: 50px; ">Products Management</h1>
        <p style="margin-left: 50px;">Manage products and their details</p>
    </div>

    <section class="content" style="max-width: 100%; display: block; margin-left: 5px; margin-top: 20px;">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card" style="background:#fff; display: block; padding:20px">
                        <div class="card-header" style="background:#fff;">
                            <h5 class="card-title fs-4 fw-bold m-0">Product List</h5>


                            <button type="button" class="btn-product" data-bs-toggle="modal"
                                data-bs-target="#addProductModal">
                                <i class="bi bi-plus-lg"></i>
                                Add Item
                            </button>

                            <div class="modal fade" id="addProductModal" tabindex="-1"
                                aria-labelledby="addTeacherModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content border-0 shadow-lg"
                                        style="border-radius: 15px; overflow: hidden;">

                                        <div class="modal-header bg-dark text-white py-3">
                                            <h5 class="modal-title fw-bold" id="addTeacherModalLabel">
                                                <i class="fas fa-box-open me-2"></i> Register New Product
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body p-4">
                                            @include('layout.partials.alerts')

                                            <form method="POST" action="{{ route('save_product') }}"
                                                enctype="multipart/form-data">
                                                @csrf
                                                <div class="mb-4">
                                                    <p class="text-muted small fw-bold text-uppercase mb-3 border-bottom">
                                                        Basic Information
                                                    </p>
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
                                                                    <i class="bi bi-box-seam text-muted"></i>
                                                                </span>
                                                                <input type="text" name="product_name" list="productData"
                                                                    id="product_input"
                                                                    class="form-control bg-light border-start-0 shadow-none"
                                                                    placeholder="Search or enter product..."
                                                                    style="border-radius: 0 10px 10px 0; height: 45px;"
                                                                    required>

                                                                <datalist id="productData">
                                                                    @foreach ($products as $product)
                                                                        <option value="{{ $product->product_name }}">
                                                                    @endforeach
                                                                </datalist>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <p class="text-muted small fw-bold text-uppercase mb-3 border-bottom pb-1">
                                                    Pricing</p>
                                                <div class="row g-3 mb-4">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Cost Price</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">$</span>
                                                            <input type="number" name="product_cost" class="form-control"
                                                                step="0.01" placeholder="0.00"
                                                                value="{{ old('product_cost') }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Selling Price</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">$</span>
                                                            <input type="number" name="product_price" class="form-control"
                                                                step="0.01" placeholder="0.00"
                                                                value="{{ old('product_price') }}" required>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="d-flex justify-content-end gap-2 mt-4">
                                                    <button type="button" class="btn btn-light px-4"
                                                        data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit"
                                                        class="btn btn-primary px-5 fw-bold shadow-sm">Save
                                                        Product</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example2" class="table table-bordered" style="width:100%">
                                    <thead style="text-align: center; background-color: #f8fafc;">
                                        <tr>
                                            <th class="text-secondary text-uppercase small fw-bold">Product ID</th>
                                            <th class="text-secondary text-uppercase small fw-bold">Name</th>
                                            <th class="text-secondary text-uppercase small fw-bold">Category</th>
                                            <th class="text-secondary text-uppercase small fw-bold">Cost</th>
                                            <th class="text-secondary text-uppercase small fw-bold">Price</th>
                                            <th class="text-secondary text-uppercase small fw-bold">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        style="font-family: 'Inter', sans-serif; text-align: center; vertical-align: middle;">
                                        {{-- DataTables will populate this --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="modal fade" id="UpdateProductModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title">Update Product</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form id="updateProductForm" action="{{ route('update_product') }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <input type="hidden" name="product_ID" id="edit_id">
                                            <div class="mb-3">
                                                <label class="form-label">Product Name</label>
                                                <input type="text" id="edit_name" name="product_name"
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
                                                <label class="form-label">Cost</label>
                                                <input type="text" id="edit_cost" name="cost" class="form-control"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Price</label>
                                                <input type="text" id="edit_price" name="price"
                                                    class="form-control" required>
                                            </div>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary w-100">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <link rel="stylesheet" href="{{ asset('css/pages/products_style.css') }}">
@endsection

@section('scripts src')

    <script>
        $(document).ready(function() {
            var table = $('#example2').DataTable({
                destroy: true, // use this to reinitialize the table if it already exists. use ths if you are using AJAX to load data and want to refresh the table with new data
                processing: true, // This shows a loading indicator while the data is being fetched, enhancing user experience.
                serverSide: true, // This enables the fast loading for 4k rows. instead of loading all data at once, it loads only the data needed for the current page.
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Processing...</span></div>'
                },
                ajax: "{{ route('view_products') }}", // Ensure this matches your route name

                columns: [{
                        data: 'product_ID',
                        name: 'products.product_ID'
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
                var name = $(this).data('name');
                var catID = $(this).data('category-id');
                var price = $(this).data('price');
                var cost = $(this).data('cost');

                $('#edit_id').val(id);
                $('#edit_name').val(name);
                $('#edit_category').val(catID);
                $('#edit_cost').val(cost);
                $('#edit_price').val(price);


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

            // ==========================================
            // 4. DYNAMIC DROPDOWNS (Filtered Products with Batch Qty)
            // ==========================================
            $('#category_ID_add').on('change', function(e) {

                var $productSelect = $('#product_ID_add');
                var categorySelect = $(this).val();

                // 2. Reset the product dropdown 
                $productSelect.empty().append('<option value="">Loading....</option>');

                if (categorySelect) {
                    $.ajax({
                        // 3. Added a slash before the ID so the URL is correct
                        url: "/admin/getProductsByCategory/" + categorySelect,
                        method: "GET",
                        dataType: "json",
                        success: function(data) {
                            // 4. Clear the "Loading" message
                            $productSelect.empty().append(
                                '<option value="">Select Product</option>');

                            // 5. Use 'value' (the variable from the function) instead of 'product'
                            $.each(data, function(key, value) {
                                $productSelect.append('<option value="' + value
                                    .product_ID + '">' +
                                    value.product_name + '</option>');
                            });
                        },
                        error: function() {
                            $productSelect.empty().append(
                                '<option value="">Error fetching products</option>');
                        }
                    });
                } else {
                    $productSelect.empty().append('<option value="">Select Category First</option>');
                }
            });


        });
    </script>


@endsection
