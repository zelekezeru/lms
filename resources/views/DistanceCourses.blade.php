<?php
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Distance Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #555; padding: 6px; text-align: center; }
        th { background-color: #f0f0f0; font-weight: bold; }
        h2 { text-align: center; margin-bottom: 5px; }
        .footer { margin-top: 20px; font-size: 11px; text-align: center; }
    </style>
</head>
<body>

<h2>Distance Report</h2>
<p style="text-align: center;">Generated on {{ now()->format('F j, Y') }}</p>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Course Name</th>
            <th>Total Students</th>
            @foreach($centers as $center)
                <th>Center - {{ $center->id }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
            <tr>
                <td>{{ $row['no'] }}</td>
                <td>{{ $row['name'] }}</td>
                <td>{{ $row['total'] }}</td>
                @foreach($centers as $center)
                    <td>{{ $row['center_'.$center->id] ?? 0 }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    SITS - Distance Report
</div>

</body>
</html>
