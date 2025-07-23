<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Medichain</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('styles.css') }}">
    
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
