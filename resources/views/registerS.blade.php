<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | Medichain</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('styles.css') }}">

    <style>
        /* Smooth fade effect */
        body, html {
            margin: 0;
            padding: 0;
            height: 100vh;
            font-family: 'Segoe UI', sans-serif;

            opacity: 1;
            transition: opacity 0.6s ease;
        }
        body.fade-out { opacity: 0; }

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
        .left .brand h1 { margin: 0; font-size: 2.5rem; color: #fff; }
        .left .brand p { margin-top: 10px; line-height: 1.5; font-size: 1rem; color: #fff; }

        .right {
            flex: 1;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .form-container {
            width: 100%;
            max-width: 380px;
            padding: 2rem;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);

            transform: translateY(30px);
            opacity: 0;
            animation: slideIn 0.8s ease forwards;
        }

        @keyframes slideIn {
            to { transform: translateY(0); opacity: 1; }
        }

        .form-container h2 { text-align: center; margin-bottom: 20px; }

        .form-group { margin-bottom: 15px; }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            font-size: 0.9rem;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 0.95rem;
        }
        .form-group input:focus {
            border-color: #007bff;
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            transition: background 0.3s ease;
        }
        button:hover { background: #0056b3; }

        .error, .success {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
        .error { background: #ffe0e0; color: red; }
        .success { background: #e0ffe0; color: green; }

        .login-link {
            text-align: center;
            margin-top: 15px;
            font-size: 0.9rem;
        }
        .login-link a {
            color: #007bff;
            text-decoration: none;
        }
        .login-link a:hover { text-decoration: underline; }

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
            <p>Empowering Healthcare, One Click at a Time:<br>Your Health, Your Records, Your Control.</p>
        </div>
    </div>

    <div class="right">
        <form method="POST" action="{{ url('/registerS') }}" class="form-container">
            @csrf
            <h2>Register Pharmacy</h2>

            @if($errors->any())
                <div class="error">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="success">{{ session('success') }}</div>
            @endif

            <div class="form-group">
                <label for="name">Owner's Full Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label for="shop_name">Shop Name</label>
                <input type="text" id="shop_name" name="shop_name" value="{{ old('shop_name') }}" required>
            </div>

            <div class="form-group">
                <label for="location">Street / Area</label>
                <input type="text" id="location" name="location" value="{{ old('location') }}" required>
            </div>

            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city" value="{{ old('city') }}" placeholder="Enter your city" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required>
            </div>

            <div class="form-group">
                <label for="user_name">Username</label>
                <input type="text" id="user_name" name="user_name" value="{{ old('user_name') }}" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit">Register</button>

            <div class="login-link">
                Already have an account? <a href="{{ url('/login') }}">Login</a>
            </div>
        </form>
    </div>
</div>

<!-- Fade transition -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    // Ensure body is visible
    document.body.classList.remove("fade-out");

    const links = document.querySelectorAll("a, button[onclick]");
    links.forEach(el => {
        el.addEventListener("click", e => {
            const href = el.getAttribute("href") || el.getAttribute("onclick")?.match(/'(.*?)'/)?.[1];
            if (href && !href.startsWith("#") && !href.startsWith("http")) {
                e.preventDefault();
                document.body.classList.add("fade-out");
                setTimeout(() => { window.location.href = href; }, 600);
            }
        });
    });
});
</script>
</body>
</html>
