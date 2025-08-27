<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PrescriptionController extends Controller
{
    protected $supabaseUrl = 'https://zazdljyechhzsiodnvts.supabase.co';
    protected $supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InphemRsanllY2hoenNpb2RudnRzIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTMwMjE2MzMsImV4cCI6MjA2ODU5NzYzM30.OZLL_quXsqD2PJEtyQjSBOR9SaZBVXvaTfoAcBYCZTM';

    // ---------------------------
    // Show modal form for prescription upload
    // ---------------------------
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

    // ---------------------------
    // Handle modal form submission
    // ---------------------------
    public function uploadPrescription(Request $request, $shop_id)
    {
        $request->validate([
            'prescription_image' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'patient_name'       => 'required|string|max:255',
            'doctor_name'        => 'required|string|max:255',
            'prescription_date'  => 'required|date',
            'Address'            => 'required|string|max:255',
            'Phone'              => 'required|string|max:20',
            'Email'              => 'required|email|max:255',
            'instructions'       => 'nullable|string',
        ]);

        $client = $this->supabaseClient();

        // Get customer_id from session
        $customerId = session('user')['customer_id'] ?? null;
        if (!$customerId) {
            return back()->with('error', 'Customer ID not found. Please log in again.');
        }

        // Convert image to base64
        $image       = $request->file('prescription_image');
        $base64Image = base64_encode(file_get_contents($image->getRealPath()));
        $imageMime   = $image->getMimeType();

        // ----------------------
        // 1️⃣ Insert into prescriptions table
        // ----------------------
        $prescriptionPayload = [
            'shop_id'          => $shop_id,
            'customer_id'      => $customerId,
            'patient_name'     => $request->input('patient_name'),
            'doctor_name'      => $request->input('doctor_name'),
            'prescription_date'=> $request->input('prescription_date'),
            'address'          => $request->input('Address'),
            'phone'            => $request->input('Phone'),
            'email'            => $request->input('Email'),
            'instructions'     => $request->input('instructions'),
            'image_data'       => $base64Image,
            'image_type'       => $imageMime,
            'status'           => 'Pending',
            'uploaded_at'      => Carbon::now()->toISOString(),
        ];

        $prescriptionResponse = $client->post('/rest/v1/prescriptions', [
            'json' => [$prescriptionPayload],
            'headers' => ['Prefer' => 'return=representation']
        ]);

        if ($prescriptionResponse->getStatusCode() !== 201) {
            return back()->with('error', 'Failed to upload prescription: ' . $prescriptionResponse->getBody());
        }

        $prescriptionData = json_decode($prescriptionResponse->getBody(), true)[0] ?? null;
        $prescriptionId = $prescriptionData['id'] ?? null;

        if (!$prescriptionId) {
            return back()->with('error', 'Prescription saved but could not retrieve ID.');
        }

        // ----------------------
        // 2️⃣ Insert into orders table
        // ----------------------
        $orderPayload = [
            'order_id'         => Str::uuid()->toString(),
            'prescription_id'  => $prescriptionId,
            'customer_id'      => $customerId,
            'shop_id'          => $shop_id,
            'patient_name'     => $request->input('patient_name'),
            'doctor_name'      => $request->input('doctor_name'),
            'prescription_date'=> $request->input('prescription_date'),
            'address'          => $request->input('Address'),
            'phone'            => $request->input('Phone'),
            'email'            => $request->input('Email'),
            'instructions'     => $request->input('instructions'),
            'image_data'       => $base64Image,
            'image_type'       => $imageMime,
            'status'           => 'Pending',
            'created_at'       => Carbon::now()->toISOString(),
        ];

        $orderResponse = $client->post('/rest/v1/orders', [
            'json' => [$orderPayload],
            'headers' => ['Prefer' => 'return=representation']
        ]);

        if ($orderResponse->getStatusCode() !== 201) {
            return back()->with('error', 'Prescription uploaded but failed to create order: ' . $orderResponse->getBody());
        }

        return redirect('/dashboard')->with('success', 'Prescription uploaded and order created successfully!');
    }

    // ---------------------------
    // Pharmacy Dashboard
    // ---------------------------
    public function viewDashboard($shop_id)
    {
        $client = $this->supabaseClient();

        $pharmacyResponse = $client->get('/rest/v1/Shop', [
            'query' => ['shop_id' => 'eq.' . $shop_id]
        ]);
        $pharmacy = json_decode($pharmacyResponse->getBody(), true)[0] ?? null;

        if (!$pharmacy) {
            return redirect('/dashboard')->with('error', 'Pharmacy not found');
        }

        $prescriptionResponse = $client->get('/rest/v1/prescriptions', [
            'query' => ['shop_id' => 'eq.' . $shop_id]
        ]);
        $prescriptions = json_decode($prescriptionResponse->getBody(), true);

        return view('pharmacy-dashboard', compact('pharmacy', 'prescriptions'));
    }

    // ---------------------------
    // Update Prescription Status
    // ---------------------------
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:Pending,Accepted,Ready,Delivered',
        ]);

        $client = $this->supabaseClient();

        $client->patch('/rest/v1/prescriptions?id=eq.' . $id, [
            'json' => ['status' => $request->status]
        ]);

        return back()->with('success', "Prescription status updated to {$request->status}!");
    }

    // ---------------------------
    // Delete Prescription
    // ---------------------------
    public function delete($id)
    {
        $client = $this->supabaseClient();

        $client->delete('/rest/v1/prescriptions?id=eq.' . $id);

        return back()->with('success', 'Prescription deleted successfully!');
    }

    // ---------------------------
    // Private Helper
    // ---------------------------
    private function supabaseClient()
    {
        return new Client([
            'base_uri' => $this->supabaseUrl,
            'headers'  => [
                'apikey'        => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ]
        ]);
    }
}
