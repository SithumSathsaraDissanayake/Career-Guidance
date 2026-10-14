<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Vision - FuturePath</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Segoe UI', system-ui, sans-serif; 
            background: #f8fafc;
            color: #1e293b;
        }
        
        /* Navbar */
        .custom-navbar { 
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important; 
            padding: 15px 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .custom-navbar .navbar-brand { 
            font-weight: 800; 
            font-size: 1.5rem; 
            color: #38bdf8 !important; 
        }
        .custom-navbar .nav-link { 
            color: #cbd5e1 !important; 
            font-weight: 500;
            transition: color 0.2s;
        }
        .custom-navbar .nav-link:hover { 
            color: #38bdf8 !important; 
        }
        
        /* Hero Section */
        .hero-section {
            background: 
                linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 58, 138, 0.92) 50%, rgba(2, 132, 199, 0.88) 100%),
                url('https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: white;
            padding: 100px 20px 80px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(circle at 30% 50%, rgba(56, 189, 248, 0.15), transparent 70%);
            pointer-events: none;
        }
        
        .hero-icon {
            font-size: 5rem;
            color: #38bdf8;
            margin-bottom: 20px;
            animation: pulse 3s ease-in-out infinite;
            filter: drop-shadow(0 0 30px rgba(56, 189, 248, 0.5));
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.08); }
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 15px;
            text-shadow: 2px 2px 12px rgba(0, 0, 0, 0.4);
            background: linear-gradient(135deg, #ffffff, #38bdf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .hero-subtitle {
            font-size: 1.2rem;
            color: #cbd5e1;
            max-width: 800px;
            margin: 0 auto;
            line-height: 1.7;
            font-weight: 300;
        }
        
        /* Content Section */
        .content-wrapper {
            max-width: 1200px;
            margin: -60px auto 0;
            padding: 0 20px 60px;
            position: relative;
            z-index: 10;
        }
        
        .vision-card {
            background: white;
            border-radius: 24px;
            padding: 50px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.08);
            border: 1px solid #e5e7eb;
            animation: slideUp 0.6s ease-out;
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .vision-statement {
            font-size: 1.35rem;
            line-height: 1.9;
            color: #334155;
            font-weight: 400;
            text-align: center;
            padding: 30px;
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            border-radius: 16px;
            border-left: 6px solid #0284c7;
            margin-bottom: 50px;
        }
        
        .vision-statement strong {
            color: #0369a1;
            font-weight: 700;
        }
        
        /* Feature Grid */
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        
        .feature-item {
            background: linear-gradient(135deg, #ffffff, #f8fafc);
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 30px 25px;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .feature-item::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #0284c7, #38bdf8);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }
        
        .feature-item:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(2, 132, 199, 0.15);
            border-color: #38bdf8;
        }
        
        .feature-item:hover::before {
            transform: scaleX(1);
        }
        
        .feature-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0284c7, #0ea5e9);
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 20px;
            box-shadow: 0 8px 20px rgba(2, 132, 199, 0.3);
        }
        
        .feature-item h4 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 12px;
        }
        
        .feature-item p {
            color: #64748b;
            font-size: 0.9rem;
            line-height: 1.7;
            margin: 0;
        }
        
        /* CTA Section */
        .cta-section {
            text-align: center;
            margin-top: 60px;
            padding: 50px 30px;
            background: linear-gradient(135deg, #0f172a, #1e293b);
            border-radius: 20px;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .cta-section::before {
            content: '';
            position: absolute;
            top: -50%; right: -50%;
            width: 200%; height: 200%;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.1), transparent 70%);
        }
        
        .cta-section h3 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 15px;
            position: relative;
        }
        
        .cta-section p {
            color: #cbd5e1;
            margin-bottom: 25px;
            position: relative;
        }
        
        .btn-cta {
            background: linear-gradient(135deg, #0284c7, #38bdf8);
            border: none;
            color: white;
            font-weight: 700;
            padding: 14px 40px;
            border-radius: 50px;
            font-size: 1.05rem;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            position: relative;
        }
        
        .btn-cta:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(56, 189, 248, 0.4);
            color: white;
        }
        
        /* Footer */
        .custom-footer {
            background: #0f172a;
            color: #94a3b8;
            padding: 40px 20px 20px;
            text-align: center;
            margin-top: 60px;
        }
        
        .custom-footer .footer-brand {
            font-size: 1.4rem;
            font-weight: 800;
            color: #38bdf8;
            margin-bottom: 10px;
        }
        
        .custom-footer p {
            font-size: 0.9rem;
            margin-bottom: 20px;
        }
        
        .custom-footer .footer-divider {
            border-top: 1px solid #1e293b;
            padding-top: 20px;
            font-size: 0.85rem;
            color: #64748b;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-title { font-size: 2.2rem; }
            .hero-subtitle { font-size: 1rem; }
            .hero-icon { font-size: 3.5rem; }
            .hero-section { padding: 60px 20px 50px; }
            .vision-card { padding: 30px 20px; }
            .vision-statement { font-size: 1.1rem; padding: 20px; }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg custom-navbar">
        <div class="container-fluid" style="max-width: 1200px; margin: 0 auto;">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-compass-fill me-2"></i>FuturePath
            </a>
            <button class="navbar-toggler text-white border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navMenu">
                <ul class="navbar-nav gap-2 align-items-center">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="our_vision.php">Our Vision</a></li>
                    <li class="nav-item"><a class="nav-link" href="our_mission.php">Our Mission</a></li>
                    <li class="nav-item ms-2">
                        <a href="login.php" class="btn btn-sm btn-outline-info rounded-pill px-3">Login</a>
                    </li>
                    <li class="nav-item">
                        <a href="register.php" class="btn btn-sm btn-info text-dark rounded-pill px-3 fw-bold">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero-section">
        <div class="hero-icon">
            <i class="bi bi-eye-fill"></i>
        </div>
        <h1 class="hero-title">Our Vision</h1>
        <p class="hero-subtitle">
            Empowering every Sri Lankan student to discover their true potential and build a 
            meaningful, successful career path for tomorrow.
        </p>
    </section>

    <!-- Main Content -->
    <div class="content-wrapper">
        <div class="vision-card">
            
            <!-- Vision Statement -->
            <div class="vision-statement">
                <i class="bi bi-quote fs-1 text-info"></i><br>
                To become <strong>Sri Lanka's most trusted career guidance platform</strong> — 
                a bridge that connects young minds with their dreams, using the power of 
                technology, data, and professional expertise.
            </div>
            
            <!-- Vision Pillars -->
            <h3 class="text-center fw-bold mb-2" style="color: #0f172a;">What We Envision</h3>
            <p class="text-center text-muted mb-5">Four pillars that guide our journey</p>
            
            <div class="feature-grid">
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <h4>Data-Driven Guidance</h4>
                    <p>
                        A future where every student's career choice is backed by intelligent analysis 
                        of their skills, aptitude, and academic background.
                    </p>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <h4>Expert Counseling Access</h4>
                    <p>
                        Professional career counselors accessible to every student — regardless of 
                        location, background, or economic status.
                    </p>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="bi bi-briefcase-fill"></i>
                    </div>
                    <h4>Direct Job Connection</h4>
                    <p>
                        A network that connects talented students directly with reputable employers 
                        across IT, engineering, healthcare, and beyond.
                    </p>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="bi bi-stars"></i>
                    </div>
                    <h4>Lifelong Learning</h4>
                    <p>
                        Continuous skill development through curated courses that keep our youth 
                        competitive in a rapidly changing world.
                    </p>
                </div>
                
            </div>
            
            <!-- CTA -->
            <div class="cta-section">
                <h3>Ready to Shape Your Future?</h3>
                <p>Join thousands of students who are already discovering their path</p>
                <a href="register.php" class="btn-cta">
                    Get Started <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            
        </div>
    </div>

    <!-- Footer -->
    <footer class="custom-footer">
        <div class="footer-brand">
            <i class="bi bi-compass-fill me-2"></i>FuturePath
        </div>
        <p>Career Guidance System — Sri Lanka</p>
        <div class="footer-divider">
            © 2026 FuturePath. All Rights Reserved.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>