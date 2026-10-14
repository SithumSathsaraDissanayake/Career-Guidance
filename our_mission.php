<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Mission - FuturePath</title>
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
                linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(6, 78, 59, 0.92) 50%, rgba(16, 185, 129, 0.88) 100%),
                url('https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=1920&q=80');
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
            background: radial-gradient(circle at 70% 50%, rgba(16, 185, 129, 0.15), transparent 70%);
            pointer-events: none;
        }
        
        .hero-icon {
            font-size: 5rem;
            color: #10b981;
            margin-bottom: 20px;
            animation: pulse 3s ease-in-out infinite;
            filter: drop-shadow(0 0 30px rgba(16, 185, 129, 0.5));
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
            background: linear-gradient(135deg, #ffffff, #10b981);
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
        
        .mission-card {
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
        
        .mission-statement {
            font-size: 1.35rem;
            line-height: 1.9;
            color: #334155;
            font-weight: 400;
            text-align: center;
            padding: 30px;
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            border-radius: 16px;
            border-left: 6px solid #10b981;
            margin-bottom: 50px;
        }
        
        .mission-statement strong {
            color: #047857;
            font-weight: 700;
        }
        
        /* Action Grid */
        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        
        .action-item {
            background: linear-gradient(135deg, #ffffff, #f8fafc);
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 30px 25px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        
        .action-item::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #10b981, #34d399);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }
        
        .action-item:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(16, 185, 129, 0.15);
            border-color: #10b981;
        }
        
        .action-item:hover::before {
            transform: scaleX(1);
        }
        
        .action-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 20px;
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        }
        
        .action-item h4 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 12px;
        }
        
        .action-item p {
            color: #64748b;
            font-size: 0.9rem;
            line-height: 1.7;
            margin: 0;
        }
        
        /* Commitment Section */
        .commitment-section {
            background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
            border-radius: 20px;
            padding: 40px;
            margin-top: 50px;
            border-left: 6px solid #10b981;
        }
        
        .commitment-section h3 {
            color: #065f46;
            font-weight: 800;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .commitment-list {
            list-style: none;
            padding: 0;
        }
        
        .commitment-list li {
            padding: 12px 0;
            color: #334155;
            font-size: 1rem;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            border-bottom: 1px dashed #a7f3d0;
        }
        
        .commitment-list li:last-child {
            border-bottom: none;
        }
        
        .commitment-list li i {
            color: #10b981;
            font-size: 1.2rem;
            margin-top: 3px;
            flex-shrink: 0;
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
            top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.1), transparent 70%);
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
            background: linear-gradient(135deg, #10b981, #34d399);
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
            box-shadow: 0 15px 35px rgba(16, 185, 129, 0.4);
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
            .mission-card { padding: 30px 20px; }
            .mission-statement { font-size: 1.1rem; padding: 20px; }
            .commitment-section { padding: 25px 20px; }
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
                    <li class="nav-item"><a class="nav-link" href="our_vision.php">Our Vision</a></li>
                    <li class="nav-item"><a class="nav-link active" href="our_mission.php">Our Mission</a></li>
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
            <i class="bi bi-flag-fill"></i>
        </div>
        <h1 class="hero-title">Our Mission</h1>
        <p class="hero-subtitle">
            To guide, empower, and connect every student with the right skills, the right 
            mentors, and the right opportunities — creating a future-ready generation.
        </p>
    </section>

    <!-- Main Content -->
    <div class="content-wrapper">
        <div class="mission-card">
            
            <!-- Mission Statement -->
            <div class="mission-statement">
                <i class="bi bi-quote fs-1 text-success"></i><br>
                Our mission is to <strong>bridge the gap between education and employment</strong> 
                by providing intelligent, personalized, and accessible career guidance to every 
                Sri Lankan student — through innovation, empathy, and excellence.
            </div>
            
            <!-- Mission Actions -->
            <h3 class="text-center fw-bold mb-2" style="color: #0f172a;">What We Do</h3>
            <p class="text-center text-muted mb-5">Concrete actions we take every day</p>
            
            <div class="action-grid">
                
                <div class="action-item">
                    <div class="action-icon">
                        <i class="bi bi-clipboard-data-fill"></i>
                    </div>
                    <h4>Assess & Analyze</h4>
                    <p>
                        Provide scientifically designed aptitude tests that analyze skills, 
                        interests, and academic background to identify the best career path.
                    </p>
                </div>
                
                <div class="action-item">
                    <div class="action-icon">
                        <i class="bi bi-person-video3"></i>
                    </div>
                    <h4>Guide with Experts</h4>
                    <p>
                        Connect students with professional counselors for one-on-one guidance 
                        sessions — via chat, video call, or appointment booking.
                    </p>
                </div>
                
                <div class="action-item">
                    <div class="action-icon">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <h4>Build Skills</h4>
                    <p>
                        Curate and recommend industry-relevant courses to develop the 
                        technical, cognitive, and professional skills needed in modern careers.
                    </p>
                </div>
                
                <div class="action-item">
                    <div class="action-icon">
                        <i class="bi bi-building-fill-check"></i>
                    </div>
                    <h4>Connect with Employers</h4>
                    <p>
                        Bridge students with reputable companies through a centralized job 
                        portal — making the transition from education to employment seamless.
                    </p>
                </div>
                
                <div class="action-item">
                    <div class="action-icon">
                        <i class="bi bi-file-earmark-person-fill"></i>
                    </div>
                    <h4>Empower with Tools</h4>
                    <p>
                        Provide free tools like CV builders and PDF exports that help students 
                        present themselves professionally to employers.
                    </p>
                </div>
                
                <div class="action-item">
                    <div class="action-icon">
                        <i class="bi bi-translate"></i>
                    </div>
                    <h4>Serve in Every Language</h4>
                    <p>
                        Offer services in English, Sinhala, and Tamil — ensuring every student 
                        can access guidance in the language they're most comfortable with.
                    </p>
                </div>
                
            </div>
            
            <!-- Our Commitment -->
            <div class="commitment-section">
                <h3>
                    <i class="bi bi-patch-check-fill"></i>
                    Our Commitment to You
                </h3>
                <ul class="commitment-list">
                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        <span><strong>Accessibility:</strong> Free and easy-to-use platform for every Sri Lankan student, regardless of background.</span>
                    </li>
                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        <span><strong>Accuracy:</strong> Data-driven career recommendations built on proven algorithms and expert insights.</span>
                    </li>
                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        <span><strong>Privacy:</strong> Protecting your personal data with strict security and confidentiality measures.</span>
                    </li>
                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        <span><strong>Excellence:</strong> Continuous improvement to deliver the highest quality career guidance experience.</span>
                    </li>
                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        <span><strong>Community:</strong> Building a network of students, counselors, and employers working together for success.</span>
                    </li>
                </ul>
            </div>
            
            <!-- CTA -->
            <div class="cta-section">
                <h3>Be Part of Our Mission</h3>
                <p>Your career journey starts with a single step</p>
                <a href="register.php" class="btn-cta">
                    Join Now <i class="bi bi-arrow-right"></i>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>