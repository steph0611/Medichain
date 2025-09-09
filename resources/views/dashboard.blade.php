<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="{{ asset('dashstyles.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
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
            <div class="relative inline-block text-left avatar-container">
                <!-- Avatar Button -->
                <div 
                    class="avatar-btn w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 
                        text-white flex items-center justify-center font-bold cursor-pointer 
                        hover:scale-105 hover:shadow-md transition-all duration-200"
                    onclick="toggleDropdown()"
                >
                    {{ strtoupper(substr(session('user')['full_name'] ?? session('user')['username'], 0, 1)) }}
                </div>

                <!-- Dropdown -->
                <div 
                    id="userDropdown"
                    class="absolute right-0 mt-3 w-72 bg-white rounded-2xl shadow-xl border border-gray-100 
                        overflow-hidden hidden transform opacity-0 scale-95 transition-all duration-200"
                >
                    <!-- Header -->
                    <div class="flex items-center gap-3 p-4 bg-gradient-to-r from-blue-50 to-indigo-50">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 
                                    text-white flex items-center justify-center font-bold text-lg shadow">
                            {{ strtoupper(substr(session('user')['full_name'] ?? session('user')['username'], 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">
                                {{ session('user')['full_name'] ?? 'Guest' }}
                            </p>
                            <p class="text-xs text-gray-500 truncate">
                                {{ session('user')['email'] ?? 'email@example.com' }}
                            </p>
                        </div>
                    </div>

                    <!-- Links -->
                    <div class="p-2">
                        <a href="/profile" 
                        class="flex items-center gap-2 px-4 py-2 rounded-lg text-gray-700 
                                hover:bg-gray-100 hover:text-blue-600 transition">
                            👤 View Profile
                        </a>
                        <a href="/settings" 
                        class="flex items-center gap-2 px-4 py-2 rounded-lg text-gray-700 
                                hover:bg-gray-100 hover:text-blue-600 transition">
                            ⚙️ Settings
                        </a>
                    </div>

                    <!-- Logout -->
                    <div class="p-2 border-t border-gray-100">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" 
                                    class="flex items-center gap-2 w-full px-4 py-2 rounded-lg 
                                        text-red-600 hover:bg-red-50 transition">
                                🚪 Logout
                            </button>
                        </form>
                    </div>
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

       

    <script>
         function toggleDropdown() {
            const dropdown = document.getElementById("userDropdown");
            dropdown.classList.toggle("hidden");
            dropdown.classList.toggle("opacity-0");
            dropdown.classList.toggle("scale-95");
        }

        // Close dropdown when clicking outside
        window.addEventListener('click', function (e) {
            const dropdown = document.getElementById("userDropdown");
            const avatar = document.querySelector('.avatar-btn');
            if (dropdown && !dropdown.contains(e.target) && !avatar.contains(e.target)) {
                dropdown.classList.add("hidden", "opacity-0", "scale-95");
                dropdown.classList.remove("opacity-100", "scale-100");
            }
        });

        // -------------------------
        // Mobile Menu
        // -------------------------
        function toggleMobileMenu() {
            const menu = document.getElementById("mobileMenu");
            menu.classList.toggle("hidden");
        }

        // -------------------------
        // Modal (Upload Prescription)
        // -------------------------
        function openModal(shopId, shopName) {
            const modal = document.getElementById("uploadModal");
            modal.classList.remove("hidden", "opacity-0", "scale-95");
            modal.classList.add("opacity-100", "scale-100");

            document.getElementById("modalTitle").innerText = "Upload Details for " + shopName;
            document.getElementById("uploadForm").action = `/pharmacy/upload/${shopId}`;
        }

        function closeModal() {
            const modal = document.getElementById("uploadModal");
            modal.classList.add("opacity-0", "scale-95");
            setTimeout(() => {
                modal.classList.add("hidden");
            }, 200);
        }

        // Close modal when clicking outside
        window.onclick = function (event) {
            const modal = document.getElementById("uploadModal");
            if (event.target === modal) {
                closeModal();
            }
        };
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
