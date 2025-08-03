<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class PrescriptionController extends Controller
{
    protected $supabaseUrl = 'https://zazdljyechhzsiodnvts.supabase.co';
    protected $supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InphemRsanllY2hoenNpb2RudnRzIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTMwMjE2MzMsImV4cCI6MjA2ODU5NzYzM30.OZLL_quXsqD2PJEtyQjSBOR9SaZBVXvaTfoAcBYCZTM';

    // Show modal form for prescription upload
    public function showUploadForm($shop_id)
    {
        $client = new Client([
            'base_uri' => $this->supabaseUrl,
            'headers' => [
                'apikey' => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
                'Accept' => 'application/json',
            ]
        ]);

        $response = $client->get('/rest/v1/Shop', [
            'query' => ['shop_id' => 'eq.' . $shop_id]
        ]);

        $pharmacy = json_decode($response->getBody(), true)[0] ?? null;

        if (!$pharmacy) {
            return redirect('/dashboard')->with('error', 'Pharmacy not found');
        }

        return view('upload-prescription', compact('pharmacy'));
    }

    // Handle modal form submission
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

        // Step 1: Get the pharmacy's custom Supabase URL & key
        $mainClient = new Client([
            'base_uri' => $this->supabaseUrl,
            'headers' => [
                'apikey' => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
                'Accept' => 'application/json',
            ]
        ]);

        $response = $mainClient->get('/rest/v1/Shop', [
            'query' => ['shop_id' => 'eq.' . $shop_id]
        ]);

        $pharmacy = json_decode($response->getBody(), true)[0] ?? null;

        if (!$pharmacy) {
            return redirect('/dashboard')->with('error', 'Pharmacy not found');
        }

        $pharmacyUrl = $pharmacy['url'];
        $pharmacyKey = $pharmacy['api_key'];

        // Step 2: Convert image to base64
        $image = $request->file('prescription_image');
        $base64Image = base64_encode(file_get_contents($image->getRealPath()));
        $imageMime = $image->getMimeType();

        // Step 3: Send data to that pharmacy's Supabase prescriptions table
        $pharmacyClient = new Client([
            'base_uri' => $pharmacyUrl,
            'headers' => [
                'apikey' => $pharmacyKey,
                'Authorization' => 'Bearer ' . $pharmacyKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        ]);

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
            'uploaded_at' => now()->toISOString(),
        ];

        $insertResponse = $pharmacyClient->post('/rest/v1/prescriptions', [
            'json' => $payload,
        ]);

        if ($insertResponse->getStatusCode() !== 201) {
            return back()->with('error', 'Failed to upload prescription.');
        }

        return redirect('/dashboard')->with('success', 'Prescription uploaded successfully!');
    }
}
