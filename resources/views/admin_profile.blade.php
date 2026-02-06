@extends('themes.main')

@section('content_header')
    <style>
        /* --- Dashboard Layout --- */
        .admin-header-section {
            background-color: rgba(252, 252, 252, 0.876);
            transition: background-color 0.3s ease;
        }

        .dark-mode .admin-header-section {
            background-color: #121212 !important;
        }

        /* Aesthetic Improvements */
        .profile-card {
            border-radius: 20px;
            transition: all 0.3s ease;
            border: none;
        }

        .profile-img-container {
            position: relative;
            display: inline-block;
        }

        .edit-overlay {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: #4e73df;
            color: white;
            border-radius: 50%;
            padding: 5px 10px;
            cursor: pointer;
            border: 3px solid #fff;
        }

        .info-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            color: #858796;
            font-weight: 700;
        }

        .info-value {
            font-size: 1.1rem;
            color: #3a3b45;
            font-weight: 600;
        }

        .activity-item {
            border-left: 3px solid #e3e6f0;
            padding-left: 20px;
            position: relative;
            padding-bottom: 15px;
        }

        .activity-item::before {
            content: "";
            position: absolute;
            left: -8px;
            top: 0;
            width: 13px;
            height: 13px;
            background: #fff;
            border: 3px solid #4e73df;
            border-radius: 50%;
        }
    </style>

    <section class="admin-header-section py-4">
        <div class="container-fluid ">
            <div class="row justify-content-center">
                <div class="col-md-10" style="margin-top: 30px;">
                    <div class="card border-0 shadow-lg" style="border-radius: 25px; overflow: hidden; background: #fff;">

                        <div style="height: 100px; background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);"></div>

                        <div class="card-body pt-0 px-5 pb-5">
                            <div class="row">
                                <div class="col-md-4 text-center" style="margin-top: -50px;">
                                    <div class="position-relative d-inline-block">
                                        <img class="rounded-circle shadow-sm"
                                            src="{{ session('profile') ? asset(session('profile')) . '?' . time() : asset('dist/img/avatar.png') }}"
                                            style="width:160px; height:160px; object-fit:cover; border:5px solid white;"
                                            onerror="this.onerror=null; this.src='{{ asset('dist/img/avatar.png') }}';">

                                        <button class="btn btn-primary btn-sm position-absolute rounded-circle shadow"
                                            style="bottom: 5px; right: 5px; width: 38px; height: 38px; border: 3px solid #fff;"
                                            data-toggle="modal" data-target="#updateAdmin">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                    </div>
                                    <h3 class="mt-3 font-weight-bold mb-0 text-dark">{{ session('name') }}</h3>
                                    <span class="badge badge-soft-primary px-3 py-1 rounded-pill"
                                        style="background: #eef2ff; color: #4e73df;">{{ session('role') }}</span>
                                </div>

                                <div class="col-md-8 pt-4">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h5 class="font-weight-bold text-secondary mb-0">Personal Details</h5>
                                        <button class="btn btn-sm btn-light border rounded-pill px-3" data-toggle="modal"
                                            data-target="#updateAdmin">
                                            Update Account
                                        </button>
                                        <div class="modal fade" id="updateAdmin" tabindex="-1" role="dialog"
                                            aria-labelledby="updateAdminLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">

                                                    <div class="modal-header border-0 pt-4 px-4">
                                                        <h5 class="modal-title font-weight-bold text-dark"
                                                            id="updateAdminLabel">
                                                            <i class="bi bi-person-gear mr-2 text-primary"></i> Edit Profile
                                                            Settings
                                                        </h5>
                                                        <button type="button" class="btn-close shadow-none"
                                                            data-dismiss="modal" aria-label="Close"
                                                            style="background: none; border: none; font-size: 1.5rem;">&times;</button>
                                                    </div>

                                                    @foreach ($admins as $admin)
                                                        <form method="POST"
                                                            action="{{ route('adminProfile', ['id' => $admin->id]) }}"
                                                            enctype="multipart/form-data">

                                                            @csrf
                                                            <div class="modal-body px-4">
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

                                                                    <input type="file" name="profile_image"
                                                                        id="teacherPicUpload" class="d-none"
                                                                        accept="image/*"
                                                                        onchange="previewTeacherFile(this)">

                                                                    <p class="small text-muted mt-2 mb-0">Click the photo to
                                                                        change
                                                                    </p>
                                                                </div>

                                                                <div class="form-group mb-3">
                                                                    <label
                                                                        class="small font-weight-bold text-muted text-uppercase">Full
                                                                        Name</label>
                                                                    <input type="text" name="name"
                                                                        value="{{ $admin->name }}"
                                                                        class="form-control form-control-lg border-0 bg-light"
                                                                        style="border-radius: 12px; font-size: 0.95rem;"
                                                                        required>
                                                                </div>

                                                                <div class="form-group mb-3">
                                                                    <label
                                                                        class="small font-weight-bold text-muted text-uppercase">Email
                                                                        Address</label>
                                                                    <input type="email" name="email"
                                                                        value="{{ $admin->email }}"
                                                                        class="form-control form-control-lg border-0 bg-light"
                                                                        style="border-radius: 12px; font-size: 0.95rem;"
                                                                        required>
                                                                </div>

                                                                <div class="form-group mb-3">
                                                                    <label
                                                                        class="small font-weight-bold text-muted text-uppercase">New
                                                                        Password
                                                                    </label>
                                                                    <input type="password" name="password"
                                                                        {{-- value="{{ $teacher->teacher_password }}" --}}
                                                                        class="form-control form-control-lg border-0 bg-light rounded-pill px-4"
                                                                        style="font-size: 0.95rem;"
                                                                        placeholder="••••••••••">
                                                                </div>



                                                                <div class="row">
                                                                    <div class="col-md-6 form-group mb-3">
                                                                        <label
                                                                            class="small font-weight-bold text-muted text-uppercase">Phone</label>
                                                                        <input type="number" name="phone"
                                                                            value="{{ $admin->phone }}"
                                                                            class="form-control form-control-lg border-0 bg-light"
                                                                            style="border-radius: 12px; font-size: 0.95rem;"
                                                                            required>
                                                                    </div>
                                                                    <div class="col-md-6 form-group mb-3">
                                                                        <label
                                                                            class="small font-weight-bold text-muted text-uppercase">Gender</label>
                                                                        <select name="gender"
                                                                            class="form-control form-control-lg border-0 bg-light text-capitalize"
                                                                            style="border-radius: 12px; font-size: 0.95rem;"
                                                                            required>
                                                                            <option value="male"
                                                                                {{ $admin->gender == 'male' ? 'selected' : '' }}>
                                                                                Male</option>
                                                                            <option value="female"
                                                                                {{ $admin->gender == 'female' ? 'selected' : '' }}>
                                                                                Female</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                            </div>

                                                            <div class="modal-footer border-0 px-4 pb-4">
                                                                <button type="button"
                                                                    class="btn btn-light rounded-pill px-4 font-weight-bold"
                                                                    data-dismiss="modal">Cancel</button>
                                                                <button type="submit"
                                                                    class="btn btn-primary rounded-pill px-4 font-weight-bold shadow">Save
                                                                    Changes</button>
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

                                    @foreach ($admins as $admin)
                                        <div class="row">
                                            <div class="col-sm-6 mb-4">
                                                <label
                                                    class="text-muted small font-weight-bold text-uppercase mb-1 d-block">Full
                                                    Name</label>
                                                <p class="text-dark font-weight-600 mb-0">{{ $admin->name }}</p>
                                            </div>
                                            <div class="col-sm-6 mb-4">
                                                <label
                                                    class="text-muted small font-weight-bold text-uppercase mb-1 d-block">Email
                                                    Address</label>
                                                <p class="text-dark font-weight-600 mb-0">{{ $admin->email }}</p>
                                            </div>
                                            <div class="col-sm-6 mb-4">
                                                <label
                                                    class="text-muted small font-weight-bold text-uppercase mb-1 d-block">Phone
                                                    Number</label>
                                                <p class="text-dark font-weight-600 mb-0">{{ $admin->phone }}</p>
                                            </div>
                                            <div class="col-sm-6 mb-4">
                                                <label
                                                    class="text-muted small font-weight-bold text-uppercase mb-1 d-block">Gender</label>
                                                <p class="text-dark font-weight-600 mb-0 text-capitalize">
                                                    {{ $admin->gender }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <section class="content">
                    <div class="container-fluid">
                        <div class="row justify-content-center">
                            <div class="col-md-10 mt-4 mb-5">
                                <div class="card border-0 shadow-sm" style="border-radius: 20px; background: #ffffff;">

                                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                                        <h5 class="mb-0 font-weight-bold" style="color: #334155;">
                                            <i class="bi bi-activity text-primary mr-2"></i> Recent Activity Log
                                        </h5>
                                        <p class="text-muted small mb-0">Track your most recent system changes</p>
                                    </div>

                                    <div class="card-body px-4 pb-4">
                                        <div
                                            style="max-height: 400px; overflow-y: auto; padding-right: 15px; scrollbar-width: thin;">

                                            <div class="timeline-container"
                                                style="position: relative; padding-left: 20px; border-left: 2px solid #e2e8f0; margin-left: 10px;">

                                                @foreach ($logs as $log)
                                                    @php
                                                        // Keeping your existing logic exactly as you had it
                                                        switch ($log->action) {
                                                            case 'added':
                                                                $bgColor = 'rgba(0, 128, 0, 0.15)';
                                                                $textColor = '#166534';
                                                                $dotColor = '#22c55e';
                                                                break;
                                                            case 'updated':
                                                                $bgColor = 'rgba(255, 165, 0, 0.15)';
                                                                $textColor = '#9a3412';
                                                                $dotColor = '#f97316';
                                                                break;
                                                            case 'deleted':
                                                                $bgColor = 'rgba(255, 0, 0, 0.15)';
                                                                $textColor = '#991b1b';
                                                                $dotColor = '#ef4444';
                                                                break;
                                                            default:
                                                                $bgColor = '#f1f5f9';
                                                                $textColor = '#475569';
                                                                $dotColor = '#94a3b8';
                                                        }
                                                    @endphp

                                                    <div class="mb-4 position-relative">
                                                        <div
                                                            style="position: absolute; left: -27px; top: 5px; width: 12px; height: 12px; background: {{ $dotColor }}; border: 3px solid #fff; border-radius: 50%; box-shadow: 0 0 0 2px {{ $bgColor }};">
                                                        </div>

                                                        <div class="p-3"
                                                            style="background: #f8fafc; border-radius: 12px; transition: transform 0.2s ease;">
                                                            <div
                                                                class="d-flex justify-content-between align-items-start mb-1">
                                                                <span
                                                                    style="background: {{ $bgColor }}; color: {{ $textColor }}; padding: 2px 10px; border-radius: 8px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">
                                                                    {{ $log->action }}
                                                                </span>
                                                                <small class="text-muted font-italic"
                                                                    style="font-size: 11px;">
                                                                    <i class="bi bi-clock-history mr-1"></i>
                                                                    {{ $log->created_at->diffForHumans() }}
                                                                </small>
                                                            </div>

                                                            <p class="mb-0"
                                                                style="font-size: 14px; color: #334155; line-height: 1.5;">
                                                                {{ $log->description }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                @endforeach

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </section>

    {{-- @include('partials.admin_modal') --}}
@endsection
