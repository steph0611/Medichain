<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
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

            <div class="card" style="margin-bottom: 1.5rem;">

                <!-- Tabs -->
                <div style="display: flex; border-bottom: 2px solid #e5e7eb; margin-bottom: 1rem;">
                    <button id="nearbyTab" class="tab-btn active" onclick="showTab('nearby')">
                        Pharmacies in {{ session('user')['city'] }}
                    </button>
                    <button id="allTab" class="tab-btn" onclick="showTab('all')">
                        All Pharmacies
                    </button>
                </div>

                <!-- Nearby Pharmacies Tab -->
                <div id="nearbySection">
                    <h2 class="tab-heading">Available Pharmacies in {{ session('user')['city'] }}</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Pharmacy</th>
                                <th>Location</th>
                                <th>Phone</th>
                                <th>Distance</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pharmacies as $pharmacy)
                                <tr>
                                    <td>{{ $pharmacy['shop_name'] }}</td>
                                    <td>{{ $pharmacy['location'] }}, {{ $pharmacy['city'] }}</td>
                                    <td>{{ $pharmacy['phone'] ?? 'N/A' }}</td>
                                    <td>
                                        @if(!is_null($pharmacy['distance']))
                                            {{ number_format($pharmacy['distance'], 2) }} km
                                        @else
                                            <span style="color: #888;">unknown</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn-primary" onclick="openModal({{ $pharmacy['shop_id'] }}, '{{ $pharmacy['shop_name'] }}')">
                                            Upload Prescription
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align:center; color: #6b7280;">
                                        No pharmacies available in {{ session('user')['city'] }}.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- All Pharmacies Tab -->
                <div id="allSection" style="display: none;">
                    <h2 class="tab-heading">All Pharmacies</h2>

                    <!-- 🔎 Search Bar -->
                    <input 
                        type="text" 
                        id="pharmacySearch" 
                        placeholder="Search by name, city, or location..." 
                        style="width: 100%; padding: 0.5rem; margin-bottom: 1rem; border: 1px solid #d1d5db; border-radius: 0.375rem;"
                    >

                    <table id="allPharmaciesTable">
                        <thead>
                            <tr>
                                <th>Pharmacy</th>
                                <th>Location</th>
                                <th>Phone</th>
                                <th>City</th>
                                <th>Distance</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allPharmacies as $pharmacy)
                                <tr>
                                    <td>{{ $pharmacy['shop_name'] }}</td>
                                    <td>{{ $pharmacy['location'] }}</td>
                                    <td>{{ $pharmacy['phone'] ?? 'N/A' }}</td>
                                    <td>{{ $pharmacy['city'] }}</td>
                                    <td>
                                        @if(!is_null($pharmacy['distance']))
                                            {{ number_format($pharmacy['distance'], 2) }} km
                                        @else
                                            <span style="color: #888;">unknown</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn-primary" onclick="openModal({{ $pharmacy['shop_id'] }}, '{{ $pharmacy['shop_name'] }}')">
                                            Upload Prescription
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align:center; color: #6b7280;">
                                        No pharmacies available.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL -->
    <div id="uploadModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>

            <!-- Dynamic Title -->
            <h1 id="modalTitle">Upload Prescription</h1>
            <p>Please upload your prescription image or PDF. Our team will review and confirm your order.</p>

            <!-- Upload Form -->
            <form id="uploadForm" action="/upload" method="POST" enctype="multipart/form-data">
                @csrf

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

                    <!-- Preview section -->
                    <div id="imagePreview" class="preview-container"></div>
                </div>
            

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
    </div>

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
            max-height: 90vh;     /* take up to 90% of viewport height */
            overflow-y: auto;     /* enable vertical scroll */
            border-radius: 12px;
        }
        .modal-body {
            overflow-y: auto;      /* scroll only inside this wrapper */
            max-height: 70vh;      /* adjust to prevent modal from stretching */
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

        body {
        font-family: Arial, Helvetica, sans-serif;
        background-color: #f3f4f6;
        color: #1f2937;
        margin: 0;
        }

        a {
        text-decoration: none;
        color: inherit;
        }

        .bg-blue-900 {
        background-color: #1e3a8a;
        }

        .bg-blue-700 {
        background-color: #1d4ed8;
        }

        .text-white {
        color: #ffffff;
        }

        .topbar {
            background: linear-gradient(90deg, #1e3a8a, #2563eb);
            color: white;
            padding: 0.75rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: Arial, sans-serif;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .logo-section {
            display: flex;
            flex-direction: column;
            font-size: 1rem;
        }

        .logo-main {
            font-weight: bold;
            font-size: 1.25rem;
        }

        .logo-sub {
            font-size: 0.75rem;
            color: #cbd5e1;
            margin-top: -0.2rem;
        }

        .main-nav {
            display: flex;
            gap: 1.5rem;
        }

        .nav-item {
            padding: 0.4rem 1rem;
            border-radius: 6px;
            color: white;
            font-weight: 500;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background-color 0.3s ease;
        }

        .nav-item:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .nav-item.active {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .user-section {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .avatar-container {
        position: relative;
        display: inline-block;
        }

        .avatar-btn {
        width: 40px;
        height: 40px;
        background-color: #0078d4;
        color: white;
        border-radius: 50%;
        font-weight: bold;
        font-size: 1.2em;
        text-align: center;
        line-height: 40px;
        cursor: pointer;
        user-select: none;
        }

        .dropdown {
        display: none;
        position: absolute;
        right: 0;
        top: 50px;
        width: 260px;
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 6px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        z-index: 100;
        }

        .dropdown.show {
        display: block;
        }

        .dropdown-header {
        padding: 15px;
        border-bottom: 1px solid #eee;
        display: flex;
        align-items: center;
        }

        .dropdown-avatar {
        width: 50px;
        height: 50px;
        background-color: #d1eaff;
        color: #333;
        border-radius: 50%;
        font-weight: bold;
        font-size: 1.4em;
        text-align: center;
        line-height: 50px;
        margin-right: 12px;
        }

        .dropdown-user-info {
        display: flex;
        flex-direction: column;
        }

        .dropdown-user-info span {
        margin: 2px 0;
        }

        .dropdown-link {
        padding: 10px 15px;
        display: block;
        color: #333;
        text-decoration: none;
        }

        .dropdown-link:hover {
        background-color: #f5f5f5;
        }

        .dropdown-divider {
        height: 1px;
        background-color: #eee;
        margin: 5px 0;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            font-size: 0.8rem;
        }

        .user-name {
            font-weight: 500;
        }

        .user-role {
            font-size: 0.75rem;
            color: #cbd5e1;
            margin-top: -2px;
        }

        .sidebar {
        background-color: #ffffff;
        width: 200px;
        padding: 2rem 1rem;
        box-shadow: 1px 0 3px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        height: calc(100vh - 64px);
        }

        .sidebar-link {
        display: block;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        margin-bottom: 0.5rem;
        color: #374151;
        }

        .sidebar-link.active,
        .sidebar-link:hover {
        background-color: #e0f2fe;
        color: #1d4ed8;
        font-weight: 600;
        }

        .card {
        background: #ffffff;
        border-radius: 8px;
        padding: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .card-number {
        font-size: 1.5rem;
        font-weight: bold;
        }

        .card-label {
        color: #6b7280;
        }

        .card-sub {
        font-size: 0.875rem;
        color: #10b981;
        }

        .icon-box {
        font-size: 1.75rem;
        background-color: #f3f4f6;
        padding: 0.5rem;
        border-radius: 8px;
        display: inline-block;
        margin-right: 0.75rem;
        }

        .grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
        }

        table {
        width: 100%;
        border-collapse: collapse;
        }

        th, td {
        text-align: left;
        padding: 0.5rem;
        }

        th {
        color: #374151;
        font-weight: 600;
        }

        td {
        color: #374151;
        }

        tr {
        border-bottom: 1px solid #e5e7eb;
        }

        .status {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: bold;
        color: white;
        }

        .status.ready {
        background-color: #10b981;
        }

        .status.delivered {
        background-color: #3b82f6;
        }

        .btn-primary {
        background-color: #2563eb;
        color: white;
        padding: 0.4rem 0.8rem;
        font-size: 0.875rem;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        }

        .btn-primary:hover {
        background-color: #1d4ed8;
        }

        .upload-box {
        border: 2px dashed #d1d5db;
        padding: 2rem;
        border-radius: 0.75rem;
        text-align: center;
        background-color: #fafafa;
        }

        .main-content {
            flex: 1;
            padding: 2rem;
            max-width: 960px;
            margin: 0 auto;
        }
        .main-heading {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0.25rem;
        }
        .subheading {
            color: #6b7280;
            margin-bottom: 1.5rem;
        }

        .hamburger {
        display: none;
        }


        @media (max-width: 768px) {
        /* Topbar becomes vertical */
        .topbar {
            flex-direction: column;
            align-items: flex-start;
            padding: 1rem;
        }

        .logo-section {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .hamburger {
            align-self: flex-start;
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .user-section {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 0.75rem;
            margin-top: 0.5rem;
            width: 100%;
        }

        .avatar-container {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
            font-size: 0.9rem;
        }


        .hamburger {
        display: block;
        font-size: 1.5rem;
        cursor: pointer;
        color: white;
        }

        .main-nav {
        display: none;
        flex-direction: column;
        width: 100%;
        margin-top: 1rem;
        }

        .main-nav.show {
        display: flex;
        }


        .nav-item {
        width: 100%;
        text-align: left;
        }

        /* Sidebar collapses to horizontal nav or hides */
        .sidebar {
        display: none;
        }

        /* Main content takes full width */
        .main-content {
        width: 100%;
        padding: 1rem;
        }

        /* Cards in dashboard stack vertically */
        .grid {
        grid-template-columns: 1fr;
        gap: 1rem;
        }

        /* Recent orders table becomes vertical cards */
        table,
        thead,
        tbody,
        th,
        td,
        tr {
        display: block;
        width: 100%;
        }

        thead {
        display: none;
        }

        tr {
        margin-bottom: 1rem;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 0.75rem;
        background-color: white;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        td {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        font-size: 0.875rem;
        border-bottom: 1px solid #f3f4f6;
        }

        td:last-child {
        border-bottom: none;
        }

        /* Action buttons full width */
        .btn-primary,
        .actions-column .btn-primary {
        width: 100%;
        margin-top: 0.5rem;
        }

        .upload-box {
        padding: 1rem;
        }

        .main-heading {
        font-size: 1.25rem;
        }

        .subheading {
        font-size: 0.9rem;
        }

        /* Avatar section becomes vertical */
        .user-section {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
        }

        .dropdown {
        width: 100%;
        left: 0;
        right: 0;
        }
        }
        body {
        font-family: Arial, Helvetica, sans-serif;
        background-color: #f3f4f6;
        color: #1f2937;
        margin: 0;
        }

        a {
        text-decoration: none;
        color: inherit;
        }

        .bg-blue-900 {
        background-color: #1e3a8a;
        }

        .bg-blue-700 {
        background-color: #1d4ed8;
        }

        .text-white {
        color: #ffffff;
        }

        .topbar {
            background: linear-gradient(90deg, #1e3a8a, #2563eb);
            color: white;
            padding: 0.75rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: Arial, sans-serif;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .logo-section {
            display: flex;
            flex-direction: column;
            font-size: 1rem;
        }

        .logo-main {
            font-weight: bold;
            font-size: 1.25rem;
        }

        .logo-sub {
            font-size: 0.75rem;
            color: #cbd5e1;
            margin-top: -0.2rem;
        }

        .main-nav {
            display: flex;
            gap: 1.5rem;
        }

        .nav-item {
            padding: 0.4rem 1rem;
            border-radius: 6px;
            color: white;
            font-weight: 500;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background-color 0.3s ease;
        }

        .nav-item:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .nav-item.active {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .user-section {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .avatar-container {
        position: relative;
        display: inline-block;
        }

        .avatar-btn {
        width: 40px;
        height: 40px;
        background-color: #0078d4;
        color: white;
        border-radius: 50%;
        font-weight: bold;
        font-size: 1.2em;
        text-align: center;
        line-height: 40px;
        cursor: pointer;
        user-select: none;
        }

        .dropdown {
        display: none;
        position: absolute;
        right: 0;
        top: 50px;
        width: 260px;
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 6px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        z-index: 100;
        }

        .dropdown.show {
        display: block;
        }

        .dropdown-header {
        padding: 15px;
        border-bottom: 1px solid #eee;
        display: flex;
        align-items: center;
        }

        .dropdown-avatar {
        width: 50px;
        height: 50px;
        background-color: #d1eaff;
        color: #333;
        border-radius: 50%;
        font-weight: bold;
        font-size: 1.4em;
        text-align: center;
        line-height: 50px;
        margin-right: 12px;
        }

        .dropdown-user-info {
        display: flex;
        flex-direction: column;
        }

        .dropdown-user-info span {
        margin: 2px 0;
        }

        .dropdown-link {
        padding: 10px 15px;
        display: block;
        color: #333;
        text-decoration: none;
        }

        .dropdown-link:hover {
        background-color: #f5f5f5;
        }

        .dropdown-divider {
        height: 1px;
        background-color: #eee;
        margin: 5px 0;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            font-size: 0.8rem;
        }

        .user-name {
            font-weight: 500;
        }

        .user-role {
            font-size: 0.75rem;
            color: #cbd5e1;
            margin-top: -2px;
        }

        .sidebar {
        background-color: #ffffff;
        width: 200px;
        padding: 2rem 1rem;
        box-shadow: 1px 0 3px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        height: calc(100vh - 64px);
        }

        .sidebar-link {
        display: block;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        margin-bottom: 0.5rem;
        color: #374151;
        }

        .sidebar-link.active,
        .sidebar-link:hover {
        background-color: #e0f2fe;
        color: #1d4ed8;
        font-weight: 600;
        }

        .main-content {
            flex: 1;
            padding: 2rem;
            max-width: 960px;
            margin: 0 auto;
        }
        .main-content {
            flex: 1;
            padding: 2rem;
            background: #f9f9f9;
        }

        .upload-form {
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0,0,0,0.05);
        }

        .form-section {
            margin-bottom: 2rem;
        }

        .form-section h3 {
            margin-bottom: 1rem;
            font-size: 1.2rem;
            color: #333;
        }

        .upload-box {
            border: 2px dashed #ccc;
            padding: 2rem;
            text-align: center;
            background: #fefefe;
            border-radius: 8px;
            position: relative;
        }

        .upload-label {
            display: block;
            cursor: pointer;
            color: #555;
        }

        .upload-label input[type="file"] {
            display: none;
        }

        .upload-icon {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .file-hint {
            margin-top: 0.5rem;
            font-size: 0.85rem;
            color: #888;
        }

        .p_grid {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .p_form-group {
            flex: 1 1 45%;
            display: flex;
            flex-direction: column;
        }

        .p_full-width {
            flex: 1 1 100%;
        }

        .form-group label {
            margin-bottom: 0.4rem;
            font-weight: 500;
        }

        input, select, textarea {
            padding: 0.6rem;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1rem;
        }

        .submit-btn {
            background-color: #3b82f6;
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            font-size: 1rem;
            border-radius: 6px;
            cursor: pointer;
        }

        .submit-btn:hover {
            background-color: #2563eb;
        }
       
        .upload-box {
            border: 2px dashed #007bff;
            padding: 20px;
            margin-top: 20px;
            border-radius: 10px;
            cursor: pointer;
        }
        .upload-label {
            display: block;
            cursor: pointer;
        }
        .upload-label input {
            display: none;
        }
        .file-hint {
            font-size: 0.9em;
            color: #666;
            margin-top: 5px;
        }
        .preview-container {
            margin-top: 1rem;
            max-height: 250px;   /* control height */
            overflow-y: auto;    /* scroll vertically if content is large */
            border: 1px solid #ddd;
            padding: 8px;
            border-radius: 8px;
            background: #fafafa;
        }

        .preview-container img {
            max-width: 100%;
            margin-bottom: 10px;
            border-radius: 6px;
        }
        .remove-btn {
            margin-top: 10px;
            background: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .submit-btn {
            margin-top: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .tab-btn {
            flex: 1;
            padding: 0.75rem 1rem;
            text-align: center;
            font-weight: 600;
            color: #6b7280;
            border: none;
            background: none;
            cursor: pointer;
            transition: color 0.2s, border-bottom 0.2s;
        }
        .tab-btn:hover {
            color: #111827;
        }
        .tab-btn.active {
            color: #2563eb; /* Blue highlight */
            border-bottom: 3px solid #2563eb;
        }
        .tab-heading {
            font-size: 1.125rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: scale(0.9);}
            to {opacity: 1; transform: scale(1);}
        }
    </style>

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
            form.action = `/pharmacy/upload/${shopId}`;
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
    <script>
    document.getElementById("prescriptionImage").addEventListener("change", function(event) {
        const previewContainer = document.getElementById("imagePreview");
        previewContainer.innerHTML = ""; // Clear old preview

        const file = event.target.files[0];
        if (!file) return;

        if (file.type.startsWith("image/")) {
            // Show image preview
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement("img");
                img.src = e.target.result;
                previewContainer.appendChild(img);
            };
            reader.readAsDataURL(file);
        } else if (file.type === "application/pdf") {
            // Show PDF placeholder
            previewContainer.innerHTML = `<p style="color:#555;">📄 PDF selected: ${file.name}</p>`;
        } else {
            previewContainer.innerHTML = `<p style="color:red;">Unsupported file format</p>`;
            return;
        }

        // Add remove button
        const removeBtn = document.createElement("button");
        removeBtn.type = "button";
        removeBtn.textContent = "Remove File";
        removeBtn.className = "remove-btn";
        removeBtn.onclick = function() {
            document.getElementById("prescriptionImage").value = "";
            previewContainer.innerHTML = "";
        };
        previewContainer.appendChild(removeBtn);
    });
    </script>
    <script>
    function showTab(tab) {
        // Sections
        document.getElementById('nearbySection').style.display = (tab === 'nearby') ? 'block' : 'none';
        document.getElementById('allSection').style.display = (tab === 'all') ? 'block' : 'none';

        // Tabs
        document.getElementById('nearbyTab').classList.toggle('active', tab === 'nearby');
        document.getElementById('allTab').classList.toggle('active', tab === 'all');
    }
    </script>
    <script>
    document.getElementById('pharmacySearch').addEventListener('keyup', function() {
        let searchValue = this.value.toLowerCase();
        let rows = document.querySelectorAll('#allPharmaciesTable tbody tr');

        rows.forEach(row => {
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(searchValue) ? '' : 'none';
        });
    });
    </script>

</body>
</html>
