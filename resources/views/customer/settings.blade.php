<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | MediChain</title>
    <link rel="stylesheet" href="{{ asset('dashstyles.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <!-- Topbar -->
    <div class="topbar">
        <div class="logo-section">
            <span class="logo-main">MediChain</span>
            <span class="logo-sub">Customer Portal</span>
        </div>

        <div class="hamburger" onclick="toggleMobileMenu()">☰</div>

        <nav class="main-nav hidden md:flex" id="mobileMenu">
            <a href="{{ url('/dashboard') }}" class="nav-item">Dashboard</a>
            <a href="{{ url('/orders') }}" class="nav-item">My Orders</a>
            <a href="{{ url('/pharmacies') }}" class="nav-item">Pharmacies</a>
            <a href="{{ url('/history') }}" class="nav-item">History</a>
        </nav>

        <div class="user-section">
            <div class="relative inline-block text-left avatar-container">
                <div class="avatar-btn w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white flex items-center justify-center font-bold cursor-pointer"
                     onclick="toggleDropdown()">
                    {{ strtoupper(substr(session('user')['full_name'] ?? session('user')['username'], 0, 1)) }}
                </div>

                <div id="userDropdown"
                     class="absolute right-0 mt-3 w-72 bg-white rounded-2xl shadow-xl border border-gray-100 hidden transform opacity-0 scale-95 transition-all duration-200">
                    <div class="flex items-center gap-3 p-4 bg-gradient-to-r from-blue-50 to-indigo-50">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white flex items-center justify-center font-bold text-lg shadow">
                            {{ strtoupper(substr(session('user')['full_name'] ?? session('user')['username'], 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">{{ session('user')['full_name'] ?? 'Guest' }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ session('user')['email'] ?? 'email@example.com' }}</p>
                        </div>
                    </div>
                    <div class="p-2">
                        <a href="/customer/profile"
                           class="flex items-center gap-2 px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-100 hover:text-blue-600 transition">
                            👤 View Profile
                        </a>
                    </div>
                    <div class="p-2 border-t border-gray-100">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="flex items-center gap-2 w-full px-4 py-2 rounded-lg text-red-600 hover:bg-red-50 transition">
                                🚪 Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="user-info">
                <div class="user-name">{{ session('user')['full_name'] }}</div>
                <div class="user-role">Patient</div>
            </div>
        </div>
    </div>

    <!-- Sidebar + Content -->
    <div style="display: flex;">
        <div class="sidebar">
            <a href="{{ url('/dashboard') }}" class="sidebar-link">Dashboard</a>
            <a href="{{ url('/orders') }}" class="sidebar-link">My Orders</a>
            <a href="{{ url('/pharmacies') }}" class="sidebar-link">Pharmacies</a>
            <a href="{{ url('/customer/settings') }}" class="sidebar-link active">Settings</a>
        </div>

        <div class="main-content p-6 flex-1">
            <h1 class="main-heading text-2xl font-semibold mb-2">Settings</h1>
            <p class="subheading text-gray-500 mb-6">Manage your account preferences, notifications, and privacy.</p>

            <!-- Alerts -->
            @if(session('success'))
                <div class="p-4 mb-4 text-green-800 bg-green-100 rounded-lg">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="p-4 mb-4 text-red-800 bg-red-100 rounded-lg">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Settings Form -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <h2 class="text-xl font-semibold text-gray-800 mb-6 border-b pb-2">Account Settings</h2>
                <form method="POST" action="{{ route('customer.settings.update') }}" class="space-y-5">
                    @csrf
                    @method('PATCH')

                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="email_notifications" id="email_notifications" class="form-checkbox h-5 w-5 text-blue-600"
                            {{ $settings['email_notifications'] ? 'checked' : '' }}>
                        <label for="email_notifications" class="text-gray-700">Email Notifications</label>
                    </div>

                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="sms_notifications" id="sms_notifications" class="form-checkbox h-5 w-5 text-blue-600"
                            {{ $settings['sms_notifications'] ? 'checked' : '' }}>
                        <label for="sms_notifications" class="text-gray-700">SMS Notifications</label>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Theme</label>
                        <select name="theme" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                            <option value="light" {{ $settings['theme'] === 'light' ? 'selected' : '' }}>Light</option>
                            <option value="dark" {{ $settings['theme'] === 'dark' ? 'selected' : '' }}>Dark</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="show_location" id="show_location" class="form-checkbox h-5 w-5 text-blue-600"
                            {{ $settings['show_location'] ? 'checked' : '' }}>
                        <label for="show_location" class="text-gray-700">Show Location</label>
                    </div>

                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="show_phone" id="show_phone" class="form-checkbox h-5 w-5 text-blue-600"
                            {{ $settings['show_phone'] ? 'checked' : '' }}>
                        <label for="show_phone" class="text-gray-700">Show Phone Number</label>
                    </div>

                    <button type="submit"
                        class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 shadow-md hover:shadow-lg transition-all">
                        💾 Save Settings
                    </button>
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
        function toggleMobileMenu() {
            const menu = document.getElementById("mobileMenu");
            menu.classList.toggle("hidden");
        }
    </script>
</body>
</html>
