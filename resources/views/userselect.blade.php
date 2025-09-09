<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | Medichain</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
    /* Reset + base */
    * { box-sizing: border-box; }

    body, html {
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', sans-serif;
        background-color: #f2f2f2;
        height: 100vh;
        opacity: 1; /* start hidden */
        transition: opacity 0.6s ease;
    }

    body.fade-in { opacity: 1; }

    .container { display: flex; height: 100vh; }

    .left {
        flex: 1;
        background: url('/images/backimg1.png') no-repeat center center;
        background-size: cover;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        align-items: flex-start;
        padding: 40px;
        color: white;
    }

    .left .brand h1 { margin: 0; font-size: 2.5rem; color: #fff; }
    .left .brand p { font-size: 1rem; margin-top: 10px; color: #fff; line-height: 1.5; }

    .right {
        flex: 1; background-color: white;
        display: flex; justify-content: center; align-items: center;
        padding: 40px;
    }

    .form-box {
        width: 100%; max-width: 380px;
        padding: 2rem;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
        border-radius: 12px;
        background-color: #fff;
        text-align: center;

        transform: translateY(30px);
        opacity: 0;
        animation: slideIn 0.8s ease forwards;
    }

    @keyframes slideIn {
        to { transform: translateY(0); opacity: 1; }
    }

    .form-title {
        font-size: 28px; font-weight: bold; margin-bottom: 25px;
    }

    .form-group { display: flex; flex-direction: column; gap: 15px; }

    .role-button {
        padding: 12px; font-size: 16px; font-weight: 500;
        border: none; border-radius: 8px; cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .role-button.shop { background-color: #007bff; color: white; }
    .role-button.shop:hover { background-color: #0056d2; }

    .role-button.customer { background-color: #28a745; color: white; }
    .role-button.customer:hover { background-color: #1e7e34; }

    .bottom-text { margin-top: 20px; font-size: 14px; color: #444; }
    .login-link { color: #007bff; text-decoration: none; font-weight: 500; }
    .login-link:hover { text-decoration: underline; }

    .error {
        background: #ffe0e0; padding: 10px; border-radius: 5px;
        color: red; margin-bottom: 15px;
    }

    .success {
        background: #e0ffe0; padding: 10px; border-radius: 5px;
        color: green; margin-bottom: 15px;
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

    <!-- Fade transition script -->
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        document.body.classList.add("fade-in");

        const links = document.querySelectorAll("a, button[onclick]");

        links.forEach(el => {
            el.addEventListener("click", e => {
                const href = el.getAttribute("href") || el.getAttribute("onclick")?.match(/'(.*?)'/)?.[1];
                if (href && !href.startsWith("#") && !href.startsWith("http")) {
                    e.preventDefault();
                    document.body.classList.remove("fade-in");
                    setTimeout(() => { window.location.href = href; }, 600);
                }
            });
        });
    });
    </script>
</body>
</html>
