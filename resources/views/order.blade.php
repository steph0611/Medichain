<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mechain Order</title>
    <link rel="stylesheet" href="{{ asset('styles4.css') }}">
</head>
<body>

<div class="topbar">
        <div class="logo-section">
            <span class="logo-main">MediChain</span>
            <span class="logo-sub">Customer Portal</span>
        </div>
        <nav class="main-nav">
            <a href="{{ url('/dashboard') }}" class="nav-item">Dashboard</a>
            <a href="{{ url('/orders') }}" class="nav-item active">My Orders</a>
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
    <h2>Upload Prescription</h2>

    <form class="upload-form" method="POST" action="/upload-prescription" enctype="multipart/form-data">
        @csrf

        <!-- Step 1 -->
        <div class="form-section">
            <h3>Step 1: Upload Prescription Image</h3>
            <div class="upload-box">
                <label for="prescriptionImage" class="upload-label">
                    <div class="upload-icon">📷</div>
                    <p>Drag and drop your prescription here<br><span>or click to browse files</span></p>
                    <input type="file" name="prescription_image" id="prescriptionImage" accept=".jpg,.jpeg,.png,.pdf" required>
                </label>
                <p class="file-hint">Supported formats: JPG, PNG, PDF • Max size: 10MB</p>
            </div>
        </div>

        <!-- Step 2 -->
        <div class="form-section">
            <h3>Step 2: Prescription Details</h3>
            <div class="grid">
                <div class="form-group">
                    <label>Patient Name</label>
                    <input type="text" name="patient_name"required>
                </div>
                <div class="form-group">
                    <label>Doctor Name</label>
                    <input type="text" name="doctor_name" placeholder="Enter doctor's name" required>
                </div>
                <div class="form-group">
                    <label>Prescription Date</label>
                    <input type="date" name="prescription_date" required>
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="Address" required>
                </div>

                <div class="form-group">
                    <label>Contact No.</label>
                    <input type="text" name="Phone" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="text" name="Email" required>
                </div>
                
                <div class="form-group full-width">
                    <label>Special Instructions</label>
                    <textarea name="instructions" rows="3" placeholder="Enter any notes..."></textarea>
                </div>
            </div>
        </div>

        <button type="submit" class="submit-btn">Submit Prescription</button>
    </form>
</div>  

    
</body>
</html>