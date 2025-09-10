{{-- resources/views/admin/users.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Users | MediChain</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
</head>
<body class="bg-gray-50 flex min-h-screen">

  <aside class="w-64 bg-white shadow-md flex flex-col min-h-screen">
    <!-- Brand / Header -->
    <div class="px-6 py-4 text-xl font-bold text-blue-700 border-b">MediChain Admin</div>

    <!-- Navigation -->
    <nav class="flex-1 px-3 py-5 space-y-2 text-sm">
      <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100">
        📊 Dashboard
      </a>
      <a href="{{ route('admin.pharmacies.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100">
        🏥 Pharmacies
      </a>
      <a href="{{ route('admin.prescriptions.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100">
        💊 Prescriptions
      </a>
      <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg bg-blue-700 text-white font-medium hover:bg-blue-600">
        👥 Users
      </a>
      <a href="" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100">
        ⚙ Settings
      </a>
    </nav>

    <!-- Footer -->
    <div class="px-6 py-4 text-xs text-gray-500 border-t">© 2025 MediChain</div>
  </aside>

  <div class="flex-1 flex flex-col">
    <header class="bg-white shadow-sm px-6 py-4">
      <h1 class="text-lg font-semibold text-gray-700">Users Overview</h1>
    </header>

    <main class="p-6">
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">

        {{-- Admins Tile --}}
        <a href="{{ route('admin.users.show', 'admins') }}" class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl transition-all text-center" data-aos="fade-up">
          <h2 class="text-lg font-bold text-gray-700">Admins</h2>
          <p class="text-sm text-gray-500 mt-2">View all admins</p>
        </a>

        {{-- Pharmacies Tile --}}
        <a href="{{ route('admin.users.show', 'pharmacies') }}" class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl transition-all text-center" data-aos="fade-up" data-aos-delay="50">
          <h2 class="text-lg font-bold text-gray-700">Pharmacies</h2>
          <p class="text-sm text-gray-500 mt-2">View all pharmacies</p>
        </a>

        {{-- Customers Tile --}}
        <a href="{{ route('admin.users.show', 'customers') }}" class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl transition-all text-center" data-aos="fade-up" data-aos-delay="100">
          <h2 class="text-lg font-bold text-gray-700">Customers</h2>
          <p class="text-sm text-gray-500 mt-2">View all customers</p>
        </a>

      </div>
    </main>
  </div>

<script>
  AOS.init({ once: true, duration: 600 });
</script>
</body>
</html>
