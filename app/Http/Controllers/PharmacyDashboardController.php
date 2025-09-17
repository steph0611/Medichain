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
        $prescriptions = json_decode($prescriptionResponse->getBody(), true) ?? [];

        // Get cancelled prescriptions (safe: if table missing or query fails we return empty array)
        try {
            $cancelledResponse = $client->get('/rest/v1/cancelled_prescriptions', [
                'query' => ['shop_id' => 'eq.' . $shop_id],
            ]);
            $cancelledPrescriptions = json_decode($cancelledResponse->getBody(), true) ?? [];
        } catch (\Exception $e) {
            // prevent undefined variable in view — log if needed
            \Log::warning('Error fetching cancelled_prescriptions: ' . $e->getMessage());
            $cancelledPrescriptions = [];
        }

        return view('pharmacy-dashboard', compact('pharmacy', 'prescriptions', 'cancelledPrescriptions'));
    }

    // ---------------------------
    // Update prescription + order status
    // ---------------------------
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:Pending,Accepted,Ready,Delivered,Cancelled',
        ]);

        $client = $this->supabaseClient();
        $now = Carbon::now()->toIso8601String();

        try {
            if ($request->status === 'Cancelled') {
                // 1️⃣ Fetch prescription data
                $response = $client->get("/rest/v1/prescriptions?id=eq.$id", [
                    'headers' => ['Prefer' => 'return=representation']
                ]);
                $prescription = json_decode($response->getBody(), true)[0] ?? null;

                if ($prescription) {
                    // Prepare insert data: keep original prescription id in prescription_id field,
                    // but remove the id field to avoid conflict with cancelled_prescriptions PK
                    $insertData = $prescription;
                    $insertData['prescription_id'] = $prescription['id'];
                    if (isset($insertData['id'])) {
                        unset($insertData['id']);
                    }
                    $insertData['status'] = 'Cancelled';
                    $insertData['cancelled_at'] = $now;
                    $insertData['updated_at'] = $now;
                    // Insert into cancelled_prescriptions
                    $client->post('/rest/v1/cancelled_prescriptions', [
                        'json' => $insertData,
                        'headers' => ['Prefer' => 'return=representation']
                    ]);

                    // Delete original prescription
                    $client->delete("/rest/v1/prescriptions?id=eq.$id");

                    // Update related orders (if any)
                    $client->patch("/rest/v1/orders?prescription_id=eq.$id", [
                        'json' => [
                            'status' => 'Cancelled',
                            'updated_at' => $now
                        ],
                        'headers' => ['Prefer' => 'return=representation']
                    ]);
                } else {
                    return back()->with('error', 'Prescription not found to cancel.');
                }
            } else {
                // Normal flow: update prescriptions + orders
                $client->patch("/rest/v1/prescriptions?id=eq.$id", [
                    'json' => [
                        'status' => $request->status,
                        'updated_at' => $now
                    ],
                    'headers' => ['Prefer' => 'return=representation']
                ]);

                $client->patch("/rest/v1/orders?prescription_id=eq.$id", [
                    'json' => [
                        'status' => $request->status,
                        'updated_at' => $now
                    ],
                    'headers' => ['Prefer' => 'return=representation']
                ]);
            }

            return back()->with('success', "Prescription updated to {$request->status}!");
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
