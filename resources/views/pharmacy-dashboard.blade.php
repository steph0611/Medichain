<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy Dashboard</title>
    <link rel="stylesheet" href="{{ asset('styles5.css') }}">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">MediChain</div>
        <ul class="menu">
            <li><a href="#">Dashboard</a></li>
            <li><a href="#">Prescriptions</a></li>
            <li><a href="#">Inventory</a></li>
            <li><a href="#">Orders</a></li>
            <li><a href="#">Settings</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <h1>Welcome, {{ $pharmacy['shop_name'] }}</h1>
            <p>Manage all your prescriptions in one place.</p>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert error">{{ session('error') }}</div>
        @endif

        <!-- Pharmacy Info -->
        <div class="info-cards">
            <div class="card">
                <h2>📍 Location</h2>
                <p>{{ $pharmacy['city'] ?? 'N/A' }}</p>
            </div>
            <div class="card">
                <h2>📞 Contact</h2>
                <p>{{ $pharmacy['phone'] ?? 'N/A' }}</p>
            </div>
            <div class="card">
                <h2>✉️ Email</h2>
                <p>{{ $pharmacy['email'] ?? 'N/A' }}</p>
            </div>
        </div>

        <!-- Prescriptions Table -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">📋 Uploaded Prescriptions</h2>

            @if(count($prescriptions) === 0)
                <p class="text-gray-600">No prescriptions uploaded yet.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full border border-gray-200 rounded-lg text-sm">
                        <thead class="bg-blue-600 text-white">
                            <tr>
                                <th class="py-3 px-4 text-left">Patient Name</th>
                                <th class="py-3 px-4 text-left">Doctor Name</th>
                                <th class="py-3 px-4 text-left">Date</th>
                                <th class="py-3 px-4 text-left">Phone</th>
                                <th class="py-3 px-4 text-left">Email</th>
                                <th class="py-3 px-4 text-left">Address</th>
                                <th class="py-3 px-4 text-left">Instructions</th>
                                <th class="py-3 px-4 text-left">Image</th>
                                <th class="py-3 px-4 text-left">Uploaded At</th>
                                <th class="py-3 px-4 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach($prescriptions as $prescription)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4">{{ $prescription['patient_name'] }}</td>
                                    <td class="py-3 px-4">{{ $prescription['doctor_name'] }}</td>
                                    <td class="py-3 px-4">{{ \Carbon\Carbon::parse($prescription['prescription_date'])->format('Y-m-d') }}</td>
                                    <td class="py-3 px-4">{{ $prescription['phone'] }}</td>
                                    <td class="py-3 px-4 text-blue-700">{{ $prescription['email'] }}</td>
                                    <td class="py-3 px-4">{{ $prescription['address'] }}</td>
                                    <td class="py-3 px-4">{{ $prescription['instructions'] ?? '-' }}</td>
                                    <td class="py-3 px-4">
                                        @if(str_contains($prescription['image_type'], 'pdf'))
                                            <a href="data:{{ $prescription['image_type'] }};base64,{{ $prescription['image_data'] }}"
                                               target="_blank"
                                               class="text-blue-600 hover:underline">View PDF</a>
                                        @else
                                            <img src="data:{{ $prescription['image_type'] }};base64,{{ $prescription['image_data'] }}"
                                                 alt="Prescription Image"
                                                 class="w-24 h-auto border rounded" />
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">{{ \Carbon\Carbon::parse($prescription['uploaded_at'])->format('Y-m-d H:i') }}</td>

                                    <!-- NEW: Status Update -->
                                    <td class="py-3 px-4">
                                        <form action="{{ route('prescriptions.updateStatus', $prescription['id']) }}" method="POST" class="flex space-x-2">
                                            @csrf
                                            @method('PUT')
                                            <select name="status" class="border rounded px-2 py-1 text-sm">
                                                <option value="Pending" {{ $prescription['status'] == 'Pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="Accepted" {{ $prescription['status'] == 'Accepted' ? 'selected' : '' }}>Accepted</option>
                                                <option value="Ready" {{ $prescription['status'] == 'Ready' ? 'selected' : '' }}>Ready</option>
                                                <option value="Delivered" {{ $prescription['status'] == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                                            </select>
                                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                                                Update
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
