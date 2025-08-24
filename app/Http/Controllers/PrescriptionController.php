<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PrescriptionController extends Controller
{
    protected $supabaseUrl = 'https://zazdljyechhzsiodnvts.supabase.co';
    protected $supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InphemRsanllY2hoenNpb2RudnRzIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTMwMjE2MzMsImV4cCI6MjA2ODU5NzYzM30.OZLL_quXsqD2PJEtyQjSBOR9SaZBVXvaTfoAcBYCZTM';

    // ---------------------------
    // Upload Prescription
    // ---------------------------
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

        // ✅ Prepare file
        $image = $request->file('prescription_image');
        $base64Image = base64_encode(file_get_contents($image->getRealPath()));
        $imageMime = $image->getMimeType();

        // ✅ Payload for main DB
        $payload = [
            'shop_id' => $shop_id,
            'customer_id' => Auth::id(),   // ✅ logged-in user ID
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

        // ✅ Insert into MAIN database prescriptions table
        try {
            $response = $mainClient->post('/rest/v1/prescriptions', [
                'json' => $payload,
            ]);

            if ($response->getStatusCode() !== 201) {
                return back()->with('error', 'Failed to upload prescription.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error uploading prescription: ' . $e->getMessage());
        }

        return redirect('/dashboard')->with('success', 'Prescription uploaded successfully!');
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
}
