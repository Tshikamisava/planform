<h1>Recipient Dashboard</h1>
<p>Welcome, {{ auth()->user()->name }}!</p>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<h2>Your DRC Submissions</h2>
@if($submissions->isEmpty())
    <p>No submissions found.</p>
@else
    <table class="table">
        <thead>
            <tr>
                <th>DRC ID</th>
                <th>Date</th>
                <th>Author</th>
                <th>Details</th>
                <th>File</th>
            </tr>
        </thead>
        <tbody>
            @foreach($submissions as $submission)
                <tr>
                    <td>{{ $submission->drc_id }}</td>
                    <td>{{ $submission->submission_date }}</td>
                    <td>{{ $submission->author }}</td>
                    <td>{{ $submission->details }}</td>
                    <td>
                        @if($submission->file_path)
                            <a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank">View File</a>
                        @else
                            No file
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
