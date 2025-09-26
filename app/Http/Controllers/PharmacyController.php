<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class PharmacyController extends Controller
{
    protected $supabaseUrl = 'https://zazdljyechhzsiodnvts.supabase.co';
    protected $supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InphemRsanllY2hoenNpb2RudnRzIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTMwMjE2MzMsImV4cCI6MjA2ODU5NzYzM30.OZLL_quXsqD2PJEtyQjSBOR9SaZBVXvaTfoAcBYCZTM';

    /**
     * Show all pharmacies (from Shop table) with distance calculation.
     */
    public function index(Request $request)
    {
        $user = session('user');

        $custLat = $user['latitude'] ?? null;
        $custLng = $user['longitude'] ?? null;

        $response = Http::withOptions([
            'verify' => false  // Disable SSL verification for development
        ])->withHeaders([
            'apikey'        => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
        ])->get($this->supabaseUrl . '/rest/v1/Shop', [
            'select' => '*'
        ]);

        if ($response->failed()) {
            return back()->with('error', 'Unable to fetch pharmacies.');
        }

        $pharmacies = $response->json();

        // Calculate distance if user location exists
        if ($custLat && $custLng) {
            foreach ($pharmacies as &$shop) {
                if (!empty($shop['latitude']) && !empty($shop['longitude'])) {
                    $shop['distance'] = $this->calculateDistance($custLat, $custLng, $shop['latitude'], $shop['longitude']);
                } else {
                    $shop['distance'] = null;
                }
            }

            // Sort by distance
            usort($pharmacies, function($a, $b) {
                return ($a['distance'] ?? INF) <=> ($b['distance'] ?? INF);
            });
        }

        return view('pharmacies', compact('pharmacies'));
    }

    /**
     * Show a single pharmacy by ID.
     */
    public function show($id)
    {
        $user = session('user');

        $custLat = $user['latitude'] ?? null;
        $custLng = $user['longitude'] ?? null;

        $response = Http::withOptions([
            'verify' => false  // Disable SSL verification for development
        ])->withHeaders([
            'apikey'        => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
        ])->get($this->supabaseUrl . '/rest/v1/Shop', [
            'id' => "eq.$id",
            'select' => '*'
        ]);

        if ($response->failed() || empty($response->json())) {
            return back()->with('error', 'Pharmacy not found.');
        }

        $pharmacy = $response->json()[0];

        if ($custLat && $custLng && !empty($pharmacy['latitude']) && !empty($pharmacy['longitude'])) {
            $pharmacy['distance'] = $this->calculateDistance($custLat, $custLng, $pharmacy['latitude'], $pharmacy['longitude']);
        } else {
            $pharmacy['distance'] = null;
        }

        return view('pharmacy-details', compact('pharmacy'));
    }

    /**
     * Show orders related to a pharmacy.
     */
    public function orders($id)
    {
        $response = Http::withOptions([
            'verify' => false  // Disable SSL verification for development
        ])->withHeaders([
            'apikey'        => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
        ])->get($this->supabaseUrl . '/rest/v1/Orders', [
            'shop_id' => "eq.$id",
            'select'  => '*'
        ]);

        if ($response->failed()) {
            return back()->with('error', 'Unable to fetch orders.');
        }

        $orders = $response->json();

        return view('pharmacy-orders', compact('orders'));
    }

    // Haversine formula for distance calculation
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // distance in km
    }
}
