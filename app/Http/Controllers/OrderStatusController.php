<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class OrderStatusController extends Controller
{
    protected $supabaseUrl = 'https://zazdljyechhzsiodnvts.supabase.co';
    protected $supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InphemRsanllY2hoenNpb2RudnRzIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTMwMjE2MzMsImV4cCI6MjA2ODU5NzYzM30.OZLL_quXsqD2PJEtyQjSBOR9SaZBVXvaTfoAcBYCZTM';

    public function index()
    {
        $customer = Session::get('user');

        if (!$customer || !isset($customer['customer_id'])) {
            return redirect('/login')->with('error', 'Please login first.');
        }

        $customerId = $customer['customer_id'];

        // Fetch all orders for this customer
        $response = Http::withHeaders([
            'apikey' => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
        ])->get($this->supabaseUrl . '/rest/v1/orders', [
            'customer_id' => "eq.$customerId",
            'order' => 'created_at.desc',
        ]);

        $orders = $response->json();

        // Separate ongoing vs recent
        $ongoingOrders = array_filter($orders, fn($o) =>
            in_array($o['status'], ['Pending', 'Accepted', 'Ready'])
        );

        $recentOrders = array_filter($orders, fn($o) =>
            in_array($o['status'], ['Delivered', 'Cancelled'])
        );

        return view('orders.index', compact('ongoingOrders', 'recentOrders'));
    }

    // Cancel an order by updating its status
    public function cancelOrder($orderId, Request $request)
    {
        $reason = $request->input('reason', 'No reason provided');

        $response = Http::withHeaders([
            'apikey' => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
        ])->patch($this->supabaseUrl . '/rest/v1/orders?id=eq.' . $orderId, [
            'status' => 'Cancelled',
            'cancel_reason' => $reason,       // Make sure this column exists in Supabase
            'cancelled_at' => now()->toIso8601String(),
        ]);

        if ($response->failed()) {
            return back()->with('error', 'Error cancelling order: ' . $response->body());
        }

        return back()->with('success', 'Order cancelled successfully.');
    }
}
