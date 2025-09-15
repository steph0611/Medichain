<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payment Success | MediChain</title>
<style>
    body { font-family: Arial, sans-serif; padding: 50px; text-align: center; background-color: #f5f5f5; }
    .container { display: inline-block; background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    h1 { color: green; }
    a { display: inline-block; margin-top: 20px; text-decoration: none; background-color: #6772e5; color: white; padding: 10px 20px; border-radius: 4px; }
</style>
</head>
<body>
<div class="container">
    <h1>Payment Successful!</h1>
    <p>Prescription ID: {{ $prescriptionId }}</p>
    <a href="/dashboard">Go to Dashboard</a>
</div>
</body>
</html>
