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


                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addTeacherModal"
                                style="float: right; border-radius: 20px; background-color: #4CAF50; border: none; padding: 10px 20px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
                                Register Product
                            </button>

                            <div class="modal fade" id="addTeacherModal" tabindex="-1"
                                aria-labelledby="addTeacherModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content" style="border-radius: 20px;">
                                        <div class="modal-header bg-primary">
                                            <h5 class="modal-title" id="addTeacherModalLabel">Register Products</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            @include('layout.partials.alerts')
                                            <form method="POST" action="{{ route('save_product') }}"
                                                enctype="multipart/form-data">
                                                @csrf

                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label for="category">Category</label>
                                                        <select name="category_ID" id="category" class="form-control"
                                                            required>
                                                            <option value="">-- Select Category --</option>

                                                            @foreach ($categories as $category)
                                                                <option value="{{ $category->category_ID }}">
                                                                    {{ strtoupper($category->category_name) }}
                                                                </option>
                                                            @endforeach

                                                        </select>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Product Name:</label>
                                                        <input type="text" name="product_name" class="form-control"
                                                            value="{{ old('product_name') }}" required>
                                                    </div>

                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Date of Expiry:</label>
                                                        <input type="date" name="product_exp" class="form-control"
                                                            value="{{ old('product_exp') }}" required>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Price:</label>
                                                        <input type="number" name="product_price" class="form-control"
                                                            value="{{ old('product_price') }}" required>
                                                    </div>

                                                </div>


                                                <div class="col-md-6">
                                                    <label class="form-label">Cost:</label>
                                                    <input type="number" name="product_cost" class="form-control"
                                                        value="{{ old('product_cost') }}" required>
                                                </div>


                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Unit Amount:</label>
                                                        <input type="number" name="product_unit_amount"
                                                            class="form-control" value="{{ old('product_unit_amount') }}"
                                                            required>
                                                    </div>
                                                    {{-- <div class="col-md-6">
                                                        <label for="unit">Unit</label>
                                                        <select name="unit_ID" id="unit" class="form-control" required>
                                                            <option value="">-- Select Unit --</option>

                                                            @foreach ($units as $unit)
                                                                <option value="{{ $unit->unit_ID }}">
                                                                    {{ strtoupper($unit->unit_title) }}
                                                                </option>
                                                            @endforeach

                                                        </select>
                                                    </div> --}}
                                                </div>



                                                <button type="submit" class="btn btn-primary w-100 mt-3">Register</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="card-body">
                            <table id="example2" class="table table-bordered table-hover display block" style="width:100%">
                                <thead style="text-align: center;">
                                    <tr>
                                        <th>Product ID</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Cost</th>
                                        <th>Price</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody style="text-align: center;">

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


    <style>
        /* 1. Fix the vertical distance (The "Too High" gap) */
        .dataTables_wrapper .row:first-child {
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
        }

        /* 2. Style the search box to be sleek and professional */
        .dataTables_filter {
            margin: 0 !important;
            /* Removes default spacing pushing it down */
        }

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

        /* 3. Match the Button heights to the search box */
        .dt-button {
            height: 38px !important;
            display: flex !important;
            align-items: center !important;
            font-weight: 500 !important;
            border-radius: 12px !important;
        }
    </style>
@endsection

@section('tables')
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>

    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>

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
        });
    </script>



@endsection
