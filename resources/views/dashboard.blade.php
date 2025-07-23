<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MediChain Customer Portal</title>
    <link rel="stylesheet" href="{{ asset('styles2.css') }}">
</head>
<body>
    <div class="topbar">
        <div class="logo-section">
            <span class="logo-main">MediChain</span>
            <span class="logo-sub">Customer Portal</span>
        </div>
        <nav class="main-nav">
            <a href="#" class="nav-item active">Dashboard</a>
            <a href="#" class="nav-item">My Orders</a>
            <a href="#" class="nav-item">Pharmacies</a>
            <a href="#" class="nav-item">History</a>
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
                <a href="#" class="sidebar-link active">Dashboard</a>
                <a href="#" class="sidebar-link">Upload Prescription</a>
                <a href="#" class="sidebar-link">Find Pharmacy</a>
            </div>
            <div class="mt-8">
                <a href="#" class="sidebar-link">My Orders</a>
                <a href="#" class="sidebar-link">Payment Methods</a>
                <a href="#" class="sidebar-link">Addresses</a>
                <a href="#" class="sidebar-link">Settings</a>
            </div>
        </div>

        <div class="main-content">
            <h1 class="main-heading">Welcome back, {{ session('user')['username'] }}!</h1>
            <p class="subheading">Here's an overview of your prescription orders and health management.</p>

            <div class="grid">
                <div class="card">
                    <div><span class="icon-box">📦</span><span class="card-number">3</span></div>
                    <div class="card-label">Active Orders</div>
                    <div class="card-sub">2 ready for pickup</div>
                </div>
                <div class="card">
                    <div><span class="icon-box">🏥</span><span class="card-number">5</span></div>
                    <div class="card-label">Preferred Pharmacies</div>
                    <div class="card-sub">All nearby</div>
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

            <div class="card" style="margin-bottom: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h2 style="font-size: 1.125rem; font-weight: bold;">Recent Orders</h2>
                    <button class="btn-primary">New Prescription</button>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Pharmacy</th>
                            <th>Medication</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#MC2025010</td>
                            <td>HealthPlus Pharmacy</td>
                            <td>Lisinopril 10mg</td>
                            <td><span class="status ready">READY</span></td>
                            <td>$24.99</td>
                            <td><button class="btn-primary">Get Directions</button></td>
                        </tr>
                        <tr>
                            <td>#MC2025011</td>
                            <td>MedExpress</td>
                            <td>Metformin 1000mg</td>
                            <td><span class="status ready">READY</span></td>
                            <td>$18.75</td>
                            <td><button class="btn-primary">Track</button></td>
                        </tr>
                        <tr>
                            <td>#MC2025009</td>
                            <td>Community Pharmacy</td>
                            <td>Vitamin D3</td>
                            <td><span class="status delivered">DELIVERED</span></td>
                            <td>$15.50</td>
                            <td><button class="btn-primary">Reorder</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="card upload-box">
                <div style="font-size: 2rem;">📷</div>
                <p><strong>Upload Prescription</strong></p>
                <p style="color: #6b7280;">Take a photo or select from gallery</p>
                <button class="btn-primary">Choose File</button>
            </div>
        </div>
    </div>

    <script>
        function toggleDropdown() {
            document.getElementById("userDropdown").classList.toggle("show");
        }

        // Close dropdown if clicked outside
        window.addEventListener('click', function(e) {
            const dropdown = document.getElementById("userDropdown");
            const avatar = document.querySelector('.avatar-btn');
            if (!dropdown.contains(e.target) && !avatar.contains(e.target)) {
                dropdown.classList.remove("show");
            }
        });
    </script>

</body>
</html>