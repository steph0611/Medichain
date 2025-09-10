<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Settings | MediChain</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex min-h-screen">

    @include('admin.sidebar')

    <div class="flex-1 flex flex-col">
        <header class="bg-white shadow-sm px-6 py-4 flex justify-between items-center">
            <h1 class="text-lg font-semibold text-gray-700">Admin Settings</h1>
        </header>

        <main class="p-6">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ implode(' | ', $errors->all()) }}
                </div>
            @endif

            <div class="bg-white p-6 rounded-xl shadow">
                <h2 class="text-base font-semibold mb-4">Update Info</h2>
                <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="text" name="name" value="{{ $admin['full_name'] ?? '' }}" placeholder="Name" class="border rounded p-2" required>
                        <input type="text" name="username" value="{{ $admin['username'] ?? '' }}" placeholder="Username" class="border rounded p-2" required>
                        <input type="email" name="email" value="{{ $admin['email'] ?? '' }}" placeholder="Email" class="border rounded p-2" required>
                        <input type="password" name="password" placeholder="Password (leave blank to keep current)" class="border rounded p-2">
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Update</button>
                </form>

                <form action="{{ route('admin.settings.logout') }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Logout</button>
                </form>
            </div>

        </main>
    </div>

</body>
</html>
