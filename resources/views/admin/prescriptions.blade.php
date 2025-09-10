<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Prescriptions | MediChain</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- AOS Animations -->
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: {
              blue: '#2563eb',
              purple: '#7c3aed',
              green: '#22c55e',
              orange: '#f97316'
            }
          }
        }
      }
    }
  </script>
</head>
<body class="bg-gray-50 flex min-h-screen">

  <!-- Sidebar -->
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
      <a href="{{ route('admin.prescriptions.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg bg-blue-700 text-white font-medium hover:bg-blue-600">
        💊 Prescriptions
      </a>
      <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100">
        👥 Users
      </a>
      <a href="" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100">
        ⚙ Settings
      </a>
    </nav>

    <!-- Footer -->
    <div class="px-6 py-4 text-xs text-gray-500 border-t">© 2025 MediChain</div>
  </aside>


  <!-- Main Content -->
  <div class="flex-1 flex flex-col">

    <!-- Topbar -->
    <header class="bg-white shadow-sm px-6 py-4 flex justify-between items-center">
      <h1 class="text-lg font-semibold text-gray-700">Prescriptions</h1>
      <div class="flex items-center gap-3">
        <span class="text-sm text-gray-500">Hello, Admin</span>
        <div class="w-9 h-9 flex items-center justify-center rounded-full bg-brand-blue text-white font-bold">A</div>
      </div>
    </header>

    <!-- Main Section -->
    <main class="p-6 space-y-8">

      <!-- Error Message -->
      @if(!empty($error))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" data-aos="fade-down">
          {{ $error }}
        </div>
      @endif

      <!-- Pharmacy Tiles -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($pharmacies as $p)
          <a href="{{ route('admin.prescriptions.show', $p['shop_id']) }}"
             class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl transition-all text-center transform hover:-translate-y-1 hover:scale-105"
             data-aos="fade-up"
             data-aos-delay="{{ $loop->index * 100 }}">
            <h2 class="text-lg font-bold text-gray-700">{{ $p['name'] }}</h2>
            <p class="text-sm text-gray-500 mt-2">View prescriptions</p>
          </a>
        @empty
          <div class="col-span-full text-center text-gray-500" data-aos="fade-up">
            No pharmacies found.
          </div>
        @endforelse
      </div>

    </main>
  </div>

  <script>
    AOS.init({ once: true, duration: 600 });
  </script>
</body>
</html>
