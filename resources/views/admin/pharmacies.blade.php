<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Pharmacies | MediChain</title>
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
      <a href="{{ route('admin.pharmacies.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg bg-blue-700 text-white font-medium hover:bg-blue-600">
        🏥 Pharmacies
      </a>
      <a href="{{ route('admin.prescriptions.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100">
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
      <h1 class="text-lg font-semibold text-gray-700">Pharmacies</h1>
      <div class="flex items-center gap-3">
        <span class="text-sm text-gray-500">Hello, Admin</span>
        <div class="w-9 h-9 flex items-center justify-center rounded-full bg-brand-blue text-white font-bold">A</div>
      </div>
    </header>

    <!-- Main Section -->
    <main class="p-6 space-y-8">
      
      <!-- Add Pharmacy Form -->
      <div class="bg-white p-6 rounded-xl shadow" data-aos="fade-up">
        <h2 class="text-base font-semibold mb-4">Add New Pharmacy</h2>
        <form action="{{ route('admin.pharmacies.store') }}" method="POST" class="space-y-4">
          @csrf
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="name" placeholder="Pharmacy Name" class="border rounded p-2 focus:ring-2 focus:ring-brand-blue" required>
            <input type="text" name="user_name" placeholder="Username" class="border rounded p-2 focus:ring-2 focus:ring-brand-blue" required>
            <input type="email" name="email" placeholder="Email" class="border rounded p-2 focus:ring-2 focus:ring-brand-blue" required>
            <input type="password" name="password" placeholder="Password" class="border rounded p-2 focus:ring-2 focus:ring-brand-blue" required>
          </div>
          <button type="submit" class="bg-brand-green hover:bg-green-700 text-white px-4 py-2 rounded shadow transition-all">Add Pharmacy</button>
        </form>
      </div>

      <!-- Pharmacies Table -->
      <div class="bg-white p-6 rounded-xl shadow" data-aos="fade-up" data-aos-delay="100">
        <h2 class="text-base font-semibold mb-4">All Pharmacies</h2>
        <div class="overflow-x-auto">
          <table class="w-full text-sm text-left border">
            <thead class="bg-gray-100 border-b text-gray-600">
              <tr>
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Name</th>
                <th class="px-4 py-2">Email</th>
                <th class="px-4 py-2">Phone</th>
                <th class="px-4 py-2">Address</th>
                <th class="px-4 py-2">Registered</th>
                <th class="px-4 py-2">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($pharmacies as $p)
                <tr class="border-b hover:bg-gray-50 transition-colors">
                  <td class="px-4 py-2">{{ $p['shop_id'] ?? '-' }}</td>
                  <td class="px-4 py-2 font-medium">{{ $p['name'] ?? '-' }}</td>
                  <td class="px-4 py-2">{{ $p['email'] ?? '-' }}</td>
                  <td class="px-4 py-2">{{ $p['phone'] ?? '-' }}</td>
                  <td class="px-4 py-2">{{ $p['location'] ?? '-' }}</td>
                  <td class="px-4 py-2">{{ isset($p['created_at']) ? \Carbon\Carbon::parse($p['created_at'])->toFormattedDateString() : '-' }}</td>
                  <td class="px-4 py-2">
                    <form action="{{ route('admin.pharmacies.destroy', $p['shop_id']) }}" method="POST" onsubmit="return confirm('Delete this pharmacy?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded shadow transition-all">Delete</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="px-4 py-4 text-center text-gray-500">No pharmacies found.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </main>
  </div>

  <script>
    AOS.init({ once: true, duration: 600 });
  </script>
</body>
</html>
