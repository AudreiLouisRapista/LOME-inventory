@extends('themes.main')

{{-- 1. DEFINE PAGE TITLE --}}
@section('title', 'User Profile')

{{-- 2. DEFINE CONTENT HEADER (Breadcrumbs) --}}
@section('content_header')
    <div class="header">
        <h1 class="title" style="margin-left: 50px; ">Student Management</h1>
        <p style="margin-left: 50px;">Manage Students and their informations</p>
    </div>

    <section class="content" style = "width: 1400 px; margin-left: 20px; margin-top: 20px;">
        <div class="container-fluid;">
            <div class="row">
                <div class="col-12">
                    <div class="card" style=" border-radius: 30px; background:#fff; padding:20px">
                        <div class="card-header" style="background:#fff;">
                            <h3 class="card-title">Student list</h3>
                            <!-- Button trigger modal -->

                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addTeacherModal"
                                style="float: right; border-radius: 20px; background-color: #4CAF50; border: none; padding: 10px 20px; font-size: 16px; cursor: pointer; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); transition: background-color 0.3s ease;">
                                Add Student
                            </button>

                            <!-- Modal -->
                            <div class="modal fade" id="addTeacherModal" tabindex="-1"
                                aria-labelledby="addTeacherModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content" style="border-radius: 20px;">
                                        <!-- Square-ish rounded corners -->
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addTeacherModalLabel">Add Student</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            @include('layout.partials.alerts')

                                            <form method="POST" action="{{ route('save_student') }}"
                                                enctype="multipart/form-data">
                                                @csrf


                                                <div class="col-md-6">
                                                    <label for="student_firstname" class="form-label">Fist Name:</label>
                                                    <input type="text" name="student_firstname" id="student_firstname"
                                                        class="form-control" placeholder="Enter Student First name"
                                                        value="{{ old('student_firstname') }}" required>
                                                    @error('student_firstname')
                                                        <span class="text-danger small">{{ $message }}</span>
                                                    @enderror
                                                </div>


                                                <div class="col-md-6">
                                                    <label for="student_lastname" class="form-label">Last Name:</label>
                                                    <input type="text" name="student_lastname" id="student_lastname"
                                                        class="form-control" placeholder="Enter Student Last name"
                                                        value="{{ old('student_lastname') }}" required>
                                                    @error('student_lastname')
                                                        <span class="text-danger small">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                        </div>

                                        <div class="text-center mt-4">
                                            <button type="submit" class="btn btn-primary w-95">Register</button>
                                        </div>

                                        </form>
                                    </div>
                                </div>
                            </div>




                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-hover">
                                <thead>
                                    <tr>

                                        <th>First Name</th>
                                        <th>Last Name</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($view_student as $student)
                                        <tr>


                                            <td>{{ $student->student_firstname }}</td>
                                            <td>{{ $student->student_lastname }}</td>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

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

@section('tables')
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>


    <script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="../../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="../../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="../../plugins/jszip/jszip.min.js"></script>
    <script src="../../plugins/pdfmake/pdfmake.min.js"></script>
    <script src="../../plugins/pdfmake/vfs_fonts.js"></script>
    <script src="../../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="../../plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="../../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../../dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../../dist/js/demo.js"></script>
    <!-- Page specific script -->
    <script>
        $(function() {
            $("#example1").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });
    </script>

@endsection
