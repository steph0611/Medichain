<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Carbon\Carbon;

class PrescriptionController extends Controller
{
    protected $supabaseUrl = 'https://zazdljyechhzsiodnvts.supabase.co';
    protected $supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InphemRsanllY2hoenNpb2RudnRzIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTMwMjE2MzMsImV4cCI6MjA2ODU5NzYzM30.OZLL_quXsqD2PJEtyQjSBOR9SaZBVXvaTfoAcBYCZTM';

    public function showUploadForm($shop_id)
    {
        $client = $this->supabaseClient();

        $response = $client->get('/rest/v1/Shop', [
            'query' => ['shop_id' => 'eq.' . $shop_id]
        ]);

        $pharmacy = json_decode($response->getBody(), true)[0] ?? null;

        if (!$pharmacy) {
            return redirect('/dashboard')->with('error', 'Pharmacy not found');
        }

        return view('upload-prescription', compact('pharmacy'));
    }

    public function uploadPrescription(Request $request, $shop_id)
    {
        $request->validate([
            'prescription_image' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'patient_name' => 'required|string|max:255',
            'doctor_name' => 'required|string|max:255',
            'prescription_date' => 'required|date',
            'Address' => 'required|string|max:255',
            'Phone' => 'required|string|max:20',
            'Email' => 'required|email|max:255',
            'instructions' => 'nullable|string',
        ]);

        $mainClient = $this->supabaseClient();

        $response = $mainClient->get('/rest/v1/Shop', [
            'query' => ['shop_id' => 'eq.' . $shop_id]
        ]);

        $pharmacy = json_decode($response->getBody(), true)[0] ?? null;
        if (!$pharmacy) {
            return redirect('/dashboard')->with('error', 'Pharmacy not found');
        }

        $pharmacyClient = $this->pharmacyClient($pharmacy['url'], $pharmacy['api_key']);

        $image = $request->file('prescription_image');
        $base64Image = base64_encode(file_get_contents($image->getRealPath()));
        $imageMime = $image->getMimeType();

        $payload = [
            'patient_name' => $request->input('patient_name'),
            'doctor_name' => $request->input('doctor_name'),
            'prescription_date' => $request->input('prescription_date'),
            'address' => $request->input('Address'),
            'phone' => $request->input('Phone'),
            'email' => $request->input('Email'),
            'instructions' => $request->input('instructions'),
            'image_data' => $base64Image,
            'image_type' => $imageMime,
            'status' => 'Pending',
            'uploaded_at' => Carbon::now()->toISOString(),
        ];

        $insertResponse = $pharmacyClient->post('/rest/v1/prescriptions', [
            'json' => $payload,
        ]);

        if ($insertResponse->getStatusCode() !== 201) {
            return back()->with('error', 'Failed to upload prescription.');
        }

        return redirect('/dashboard')->with('success', 'Prescription uploaded successfully!');
    }

    public function viewDashboard($shop_id)
    {
        $mainClient = $this->supabaseClient();

        $response = $mainClient->get('/rest/v1/Shop', [
            'query' => ['shop_id' => 'eq.' . $shop_id]
        ]);

        $pharmacy = json_decode($response->getBody(), true)[0] ?? null;
        if (!$pharmacy) {
            return redirect('/dashboard')->with('error', 'Pharmacy not found');
        }

        $pharmacyClient = $this->pharmacyClient($pharmacy['url'], $pharmacy['api_key']);

        $prescriptionResponse = $pharmacyClient->get('/rest/v1/prescriptions?select=*');
        $prescriptions = json_decode($prescriptionResponse->getBody(), true);

        return view('pharmacy-dashboard', compact('pharmacy', 'prescriptions'));
    }

    // ✅ ✅ FIXED METHOD
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:Pending,Accepted,Ready,Delivered',
        ]);

        // Search through all pharmacy databases
        $mainClient = $this->supabaseClient();

        $shopsResponse = $mainClient->get('/rest/v1/Shop');
        $shops = json_decode($shopsResponse->getBody(), true);

        foreach ($shops as $shop) {
            $pharmacyClient = $this->pharmacyClient($shop['url'], $shop['api_key']);

            $prescriptionResponse = $pharmacyClient->get("/rest/v1/prescriptions?id=eq.$id");
            $prescriptions = json_decode($prescriptionResponse->getBody(), true);

            if (!empty($prescriptions)) {
                $pharmacyClient->patch("/rest/v1/prescriptions?id=eq.$id", [
                    'json' => ['status' => $request->status]
                ]);

                return back()->with('success', "Prescription status updated to {$request->status}!");
            }
        }

        return back()->with('error', "Prescription with ID $id not found.");
    }

    public function markProcessed($id)
    {
        $client = $this->supabaseClient();

        $client->patch('/rest/v1/prescriptions?id=eq.' . $id, [
            'json' => ['status' => 'Processed']
        ]);

        return back()->with('success', 'Prescription marked as processed!');
    }

    public function delete($id)
    {
        $mainClient = $this->supabaseClient();

        $shopsResponse = $mainClient->get('/rest/v1/Shop');
        $shops = json_decode($shopsResponse->getBody(), true);

        foreach ($shops as $shop) {
            $pharmacyClient = $this->pharmacyClient($shop['url'], $shop['api_key']);

            $prescriptionResponse = $pharmacyClient->get("/rest/v1/prescriptions?id=eq.$id");
            $prescriptions = json_decode($prescriptionResponse->getBody(), true);

            if (!empty($prescriptions)) {
                $pharmacyClient->delete("/rest/v1/prescriptions?id=eq.$id");
                return back()->with('success', "Prescription deleted successfully!");
            }
        }

        return back()->with('error', "Prescription not found.");
    }

    // ---------------------------
    // Helpers
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
            ]
        ]);
    }

    private function pharmacyClient($url, $key)
    {
        return new Client([
            'base_uri' => $url,
            'headers' => [
                'apikey' => $key,
                'Authorization' => 'Bearer ' . $key,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]
        ]);
    }
}
