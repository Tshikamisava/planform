<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #4B5563;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #333;
        }
        td {
            padding: 6px 8px;
            border: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 8px;
            color: #666;
        }
        .summary-stats {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
        }
        .stat-box {
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            flex: 1;
            margin: 0 5px;
        }
        .stat-value {
            font-size: 16px;
            font-weight: bold;
            color: #2563eb;
        }
        .stat-label {
            font-size: 9px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Date Range: {{ $startDate }} to {{ $endDate }}</p>
        <p>Generated: {{ $generatedAt }}</p>
    </div>

    @if(!empty($data))
        @php
            $headers = array_keys($data[0]);
            $totalRecords = count($data);
        @endphp

        <div style="margin: 15px 0; font-weight: bold;">
            Total Records: {{ $totalRecords }}
        </div>

        <table>
            <thead>
                <tr>
                    @foreach($headers as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($data as $row)
                    <tr>
                        @foreach($row as $cell)
                            <td>{{ $cell }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="text-align: center; margin: 50px 0; color: #666;">No data available for the selected period.</p>
    @endif

    <div class="footer">
        <p>Planform DCR Management System - Confidential Report</p>
        <p>Page 1 of 1</p>
    </div>
</body>
</html>
