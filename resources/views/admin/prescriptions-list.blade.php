<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Prescriptions | {{ $pharmacy['name'] ?? 'Pharmacy' }}</title>
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


  <div class="flex-1 flex flex-col">

    <!-- Header -->
    <header class="bg-white shadow-sm px-6 py-4 flex justify-between items-center">
      <h1 class="text-lg font-semibold text-gray-700">Prescriptions - {{ $pharmacy['name'] ?? 'Pharmacy' }}</h1>
      <a href="{{ route('admin.prescriptions.index') }}" class="text-blue-600 hover:underline text-sm">← Back to pharmacies</a>
    </header>

    <!-- Main Content -->
    <main class="p-6">

      <div class="bg-white p-6 rounded-xl shadow" data-aos="fade-up">
        <h2 class="text-base font-semibold mb-4">Uploaded Prescriptions</h2>
        <div class="overflow-x-auto">
          <table class="w-full text-sm text-left border">
            <thead class="bg-gray-100 border-b text-gray-600">
              <tr>
                <th class="px-4 py-2">Prescription ID</th>
                <th class="px-4 py-2">Customer</th>
                <th class="px-4 py-2">Submitted At</th>
                <th class="px-4 py-2">Details</th>
              </tr>
            </thead>
            <tbody>
              @forelse($prescriptions as $pr)
                <tr class="border-b hover:bg-gray-50 transition-all transform hover:scale-[1.01] cursor-pointer" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                  <td class="px-4 py-2">{{ $pr['id'] ?? '-' }}</td>
                  <td class="px-4 py-2">{{ $pr['patient_name'] ?? '-' }}</td>
                  <td class="px-4 py-2">{{ isset($pr['uploaded_at']) ? \Carbon\Carbon::parse($pr['uploaded_at'])->toFormattedDateString() : '-' }}</td>
                  <td class="px-4 py-2">{{ $pr['instructions'] ?? '-' }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="px-4 py-4 text-center text-gray-500" data-aos="fade-up">No prescriptions found.</td>
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
