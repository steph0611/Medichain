<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class DashboardController extends Controller
{
    protected $supabaseUrl = 'https://zazdljyechhzsiodnvts.supabase.co';
    protected $supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InphemRsanllY2hoenNpb2RudnRzIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTMwMjE2MzMsImV4cCI6MjA2ODU5NzYzM30.OZLL_quXsqD2PJEtyQjSBOR9SaZBVXvaTfoAcBYCZTM';

    public function index(Request $request)
    {
        $user = session('user');

        // Customer details
        $custLat = $user['latitude'] ?? null;
        $custLng = $user['longitude'] ?? null;  
        $custLocation = $user['city'] ?? null;

        $pharmacies = [];
        $allPharmacies = [];

        $client = new Client([
            'base_uri' => $this->supabaseUrl,
            'headers' => [
                'apikey' => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
                'Accept' => 'application/json',
            ]
        ]);

        try {
            // ✅ Filtered pharmacies (only in user's city)
            $response = $client->get('/rest/v1/Shop', [
                'query' => [
                    'select' => '*',
                    'location' => 'eq.' . $custLocation,
                ]
            ]);

            $shops = json_decode($response->getBody(), true);

            foreach ($shops as &$shop) {
                if (!empty($shop['latitude']) && !empty($shop['longitude']) && $custLat && $custLng) {
                    $shop['distance'] = $this->calculateDistance($custLat, $custLng, $shop['latitude'], $shop['longitude']);
                } else {
                    $shop['distance'] = null;
                }
            }

            // Sort by distance
            usort($shops, function($a, $b) {
                return ($a['distance'] ?? INF) <=> ($b['distance'] ?? INF);
            });

            $pharmacies = $shops;

            // ✅ All pharmacies (no filter)
            $allResponse = $client->get('/rest/v1/Shop', [
                'query' => [
                    'select' => '*',
                ]
            ]);

            $allShops = json_decode($allResponse->getBody(), true);

            foreach ($allShops as &$shop) {
                if (!empty($shop['latitude']) && !empty($shop['longitude']) && $custLat && $custLng) {
                    $shop['distance'] = $this->calculateDistance($custLat, $custLng, $shop['latitude'], $shop['longitude']);
                } else {
                    $shop['distance'] = null;
                }
            }

            // Sort all pharmacies by distance too
            usort($allShops, function($a, $b) {
                return ($a['distance'] ?? INF) <=> ($b['distance'] ?? INF);
            });

            $allPharmacies = $allShops;

        } catch (\Exception $e) {
            $pharmacies = [];
            $allPharmacies = [];
        }

        // ✅ Pass both
        return view('dashboard', compact('pharmacies', 'allPharmacies'));
    }

    // Haversine formula
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c; // distance in km
    }
}
