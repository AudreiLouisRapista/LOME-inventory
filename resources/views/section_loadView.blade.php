@extends('themes.main')

{{-- 1. DEFINE PAGE TITLE --}}
@section('title', 'User Profile')

{{-- 2. DEFINE CONTENT HEADER (Breadcrumbs) --}}
@section('content_header')
    <div class="header">
        <h1 class="title" style="margin-left: 50px; ">Section Load</h1>
        <p style="margin-left: 50px;">Check the Loads of every teachers</p>
    </div>

    <section class="content" style = "width: 1400 px; margin-left: 20px; margin-top: 20px;">
        <div class="container-fluid;">
            <div class="row">
                <div class="col-12">
                    <div class="card" style=" border-radius: 30px; background:#fff; padding:20px">
                        <div class="card-header" style="background:#fff;">
                            <h3 class="card-title">Section list</h3>
                            <div class="d-flex justify-content-end mb-3">
                                <form method="GET" action="{{ route('section_loads') }}" id="filterForm">
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
                                <div class="alert alert-warning text-center" role="alert">
                                    Please select a **School Year** from the dropdown menu above to view the section's
                                    assigned loads.
                                </div>
                            @else
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Section Name</th>
                                            <th>Section Capacity</th>
                                            {{-- <th>Grade Level</th> --}}
                                            <th>Section Strand</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($sections as $s)
                                            <tr>
                                                <td>{{ $s->section_name }}</td>
                                                <td>{{ $s->section_capacity }}</td>
                                                {{-- <td>{{ $s->grade_name }}</td> --}}
                                                <td>{{ $s->section_strand }}</td>

                                                <td class="text-center">
                                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                        data-bs-target="#viewSectionModal{{ $s->section_id }}">
                                                        VIEW LOAD
                                                    </button>

                                                    <div class="modal fade" id="viewSectionModal{{ $s->section_id }}"
                                                        tabindex="-1">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header bg-primary">
                                                                    <h5 class="modal-title">{{ $s->section_name }} – Loads
                                                                    </h5>

                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <table class="table table-bordered table-hover">
                                                                        <thead>
                                                                        </thead>
                                                                        <tbody>

                                                                            <tr>
                                                                                <th>Subject Name</th>
                                                                                <th>Class Time</th>
                                                                                {{-- <th>Grade Level</th> --}}
                                                                                <th>Date</th>
                                                                                <th>Grade Level</th>

                                                                            </tr>

                                                                            @forelse ($section_loads[$s->section_id] ?? [] as $load)
                                                                                <tr>
                                                                                    <td>{{ $load->sub_name }}</td>
                                                                                    <td>
                                                                                        {{ date('g:i A', strtotime($load->sub_Stime)) }}
                                                                                        -
                                                                                        {{ date('g:i A', strtotime($load->sub_Etime)) }}
                                                                                    </td>
                                                                                    <td>{{ $load->sub_date }}</td>
                                                                                    <td>{{ $load->grade_name }}</td>
                                                                                    {{-- <td>{{ $load->schooler_id }}</td> --}}
                                                                                </tr>
                                                                            @empty
                                                                                <tr>
                                                                                    <td colspan="5"
                                                                                        class="text-center text-muted">
                                                                                        No schedule assigned for the
                                                                                        selected school year.
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

@endsection



@section('scripts')

    <script>
        $(document).ready(function() {
            $('#schoolYearSelect1').change(function() {
                var selectedYear = $(this).val();

                // Get the base URL for the view (e.g., /teacher_loadView)
                var baseUrl = '{{ route('section_loads') }}';

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
