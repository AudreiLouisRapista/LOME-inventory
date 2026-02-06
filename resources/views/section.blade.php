@extends('themes.main')

{{-- 1. DEFINE PAGE TITLE --}}
@section('title', 'User Profile')

{{-- 2. DEFINE CONTENT HEADER (Breadcrumbs) --}}
@section('content_header')
    <div class="header">
        <h1 class="title" style="margin-left: 50px; ">Section Management</h1>
        <p style="margin-left: 50px;">Manage Section and their class assignments</p>
    </div>

    <section class="content" style = "width: 1400 px; margin-left: 20px; margin-top: 20px;">
        <div class="container-fluid;">
            <div class="row">
                <div class="col-12">
                    <div class="card" style=" border-radius: 30px; background:#fff; padding:20px">
                        <div class="card-header" style="background:#fff;">
                            <h3 class="card-title">Section list</h3>
                            <!-- Button trigger modal -->
                            <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                data-bs-target="#exampleModal" style="float: right;">
                                Add Section
                            </button>

                            <!-- Modal -->
                            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Add Section</h1>

                                        </div>
                                        <div class="modal-body">
                                            @include('layout.partials.alerts')

                                            <h4 class="mb-4 text-center">Section information</h4>

                                            <form method="POST" action="{{ route('save_section') }}"
                                                enctype="multipart/form-data">
                                                @csrf



                                                <!-- section name -->
                                                <div class="mb-3">
                                                    <label for="section_name" class="form-label">Section Name:</label>
                                                    <input type="text" name="section_name" id="section_name"
                                                        class="form-control" placeholder="Enter Section name"
                                                        value="{{ old('section_name') }}" required>
                                                    @error('section_name')
                                                        <span class="text-danger small">{{ $message }}</span>
                                                    @enderror
                                                </div>



                                                <!-- Section capacity -->
                                                <div class="mb-3">
                                                    <label for="section_capacity" class="form-label">Enter the capacity of
                                                        the Room:</label>
                                                    <input type="number" name="section_capacity" id="section_capacity"
                                                        class="form-control" placeholder="Enter the capacity"
                                                        value="{{ old('section_capacity') }}" required>
                                                    @error('section_capacity')
                                                        <span class="text-danger small">{{ $message }}</span>
                                                    @enderror
                                                </div>



                                                <!-- Grade Level -->
                                                <div class="mb-3">
                                                    <label for="grade_id" class="form-label">Grade Level</label>
                                                    <select type="text" class="form-control" id="grade_id"
                                                        name="grade_id" placeholder="Grade Level" required>
                                                        <option value="">-- Select Grade Level --</option>
                                                        <option value="7">Grade 7</option>
                                                        <option value="8">Grade 8</option>
                                                        <option value="9">Grade 9</option>
                                                        <option value="10">Grade 10</option>
                                                        <option value="11">Grade 11</option>
                                                        <option value="12">Grade 12</option>
                                                    </select>
                                                    @error('grade_id')
                                                        <span class="text-danger small">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label for="section_strand" class="form-label">Strand</label>
                                                    <select type="text" class="form-control" id="section_strand"
                                                        name="section_strand" placeholder="Section Strand" required>
                                                        <option value="">-- Select Strand --</option>
                                                        <option value="None">None</option>
                                                        <option value="AMB">AMB</option>
                                                        <option value="GAS">GAS</option>
                                                        <option value="STEM">STEM</option>
                                                        <option value="HUMSS">HUMSS</option>
                                                        <option value="TVL">TVL</option>
                                                    </select>
                                                    @error('section_strand')
                                                        <span class="text-danger small">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <!-- Submit -->
                                                <div class="text-center mt-4">
                                                    <button type="submit" class="btn btn-primary w-100">Register</button>
                                                </div>
                                            </form>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="example2" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Section Name</th>
                                        <th>Section Capacity</th>
                                        <th>Grade Level</th>
                                        <th>Section Strand</th>
                                        <th>Action</th>


                                        <!-- <th>ACtion</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($view_section as $sec)
                                        <tr>
                                            <td>{{ $sec->section_name }}</td>
                                            <td>{{ $sec->section_capacity }}</td>
                                            <td>{{ $sec->grade_title }}</td>
                                            <td>{{ $sec->section_strand }}</td>

                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">

                                                    <button class="btn btn-sm"
                                                        style="background-color: #f0fdf4; color: #06bd4c; border: 1px solid #dcfce7; border-radius: 8px; padding: 6px 10px; transition: 0.3s;"
                                                        onmouseover="this.style.backgroundColor='#dcfce7'"
                                                        onmouseout="this.style.backgroundColor='#f0fdf4'"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#UpdateSectionModal{{ $sec->section_id }}">
                                                        <i class="bi bi-pen" style="font-size: 14px;"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>


                                        <div class="modal fade" id="UpdateSectionModal{{ $sec->section_id }}"
                                            tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary">
                                                        <h5 class="modal-title">Update Section</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        @include('layout.partials.alerts')

                                                        <form method="POST"
                                                            action="{{ route('update_section', $sec->section_id) }}">
                                                            @csrf
                                                            <input type="hidden" name="section_id"
                                                                value="{{ $sec->section_id }}">
                                                            <div class="mb-3">
                                                                <label class="form-label">Name</label>
                                                                <input type="text" name="section_name"
                                                                    value="{{ $sec->section_name }}" class="form-control"
                                                                    required>
                                                            </div>


                                                            <div class="mb-3">
                                                                <label class="form-label">Sction Capacity</label>
                                                                <input type="number" name="section_capacity"
                                                                    value="{{ $sec->section_capacity }}"
                                                                    class="form-control" required>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="grade_id" class="form-label">Grade
                                                                    Level</label>
                                                                <select class="form-control" id="grade_id"
                                                                    name="grade_id" required>
                                                                    <option value="7"
                                                                        {{ $sec->grade_id == 7 ? 'selected' : '' }}>Grade 7
                                                                    </option>
                                                                    <option value="8"
                                                                        {{ $sec->grade_id == 8 ? 'selected' : '' }}>Grade 8
                                                                    </option>
                                                                    <option value="9"
                                                                        {{ $sec->grade_id == 9 ? 'selected' : '' }}>Grade 9
                                                                    </option>
                                                                    <option value="10"
                                                                        {{ $sec->grade_id == 10 ? 'selected' : '' }}>Grade
                                                                        10
                                                                    </option>
                                                                    <option value="11"
                                                                        {{ $sec->grade_id == 11 ? 'selected' : '' }}>Grade
                                                                        11
                                                                    </option>
                                                                    <option value="12"
                                                                        {{ $sec->grade_id == 12 ? 'selected' : '' }}>Grade
                                                                        12
                                                                    </option>
                                                                </select>
                                                                @error('grade_id')
                                                                    <span class="text-danger small">{{ $message }}</span>
                                                                @enderror
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="section_strand" class="form-label">Strand
                                                                </label>
                                                                <select type="text" class="form-control"
                                                                    id="section_strand" name="section_strand"
                                                                    placeholder="Section Strand" required>
                                                                    <option value="{{ $sec->section_strand }}">
                                                                        {{ $sec->section_strand }}
                                                                    </option>
                                                                    <option value="None">None</option>
                                                                    <option value="AMB">AMB</option>
                                                                    <option value="GAS">GAS</option>
                                                                    <option value="STEM">STEM</option>
                                                                    <option value="HUMSS">HUMSS</option>
                                                                    <option value="TVL">TVL</option>
                                                                </select>
                                                                @error('section_strand')
                                                                    <span class="text-danger small">{{ $message }}</span>
                                                                @enderror
                                                            </div>



                                                            <button type="submit"
                                                                class="btn btn-primary w-100">Update</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                </tbody>
                            </table>


                            {{-- <div class="row">
                                <div class="col-sm-12 col-md-5">
                                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">
                                        Showing 11 to 20 of 57 entries
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-7">
                                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                        <ul class="pagination">
                                            <li class="paginate_button page-item previous" id="example2_previous">
                                                <a href="#" aria-controls="example2" data-dt-idx="0"
                                                    tabindex="0" class="page-link">Previous</a>
                                            </li>
                                            <li class="paginate_button page-item ">
                                                <a href="#" aria-controls="example2" data-dt-idx="1"
                                                    tabindex="0" class="page-link">1</a>
                                            </li>
                                            <li class="paginate_button page-item active">
                                                <a href="#" aria-controls="example2" data-dt-idx="2"
                                                    tabindex="0" class="page-link">2</a>
                                            </li>
                                            <li class="paginate_button page-item ">
                                                <a href="#" aria-controls="example2" data-dt-idx="3"
                                                    tabindex="0" class="page-link">3</a>
                                            </li>
                                            <li class="paginate_button page-item ">
                                                <a href="#" aria-controls="example2" data-dt-idx="4"
                                                    tabindex="0" class="page-link">4</a>
                                            </li>
                                            <li class="paginate_button page-item ">
                                                <a href="#" aria-controls="example2" data-dt-idx="5"
                                                    tabindex="0" class="page-link">5</a>
                                            </li>
                                            <li class="paginate_button page-item ">
                                                <a href="#" aria-controls="example2" data-dt-idx="6"
                                                    tabindex="0" class="page-link">6</a>
                                            </li>
                                            <li class="paginate_button page-item next" id="example2_next">
                                                <a href="#" aria-controls="example2" data-dt-idx="7"
                                                    tabindex="0" class="page-link">Next</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div> --}}
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
                    "searchPlaceholder": "Search schedule..."
                }
            });

            // 1. Move buttons to the container first
            table.buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');


        });
    </script>
@endsection
