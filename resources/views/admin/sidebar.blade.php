<aside class="w-64 bg-white shadow-md flex flex-col min-h-screen">
    <div class="px-6 py-4 text-xl font-bold text-blue-700 border-b">MediChain Admin</div>
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
        <a href="" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100">
            👥 Users
        </a>
        <a href="" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100">
            ⚙ Settings
        </a>
    </nav>
    <div class="px-6 py-4 text-xs text-gray-500 border-t">© 2025 MediChain</div>
</aside>
