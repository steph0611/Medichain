<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    private $supabaseUrl;
    private $supabaseKey;

    public function __construct()
    {
        $this->supabaseUrl = rtrim(env('SUPABASE_URL'), '/');
        $this->supabaseKey = env('SUPABASE_KEY');
    }

    public function index()
    {
        return view('admin.dashboard');
    }

    public function stats(Request $request)
    {
        $client = $this->supabaseClient();

        // Prepare 12-month buckets
        $months = [];
        $now = Carbon::now();
        for ($i = 11; $i >= 0; $i--) {
            $m = $now->copy()->subMonths($i);
            $months[] = [
                'key'   => $m->format('Y-m'),
                'start' => $m->copy()->startOfMonth()->toISOString(),
                'end'   => $m->copy()->endOfMonth()->toISOString(),
                'label' => $m->format('M Y'),
            ];
        }

        try {
            // === Pharmacies ===
            $shopsRes = $client->get('/rest/v1/Shop', ['query' => ['select' => '*']]);
            $shops = json_decode((string)$shopsRes->getBody(), true) ?? [];

            // === Prescriptions ===
            $oldestStart = $months[0]['start'];
            $presRes = $client->get('/rest/v1/prescriptions', [
                'query' => [
                    'select' => '*',
                    'prescription_date' => 'gte.' . substr($oldestStart, 0, 10),
                ],
            ]);
            $prescriptions = json_decode((string)$presRes->getBody(), true) ?? [];

            // === Customers ===
            $customersRes = $client->get('/rest/v1/customer', [
                'query' => ['select' => 'customer_id,created_at,last_active']
            ]);
            $customers = json_decode((string)$customersRes->getBody(), true) ?? [];

            // === Orders / Payments ===
            $ordersRes = $client->get('/rest/v1/orders', [
                'query' => [
                    'select' => 'amount,created_at',
                    'created_at' => 'gte.' . substr($oldestStart, 0, 10),
                ],
            ]);
            $orders = json_decode((string)$ordersRes->getBody(), true) ?? [];

            // Function to increment monthly buckets
            $incMonth = function (&$buckets, $dateStr) {
                if (!$dateStr) return;
                try {
                    $dt = Carbon::parse($dateStr);
                } catch (\Exception $e) {
                    return;
                }
                $key = $dt->format('Y-m');
                foreach ($buckets as &$b) {
                    if ($b['key'] === $key) {
                        $b['count']++;
                        return;
                    }
                }
            };

            // Monthly buckets
            $pharmaciesByMonth = array_map(fn($m) => ['label' => $m['label'], 'count' => 0, 'key' => $m['key']], $months);
            $prescriptionsByMonth = array_map(fn($m) => ['label' => $m['label'], 'count' => 0, 'key' => $m['key']], $months);
            $customersByMonth = array_map(fn($m) => ['label' => $m['label'], 'count' => 0, 'key' => $m['key']], $months);
            $incomeByMonth = array_map(fn($m) => ['label' => $m['label'], 'count' => 0, 'key' => $m['key']], $months);

            foreach ($shops as $shop) {
                $date = $shop['created_at'] ?? $shop['registered_at'] ?? $shop['added_at'] ?? null;
                $incMonth($pharmaciesByMonth, $date);
            }

            foreach ($prescriptions as $p) {
                $date = $p['prescription_date'] ?? $p['uploaded_at'] ?? $p['created_at'] ?? null;
                $incMonth($prescriptionsByMonth, $date);
            }

            foreach ($customers as $c) {
                $date = $c['created_at'] ?? null;
                $incMonth($customersByMonth, $date);
            }

            // Sum orders.amount per month
            foreach ($orders as $o) {
                $dt = Carbon::parse($o['created_at'] ?? null);
                $key = $dt->format('Y-m');
                foreach ($incomeByMonth as &$m) {
                    if ($m['key'] === $key) {
                        $m['count'] += floatval($o['amount']);
                        break;
                    }
                }
            }

            // Totals
            $totalPharmacies = count($shops);
            $totalPrescriptions = count($prescriptions);
            $totalCustomers = count($customers);
            $totalRevenue = array_sum(array_map(fn($m) => $m['count'], $incomeByMonth));

            // ✅ Real active users (last 5 minutes, fallback = created today)
            $activeThreshold = Carbon::now()->subMinute();
            $today = Carbon::today();
            $activeUsers = collect($customers)->filter(function ($c) use ($activeThreshold, $today) {
                try {
                    if (!empty($c['last_active'])) {
                        return Carbon::parse($c['last_active'])->gte($activeThreshold);
                    }
                    if (!empty($c['created_at'])) {
                        return Carbon::parse($c['created_at'])->gte($today);
                    }
                } catch (\Exception $e) {
                    return false;
                }
                return false;
            })->count();

            // Recent Activity
            $activities = [];
            foreach ($shops as $shop) {
                $activities[] = [
                    'user'   => $shop['name'] ?? 'Unknown Pharmacy',
                    'action' => 'Registered Pharmacy',
                    'date'   => $shop['created_at'] ?? $shop['registered_at'] ?? null,
                ];
            }
            foreach ($prescriptions as $p) {
                $activities[] = [
                    'user'   => $p['patient_name'] ?? 'Unknown Patient',
                    'action' => 'Submitted Prescription',
                    'date'   => $p['prescription_date'] ?? $p['uploaded_at'] ?? null,
                ];
            }
            foreach ($customers as $c) {
                $activities[] = [
                    'user'   => 'Customer #' . ($c['customer_id'] ?? 'unknown'),
                    'action' => 'Registered Customer',
                    'date'   => $c['created_at'] ?? null,
                ];
            }

            usort($activities, fn($a, $b) => strtotime($b['date'] ?? 0) <=> strtotime($a['date'] ?? 0));
            $recentActivity = array_slice($activities, 0, 10);

            // Return JSON payload
            return response()->json([
                'success' => true,
                'totals' => [
                    'pharmacies'     => $totalPharmacies,
                    'prescriptions'  => $totalPrescriptions,
                    'customers'      => $totalCustomers,
                    'active_users'   => $activeUsers,
                    'revenue'        => $totalRevenue, // updated
                ],
                'labels'                  => array_map(fn($m) => $m['label'], $months),
                'pharmacies_by_month'     => array_map(fn($m) => $m['count'], $pharmaciesByMonth),
                'prescriptions_by_month'  => array_map(fn($m) => $m['count'], $prescriptionsByMonth),
                'customers_per_month'     => array_map(fn($m) => $m['count'], $customersByMonth),
                'income_per_month'        => array_map(fn($m) => $m['count'], $incomeByMonth),
                'recent_activity'         => $recentActivity,
            ]);

        } catch (\Exception $e) {
            \Log::error('Admin stats error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch stats: ' . $e->getMessage()
            ], 500);
        }
    }

    private function supabaseClient()
    {
        return new Client([
            'base_uri' => $this->supabaseUrl,
            'headers' => [
                'apikey'        => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ],
            'timeout' => 20,
            'verify' => false  // Disable SSL verification for development
        ]);
    }
}
