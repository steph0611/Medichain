<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Carbon\Carbon;

class PharmacyDashboardController extends Controller
{
    protected $supabaseUrl = 'https://zazdljyechhzsiodnvts.supabase.co';
    protected $supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InphemRsanllY2hoenNpb2RudnRzIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTMwMjE2MzMsImV4cCI6MjA2ODU5NzYzM30.OZLL_quXsqD2PJEtyQjSBOR9SaZBVXvaTfoAcBYCZTM';

    // ---------------------------
    // Show pharmacy dashboard
    // ---------------------------
    public function index($shop_id)
    {
        $client = $this->supabaseClient();

        // Get pharmacy info
        $pharmacyResponse = $client->get('/rest/v1/Shop', [
            'query' => ['shop_id' => 'eq.' . $shop_id],
        ]);
        $pharmacy = json_decode($pharmacyResponse->getBody(), true)[0] ?? null;

        if (!$pharmacy) {
            return redirect('/dashboard')->with('error', 'Pharmacy not found');
        }

        // Get prescriptions for this pharmacy
        $prescriptionResponse = $client->get('/rest/v1/prescriptions', [
            'query' => ['shop_id' => 'eq.' . $shop_id],
        ]);
        $prescriptions = json_decode($prescriptionResponse->getBody(), true);

        return view('pharmacy-dashboard', compact('pharmacy', 'prescriptions'));
    }

    // ---------------------------
    // Update prescription + order status (like prescriptions)
    // ---------------------------
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:Pending,Accepted,Ready,Delivered',
        ]);

        $client = $this->supabaseClient();
        $now = Carbon::now()->toIso8601String();

        try {
            // 1️⃣ Update prescription status
            $client->patch('/rest/v1/prescriptions?id=eq.' . $id, [
                'json' => [
                    'status' => $request->status,   // Capitalized
                    'updated_at' => $now
                ],
                'headers' => ['Prefer' => 'return=representation']
            ]);

            // 2️⃣ Update linked order(s) with same prescription_id
            $client->patch('/rest/v1/orders?prescription_id=eq.' . $id, [
                'json' => [
                    'status' => $request->status,   // ✅ Capitalized
                    'updated_at' => $now
                ],
                'headers' => ['Prefer' => 'return=representation']
            ]);

            return back()->with('success', "Prescription & Order status updated to {$request->status}!");
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating status: ' . $e->getMessage());
        }
    }


    // ---------------------------
    // Supabase client helper
    // ---------------------------
    private function supabaseClient()
    {
        return new Client([
            'base_uri' => $this->supabaseUrl,
            'headers' => [
                'apikey' => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }
}
