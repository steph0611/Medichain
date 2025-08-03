<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediChain Customer Portal</title>
    <link rel="stylesheet" href="{{ asset('styles2.css') }}">
    <link rel="stylesheet" href="{{ asset('styles4.css') }}">
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
                <a href="#" class="sidebar-link">Upload Prescription</a>
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
                                    <button class="btn-primary" onclick="openModal({{ $pharmacy['shop_id'] }}, '{{ $pharmacy['shop_name'] }}')">
                                        Upload Prescription
                                    </button>
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

    <!-- MODAL -->
    <div id="uploadModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h1 id="modalTitle">Upload Details</h1>

            <form id="uploadForm" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Step 1 -->
                <div class="form-section">
                    <h3>Step 1: Upload Prescription Image</h3>
                    <div class="upload-box">
                        <label for="prescriptionImage" class="upload-label">
                            <div class="upload-icon">📷</div>
                            <p>Drag and drop your prescription here<br><span>or click to browse files</span></p>
                            <input type="file" name="prescription_image" id="prescriptionImage" 
                                   accept=".jpg,.jpeg,.png,.pdf" required>
                        </label>
                        <p class="file-hint">Supported formats: JPG, PNG, PDF • Max size: 10MB</p>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="form-section">
                    <h3>Step 2: Prescription Details</h3>
                    <div class="p_grid">
                        <div class="p_form-group">
                            <label>Patient Name</label>
                            <input type="text" name="patient_name" required>
                        </div>
                        <div class="p_form-group">
                            <label>Doctor Name</label>
                            <input type="text" name="doctor_name" required>
                        </div>
                        <div class="p_form-group">
                            <label>Prescription Date</label>
                            <input type="date" name="prescription_date" required>
                        </div>
                        <div class="p_form-group">
                            <label>Address</label>
                            <input type="text" name="Address" required>
                        </div>
                        <div class="p_form-group">
                            <label>Contact No.</label>
                            <input type="text" name="Phone" required>
                        </div>
                        <div class="p_form-group">
                            <label>Email</label>
                            <input type="email" name="Email" required>
                        </div>
                        <div class="p_form-group p_full-width">
                            <label>Special Instructions</label>
                            <textarea name="instructions" rows="3" placeholder="Enter any notes..."></textarea>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-primary">Submit</button>
            </form>
        </div>
    </div>

    <!-- MODAL STYLES -->
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            padding-top: 50px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.6);
        }
        .modal-content {
            background: #fff;
            margin: auto;
            padding: 30px;
            border-radius: 12px;
            width: 80%;
            max-width: 700px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            animation: fadeIn 0.3s;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: #000;
        }
        @keyframes fadeIn {
            from {opacity: 0; transform: scale(0.9);}
            to {opacity: 1; transform: scale(1);}
        }
    </style>

    <!-- SCRIPTS -->
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

        function openModal(shopId, shopName) {
            document.getElementById("uploadModal").style.display = "block";
            document.getElementById("modalTitle").innerText = "Upload Details for " + shopName;

            const form = document.getElementById("uploadForm");
            form.action = `/pharmacy/upload/${shopId}`; // or use route('pharmacy.upload.submit', shopId) in JS if needed
        }

        function closeModal() {
            document.getElementById("uploadModal").style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById("uploadModal")) {
                closeModal();
            }
        }
    </script>
</body>
</html>
