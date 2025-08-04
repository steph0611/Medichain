@extends('layouts.app')

@section('content')
<div class="main-content">
    <h1>Prescription Details for {{ $pharmacy['shop_name'] }}</h1>

    @if(session('error'))
        <div style="color:red;">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div style="color:green;">{{ session('success') }}</div>
    @endif

    @if(count($prescriptions) === 0)
        <p>No prescriptions uploaded yet.</p>
    @else
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>Patient Name</th>
                    <th>Doctor Name</th>
                    <th>Date</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Instructions</th>
                    <th>Image</th>
                    <th>Uploaded At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prescriptions as $prescription)
                    <tr>
                        <td>{{ $prescription['patient_name'] }}</td>
                        <td>{{ $prescription['doctor_name'] }}</td>
                        <td>{{ \Carbon\Carbon::parse($prescription['prescription_date'])->format('Y-m-d') }}</td>
                        <td>{{ $prescription['phone'] }}</td>
                        <td>{{ $prescription['email'] }}</td>
                        <td>{{ $prescription['address'] }}</td>
                        <td>{{ $prescription['instructions'] ?? '-' }}</td>
                        <td>
                            @if(str_contains($prescription['image_type'], 'pdf'))
                                <a href="data:{{ $prescription['image_type'] }};base64,{{ $prescription['image_data'] }}" target="_blank">View PDF</a>
                            @else
                                <img src="data:{{ $prescription['image_type'] }};base64,{{ $prescription['image_data'] }}" width="100">
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($prescription['uploaded_at'])->format('Y-m-d H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
