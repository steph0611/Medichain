<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Medichain - Empowering Healthcare</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">

  <!-- Animate.css -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

  <!-- Custom Styles -->
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      scroll-behavior: smooth;
      background: #0d0d0d;
      color: #fff;
      overflow-x: hidden;
    }

    /* Floating gradient blobs */
    .gradient-bg {
      position: fixed;
      top: 0;
      left: 0;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle at 20% 20%, rgba(0,123,255,0.6), transparent 40%),
                  radial-gradient(circle at 80% 30%, rgba(255,65,108,0.5), transparent 40%),
                  radial-gradient(circle at 50% 80%, rgba(0,198,255,0.4), transparent 40%);
      animation: moveBackground 15s infinite alternate ease-in-out;
      z-index: -1;
    }
    @keyframes moveBackground {
      from { transform: translate(0, 0) scale(1); }
      to { transform: translate(-10%, -10%) scale(1.2); }
    }

    /* Navbar */
    .navbar {
      background: rgba(0, 0, 0, 0.75);
      backdrop-filter: blur(12px);
      border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .navbar-brand {
      font-weight: 800;
      font-size: 1.6rem;
      background: linear-gradient(45deg, #00c6ff, #ff416c);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    .nav-link {
      color: #ccc !important;
      margin: 0 12px;
      transition: 0.3s;
    }
    .nav-link:hover {
      color: #fff !important;
      text-shadow: 0px 0px 8px #00c6ff;
    }

    /* Hero */
    .hero {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      position: relative;
    }
    .hero-container {
      max-width: 850px;
      padding: 50px;
      background: rgba(255, 255, 255, 0.05);
      border-radius: 25px;
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      box-shadow: 0px 8px 40px rgba(0, 198, 255, 0.3);
      animation: fadeInUp 1.2s ease;
    }
    .hero h1 {
      font-weight: 800;
      font-size: 3.5rem;
      background: linear-gradient(90deg, #00c6ff, #ff416c, #ff4b2b);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    .btn-custom {
      background: linear-gradient(45deg, #ff416c, #ff4b2b);
      border: none;
      font-size: 1.2rem;
      font-weight: 600;
      padding: 14px 36px;
      border-radius: 40px;
      color: #fff;
      transition: all 0.3s ease;
    }
    .btn-custom:hover {
      transform: scale(1.08);
      box-shadow: 0 0 25px rgba(255,65,108,0.7);
    }

    /* Section Titles */
    section {
      padding: 100px 0;
    }
    .section-title {
      font-weight: 700;
      margin-bottom: 60px;
      text-align: center;
      font-size: 2.5rem;
      position: relative;
      display: inline-block;
    }
    .section-title::after {
      content: "";
      display: block;
      width: 80px;
      height: 4px;
      background: linear-gradient(90deg, #00c6ff, #ff416c);
      margin: 12px auto 0;
      border-radius: 4px;
    }

    /* Features */
    .feature-card {
      padding: 40px 25px;
      background: rgba(255, 255, 255, 0.05);
      border-radius: 20px;
      backdrop-filter: blur(12px);
      transition: transform 0.4s, box-shadow 0.4s;
      border: 1px solid rgba(255,255,255,0.15);
    }
    .feature-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 40px rgba(0,198,255,0.3);
    }
    .feature-icon {
      font-size: 3.5rem;
      margin-bottom: 20px;
      background: linear-gradient(45deg, #00c6ff, #ff416c);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    /* Testimonials */
    blockquote {
      font-size: 1.2rem;
      font-style: italic;
      color: #ddd;
      position: relative;
    }
    blockquote::before {
      content: "“";
      font-size: 3rem;
      color: #ff416c;
      position: absolute;
      left: -15px;
      top: -15px;
    }

    /* CTA */
    .cta {
      background: linear-gradient(135deg, #00c6ff, #007bff);
      color: white;
      padding: 100px 0;
      border-radius: 30px 30px 0 0;
    }

    /* Footer */
    footer {
      background: #0b0b0b;
      color: #999;
      padding: 40px 0;
      text-align: center;
      font-size: 0.9rem;
    }
    footer a {
      color: #00c6ff;
      text-decoration: none;
    }
    footer a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="gradient-bg"></div>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
      <a class="navbar-brand" href="#">Medichain</a>
      <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">☰</button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto align-items-lg-center">
          <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
          <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
          <li class="nav-item"><a class="nav-link" href="#testimonials">Testimonials</a></li>
          <li class="nav-item"><a class="btn btn-sm btn-light ms-lg-3" href="{{ route('login') }}">Login</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero -->
  <section class="hero">
    <div class="container hero-container animate__animated animate__fadeInUp">
      <h1>Welcome to Medichain</h1>
      <p class="lead mt-3">Empowering Healthcare, One Click at a Time.<br>Your Health, Your Records, Your Control.</p>
      <p class="mt-4">Manage your health records securely and easily. Discover seamless healthcare management, privacy, and control at your fingertips.</p>
      <a href="{{ route('login') }}" class="btn btn-custom mt-4">Get Started 🚀</a>
    </div>
  </section>

  <!-- Features -->
  <section id="features">
    <div class="container text-center">
      <h2 class="section-title">Why Choose Medichain?</h2>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="feature-card">
            <div class="feature-icon">🔒</div>
            <h5>Secure Records</h5>
            <p>Your health data is encrypted and stored safely.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="feature-card">
            <div class="feature-icon">⚡</div>
            <h5>Fast Access</h5>
            <p>Retrieve your medical history anytime, anywhere.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="feature-card">
            <div class="feature-icon">🌍</div>
            <h5>Global Availability</h5>
            <p>Accessible worldwide with seamless integration.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- About -->
  <section id="about" style="background:#111;">
    <div class="container text-center text-light">
      <h2 class="section-title">About Medichain</h2>
      <p>Medichain is a platform designed to put patients in control of their healthcare data. We provide secure, easy-to-use tools for managing medical records while maintaining privacy and compliance with healthcare standards.</p>
    </div>
  </section>

  <!-- Testimonials -->
  <section id="testimonials">
    <div class="container text-center">
      <h2 class="section-title">What Our Users Say</h2>
      <div class="row g-4">
        <div class="col-md-4">
          <blockquote>"Medichain made managing my prescriptions so easy!"</blockquote>
          <p>- Sarah K.</p>
        </div>
        <div class="col-md-4">
          <blockquote>"Finally, I feel in control of my medical history."</blockquote>
          <p>- David M.</p>
        </div>
        <div class="col-md-4">
          <blockquote>"Secure, simple, and exactly what healthcare needs."</blockquote>
          <p>- Dr. Priya R.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="cta text-center">
    <div class="container">
      <h2 class="section-title">Ready to Take Control?</h2>
      <p>Join thousands of people using Medichain for secure healthcare record management.</p>
      <a href="{{ url('/userselect') }}" class="btn btn-light btn-lg mt-3">Create Your Account</a>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <p>© 2025 Medichain. All Rights Reserved.</p>
    <p><a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
