@if (session('usr_id') == null)
    {{ unauthorize() }}
@endif



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard</title>
</head>

<body>
    @if (session('role') == 1)
        Hello admin user
    @endif

</body>

</html>
