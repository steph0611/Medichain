<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Medichain</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <style>
        /* Reset and base */
        * { box-sizing: border-box; }
        html, body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f9;
            height: 100%;
        }

        /* Page fade-in (CSS-only, no JS dependency) */
        body {
            animation: pageFadeIn 0.6s ease both;
        }
        @keyframes pageFadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        /* Fade-out class (applied by JS right before navigation) */
        .fade-out {
            opacity: 0 !important;
            transition: opacity 0.5s ease;
        }

        /* Layout */
        .container {
            display: flex;
            min-height: 100vh;
        }

        .left {
            position: relative;
            flex: 1;
            background: url('/images/backimg1.png') no-repeat center center;
            background-size: cover;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 40px;
            color: white;
            overflow: hidden;
        }

        /* Dark overlay for readability */
        .left::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(0,0,0,0.25), rgba(0,0,0,0.55));
            pointer-events: none;
        }

        .left .brand { position: relative; z-index: 1; }
        .left .brand h1 { margin: 0; font-size: 2.5rem; color: #ffffff; }
        .left .brand p { font-size: 1rem; margin-top: 10px; color: #ffffff; line-height: 1.5; }

        .right {
            flex: 1;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        /* Login form + slide-in */
        .login-form {
            width: 100%;
            max-width: 350px;
            transform: translateX(50px);
            opacity: 0;
            animation: formSlideIn 0.8s ease 0.1s forwards;
        }
        @keyframes formSlideIn {
            to { transform: translateX(0); opacity: 1; }
        }

        .login-form h2 { margin-bottom: 20px; text-align: center; color: #222; }

        .form-group { margin-bottom: 15px; }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            font-size: 0.9rem;
            color: #333;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 0.95rem;
            transition: border 0.2s, box-shadow 0.2s;
        }
        .form-group input:focus {
            border: 1px solid #007bff;
            outline: none;
            box-shadow: 0 0 0 4px rgba(0,123,255,0.12);
        }

        .login-form .forgot { text-align: right; margin-top: 10px; }
        .login-form .forgot a { font-size: 13px; color: #007bff; text-decoration: none; }
        .login-form .forgot a:hover { text-decoration: underline; }

        .login-form button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border: none;
            border-radius: 6px;
            margin-top: 10px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            transition: transform 0.15s ease, filter 0.2s ease;
        }
        .login-form button:hover { transform: translateY(-2px); filter: brightness(1.05); }

        .login-form .register { margin-top: 20px; text-align: center; }
        .login-form .register a { color: #28a745; text-decoration: none; }
        .login-form .register a:hover { text-decoration: underline; }

        .error {
            background: #ffe0e0;
            padding: 10px;
            border-radius: 5px;
            color: #c00;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .container { flex-direction: column; }
            .left { display: none; }
            .right { flex: none; min-height: 100vh; }
        }

        /* Respect reduced motion preferences */
        @media (prefers-reduced-motion: reduce) {
            body, .fade-out, .login-form {
                animation: none !important;
                transition: none !important;
                transform: none !important;
                opacity: 1 !important;
            }
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
                    <a href="{{ url('/forgot-password') }}">Forgot Password?</a>
                </div>
                
                <button id="login-btn" type="submit">Log in</button>

                <div class="register">
                    <p>Don't have an account? <a href="{{ url('/userselect') }}">Sign Up</a></p>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Smooth fade-out before navigation (only for internal links)
        document.addEventListener("DOMContentLoaded", () => {
            const links = document.querySelectorAll("a[href]");
            links.forEach(link => {
                link.addEventListener("click", (e) => {
                    const href = link.getAttribute("href");
                    const isHash = href && href.startsWith("#");
                    const isExternal = href && (href.startsWith("http://") || href.startsWith("https://"));
                    const isBlank = link.target === "_blank";
                    if (!href || isHash || isExternal || isBlank) return;

                    e.preventDefault();
                    document.body.classList.add("fade-out");
                    setTimeout(() => { window.location.href = href; }, 500);
                });
            });
        });
    </script>
</body>
</html>
