<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | Medichain</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('styles.css') }}">

    <style>
        /* Page Fade-in Animation */
        body {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }

        body.loaded {
            opacity: 1;
            transform: translateY(0);
        }

        /* Container Layout */
        .container {
            display: flex;
            height: 100vh;
        }

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

        .left .brand h1 {
            font-size: 2.5rem;
            margin: 0;
        }

        .left .brand p {
            font-size: 1rem;
            margin-top: 10px;
            line-height: 1.5;
        }

        .right {
            flex: 1;
            background: white;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        .form-container {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            background: #fff;
            animation: fadeUp 1s ease;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        label {
            font-weight: 600;
            display: block;
            margin-bottom: 5px;
        }

        input, select {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            border: none;
            border-radius: 8px;
            background: #007bff;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #0056d2;
        }

        .login-link {
            margin-top: 15px;
            font-size: 14px;
            text-align: center;
        }

        .login-link a {
            color: #007bff;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        /* Error / Success */
        .error {
            background: #ffe0e0;
            color: red;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        .success {
            background: #e0ffe0;
            color: green;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        /* Mobile Responsive */
        @media(max-width: 768px) {
            .container {
                flex-direction: column;
            }
            .left {
                display: none;
            }
            .right {
                flex: none;
                height: 100vh;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Left Side -->
    <div class="left">
        <div class="brand">
            <h1>MEDICHAIN</h1>
            <p>Empowering Healthcare, One Click at a Time:<br>Your Health, Your Records, Your Control.</p>
        </div>
    </div>

    <!-- Right Side -->
    <div class="right">
        <form method="POST" action="{{ url('/registerC') }}" class="form-container" id="registerForm">
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
                <div class="success">{{ session('success') }}</div>
            @endif

            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required>
            </div>

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
                <label for="province">Province</label>
                <select id="province" name="province" required>
                    <option value="">-- Select Province --</option>
                    <option value="Central">Central</option>
                    <option value="Eastern">Eastern</option>
                    <option value="North Central">North Central</option>
                    <option value="Northern">Northern</option>
                    <option value="North Western">North Western</option>
                    <option value="Sabaragamuwa">Sabaragamuwa</option>
                    <option value="Southern">Southern</option>
                    <option value="Uva">Uva</option>
                    <option value="Western">Western</option>
                </select>
            </div>

            <div class="form-group">
                <label for="district">District</label>
                <select id="district" name="district" required>
                    <option value="">-- Select District --</option>
                </select>
            </div>

            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city" value="{{ old('city') }}" required>
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" value="{{ old('address') }}" required>
            </div>

            <input type="hidden" id="latitude" name="latitude">
            <input type="hidden" id="longitude" name="longitude">

            <button type="submit">Register</button>

            <div class="login-link">
                Already have an account? <a href="{{ url('/login') }}">Login</a>
            </div>
        </form>
    </div>
</div>

<script>
    // Fade-in trigger
    window.addEventListener("load", () => {
        document.body.classList.add("loaded");
    });

    // Districts grouped by Province
    const districtData = {
        "Central": ["Kandy", "Matale", "Nuwara Eliya"],
        "Eastern": ["Ampara", "Batticaloa", "Trincomalee"],
        "North Central": ["Anuradhapura", "Polonnaruwa"],
        "Northern": ["Jaffna", "Kilinochchi", "Mannar", "Mullaitivu", "Vavuniya"],
        "North Western": ["Kurunegala", "Puttalam"],
        "Sabaragamuwa": ["Kegalle", "Ratnapura"],
        "Southern": ["Galle", "Hambantota", "Matara"],
        "Uva": ["Badulla", "Monaragala"],
        "Western": ["Colombo", "Gampaha", "Kalutara"]
    };

    const provinceSelect = document.getElementById('province');
    const districtSelect = document.getElementById('district');

    provinceSelect.addEventListener('change', function () {
        const districts = districtData[this.value] || [];
        districtSelect.innerHTML = '<option value="">-- Select District --</option>';
        districts.forEach(d => {
            const option = document.createElement('option');
            option.value = d;
            option.textContent = d;
            districtSelect.appendChild(option);
        });
    });

    // Fetch latitude & longitude using Nominatim
    const form = document.getElementById('registerForm');
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        const address = `${document.getElementById('city').value}, ${provinceSelect.value}, Sri Lanka`;
        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(address)}&format=json&limit=1`, {
                headers: { 'User-Agent': 'Medichain/1.0' }
            });
            const data = await response.json();
            if (data.length > 0) {
                document.getElementById('latitude').value = data[0].lat;
                document.getElementById('longitude').value = data[0].lon;
            }
        } catch (error) {
            console.error('Failed to fetch coordinates:', error);
        }
        form.submit();
    });
</script>
</body>
</html>
               