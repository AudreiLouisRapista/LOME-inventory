@extends('themes.main')

{{-- 1. DEFINE PAGE TITLE --}}
@section('title', 'User Profile')

{{-- 2. DEFINE CONTENT HEADER (Breadcrumbs) --}}
@section('content_header')
    <div class="header">
        <h1 class="title" style="margin-left: 50px; ">Subject Management</h1>
        <p style="margin-left: 50px;">Manage Subjects and their class assignments</p>
    </div>

    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <!-- MAIN CARD -->
                    <div class="card" style="border-radius: 30px; background:#fff; padding:20px">

                        <div class="card-header" style="background:#fff;">
                            <h3 class="card-title">Subject List</h3>

                            <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                data-bs-target="#exampleModal" style="float: right;">
                                Add Subject
                            </button>
                        </div>

                        <!-- MODAL -->
                        <div class="modal fade" id="exampleModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5">Add Subject</h1>
                                    </div>

                                    <div class="modal-body">
                                        <h4 class="mb-4 text-center">Subject Information</h4>

                                        <form method="POST" action="{{ route('save_subjects') }}"
                                            enctype="multipart/form-data">
                                            @csrf

                                            <div class="mb-3">
                                                <label class="form-label">Academic Level</label>
                                                <select class="form-control" name="sub_yearlevel" id="levelDropdown"
                                                    required>
                                                    <option value="">-- JHS or SHS --</option>
                                                    <option value="JHS">Junior High School</option>
                                                    <option value="SHS">Senior High School</option>
                                                </select>
                                                @error('sub_yearlevel')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <input type="hidden" name="sub_strand" id="hiddenStrand">

                                            <div class="mb-3" id="strandContainer" style="display: none;">
                                                <label class="form-label">STRAND</label>
                                                <select class="form-control" name="sub_strand" id="strandDropdown">
                                                    <option value=""> CHOOSE STRAND </option>
                                                    <option value="ABM">ABM</option>
                                                    <option value="GAS">GAS</option>
                                                    <option value="STEM">STEM</option>
                                                    <option value="HUMSS">HUMSS</option>
                                                </select>
                                                @error('sub_strand')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>



                                            <div class="mb-3">
                                                <label class="form-label">Subject name</label>
                                                <select class="form-control" name="sub_name" id="subjectDropdown" required>
                                                    <option value="">-- SELECT NAME --</option>

                                                </select>
                                                @error('sub_name')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <script>
                                                const levelDropdown = document.getElementById('levelDropdown');
                                                const strandContainer = document.getElementById('strandContainer');
                                                const strandDropdown = document.getElementById('strandDropdown');
                                                const subjectDropdown = document.getElementById('subjectDropdown');

                                                // SUBJECT LISTS
                                                const jhsSubjects = [
                                                    'Mathematics', 'Science', 'English', 'Filipino',
                                                    'Araling Panlipunan', 'Edukasyon sa Pagpapakatao',
                                                    'Technology and Livelihood Education',
                                                    'MAPEH'
                                                ];

                                                const shsSubjects = {
                                                    ABM: [
                                                        'Business Mathematics', 'Accounting', 'Marketing', 'Entrepreneurship'
                                                    ],
                                                    HUMSS: [
                                                        'Creative Writing', 'Oral Communication', '21st Century Literature'
                                                    ],
                                                    STEM: [
                                                        'General Mathematics', 'Calculus', 'Physics', 'Biology', 'Chemistry'
                                                    ],
                                                    GAS: [
                                                        'Applied Economics', 'Contemporary Arts', 'Practical Research'
                                                    ]
                                                };

                                                // WHEN LEVEL CHANGES
                                                levelDropdown.addEventListener('change', function() {
                                                    const level = this.value;

                                                    // Reset subject
                                                    subjectDropdown.innerHTML = '<option value="">-- SELECT SUBJECT --</option>';

                                                    if (level === "JHS") {
                                                        strandContainer.style.display = "none"; // Hide strand
                                                        document.getElementById('hiddenStrand').value = "N/A"; // Set hidden strand input

                                                        // Load JHS subjects
                                                        jhsSubjects.forEach(sub => {
                                                            subjectDropdown.innerHTML += `<option value="${sub}">${sub}</option>`;
                                                        });
                                                    }

                                                    if (level === "SHS") {
                                                        strandContainer.style.display = "block"; // Show strand
                                                        document.getElementById('hiddenStrand').value = ""; // Clear hidden strand input

                                                        subjectDropdown.innerHTML = '<option value="">-- SELECT SUBJECT --</option>';
                                                    }
                                                });

                                                // WHEN STRAND CHANGES
                                                strandDropdown.addEventListener('change', function() {
                                                    const strand = this.value;

                                                    subjectDropdown.innerHTML = '<option value="">-- SELECT SUBJECT --</option>';

                                                    if (shsSubjects[strand]) {
                                                        shsSubjects[strand].forEach(sub => {
                                                            subjectDropdown.innerHTML += `<option value="${sub}">${sub}</option>`;
                                                        });
                                                    }
                                                });
                                            </script>






                                            <div class="mb-3">
                                                <label class="form-label">Grade Level</label>
                                                <select class="form-control" name="grade_id" required>
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

                                            <input type="hidden" name="t_status" value="unassigned">

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

                        <!-- DATATABLE -->
                        <div class="card-body">
                            <table id="example2" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Academic Level</th>
                                        <th>Strand</th>
                                        <th>Subject Name</th>
                                        <th>Grade Level</th>


                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($view_subject as $sub)
                                        <tr>
                                            <td>{{ $sub->sub_yearlevel }}</td>
                                            <td>{{ $sub->sub_strand }}</td>
                                            <td>{{ $sub->subject_name }}</td>
                                            <td>{{ $sub->grade_title }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="row">
                                <div class="col-sm-12 col-md-5">
                                    <div class="dataTables_info">

                                    </div>
                                </div>
                            </div>

                        </div>

                    </div><!-- end main card -->

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
                    "searchPlaceholder": "Search subject..."
                }
            });

            // 1. Move buttons to the container first
            table.buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');


        });
    </script>
@endsection
