<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class OrderStatusController extends Controller
{
    protected $supabaseUrl = 'https://zazdljyechhzsiodnvts.supabase.co';
    protected $supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InphemRsanllY2hoenNpb2RudnRzIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTMwMjE2MzMsImV4cCI6MjA2ODU5NzYzM30.OZLL_quXsqD2PJEtyQjSBOR9SaZBVXvaTfoAcBYCZTM';

    protected $client;

    public function __construct()
    {
        // Initialize Guzzle client
        $this->client = new Client([
            'base_uri' => $this->supabaseUrl,
            'headers' => [
                'apikey' => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        ]);
    }

    /**
     * Show customer's orders (ongoing + recent)
     */
    public function index(Request $request)
    {
        // Check if customer is logged in via <?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class OrderStatusController extends Controller
{
    protected $supabaseUrl = 'https://zazdljyechhzsiodnvts.supabase.co';
    protected $supabaseKey = 'YOUR_SUPABASE_ANON_KEY';
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => $this->supabaseUrl,
            'headers' => [
                'apikey' => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        ]);
    }

    public function index()
    {
        if (!session()->has('user') || session('role') !== 'customer') {
            return redirect('/login')->withErrors(['Please login first.']);
        }

        $customerId = session('user')['customer_id'];

        try {
            $ongoingResp = $this->client->get('/rest/v1/orders', [
                'query' => [
                    'customer_id' => 'eq.' . $customerId,
                    'status' => 'not.in.Delivered,Cancelled',
                    'select' => '*'
                ]
            ]);

            $ongoingOrders = json_decode($ongoingResp->getBody(), true);

            $recentResp = $this->client->get('/rest/v1/orders', [
                'query' => [
                    'customer_id' => 'eq.' . $customerId,
                    'status' => 'in.Delivered,Cancelled',
                    'select' => '*'
                ]
            ]);

            $recentOrders = json_decode($recentResp->getBody(), true);

        } catch (\Exception $e) {
            return back()->withErrors(['Failed to fetch orders: ' . $e->getMessage()]);
        }

        return view('orders.index', compact('ongoingOrders', 'recentOrders'));
    }

    public function store(Request $request)
    {
        if (!session()->has('user') || session('role') !== 'customer') {
            return redirect('/login')->withErrors(['Please login first.']);
        }

        $request->validate([
            'shop_id' => 'required',
            'items' => 'required|array',
        ]);

        try {
            $this->client->post('/rest/v1/orders', [
                'json' => [
                    'customer_id' => session('user')['customer_id'],
                    'shop_id' => $request->shop_id,
                    'items' => $request->items, // pass raw array, not json_encode
                    'status' => 'Pending',
                ]
            ]);

            return back()->with('success', 'Order placed successfully.');

        } catch (\Exception $e) {
            return back()->withErrors(['Failed to place order: ' . $e->getMessage()]);
        }
    }

    public function updateStatus(Request $request, $orderId)
    {
        $request->validate([
            'status' => 'required|in:Pending,Accepted,Preparing,Ready,Delivered,Cancelled',
        ]);

        try {
            $this->client->patch("/rest/v1/orders?id=eq.$orderId", [
                'json' => ['status' => $request->status]
            ]);

            return back()->with('success', 'Order status updated successfully.');

        } catch (\Exception $e) {
            return back()->withErrors(['Failed to update order: ' . $e->getMessage()]);
        }
    }
}
session
        if (!session()->has('user') || session('role') !== 'customer') {
            return redirect('/login')->withErrors(['Please login first.']);
        }

        $customerId = session('user')['customer_id'];

        try {
            // Fetch ongoing orders
            $ongoingResp = $this->client->get('/rest/v1/orders', [
                'query' => [
                    'customer_id' => 'eq.' . $customerId,
                    'status' => 'not.in.(Delivered,Cancelled)',
                    'select' => '*'
                ]
            ]);

            $ongoingOrders = json_decode($ongoingResp->getBody()->getContents(), true);

            // Fetch recent orders
            $recentResp = $this->client->get('/rest/v1/orders', [
                'query' => [
                    'customer_id' => 'eq.' . $customerId,
                    'status' => 'in.(Delivered,Cancelled)',
                    'select' => '*'
                ]
            ]);

            $recentOrders = json_decode($recentResp->getBody()->getContents(), true);

        } catch (\Exception $e) {
            $ongoingOrders = [];
            $recentOrders = [];
            return back()->withErrors(['Failed to fetch orders: ' . $e->getMessage()]);
        }

        return view('orders.index', compact('ongoingOrders', 'recentOrders'));
    }

    /**
     * Place a new order
     */
    public function store(Request $request)
    {
        if (!session()->has('user') || session('role') !== 'customer') {
            return redirect('/login')->withErrors(['Please login first.']);
        }

        $request->validate([
            'shop_id' => 'required',
            'items' => 'required|array',
        ]);

        try {
            $response = $this->client->post('/rest/v1/orders', [
                'json' => [
                    'customer_id' => session('user')['customer_id'],
                    'shop_id' => $request->shop_id,
                    'items' => json_encode($request->items),
                    'status' => 'Pending',
                ]
            ]);

            return back()->with('success', 'Order placed successfully.');

        } catch (\Exception $e) {
            return back()->withErrors(['Failed to place order: ' . $e->getMessage()]);
        }
    }

    /**
     * Update order status (for pharmacies)
     */
    public function updateStatus(Request $request, $orderId)
    {
        $request->validate([
            'status' => 'required|in:Pending,Accepted,Preparing,Ready,Delivered,Cancelled',
        ]);

        try {
            $this->client->patch("/rest/v1/orders?id=eq.$orderId", [
                'json' => ['status' => $request->status]
            ]);

            return back()->with('success', 'Order status updated successfully.');

        } catch (\Exception $e) {
            return back()->withErrors(['Failed to update order: ' . $e->getMessage()]);
        }
    }
}
