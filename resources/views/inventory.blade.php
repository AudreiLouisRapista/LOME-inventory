@extends('themes.main')

{{-- 1. DEFINE PAGE TITLE --}}
@section('title', 'User Profile')

{{-- 2. DEFINE CONTENT HEADER (Breadcrumbs) --}}
@section('content_header')
    <div class="header">
        <h1 class="title" style="margin-left: 50px; ">Inventory Management</h1>
        <p style="margin-left: 50px;">Manage Inventory for the Products</p>
    </div>

    <section class="content" style = "width: 1400 px; margin-left: 20px; margin-top: 20px;">
        <div class="container-fluid;">
            <div class="row">
                <div class="col-12">
                    <div class="card" style=" border-radius: 30px; background:#fff; padding:20px">
                        <div class="card-header" style="background:#fff;">
                            <h5 class="card-title fs-4 fw-bold m-0">Inventory List</h5>
                            <!-- Button trigger modal -->


                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#exampleModal"
                                style="float: right; border-radius: 20px; background-color: #4CAF50; border: none; padding: 10px 20px; font-size: 16px; cursor: pointer; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); transition: background-color 0.3s ease;">
                                Add Inventory
                            </button>

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
                            <table id="example2" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Inventory ID</th>
                                        <th>Product Category</th>
                                        <th>Product Name</th>
                                        <th>Quantity</th>
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
                        <!-- /.card-body -->
                    </div>

                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
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
            // 1. Initialize DataTable
            var table = $('#example2').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('view_inventory') }}",
                    type: 'GET',
                    data: function(d) {
                        // This passes filters to your table
                        d.category_ID = $('#category_ID').val();
                        d.product_ID = $('#product_ID').val();
                    }
                },
                columns: [{
                        data: 'inventory_ID',
                        name: 'inventory_ID'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    }, // This matches "Product Category" in your UI
                    {
                        data: 'product_name',
                        name: 'product_name'
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },
                    {
                        data: 'remaining_stock',
                        name: 'remaining_stock'
                    },
                    {
                        data: 'status_title',
                        name: 'status_title'

                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                "dom": '<"d-flex align-items-end justify-content-between mb-4"Bf>rtip',
                "buttons": ["excel", "pdf", "print"]
            });

            // 2. Handle Category Change
            $(document).on('change', '#category_ID', function() {
                var categoryId = $(this).val();
                var productSelect = $('#product_ID');

                productSelect.empty().append('<option value="">-- Loading Products... --</option>');

                if (categoryId) {
                    $.ajax({
                        // Ensure this route exists in web.php
                        url: "/admin/get-products-by-category/" + categoryId,
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
        });
    </script>

@endsection
