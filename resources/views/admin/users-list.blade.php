{{-- resources/views/admin/users-list.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>{{ ucfirst($userType ?? 'Users') }} | MediChain</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">

  <!-- Tailwind & AOS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
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
    <header class="bg-white shadow-sm px-6 py-4 flex justify-between items-center">
      <h1 class="text-lg font-semibold text-gray-700">{{ ucfirst($userType ?? 'Users') }}</h1>
      <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:underline text-sm">← Back to overview</a>
    </header>

    <main class="p-6 space-y-6">

      <!-- Success & Error Messages -->
      @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" data-aos="fade-down">
          {{ session('success') }}
        </div>
      @endif
      @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" data-aos="fade-down">
          {{ implode(' | ', $errors->all()) }}
        </div>
      @endif

      <!-- Add New User Form -->
      <div class="bg-white p-6 rounded-xl shadow" data-aos="fade-up">
        <h2 class="text-base font-semibold mb-4">Add New {{ ucfirst($userType ?? 'User') }}</h2>
        <form action="{{ route('admin.users.store', strtolower($userType)) }}" method="POST" class="space-y-4">
          @csrf
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="name" placeholder="Name" class="border rounded p-2" required>
            <input type="text" name="username" placeholder="Username" class="border rounded p-2" required>
            <input type="email" name="email" placeholder="Email" class="border rounded p-2" required>
            <input type="password" name="password" placeholder="Password" class="border rounded p-2" required>
          </div>
          <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Add {{ ucfirst($userType ?? 'User') }}</button>
        </form>
      </div>

      <!-- Users Table -->
      <div class="bg-white p-6 rounded-xl shadow" data-aos="fade-up">
        <h2 class="text-base font-semibold mb-4">All {{ ucfirst($userType ?? 'Users') }}</h2>
        <div class="overflow-x-auto">
          <table class="w-full text-sm text-left border">
            <thead class="bg-gray-100 border-b text-gray-600">
              <tr>
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Name</th>
                <th class="px-4 py-2">Username / Email</th>
                <th class="px-4 py-2">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($users as $u)
                @php
                  // Determine the correct ID and name fields dynamically
                  $idField = $u['id'] ?? $u['shop_id'] ?? $u['customer_id'] ?? null;
                  $nameField = $u['name'] ?? $u['shop_name'] ?? '-';
                  $usernameOrEmail = $u['username'] ?? $u['email'] ?? '-';
                @endphp
                <tr class="border-b hover:bg-gray-50 transition-all transform hover:scale-[1.01] cursor-pointer" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                  <td class="px-4 py-2">{{ $idField ?? '-' }}</td>
                  <td class="px-4 py-2 font-medium">{{ $nameField }}</td>
                  <td class="px-4 py-2">{{ $usernameOrEmail }}</td>
                  <td class="px-4 py-2">
                    @if($idField)
                    <form action="{{ route('admin.users.destroy', [strtolower($userType), $idField]) }}" method="POST" onsubmit="return confirm('Delete this {{ ucfirst($userType) }}?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">Delete</button>
                    </form>
                    @else
                      <span class="text-gray-400">N/A</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="px-4 py-4 text-center text-gray-500">No {{ $userType ?? 'users' }} found.</td>
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
