<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    protected $supabaseUrl = 'https://zazdljyechhzsiodnvts.supabase.co';
    protected $supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InphemRsanllY2hoenNpb2RudnRzIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTMwMjE2MzMsImV4cCI6MjA2ODU5NzYzM30.OZLL_quXsqD2PJEtyQjSBOR9SaZBVXvaTfoAcBYCZTM';

    private function supabaseClient()
    {
        return Http::withHeaders([
            'apikey' => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
        ])->baseUrl($this->supabaseUrl);
    }

    private function pharmacyClient($url, $apiKey)
    {
        return Http::withHeaders([
            'apikey' => $apiKey,
            'Authorization' => 'Bearer ' . $apiKey,
        ])->baseUrl($url);
    }

    public function index(Request $request)
    {
        $user = session('user');

        if (!$user || !is_array($user) || !isset($user['email'])) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $customerEmail = $user['email'];
        $mainClient = $this->supabaseClient();

        $shopsResponse = $mainClient->get('/rest/v1/Shop');
        $shops = json_decode($shopsResponse->getBody()->getContents(), true);

        if (!is_array($shops)) {
            return back()->with('error', 'Failed to load pharmacy shops.');
        }

        $orders = [];

        foreach ($shops as $shop) {
            if (!isset($shop['url'], $shop['api_key'])) {
                continue;
            }

            $pharmacyClient = $this->pharmacyClient($shop['url'], $shop['api_key']);

            $prescriptionResponse = $pharmacyClient->get('/rest/v1/prescriptions?customer_email=eq.' . $customerEmail);
            $shopOrders = json_decode($prescriptionResponse->getBody()->getContents(), true);

            if (!is_array($shopOrders)) {
                continue;
            }

            // Filter only valid array items
            $shopOrders = array_filter($shopOrders, 'is_array');

            // Add pharmacy name to each order
            foreach ($shopOrders as &$order) {
                $order['pharmacy'] = $shop['name'] ?? 'Unknown Pharmacy';
            }

            $orders = array_merge($orders, $shopOrders);
        }

        return view('orders', compact('orders'));
    }
}
