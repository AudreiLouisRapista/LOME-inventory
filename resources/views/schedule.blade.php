@extends('themes.main')

{{-- 1. DEFINE PAGE TITLE --}}
@section('title', 'User Profile')

{{-- 2. DEFINE CONTENT HEADER (Breadcrumbs) --}}
@section('content_header')
    <div class="header">
        <h1 class="title" style="margin-left: 50px; ">Schedule Management</h1>
        <p style="margin-left: 50px;">Manage Schedule for the Teachers</p>
    </div>

    <section class="content" style = "width: 1400 px; margin-left: 20px; margin-top: 20px;">
        <div class="container-fluid;">
            <div class="row">
                <div class="col-12">
                    <div class="card" style=" border-radius: 30px; background:#fff; padding:20px">
                        <div class="card-header" style="background:#fff;">
                            <h5 class="card-title fs-4 fw-bold m-0">Schedule List</h5>
                            <!-- Button trigger modal -->


                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#exampleModal"
                                style="float: right; border-radius: 20px; background-color: #4CAF50; border: none; padding: 10px 20px; font-size: 16px; cursor: pointer; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); transition: background-color 0.3s ease;">
                                Add Schedule
                            </button>

                            <!-- Modal -->
                            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="addScheduleModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content" style="border-radius: 20px;">
                                        <div class="modal-header bg-primary">
                                            <h5 class="modal-title" id="exampleModal">Add Schedule</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            @include('layout.partials.alerts')

                                            <form method="POST" action="{{ route('save_schedule') }}"
                                                enctype="multipart/form-data">
                                                @csrf

                                                <h4 class="mb-4 text-center">Schedule Information</h4>

                                                <!-- Assigned Teacher -->
                                                <div class="mb-3">
                                                    <label for="teachers_id" class="form-label">Assigned Teacher</label>
                                                    <select class="form-select" id="teachers_id" name="teachers_id">
                                                        <option value="">-- Select Teacher --</option>
                                                        <option value="0">Not Assigned</option>
                                                        @foreach ($teachers as $teacher)
                                                            <option value="{{ $teacher->teachers_id }}">
                                                                {{ $teacher->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('teachers_id')
                                                        <span class="text-danger small">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <!-- Assigned Subject -->
                                                <div class="mb-3">
                                                    <label for="subject_id" class="form-label">Assigned Subject</label>
                                                    <select class="form-select" id="subject_id" name="subject_id" required>
                                                        <option value="">-- Select Subject --</option>
                                                        @foreach ($subject as $sub)
                                                            <option value="{{ $sub->subject_id }}">
                                                                {{ $sub->subject_name }} - {{ $sub->grade_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('subject_id')
                                                        <span class="text-danger small">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <!-- Assigned Section -->
                                                <div class="mb-3">
                                                    <label for="section_id" class="form-label">Assigned Section</label>
                                                    <select class="form-select" id="section_id" name="section_id" required>
                                                        <option value="">-- Select Section --</option>
                                                        @foreach ($section as $sec)
                                                            <option value="{{ $sec->section_id }}">
                                                                {{ $sec->section_name }} - {{ $sec->grade_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('section_id')
                                                        <span class="text-danger small">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Select Days</label>
                                                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                                                        @php
                                                            $days = [
                                                                'Whole Week',
                                                                'Monday',
                                                                'Tuesday',
                                                                'Wednesday',
                                                                'Thursday',
                                                                'Friday',
                                                            ];
                                                        @endphp

                                                        @foreach ($days as $day)
                                                            <div class="form-check">
                                                                <input type="checkbox" name="days[]"
                                                                    value="{{ $day }}"
                                                                    class="form-check-input day-option"
                                                                    onclick="handleDaySelection(this)"
                                                                    {{ is_array(old('days')) && in_array($day, old('days')) ? 'checked' : '' }}>
                                                                <label class="form-check-label">{{ $day }}</label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <script>
                                                    function handleDaySelection(el) {
                                                        let checkboxes = document.querySelectorAll('.day-option');

                                                        if (el.value === "Whole Week" && el.checked) {
                                                            checkboxes.forEach(cb => {
                                                                if (cb.value !== "Whole Week") cb.checked = false;
                                                            });
                                                        } else {
                                                            // If any day except Whole Week is selected → uncheck Whole Week
                                                            document.querySelector('.day-option[value="Whole Week"]').checked = false;
                                                        }
                                                    }
                                                </script>


                                                <!-- Start and End Time -->
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label for="sub_Stime" class="form-label">Starting Time</label>
                                                        <input type="time" class="form-control" id="sub_Stime"
                                                            name="sub_Stime" required>
                                                        @error('sub_Stime')
                                                            <span class="text-danger small">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="sub_Etime" class="form-label">End Time</label>
                                                        <input type="time" class="form-control" id="sub_Etime"
                                                            name="sub_Etime" required>
                                                        @error('sub_Etime')
                                                            <span class="text-danger small">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!-- School Year -->
                                                <div class="mb-3">
                                                    <label for="schoolyear_id" class="form-label">School Year:</label>
                                                    <select name="schoolyear_id" id="schoolyear_id" class="form-select"
                                                        required>
                                                        <option value="">-- Select School Year --</option>

                                                        @php
                                                            $startYear = date('Y');
                                                            $endYear = $startYear + 2;
                                                        @endphp

                                                        @for ($year = $startYear; $year <= $endYear; $year++)
                                                            @php
                                                                $schoolyear_name = $year . '-' . ($year + 1);

                                                                $existingYear = DB::table('school_year')
                                                                    ->where('schoolyear_name', $schoolyear_name)
                                                                    ->first();
                                                            @endphp

                                                            <option value="{{ $schoolyear_name }}"
                                                                {{ old('schoolyear_name', $sched->schoolyear_name ?? '') == $schoolyear_name ? 'selected' : '' }}>
                                                                {{ $schoolyear_name }}
                                                            </option>
                                                        @endfor
                                                    </select>


                                                    @error('schoolyear_id')
                                                        <span class="text-danger small">{{ $message }}</span>
                                                    @enderror
                                                </div>


                                                <input type="hidden" name="sched_status" value="unassigned">

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
                                        <th>Assigned Teacher</th>
                                        <th>Subject</th>
                                        <th>Section</th>
                                        <th>Date and Time</th>
                                        <th>School Year</th>
                                        <th>Status</th>
                                        <th>Action</th>

                                        <!-- <th>ACtion</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($view_schedule as $sched)
                                        <tr>
                                            <td>{{ $sched->teacher_name }}</td>
                                            <td class="align-middle">
                                                <div class="fw-bold text-primary mb-1"
                                                    style="font-size: 1rem; letter-spacing: -0.3px;">
                                                    {{ $sched->sub_name }}
                                                </div>

                                                <div>
                                                    <span class="badge rounded-pill bg-light text-dark border">
                                                        <i class="bi bi-mortarboard-fill me-1 text-secondary"></i>
                                                        Grade {{ $sched->grade_name }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td>{{ $sched->sec_name }}</td>
                                            <td class="align-middle">
                                                <div class="mb-1">
                                                    @if ($sched->sub_date)
                                                        @foreach (explode('-', $sched->sub_date) as $day)
                                                            <span class="badge bg-primary shadow-sm"
                                                                style="font-size: 0.75rem;">
                                                                {{ $day }}
                                                            </span>
                                                        @endforeach
                                                    @else
                                                        <span class="text-muted small">No Days Set</span>
                                                    @endif
                                                </div>

                                                <div class="text-dark fw-bold" style="font-size: 0.9rem;">
                                                    <i class="bi bi-clock me-1 text-secondary"></i>
                                                    {{ $sched->sub_Stime ? date('g:i A', strtotime($sched->sub_Stime)) : 'N/A' }}
                                                    <span class="text-secondary mx-1">→</span>
                                                    {{ $sched->sub_Etime ? date('g:i A', strtotime($sched->sub_Etime)) : 'N/A' }}
                                                </div>
                                            </td>
                                            <td>{{ $sched->schoolyear_name }}</td>
                                            <td style="text-align: center;">
                                                @if ($sched->sched_status == 1)
                                                    {{-- Soft Green for Assigned (Status 1) --}}
                                                    <span class="badge"
                                                        style="background-color: #ecfdf5; color: #06bd4c; border: 1px solid #a7f3d0; font-weight: 600; border-radius: 50px; padding: 4px 12px; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                                                        <span
                                                            style="width: 7px; height: 7px; background-color: #10b981; border-radius: 50%;"></span>
                                                        {{ $sched->status_name }}
                                                    </span>
                                                @else
                                                    {{-- Soft Red for Unassigned (Status 0) --}}
                                                    <span class="badge"
                                                        style="background-color: #fff1f2; color: #ff2d2d; border: 1px solid #fecdd3; font-weight: 600; border-radius: 50px; padding: 4px 12px; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                                                        <span
                                                            style="width: 7px; height: 7px; background-color: #f43f5e; border-radius: 50%;"></span>
                                                        {{ $sched->status_name }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class ="text-center">


                                                <button class="btn btn-sm"
                                                    style="background-color: #fef2f2; color: #cc1e1e; border: 1px solid #fee2e2; border-radius: 8px; padding: 6px 10px; transition: 0.3s;"
                                                    onmouseover="this.style.backgroundColor='#fee2e2'"
                                                    onmouseout="this.style.backgroundColor='#fef2f2'"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteScheduleModal{{ $sched->schedule_id }}">
                                                    <i class="bi bi-person-dash" style="font-size: 14px;"></i>
                                                </button>

                                                <button class="btn btn-sm"
                                                    style="background-color: #f0fdf4; color: #06bd4c; border: 1px solid #dcfce7; border-radius: 8px; padding: 6px 10px; transition: 0.3s;"
                                                    onmouseover="this.style.backgroundColor='#dcfce7'"
                                                    onmouseout="this.style.backgroundColor='#f0fdf4'"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#UpdateScheduleModal{{ $sched->schedule_id }}">
                                                    <i class="bi bi-pen" style="font-size: 14px;"></i>
                                                </button>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="deleteScheduleModal{{ $sched->schedule_id }}"
                                            tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">Confirm Delete</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <i class="bi bi-exclamation-triangle text-danger"
                                                            style="font-size: 3rem;"></i>
                                                        <p class="mt-3">Are you sure you want to delete the schedule for
                                                            <br>
                                                            <strong>{{ $sched->sub_name }}</strong>?
                                                        </p>
                                                        <p class="text-muted small">This action cannot be undone.</p>

                                                        <form method="POST" action="{{ route('delete_schedule') }}">
                                                            @csrf
                                                            <input type="hidden" name="schedule_id"
                                                                value="{{ $sched->schedule_id }}">

                                                            <input type="hidden" name="teachers_id"
                                                                value="{{ $sched->teachers_id }}">

                                                            <div class="mt-4">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-danger">Yes, Delete
                                                                    It</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Update Schedule Modal -->
                                        <div class="modal fade" id="UpdateScheduleModal{{ $sched->schedule_id }}"
                                            tabindex="-1" role="dialog"
                                            aria-labelledby="UpdateScheduleModal{{ $sched->schedule_id }}"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content" style="border-radius: 20px;">
                                                    <div class="modal-header bg-primary">
                                                        <h5 class="modal-title">Update Schedule</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>

                                                    <div class="modal-body">
                                                        <form method="POST" action="{{ route('update_schedule') }}">
                                                            @csrf

                                                            <!-- Hidden Schedule ID -->
                                                            <input type="hidden" name="schedule_id"
                                                                value="{{ $sched->schedule_id }}">

                                                            <!-- Assigned Teacher -->
                                                            <div class="mb-3">
                                                                <label for="teachers_id" class="form-label">Assigned
                                                                    Teacher</label>
                                                                <select class="form-select" id="teachers_id"
                                                                    name="teachers_id">
                                                                    <option value="{{ $sched->teachers_id }}">
                                                                        {{ $sched->teacher_name ?? 'Not Assigned' }}
                                                                    </option>
                                                                    <option value="0">Not Assigned</option>
                                                                    @foreach ($teachers as $teacher)
                                                                        <option value="{{ $teacher->teachers_id }}">
                                                                            {{ $teacher->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @error('teachers_id')
                                                                    <span class="text-danger small">{{ $message }}</span>
                                                                @enderror
                                                            </div>

                                                            <!-- Assigned Subject -->
                                                            <div class="mb-3">
                                                                <label for="subject_id" class="form-label">Assigned
                                                                    Subject</label>
                                                                <select class="form-select" id="subject_id"
                                                                    name="subject_id" required>
                                                                    <option value="{{ $sched->subject_id }}">
                                                                        {{ $sched->sub_name }}</option>
                                                                    @foreach ($subject as $sub)
                                                                        <option value="{{ $sub->subject_id }}">
                                                                            {{ $sub->subject_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @error('subject_id')
                                                                    <span class="text-danger small">{{ $message }}</span>
                                                                @enderror
                                                            </div>


                                                            <!-- Assigned Section -->
                                                            <div class="mb-3">
                                                                <label for="subject_id" class="form-label">Assigned
                                                                    Section</label>
                                                                <select class="form-select" id="section_id"
                                                                    name="section_id" required>
                                                                    <option value="{{ $sched->section_id }}">
                                                                        {{ $sched->sec_name }}</option>
                                                                    @foreach ($section as $sec)
                                                                        <option value="{{ $sec->section_id }}">
                                                                            {{ $sec->section_name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                @error('subject_id')
                                                                    <span class="text-danger small">{{ $message }}</span>
                                                                @enderror
                                                            </div>


                                                            <!-- Select Days -->
                                                            <div class="mb-3">
                                                                <label class="form-label">Select Days</label>
                                                                <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                                                                    @php
                                                                        $days = [
                                                                            'Whole Week',
                                                                            'Monday',
                                                                            'Tuesday',
                                                                            'Wednesday',
                                                                            'Thursday',
                                                                            'Friday',
                                                                        ];
                                                                        $existingDays = explode('-', $sched->sub_date);
                                                                    @endphp

                                                                    @foreach ($days as $day)
                                                                        <div class="form-check">
                                                                            <input type="checkbox" name="days[]"
                                                                                value="{{ $day }}"
                                                                                class="form-check-input day-option"
                                                                                onclick="handleDaySelection(this)"
                                                                                {{ in_array($day, $existingDays) ? 'checked' : '' }}>
                                                                            <label
                                                                                class="form-check-label">{{ $day }}</label>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>

                                                            <script>
                                                                function handleDaySelection(el) {
                                                                    let checkboxes = document.querySelectorAll('.day-option');

                                                                    if (el.value === "Whole Week" && el.checked) {
                                                                        checkboxes.forEach(cb => {
                                                                            if (cb.value !== "Whole Week") cb.checked = false;
                                                                        });
                                                                    } else {
                                                                        document.querySelector('.day-option[value="Whole Week"]').checked = false;
                                                                    }
                                                                }
                                                            </script>

                                                            <!-- Start and End Time -->
                                                            <div class="row mb-3">
                                                                <div class="col-md-6">
                                                                    <label for="sub_Stime" class="form-label">Starting
                                                                        Time</label>
                                                                    <input type="time" class="form-control"
                                                                        id="sub_Stime" name="sub_Stime" required
                                                                        value="{{ $sched->sub_Stime }}">
                                                                    @error('sub_Stime')
                                                                        <span
                                                                            class="text-danger small">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="sub_Etime" class="form-label">End
                                                                        Time</label>
                                                                    <input type="time" class="form-control"
                                                                        id="sub_Etime" name="sub_Etime" required
                                                                        value="{{ $sched->sub_Etime }}">
                                                                    @error('sub_Etime')
                                                                        <span
                                                                            class="text-danger small">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="schoolyear_id" class="form-label">School
                                                                    Year:</label>
                                                                <select name="schoolyear_id" id="schoolyear_id"
                                                                    class="form-select" required>
                                                                    <option value="">-- Select School Year --
                                                                    </option>

                                                                    {{-- Loop through the real database records passed from the controller --}}
                                                                    @foreach ($school_year as $sy)
                                                                        <option value="{{ $sy->schoolyear_ID }}"
                                                                            {{ $sched->schoolyear_id == $sy->schoolyear_ID ? 'selected' : '' }}>
                                                                            {{ $sy->schoolyear_name }}
                                                                            {{-- This shows "2025-2026" --}}
                                                                        </option>
                                                                    @endforeach
                                                                </select>

                                                                @error('schoolyear_id')
                                                                    <span class="text-danger small">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                            <div class="text-center mt-4">
                                                                <button type="submit"
                                                                    class="btn btn-primary">Update</button>
                                                            </div>

                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

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
