<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | Medigraph</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('styles.css') }}">

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
            <form method="POST" action="{{ url('/register') }}" class="form-container">
                @csrf
                <h2>Register</h2>

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
                    <div class="success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required>
                </div>

                <div class="form-group">
                    <label for="location">Address</label>
                    <input type="text" id="location" name="location" value="{{ old('location') }}" required>
                </div>

                <button type="submit">Register</button>

                <div class="login-link">
                    Already have an account? <a href="{{ url('/login') }}">Login</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
