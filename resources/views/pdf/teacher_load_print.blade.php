<!DOCTYPE html>
<html>

<head>
    <title>Teacher Load - {{ $teacher->name }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }

        .school-name {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }

        .info-box {
            margin-bottom: 20px;
            width: 100%;
        }

        .info-box td {
            padding: 3px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
            text-transform: uppercase;
            font-size: 10px;
        }

        td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #777;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="school-name">YOUR SCHOOL NAME HERE</div>
        <div style="font-size: 14px; font-weight: bold;">TEACHER ACADEMIC LOAD</div>
        <div style="font-size: 12px;">School Year: {{ $schoolyear->schoolyear_name }}</div>
    </div>

    <table class="info-box">
        <tr>
            <td width="15%"><strong>Teacher:</strong></td>
            <td>{{ $teacher->name }}</td>
            <td width="15%" style="text-align: right;"><strong>Date:</strong></td>
            <td width="20%" style="text-align: right;">{{ date('m/d/Y') }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th>Section</th>
                <th>Grade</th>
                <th>Day</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($loads as $load)
                <tr>
                    <td><strong>{{ $load->subject_name }}</strong></td>
                    <td>{{ $load->section_name }}</td>
                    <td>{{ $load->grade_title }}</td>
                    <td>{{ $load->sub_date }}</td>
                    <td>
                        {{ date('h:i A', strtotime($load->sub_Stime)) }} -
                        {{ date('h:i A', strtotime($load->sub_Etime)) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        This is a system-generated document.
    </div>

</body>

</html>
