<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pharmacy Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            min-height: 100vh;
        }
        .sidebar {
            min-width: 220px;
            max-width: 220px;
            background: #0d6efd;
            color: #fff;
            min-height: 100vh;
            position: fixed;
        }
        .sidebar .nav-link {
            color: #fff;
            font-weight: 500;
            margin: 0.5rem 0;
            border-radius: 0.5rem;
        }
        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        .main-content {
            margin-left: 220px;
            padding: 2rem;
        }
        .dashboard-title {
            font-size: 2rem;
            color: #0d6efd;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        .card {
            border: none;
            border-radius: 1rem;
            overflow: hidden;
            animation: fadeInUp 0.8s ease;
        }
        .table-hover tbody tr:hover {
            background-color: #f1f5ff;
            transform: scale(1.01);
            transition: all 0.3s ease;
        }
        .btn {
            transition: all 0.3s ease;
            border-radius: 30px;
            padding: 4px 14px;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .badge {
            font-size: 0.9rem;
            padding: 6px 12px;
            border-radius: 20px;
            animation: popIn 0.5s ease;
        }
        @keyframes popIn {
            from { transform: scale(0.7); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        @keyframes fadeInUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        /* Sidebar icons + spacing */
        .sidebar .nav-link i {
            margin-right: 10px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar d-flex flex-column p-3">
    <div class="text-center mb-4">
        <img src="{{ asset('images/logo.png') }}" alt="Medichain Logo" style="max-width: 150px;">
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link active" href="#"><i class="bi bi-house"></i> Dashboard</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#"><i class="bi bi-bag"></i> Orders</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#"><i class="bi bi-people"></i> Patients</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#"><i class="bi bi-gear"></i> Settings</a>
        </li>
        <li class="nav-item mt-auto">
            <a class="nav-link" href="{{ route('logout') }}"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </li>


    </ul>
</div>


<!-- Main Content -->
<div class="main-content">
    <h2 class="mb-4 fw-bold dashboard-title">
        Pharmacy Dashboard - {{ $pharmacy['shop_name'] ?? 'Pharmacy' }}
    </h2>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success shadow-sm rounded-pill px-4 py-2">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger shadow-sm rounded-pill px-4 py-2">{{ session('error') }}</div>
    @endif

    {{-- Prescriptions Table --}}
    <div class="card shadow-lg">
        <div class="card-body">
            <table class="table table-hover align-middle text-center">
                <thead class="table-primary">
                    <tr>
                        <th>👤 Patient</th>
                        <th>🩺 Doctor</th>
                        <th>📅 Date</th>
                        <th>📄 Prescription</th>
                        <th>📖 Instructions</th>
                        <th>Status</th>
                        <th>⚡ Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prescriptions as $p)
                        @php $status = $p['status']; @endphp
                        <tr>
                            <td><strong>{{ $p['patient_name'] }}</strong></td>
                            <td>{{ $p['doctor_name'] }}</td>
                            <td>{{ \Carbon\Carbon::parse($p['prescription_date'])->format('d M Y') }}</td>
                            <td>
                                @if($p['image_data'] ?? false)
                                    <a href="data:{{ $p['image_type'] }};base64,{{ $p['image_data'] }}" target="_blank" 
                                       class="btn btn-outline-primary btn-sm">View</a>
                                @else
                                    <span class="text-muted fst-italic">No Image</span>
                                @endif
                            </td>
                            <td>{{ $p['instructions'] ?? '—' }}</td>
                            <td>
                                <span class="badge 
                                    @if($status === 'Pending') bg-secondary
                                    @elseif($status === 'Accepted') bg-success
                                    @elseif($status === 'Ready') bg-warning text-dark
                                    @elseif($status === 'Delivered') bg-primary
                                    @elseif($status === 'Cancelled') bg-danger
                                    @else bg-dark
                                    @endif">
                                    {{ $status }}
                                </span>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('pharmacy.updateStatus', $p['id']) }}" 
                                      class="d-flex flex-wrap justify-content-center gap-2">
                                    @csrf
                                    @method('PATCH')

                                    @if($status === 'Pending')
                                        <button type="submit" name="status" value="Accepted" class="btn btn-success btn-sm">✅ Accept</button>
                                        <button type="submit" name="status" value="Cancelled" class="btn btn-danger btn-sm">❌ Cancel</button>
                                    @elseif($status === 'Accepted')
                                        <button type="submit" name="status" value="Ready" class="btn btn-warning btn-sm">📦 Ready</button>
                                        <button type="submit" name="status" value="Delivered" class="btn btn-primary btn-sm">🚚 Delivered</button>
                                    @elseif($status === 'Ready')
                                        <button type="submit" name="status" value="Delivered" class="btn btn-primary btn-sm">🚚 Delivered</button>
                                    @else
                                        <button type="submit" name="status" value="Pending" class="btn btn-secondary btn-sm">⏳ Pending</button>
                                        <button type="submit" name="status" value="Accepted" class="btn btn-success btn-sm">✅ Accept</button>
                                        <button type="submit" name="status" value="Ready" class="btn btn-warning btn-sm">📦 Ready</button>
                                        <button type="submit" name="status" value="Delivered" class="btn btn-primary btn-sm">🚚 Delivered</button>
                                        <button type="submit" name="status" value="Cancelled" class="btn btn-danger btn-sm">❌ Cancel</button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted fst-italic">✨ No prescriptions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bootstrap icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

</body>
</html>
