<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Pharmacies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f9fafb;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 40px;
        }
        table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        th {
            background: #f3f4f6;
            color: #374151;
            text-align: center;
        }
        td {
            vertical-align: middle;
            text-align: center;
        }
        .btn-primary {
            background-color: #2563eb;
            border: none;
            padding: 6px 14px;
            border-radius: 6px;
            transition: 0.2s;
        }
        .btn-primary:hover {
            background-color: #1e40af;
        }
        .empty-row {
            text-align: center;
            color: #6b7280;
        }
    </style>
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

       
    </style>
</head>
<body>

    <div class="topbar">
        <div class="logo-section">
            <span class="logo-main">MediChain</span>
            <span class="logo-sub">Customer Portal</span>
        </div>

        <div class="hamburger" onclick="toggleMobileMenu()">☰</div>

        <nav class="main-nav" id="mobileMenu">
            <a href="{{ url('/dashboard') }}" class="nav-item ">Dashboard</a>
            <a href="{{ url('/orders') }}" class="nav-item">My Orders</a>
            <a href="{{ url('/pharmacies') }}" class="nav-item active">Pharmacies</a>
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
                <a href="{{ url('/dashboard') }}" class="sidebar-link">Dashboard</a>
                <a href="{{ url('/orders') }}" class="sidebar-link">My Orders</a>
                <a href="{{ url('/pharmacies') }}" class="sidebar-link active">Pharmacies</a>
                <a href="{{ url('/customer/profile') }}" class="sidebar-link">My Profile</a>
        </div>
<div class="container">
    <h2 class="mb-4">Available Pharmacies</h2>

    <table id="allPharmaciesTable" class="table table-bordered table-hover">
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
            @forelse($pharmacies as $pharmacy)
                <tr>
                    <td>{{ $pharmacy['shop_name'] }}</td>
                    <td>{{ $pharmacy['location'] ?? 'N/A' }}</td>
                    <td>{{ $pharmacy['phone'] ?? 'N/A' }}</td>
                    <td>{{ $pharmacy['city'] ?? 'N/A' }}</td>
                    <td>
                        @if(!empty($pharmacy['distance']))
                            {{ number_format($pharmacy['distance'], 2) }} km
                        @else
                            <span style="color: #888;">unknown</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-primary"
                                onclick="openModal({{ $pharmacy['shop_id'] }}, '{{ $pharmacy['shop_name'] }}')">
                            Upload Prescription
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty-row">No pharmacies available.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    function openModal(shopId, shopName) {
        alert("Open upload modal for " + shopName + " (ID: " + shopId + ")");
        // Replace alert with your modal logic
    }
</script>

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

</body>
</html>
