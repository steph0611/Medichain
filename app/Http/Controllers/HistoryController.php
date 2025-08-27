<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class HistoryController extends Controller
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

        // ✅ Fetch only Delivered orders
        $response = Http::withHeaders([
            'apikey' => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
        ])->get($this->supabaseUrl . '/rest/v1/orders', [
            'customer_id' => "eq.$customerId",
            'status' => 'eq.Delivered',
            'order' => 'created_at.desc',
        ]);

        $recentOrders = $response->json();

        return view('history.index', compact('recentOrders'));
    }
}
