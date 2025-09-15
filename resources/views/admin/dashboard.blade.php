{{-- resources/views/admin/dashboard.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin Dashboard | MediChain</title>

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
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

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
  <!-- AOS Animations -->
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
</head>
<body class="bg-gray-50 flex min-h-screen">

  <!-- Sidebar -->
  <aside class="w-64 bg-white shadow-md flex flex-col min-h-screen">
    <div class="px-6 py-4 text-xl font-bold text-blue-700 border-b">MediChain Admin</div>

    <nav class="flex-1 px-3 py-5 space-y-2 text-sm">
      <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg bg-blue-700 text-white font-medium hover:bg-blue-600">
        📊 Dashboard
      </a>
      <a href="{{ route('admin.pharmacies.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100">🏥 Pharmacies</a>
      <a href="{{ route('admin.prescriptions.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100">💊 Prescriptions</a>
      <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100">👥 Users</a>
      <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100">⚙ Settings</a>
    </nav>

    <div class="px-6 py-4 text-xs text-gray-500 border-t">© 2025 MediChain</div>
  </aside>

  <!-- Main Content -->
  <div class="flex-1 flex flex-col">
    <header class="bg-white shadow-sm px-6 py-4 flex justify-between items-center">
      <h1 class="text-lg font-semibold text-gray-700">Admin Dashboard</h1>
      <div class="flex items-center gap-3">
        <span class="text-sm text-gray-500">Hello, Admin</span>
        <div class="w-9 h-9 flex items-center justify-center rounded-full bg-brand-blue text-white font-bold">A</div>
      </div>
    </header>

    <main class="p-6 space-y-8">
      <!-- Stat Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-5 rounded-xl shadow" data-aos="fade-up">
          <p class="text-sm text-gray-500">Total Pharmacies</p>
          <h2 id="total-pharmacies" class="text-3xl font-bold text-brand-green mt-2">—</h2>
          <span class="text-xs text-gray-400">Registered</span>
        </div>

        <div class="bg-white p-5 rounded-xl shadow" data-aos="fade-up" data-aos-delay="100">
          <p class="text-sm text-gray-500">Total Prescriptions</p>
          <h2 id="total-prescriptions" class="text-3xl font-bold text-brand-blue mt-2">—</h2>
          <span class="text-xs text-gray-400">Submitted</span>
        </div>

        <div class="bg-white p-5 rounded-xl shadow" data-aos="fade-up" data-aos-delay="200">
          <p class="text-sm text-gray-500">Active Users</p>
          <h2 id="active-users" class="text-3xl font-bold text-brand-purple mt-2">—</h2>
          <span class="text-xs text-gray-400">This month</span>
        </div>

        <div class="bg-white p-5 rounded-xl shadow" data-aos="fade-up" data-aos-delay="300">
          <p class="text-sm text-gray-500">Monthly Income</p>
          <h2 id="monthly-income" class="text-3xl font-bold text-brand-orange mt-2">—</h2>
          <span class="text-xs text-gray-400">This month</span>
        </div>
      </div>

      <!-- Charts Row -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-xl shadow" data-aos="zoom-in">
          <h3 class="text-base font-semibold mb-3">Pharmacies Registered (Monthly)</h3>
          <canvas id="pharmChart" height="200"></canvas>
        </div>
        <div class="bg-white p-6 rounded-xl shadow" data-aos="zoom-in" data-aos-delay="100">
          <h3 class="text-base font-semibold mb-3">Prescriptions Submitted (Monthly)</h3>
          <canvas id="presChart" height="200"></canvas>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-xl shadow" data-aos="fade-up">
          <h3 class="text-base font-semibold mb-3">Customers Registered (Monthly)</h3>
          <canvas id="customersChart" height="200"></canvas>
        </div>
        <div class="bg-white p-6 rounded-xl shadow" data-aos="fade-up" data-aos-delay="150">
          <h3 class="text-base font-semibold mb-3">Income Per Month</h3>
          <canvas id="incomeChart" height="200"></canvas>
        </div>
      </div>

      <div class="bg-white p-6 rounded-xl shadow" data-aos="fade-up">
        <h3 class="text-base font-semibold mb-4">Recent Activity</h3>
        <table class="w-full text-sm">
          <thead class="text-gray-500 border-b">
            <tr>
              <th class="text-left py-2">User</th>
              <th class="text-left py-2">Action</th>
              <th class="text-left py-2">Date</th>
            </tr>
          </thead>
          <tbody id="recent-activity">
            <tr class="border-b"><td colspan="3" class="text-center py-2">Loading...</td></tr>
          </tbody>
        </table>
      </div>

    </main>
  </div>

<script>
AOS.init({ once: true });

async function fetchStats() {
    try {
        const res = await fetch("{{ route('admin.stats') }}");
        const data = await res.json();

        if (!data.success) throw new Error(data.message || 'Failed to load stats');

        // Update stat cards
        document.getElementById('total-pharmacies').textContent = data.totals.pharmacies;
        document.getElementById('total-prescriptions').textContent = data.totals.prescriptions;
        document.getElementById('active-users').textContent = data.totals.active_users ?? 0;
        document.getElementById('monthly-income').textContent = 'Rs ' + (data.totals.revenue ?? '0');

        const labels = data.labels;

        // Pharmacies Line Chart
        new Chart(document.getElementById('pharmChart'), {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Pharmacies',
                    data: data.pharmacies_by_month,
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34,197,94,0.15)',
                    fill: true,
                    tension: 0.3
                }]
            }
        });

        // Prescriptions Bar Chart
        new Chart(document.getElementById('presChart'), {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Prescriptions',
                    data: data.prescriptions_by_month,
                    backgroundColor: '#2563eb',
                    borderRadius: 6
                }]
            }
        });

        // Customers Bar Chart
        new Chart(document.getElementById('customersChart'), {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Customers',
                    data: data.customers_per_month || [],
                    backgroundColor: '#7c3aed',
                    borderRadius: 6
                }]
            }
        });

        // Income Bar Chart
        new Chart(document.getElementById('incomeChart'), {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Income (LKR)',
                    data: data.income_per_month || [],
                    backgroundColor: '#f97316',
                    borderRadius: 6
                }]
            },
            options: { scales: { y: { beginAtZero: true } } }
        });

        // Recent Activity
        const activityEl = document.getElementById('recent-activity');
        if (data.recent_activity?.length) {
            activityEl.innerHTML = '';
            data.recent_activity.forEach(a => {
                activityEl.innerHTML += `
                  <tr class="border-b">
                    <td class="py-2">${a.user}</td>
                    <td>${a.action}</td>
                    <td>${new Date(a.date).toLocaleDateString()}</td>
                  </tr>
                `;
            });
        }

    } catch (err) {
        console.error('Error fetching stats:', err);
    }
}

fetchStats();
</script>

</body>
</html>
