<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Medichain</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Embedded CSS instead of external file -->
    <style>
        /* Reset and base */
        * { box-sizing: border-box; }
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f9;
            height: 100vh;
        }
        .container { display: flex; height: 100vh; }
        .left {
            flex: 1;
            background: url('/images/backimg1.png') no-repeat center center;
            background-size: cover;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 40px;
            color: white;
        }
        .left .brand h1 { margin: 0; font-size: 2.5rem; color: #ffffff; }
        .left .brand p { font-size: 1rem; margin-top: 10px; color: #ffffff; line-height: 1.5; }
        .right {
            flex: 1; background-color: white;
            display: flex; align-items: center; justify-content: center; padding: 40px;
        }
        .login-form { width: 100%; max-width: 350px; }
        .login-form h2 { margin-bottom: 20px; text-align: center; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 6px; font-weight: 600; font-size: 0.9rem; color: #333; }
        .form-group input {
            width: 100%; padding: 10px; border: 1px solid #ccc;
            border-radius: 6px; font-size: 0.95rem; transition: border 0.3s;
        }
        .form-group input:focus { border: 1px solid #007bff; outline: none; }
        .login-form button {
            width: 100%; padding: 12px; background-color: #007bff; color: white;
            border: none; border-radius: 6px; margin-top: 10px;
            cursor: pointer; font-size: 1rem; font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .login-form button:hover { background-color: #0056b3; }
        .login-form .forgot { text-align: right; margin-top: 10px; }
        .login-form .forgot a { font-size: 13px; color: #007bff; text-decoration: none; }
        .login-form .register { margin-top: 20px; text-align: center; }
        .login-form .register a { color: #28a745; text-decoration: none; }
        .error {
            background: #ffe0e0; padding: 10px; border-radius: 5px;
            color: red; margin-bottom: 15px; font-size: 0.9rem;
        }
        @media(max-width: 768px) {
            .container { flex-direction: column; }
            .left { display: none; }
            .right { flex: none; height: 100vh; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left">
            <div class="brand">
                <h1>MEDICHAIN</h1>
                <p>Empowering Healthcare, One Click at a Time: <br>Your Health, Your Records, Your Control.</p>
            </div>
        </div>
        <div class="right">
            <form method="POST" action="{{ url('/login') }}" class="login-form">
                @csrf
                <h2>Login</h2>
                @if($errors->any())
                    <div class="error">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="forgot">
                    <a href="#">Forgot Password?</a>
                </div>
                <button type="submit">Log in</button>
                <div class="register">
                    <p>Don't have an account? <a href="{{ url('/userselect') }}">Sign Up</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
