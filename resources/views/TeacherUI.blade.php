@extends('themes.teachers')

@section('content_header')
    <section class="content" style="background: #f4f7fe; min-height: 100vh; padding-top: 30px;">
        <div class="container-fluid">
            <div class="row justify-content-center">

                <div class="col-md-11">
                    <div class="card border-0 shadow-sm" style="border-radius: 24px; background: white; overflow: hidden;">
                        <div class="row no-gutters">
                            <div class="col-md-4 d-flex flex-column justify-content-center align-items-center py-5"
                                style="background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%); border-right: 1px solid #f0f0f0;">

                                <div class="position-relative">
                                    <img class="rounded-circle shadow-sm"
                                        src="{{ session('profile') ? asset(session('profile')) . '?' . time() : asset('dist/img/avatar.png') }}"
                                        style="width:160px; height:160px; object-fit:cover; border:5px solid white;"
                                        onerror="this.onerror=null; this.src='{{ asset('dist/img/avatar.png') }}';">
                                    <div class="position-absolute shadow-sm"
                                        style="bottom: 5px; right: 10px; background: #2ecc71; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white;">
                                    </div>
                                </div>

                                <button
                                    class="btn btn-white btn-sm shadow-sm rounded-pill px-4 mt-4 text-primary font-weight-bold"
                                    data-toggle="modal" data-target="#updateTeacher" style="border: 1px solid #eee;">
                                    <i class="fas fa-user-edit mr-1"></i> Edit Profile
                                </button>

                                <div class="modal fade" id="updateTeacher" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg"
                                            style="border-radius: 20px; overflow: hidden;">

                                            <div class="modal-header border-0 pt-4 px-4" style="background: #ffffff;">
                                                <h5 class="modal-title font-weight-bold text-dark">
                                                    <i class="fas fa-user-edit mr-2 text-primary"></i> Edit Teacher Profile
                                                </h5>
                                                <button type="button" class="close shadow-none" data-dismiss="modal"
                                                    aria-label="Close"
                                                    style="border:none; background:none; font-size:1.5rem;">&times;</button>
                                            </div>

                                            @foreach ($teachers as $teacher)
                                                <form method="POST"
                                                    action="{{ route('Update_teacherProfile', ['id' => $teacher->teachers_id]) }}"
                                                    enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="modal-body px-4 pb-4">
                                                        @include('layout.partials.alerts')
                                                        <div class="text-center mb-4">
                                                            <label for="teacherPicUpload" style="cursor: pointer;"
                                                                class="position-relative">
                                                                <img src="{{ session('profile') ? asset(session('profile')) . '?' . time() : asset('dist/img/avatar.png') }}"
                                                                    class="rounded-circle border shadow-sm"
                                                                    style="width: 110px; height: 110px; object-fit: cover;"
                                                                    id="teacherPreview"
                                                                    onerror="this.onerror=null; this.src='{{ asset('dist/img/avatar.png') }}';">

                                                                <div class="position-absolute"
                                                                    style="bottom: 0; right: 0; background: #4e73df; color: #fff; border-radius: 50%; padding: 4px 8px; font-size: 12px; border: 3px solid #fff;">
                                                                    <i class="fas fa-camera"></i>
                                                                </div>
                                                            </label>

                                                            <input type="file" name="profile_image" id="teacherPicUpload"
                                                                class="d-none" accept="image/*"
                                                                onchange="previewTeacherFile(this)">

                                                            <p class="small text-muted mt-2 mb-0">Click the photo to change
                                                            </p>
                                                        </div>



                                                        <div class="form-group mb-3">
                                                            <label
                                                                class="small font-weight-bold text-muted text-uppercase">Full
                                                                Name</label>
                                                            <input type="text" name="name"
                                                                value="{{ $teacher->teacher_name }}"
                                                                class="form-control form-control-lg border-0 bg-light rounded-pill px-4"
                                                                style="font-size: 0.95rem;" required>
                                                        </div>

                                                        <div class="form-group mb-3">
                                                            <label
                                                                class="small font-weight-bold text-muted text-uppercase">Email
                                                                Address</label>
                                                            <input type="email" name="email"
                                                                value="{{ $teacher->teacher_email }}"
                                                                class="form-control form-control-lg border-0 bg-light rounded-pill px-4"
                                                                style="font-size: 0.95rem;" required>
                                                        </div>

                                                        <div class="form-group mb-3">
                                                            <label
                                                                class="small font-weight-bold text-muted text-uppercase">Password
                                                            </label>
                                                            <input type="password" name="password" {{-- value="{{ $teacher->teacher_password }}" --}}
                                                                class="form-control form-control-lg border-0 bg-light rounded-pill px-4"
                                                                style="font-size: 0.95rem;" placeholder="••••••••••">
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6 form-group mb-3">
                                                                <label
                                                                    class="small font-weight-bold text-muted text-uppercase">Phone</label>
                                                                <input type="text" name="phone"
                                                                    value="{{ $teacher->teacher_phone }}"
                                                                    class="form-control form-control-lg border-0 bg-light rounded-pill px-4"
                                                                    style="font-size: 0.95rem;" required>
                                                            </div>
                                                            <div class="col-md-6 form-group mb-3">
                                                                <label
                                                                    class="small font-weight-bold text-muted text-uppercase">Gender</label>
                                                                <select name="gender"
                                                                    class="form-control form-control-lg border-0 bg-light rounded-pill px-3"
                                                                    style="font-size: 0.95rem;" required>
                                                                    <option value="Male"
                                                                        {{ $teacher->teacher_gender == 'Male' ? 'selected' : '' }}>
                                                                        Male</option>
                                                                    <option value="Female"
                                                                        {{ $teacher->teacher_gender == 'Female' ? 'selected' : '' }}>
                                                                        Female</option>
                                                                </select>
                                                            </div>
                                                            <input type="hidden" name="teachers_id"
                                                                value="{{ $teacher->teachers_id }}">
                                                        </div>
                                                    </div>

                                                    <div class="modal-footer border-0 px-4 pb-4">
                                                        <button type="button"
                                                            class="btn btn-light rounded-pill px-4 font-weight-bold"
                                                            data-dismiss="modal">Cancel</button>
                                                        <button type="submit"
                                                            class="btn btn-primary rounded-pill px-5 font-weight-bold shadow-sm">Update
                                                            Profile</button>
                                                    </div>
                                                </form>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <script>
                                    function previewTeacherFile(input) {
                                        var file = input.files[0];
                                        if (file) {
                                            var reader = new FileReader();
                                            reader.onload = function() {
                                                $("#teacherPreview").attr("src", reader.result);
                                            }
                                            reader.readAsDataURL(file);
                                        }
                                    }
                                </script>
                            </div>

                            <div class="col-md-8 p-5">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h1 class="font-weight-bold text-dark mb-0">{{ session('name') }}</h1>
                                        <span class="badge badge-soft-primary px-3 py-2 rounded-pill mt-2"
                                            style="background: #eef2ff; color: #586bff; font-weight: 600;">
                                            <i class="fas fa-chalkboard-teacher mr-1"></i> {{ session('user_role') }}
                                        </span>
                                    </div>

                                </div>

                                <hr class="my-4" style="opacity: 0.5;">

                                @forelse ($teachers as $teacher)
                                    <div class="row">
                                        <div class="col-sm-6 mb-3">
                                            <small class="text-uppercase text-muted font-weight-bold"
                                                style="letter-spacing: 1px;">Email Address</small>
                                            <p class="text-dark font-weight-normal mb-0">
                                                {{ $teacher->teacher_email ?? 'No Email' }}</p>
                                        </div>
                                        <div class="col-sm-6 mb-3">
                                            <small class="text-uppercase text-muted font-weight-bold"
                                                style="letter-spacing: 1px;">Contact Number</small>
                                            <p class="text-dark font-weight-normal mb-0">
                                                {{ $teacher->teacher_phone ?? 'No Phone' }}</p>
                                        </div>
                                        <div class="col-sm-6">
                                            <small class="text-uppercase text-muted font-weight-bold"
                                                style="letter-spacing: 1px;">Gender</small>
                                            <p class="text-dark font-weight-normal mb-0">
                                                {{ $teacher->teacher_gender ?? 'Not Specified' }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-muted italic">Profile details unavailable.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-11 mt-4">
                    <div class="row">

                        <div class="col-lg-8">
                            <div class="card border-0 shadow-sm" style="border-radius: 20px; height: 600px;">
                                <div
                                    class="card-header border-0 bg-white py-4 d-flex justify-content-between align-items-center">
                                    <h5 class="font-weight-bold text-dark mb-0">Assigned Faculty Load</h5>
                                    <form method="GET" action="{{ route('Teacher.TeacherUI') }}">
                                        <select name="schoolyear_id"
                                            class="form-control rounded-pill border-0 shadow-sm px-4"
                                            style="background: #f8f9fa; font-size: 13px; font-weight: 600; width: 220px;"
                                            onchange="this.form.submit()">
                                            <option value="">Choose School Year</option>
                                            @foreach ($school_years_map as $id => $name)
                                                <option value="{{ $id }}"
                                                    {{ $selected_year_id == $id ? 'selected' : '' }}>{{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </div>

                                <div class="card-body pt-0" style="overflow-y: auto;">
                                    @if ($selected_year_id)
                                        <table class="table table-borderless">
                                            <thead class="sticky-top"
                                                style="background: white; border-bottom: 2px solid #f8f9fa;">
                                                <tr class="text-muted small font-weight-bold">
                                                    <th>SUBJECT</th>
                                                    <th>GRADE & SECTION</th>
                                                    <th>SCHEDULE</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($teacher_ui as $load)
                                                    <tr style="border-bottom: 1px solid #f8f9fa;">
                                                        <td class="align-middle py-3">
                                                            <span
                                                                class="font-weight-bold text-dark">{{ $load->sub_name }}</span>
                                                        </td>
                                                        <td class="align-middle">
                                                            <span class="px-3 py-1"
                                                                style="background: #f0f7ff; color: #007bff; border-radius: 8px; font-size: 12px; font-weight: 600;">
                                                                {{ $load->grade_name }} - {{ $load->sec_name }}
                                                            </span>
                                                        </td>
                                                        <td class="align-middle">
                                                            <div class="text-dark font-weight-bold mb-0"
                                                                style="font-size: 13px;">{{ $load->sub_date }}</div>
                                                            <div class="text-muted small">
                                                                {{ date('h:i A', strtotime($load->sub_Stime)) }} -
                                                                {{ date('h:i A', strtotime($load->sub_Etime)) }}</div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="text-center mt-5">
                                            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png"
                                                style="width: 80px; opacity: 0.3;">
                                            <p class="text-muted mt-3">Select a school year to view your data.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card border-0 shadow-sm"
                                style="border-radius: 20px; height: 600px; background: white;">
                                <div class="card-header border-0 bg-white py-4 text-center">
                                    <h6 class="text-uppercase text-muted font-weight-bold mb-1"
                                        style="letter-spacing: 1px; font-size: 12px;">Timeline</h6>
                                    <h5 class="font-weight-bold text-dark">Recent Activity</h5>
                                </div>

                                <div class="card-body pt-2" style="overflow-y: auto;">
                                    @forelse($teacher_ui as $load)
                                        @php
                                            // Fixed typo from create_at to created_at
                                            $createdAt = !empty($load->created_at)
                                                ? \Carbon\Carbon::parse($load->created_at)
                                                : null;
                                            $daysDiff = $createdAt ? $createdAt->diffInDays(now()) : 99;
                                        @endphp
                                        <div class="activity-item d-flex mb-4">
                                            <div class="mr-3 text-center">
                                                @if ($daysDiff < 1)
                                                    <div
                                                        style="width: 12px; height: 12px; background: #2ecc71; border-radius: 50%; margin-top: 5px; border: 3px solid #e8f8ef;">
                                                    </div>
                                                @else
                                                    <div
                                                        style="width: 12px; height: 12px; background: #cbd5e0; border-radius: 50%; margin-top: 5px; border: 3px solid #f7fafc;">
                                                    </div>
                                                @endif
                                                <div
                                                    style="width: 2px; height: 100%; background: #f1f4f8; margin: 0 auto;">
                                                </div>
                                            </div>
                                            <div>
                                                <p class="mb-0 text-dark font-weight-normal" style="font-size: 14px;">
                                                    New load added: <strong>{{ $load->sub_name }}</strong>
                                                </p>
                                                <small class="text-muted">
                                                    <i class="far fa-clock mr-1"></i>
                                                    {{ $createdAt ? $createdAt->diffForHumans() : 'Recently' }}
                                                </small>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center text-muted mt-5 small italic">No recent history.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <style>
        .modal-content {
            border: none;
            border-radius: 24px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1);
        }

        .form-control {
            border-radius: 12px;
            border: 1px solid #eef0f2;
            padding: 12px 15px;
            height: auto;
        }

        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(88, 107, 255, 0.1);
            border-color: #586bff;
        }

        .btn-primary {
            border-radius: 12px;
            padding: 10px 25px;
            font-weight: 600;
            background: #586bff;
            border: none;
            transition: 0.3s;
        }

        .btn-primary:hover {
            background: #4656e6;
            transform: translateY(-1px);
        }
    </style>
@endsection
