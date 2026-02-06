@extends('themes.main')

{{-- 1. DEFINE PAGE TITLE --}}
@section('title', 'User Profile')

{{-- 2. DEFINE CONTENT HEADER (Breadcrumbs) --}}
@section('content_header')
    <div class="header">
        <h1 class="title" style="margin-left: 50px; ">Products Management</h1>
        <p style="margin-left: 50px;">Manage products and their details</p>
    </div>

    <section class="content" style="max-width: 1400px; margin-left: 20px; margin-top: 20px;">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card" style="background:#fff; padding:20px">
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
                                                        <label class="form-label">Product Name:</label>
                                                        <input type="text" name="product_name" class="form-control"
                                                            value="{{ old('product_name') }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="category">Category</label>
                                                        <select name="category_id" id="category" class="form-control"
                                                            required>
                                                            <option value="">-- Select Category --</option>

                                                            @foreach ($categories as $category)
                                                                <option value="{{ $category->category_id }}">
                                                                    {{ strtoupper($category->name) }}
                                                                </option>
                                                            @endforeach

                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Units:</label>
                                                        <input type="number" name="product_unit" class="form-control"
                                                            value="{{ old('product_unit') }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Select Units:</label>
                                                        <select name="product_unit_type" class="form-select" required>
                                                            <option value="">-- Select --</option>
                                                            <option value="Liter">Liter</option>
                                                            <option value="Pcs">Pcs</option>
                                                            <option value="ml">ml</option>

                                                        </select>
                                                    </div>

                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Quantity:</label>
                                                    <input type="number" name="product_qts" class="form-control"
                                                        value="{{ old('product_qts') }}" required>
                                                </div>
                                                <button type="submit" class="btn btn-primary w-100 mt-3">Register</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="card-body">
                            <table id="example2" class="table table-bordered table-hover">
                                <thead style="text-align: center;">
                                    <tr>
                                        <th>Product ID</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Units</th>
                                        <th>Quantity</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody style="text-align: center;">
                                    @foreach ($view_products as $product)
                                        <tr>
                                            <td>
                                                {{ $product->product_id }}
                                            </td>
                                            <td>{{ $product->product_name }}</td>
                                            <td>{{ $product->name }}</td>
                                            <td> <span class="badge"
                                                    style="background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; font-weight: 500; border-radius: 6px; padding: 5px 10px; font-size: 14px;">
                                                    {{ $product->product_unit }} {{ $product->product_unit_type }}
                                                </span></td>
                                            <td>{{ $product->product_qts }}</td>
                                            <td> </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <button class="btn btn-sm"
                                                        style="background-color: #f0fdf4; color: #06bd4c; border: 1px solid #dcfce7; border-radius: 8px; padding: 6px 10px; transition: 0.3s;"
                                                        onmouseover="this.style.backgroundColor='#dcfce7'"
                                                        onmouseout="this.style.backgroundColor='#f0fdf4'"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#UpdateProductModal{{ $product->product_id }}">
                                                        <i class="bi bi-pen" style="font-size: 14px;"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>

                                        {{-- <div class="modal fade" id="deleteTeacherModal{{ $teacher->teachers_id }}"
                                            tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger">
                                                        <h5 class="modal-title">Unassigned Teacher</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <p>Are you sure you want to Unassigned <b>{{ $teacher->name }}</b>?
                                                            the status will be updated if the schedule of the Teacher
                                                            is less than 5
                                                        </p>
                                                        <form method="POST" action="{{ route('deact_teacher') }}">
                                                            @csrf
                                                            <input type="hidden" name="teachers_id"
                                                                value="{{ $teacher->teachers_id }}">
                                                            <button type="submit"
                                                                class="btn btn-danger">Unassigned</button>
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Cancel</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> --}}

                                        <div class="modal fade" id="UpdateProductModal{{ $product->product_id }}"
                                            tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary">
                                                        <h5 class="modal-title">Update Product</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form method="POST" action="">
                                                            {{-- {{ route('update_product', $product->product_id) }} --}}
                                                            @csrf
                                                            <div class="mb-3">
                                                                <label class="form-label">Name</label>
                                                                <input type="text" name="name"
                                                                    value="{{ $product->product_name }}"
                                                                    class="form-control" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Category</label>
                                                                <input type="text" name="category"
                                                                    value="{{ $product->name }}" class="form-control"
                                                                    required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Unit</label>
                                                                <input type="text" name="unit"
                                                                    value="{{ $product->product_unit }}"
                                                                    class="form-control" required>
                                                            </div>

                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Product Name</label>
                                                        <input type="text" name="product_name"
                                                            value="{{ $product->product_name }}" class="form-control"
                                                            required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Category</label>
                                                        <input type="text" name="category"
                                                            value="{{ $product->name }}" class="form-control" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Unit</label>
                                                        <input type="text" name="unit"
                                                            value="{{ $product->product_unit }}" class="form-control"
                                                            required>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary w-100">Update</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                        </div>
                        @endforeach
                        </tbody>
                        </table>
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
            if ($.fn.DataTable.isDataTable('#example2')) {
                $('#example2').DataTable().destroy();
            }

            var table = $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "buttons": ["excel", "pdf", "print"],
                // This 'dom' configuration groups Buttons (B) and Filter (f) in one row
                "dom": '<"d-flex align-items-end justify-content-between mb-4"Bf>rtip',
                "language": {
                    "search": "", // Removes the default "Search:" text
                    "searchPlaceholder": "Search Products..."
                }
            });

            // 1. Move buttons to the container first
            table.buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');


        });
    </script>
@endsection
