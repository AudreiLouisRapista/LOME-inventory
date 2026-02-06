<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if (session('errorMessage'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: "error",
                title: "Access Denied",
                text: "{{ session('errorMessage') }}",
                confirmButtonColor: "#4e73df", // Matches your primary blue
                timer: 5000,
                timerProgressBar: true
            });
        });
    </script>
@endif

@if (session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: "warning",
                title: "Schedule Conflict!",
                text: "{{ session('error') }}",
                confirmButtonColor: "#4e73df",
                timer: 5000,
                timerProgressBar: true
            });
        });
    </script>
@endif

@if (session('deletschedule'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: "warning",
                title: "Be informed",
                text: "{{ session('deletschedule') }}",
                confirmButtonColor: "#4e73df",
                timer: 5000,
                timerProgressBar: true
            });
        });
    </script>
@endif

@if (session('warning'))
    <script>
        window.addEventListener('load', function() {

            Swal.fire({
                icon: "error",
                title: "Be informed",
                text: @json(session('warning')),

            });
        });
    </script>
@endif


@if (session('save'))
    <script>
        window.addEventListener('load', function() {

            Swal.fire({
                icon: "success",
                title: "Success",
                text: @json(session('save')),

            });
        });
    </script>
@endif
