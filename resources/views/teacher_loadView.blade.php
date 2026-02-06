@extends('themes.main')

{{-- 1. DEFINE PAGE TITLE --}}
@section('title', 'User Profile')

{{-- 2. DEFINE CONTENT HEADER (Breadcrumbs) --}}
@section('content_header')
    <div class="header">
        <h1 class="title" style="margin-left: 50px; ">Teachers Load</h1>
        <p style="margin-left: 50px;">Check the Loads of every teachers</p>
    </div>

    <section class="content" style = "width: 1400 px; margin-left: 20px; margin-top: 20px;">
        <div class="container-fluid;">
            <div class="row">
                <div class="col-12">
                    <div class="card" style=" border-radius: 30px; background:#fff; padding:20px">
                        <div class="card-header" style="background:#fff;">
                            <h3 class="card-title">Faculty list</h3>
                            <div class="d-flex justify-content-end mb-3">
                                <form method="GET" action="{{ route('teacher_loads') }}" id="filterForm">
                                    <div class="input-group shadow-sm rounded-pill overflow-hidden"
                                        style="min-width: 250px; border: 1px solid #e0e0e0;">
                                        <span class="input-group-text bg-white border-0 ps-3">
                                            <i class="fas fa-calendar-alt text-primary"></i>
                                        </span>
                                        <select name="schoolyear_id" class="form-select border-0 ps-1 py-2 shadow-none"
                                            style="cursor: pointer; font-weight: 500; font-size: 0.85rem; color: #495057;"
                                            onchange="this.form.submit()">
                                            <option value="">Choose School Year</option>
                                            @foreach ($school_years_map as $id => $name)
                                                <option value="{{ $id }}"
                                                    {{ $selected_year_id == $id ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            @if (!$selected_year_id)
                                {{-- Message shown when no year is selected --}}
                                <div class="alert alert-warning text-center" role="alert" style="border-radius: 15px;">
                                    Please select a **School Year** from the dropdown menu above to view teacher loads.
                                </div>
                            @else
                                {{-- Table only appears after the admin selects a year --}}
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Gender</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($teachers as $f)
                                            <tr>
                                                <td>{{ $f->name }}</td>
                                                <td>{{ $f->email }}</td>
                                                <td>{{ $f->phone }}</td>
                                                <td>{{ $f->gender }}</td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                        data-bs-target="#viewTeacherModal{{ $f->teachers_id }}">
                                                        VIEW LOAD
                                                    </button>

                                                    <div class="modal fade" id="viewTeacherModal{{ $f->teachers_id }}"
                                                        tabindex="-1">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content text-start"> {{-- Added text-start to fix alignment --}}
                                                                <div class="modal-header bg-primary">
                                                                    <h5 class="modal-title text-white">
                                                                        {{ $f->name ?? $s->section_name }} – Teaching Loads
                                                                    </h5>

                                                                    @if ($selected_year_id)
                                                                        <a href="{{ route('teacher.print_pdf', ['id' => $f->teachers_id, 'year' => $selected_year_id]) }}"
                                                                            class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm ms-auto me-3">
                                                                            <i class="fas fa-file-pdf me-1"></i> Print PDF
                                                                        </a>
                                                                    @endif

                                                                    <button type="button" class="btn-close btn-close-white"
                                                                        data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <table class="table table-bordered table-hover">
                                                                        <thead class="bg-light">
                                                                            <tr>
                                                                                <th>Subject</th>
                                                                                <th>Section</th>
                                                                                <th>Time</th>
                                                                                <th>Schedule</th>
                                                                                <th>Grade</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            {{-- Check if we actually have schedule data for this specific teacher --}}
                                                                            @php $teacherLoad = $teacher_loads[$f->teachers_id] ?? collect(); @endphp

                                                                            @forelse ($teacherLoad as $load)
                                                                                <tr>
                                                                                    <td>{{ $load->sub_name }}</td>
                                                                                    <td>{{ $load->sec_name }}</td>
                                                                                    <td>
                                                                                        {{ date('g:i A', strtotime($load->sub_Stime)) }}
                                                                                        -
                                                                                        {{ date('g:i A', strtotime($load->sub_Etime)) }}
                                                                                    </td>
                                                                                    <td>{{ $load->sub_date }}</td>
                                                                                    <td>{{ $load->grade_name }}</td>
                                                                                </tr>
                                                                            @empty
                                                                                <tr>
                                                                                    <td colspan="5"
                                                                                        class="text-center text-muted">
                                                                                        No assigned loads found for this
                                                                                        teacher
                                                                                        in
                                                                                        {{ $school_years_map[$selected_year_id] ?? 'this year' }}.
                                                                                    </td>
                                                                                </tr>
                                                                            @endforelse
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif

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
        /* Professional Dropdown Styling */
        .form-select:focus {
            background-color: #f8f9ff;
            color: #4e73df;
        }

        .input-group:hover {
            border-color: #4e73df !important;
            transition: all 0.3s ease;
        }

        /* Modern scrollbar for the dropdown list */
        select option {
            padding: 10px;
            background: #fff;
        }
    </style>
@endsection



@section('scripts')
    <script>
        $(document).ready(function() {
            $('#schoolYearSelect').change(function() {
                var selectedYear = $(this).val();

                // Get the base URL for the view (e.g., /teacher_loadView)
                var baseUrl = '{{ route('teacher_loads') }}';

                // Construct the new URL with the query parameter
                var newUrl = baseUrl;
                if (selectedYear) {
                    newUrl += '?schoolyear_id=' + selectedYear;
                }

                // Redirect the browser, which causes the full page reload with the filter
                window.location.href = newUrl;
            });
        });
    </script>
@endsection
