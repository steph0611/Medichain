<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediChain Customer Portal</title>
    <link rel="stylesheet" href="{{ asset('styles2.css') }}">
</head>
<body>
    <div class="topbar">
        <div class="logo-section">
            <span class="logo-main">MediChain</span>
            <span class="logo-sub">Customer Portal</span>
        </div>

        <div class="hamburger" onclick="toggleMobileMenu()">☰</div>

        <nav class="main-nav" id="mobileMenu">
            <a href="{{ url('/dashboard') }}" class="nav-item active">Dashboard</a>
            <a href="{{ url('/orders') }}" class="nav-item">My Orders</a>
            <a href="{{ url('/pharmacies') }}" class="nav-item">Pharmacies</a>
            <a href="{{ url('/history') }}" class="nav-item">History</a>
        </nav>
        <div class="user-section">
            <div class="avatar-container">
                <div class="avatar-btn" onclick="toggleDropdown()">
                    {{ strtoupper(substr(session('user')['full_name'] ?? session('user')['username'], 0, 1)) }}
                </div>
                <div class="dropdown" id="userDropdown">
                    <div class="dropdown-header">
                        <div class="dropdown-avatar">
                            {{ strtoupper(substr(session('user')['full_name'] ?? session('user')['username'], 0, 1)) }}
                        </div>
                        <div class="dropdown-user-info">
                            <span><strong>{{ session('user')['full_name'] ?? 'Guest' }}</strong></span>
                            <span style="font-size: 0.9em; color: #666;">{{ session('user')['email'] ?? 'email@example.com' }}</span>
                        </div>
                    </div>
                    <a href="/profile" class="dropdown-link">View Profile</a>
                    <a href="/settings" class="dropdown-link">Settings</a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-link" style="color: #d00;">Logout</button>
                    </form>
                </div>
            </div>
            <div class="user-info">
                <div class="user-name">
                    {{ session('user')['full_name'] }}
                </div>
                <div class="user-role">Patient</div>
            </div>
        </div>
    </div>

    <div style="display: flex;">
        <div class="sidebar">
            <div class="mb-4">
                <a href="{{ url('/dashboard') }}" class="sidebar-link active">Dashboard</a>
                <a href="{{ url('/upload-prescription') }}" class="sidebar-link">Upload Prescription</a>
                <a href="{{ url('/pharmacies') }}" class="sidebar-link">Find Pharmacy</a>
            </div>
            <div class="mt-8">
                <a href="{{ url('/orders') }}" class="sidebar-link">My Orders</a>
                <a href="{{ url('/payment-methods') }}" class="sidebar-link">Payment Methods</a>
                <a href="{{ url('/addresses') }}" class="sidebar-link">Addresses</a>
                <a href="{{ url('/settings') }}" class="sidebar-link">Settings</a>
            </div>
        </div>

        <div class="main-content">
            <h1 class="main-heading">Welcome back, {{ session('user')['username'] }}!</h1>
            <p class="subheading">Here's an overview of pharmacies available in your city.</p>

            <div class="grid">
                <div class="card">
                    <div><span class="icon-box">📦</span><span class="card-number">3</span></div>
                    <div class="card-label">Active Orders</div>
                    <div class="card-sub">2 ready for pickup</div>
                </div>
                <div class="card">
                    <div><span class="icon-box">🏥</span><span class="card-number">{{ count($pharmacies) }}</span></div>
                    <div class="card-label">Nearby Pharmacies</div>
                    <div class="card-sub">In {{ session('user')['city'] }}</div>
                </div>
                <div class="card">
                    <div><span class="icon-box">💰</span><span class="card-number">$127</span></div>
                    <div class="card-label">Total Saved</div>
                    <div class="card-sub">This month</div>
                </div>
                <div class="card">
                    <div><span class="icon-box">📅</span><span class="card-number">12</span></div>
                    <div class="card-label">Orders This Year</div>
                    <div class="card-sub">On time delivery</div>
                </div>
            </div>

            <!-- Available Pharmacies -->
            <div class="card" style="margin-bottom: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h2 style="font-size: 1.125rem; font-weight: bold;">Available Pharmacies in {{ session('user')['city'] }}</h2>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Pharmacy</th>
                            <th>Location</th>
                            <th>Phone</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pharmacies as $pharmacy)
                            <tr>
                                <td>{{ $pharmacy['shop_name'] }}</td>
                                <td>{{ $pharmacy['location'] }}</td>
                                <td>{{ $pharmacy['phone'] }}</td>
                                <td>
                                    <a href="{{ route('pharmacy.upload.form', $pharmacy['id']) }}" class="btn-primary">
                                        Upload Prescription
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align:center; color: #6b7280;">
                                    No pharmacies available in {{ session('user')['city'] }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function toggleDropdown() {
            document.getElementById("userDropdown").classList.toggle("show");
        }

        window.addEventListener('click', function(e) {
            const dropdown = document.getElementById("userDropdown");
            const avatar = document.querySelector('.avatar-btn');
            if (!dropdown.contains(e.target) && !avatar.contains(e.target)) {
                dropdown.classList.remove("show");
            }
        });

        function toggleMobileMenu() {
            const menu = document.getElementById("mobileMenu");
            menu.classList.toggle("show");
        }
    </script>
</body>
</html>
