<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password | Medichain</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; display:flex; justify-content:center; align-items:center; height:100vh; }
        .card { background:white; padding:30px; border-radius:8px; box-shadow:0 2px 5px rgba(0,0,0,0.1); width: 350px; }
        input, button { width:100%; padding:10px; margin-top:10px; border-radius:5px; border:1px solid #ccc; font-size:1rem; }
        button { background:#28a745; color:white; border:none; cursor:pointer; }
        button:hover { background:#218838; }
        .status { background:#e0ffe0; padding:10px; border-radius:5px; color:#007700; margin-bottom:10px; }
        .error { background:#ffe0e0; padding:10px; border-radius:5px; color:#c00; margin-bottom:10px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Reset Password</h2>

        @if($errors->any())
            <div class="error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="password" name="password" placeholder="New Password" required>
            <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
            <button type="submit">Reset Password</button>
        </form>

        <p style="margin-top:15px;text-align:center;">
            <a href="{{ url('/login') }}">Back to Login</a>
        </p>
    </div>
</body>
</html>
