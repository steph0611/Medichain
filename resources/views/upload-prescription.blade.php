@extends('layouts.app')

@section('content')
<div class="main-content">
    <h1>Upload Details for {{ $pharmacy['shop_name'] }}</h1>

    @if(session('error'))
        <div style="color:red;">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div style="color:green;">{{ session('success') }}</div>
    @endif

    <form action="{{ route('pharmacy.upload.submit', $pharmacy['shop_id']) }}" method="POST">
        @csrf
        <div class="form-section">
            <h3>Step 1: Upload Prescription Image</h3>
            <div class="upload-box">
                <label for="prescriptionImage" class="upload-label">
                    <div class="upload-icon">📷</div>
                    <p>Drag and drop your prescription here<br><span>or click to browse files</span></p>
                    <input type="file" name="prescription_image" id="prescriptionImage" accept=".jpg,.jpeg,.png,.pdf" required>
                </label>
                <p class="file-hint">Supported formats: JPG, PNG, PDF • Max size: 10MB</p>
            </div>
        </div>

        <!-- Step 2 -->
        <div class="form-section">
            <h3>Step 2: Prescription Details</h3>
            <div class="grid">
                <div class="form-group">
                    <label>Patient Name</label>
                    <input type="text" name="patient_name"required>
                </div>
                <div class="form-group">
                    <label>Doctor Name</label>
                    <input type="text" name="doctor_name" placeholder="Enter doctor's name" required>
                </div>
                <div class="form-group">
                    <label>Prescription Date</label>
                    <input type="date" name="prescription_date" required>
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="Address" required>
                </div>

                <div class="form-group">
                    <label>Contact No.</label>
                    <input type="text" name="Phone" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="text" name="Email" required>
                </div>
                
                <div class="form-group full-width">
                    <label>Special Instructions</label>
                    <textarea name="instructions" rows="3" placeholder="Enter any notes..."></textarea>
                </div>
            </div>
        </div>

        <button type="submit" class="btn-primary">Submit</button>
    </form>
</div>
@endsection
