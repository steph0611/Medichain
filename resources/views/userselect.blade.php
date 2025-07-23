<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | Medichain</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('styles3.css') }}">
</head>
<body>
    <div class="container">
        <!-- Left Panel -->
        <div class="left">
            <div class="brand">
                <h1>MEDICHAIN</h1>
                <p>Empowering Healthcare, One Click at a Time:<br>Your Health, Your Records, Your Control.</p>
            </div>
        </div>

        <!-- Right Panel -->
        <div class="right">
            <div class="form-box">
                <h2 class="form-title">Select Your Role</h2>
                <form onsubmit="return false;">
                    <div class="form-group">
                        <button onclick="location.href='{{ url('/registerS') }}'" class="role-button shop">I'm a Shop Owner</button>
                        <button onclick="location.href='{{ url('/registerC') }}'" class="role-button customer">I'm a Customer</button>
                    </div>
                </form>
                <div class="bottom-text">
                    Already have an account? <a href="{{ url('/login') }}" class="login-link">Log In</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>


