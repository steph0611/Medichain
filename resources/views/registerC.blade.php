<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | Medichain</title>
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
                <input type="text" id="city" name="city" value="{{ old('city') }}" placeholder="Enter your city" required>
            </div>

            <!-- Address Input Field -->
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" value="{{ old('address') }}" placeholder="Enter your full address" required>
            </div>


            <!-- Hidden fields for Latitude & Longitude -->
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

    // Populate districts on province change
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

    // Fetch latitude & longitude using Nominatim when form is submitted
    const form = document.getElementById('registerForm');
    form.addEventListener('submit', async function(e) {
        e.preventDefault(); // Prevent immediate submit

        const address = `${document.getElementById('city').value}, ${provinceSelect.value}, Sri Lanka`;
        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(address)}&format=json&limit=1`, {
                headers: { 'User-Agent': 'Medichain/1.0' }
            });
            const data = await response.json();
            if (data.length > 0) {
                document.getElementById('latitude').value = data[0].lat;
                document.getElementById('longitude').value = data[0].lon;
            } else {
                document.getElementById('latitude').value = '';
                document.getElementById('longitude').value = '';
            }
        } catch (error) {
            console.error('Failed to fetch coordinates:', error);
            document.getElementById('latitude').value = '';
            document.getElementById('longitude').value = '';
        }

        form.submit(); // Submit after setting lat/lon
    });
</script>
</body>
</html>
