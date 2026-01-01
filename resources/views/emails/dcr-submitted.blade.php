<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New DCR Submitted</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #0d6efd;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 0.9em;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>New DCR Submitted</h1>
        <p>A new Design Change Request (DCR) has been submitted and requires your attention.</p>
        
        <h2>DCR Details:</h2>
        <ul>
            <li><strong>DCR ID:</strong> {{ $dcr->dcr_id }}</li>
            <li><strong>Author:</strong> {{ $dcr->author->name }}</li>
            <li><strong>Request Type:</strong> {{ $dcr->request_type }}</li>
            <li><strong>Reason for Change:</strong> {{ $dcr->reason_for_change }}</li>
            <li><strong>Due Date:</strong> {{ $dcr->due_date }}</li>
        </ul>

        <p>Please log in to the Planform system to review and process this request.</p>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Planform. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
