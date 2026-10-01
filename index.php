<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FuturePath - Discover Your Perfect Career Path</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            height: 100%;
            overflow: hidden; /* Scroll bar එක අයින් කරන්න */
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        
        /* Full Screen Hero Section */
        .hero-section {
            position: relative;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Background Video */
        .hero-video {
            position: absolute;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            transform: translate(-50%, -50%);
            object-fit: cover;
            z-index: 1;
        }
        
        /* Dark Overlay */
        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.65);
            z-index: 2;
        }
        
        /* Content */
        .hero-content {
            position: relative;
            z-index: 3;
            text-align: center;
            color: white;
            padding: 20px;
            max-width: 900px;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            text-shadow: 2px 2px 12px rgba(0, 0, 0, 0.9);
            line-height: 1.2;
        }
        
        .hero-subtitle {
            font-size: 1.2rem;
            font-weight: 400;
            margin-bottom: 35px;
            color: rgba(255, 255, 255, 0.9);
            text-shadow: 1px 1px 8px rgba(0, 0, 0, 0.8);
            line-height: 1.6;
        }
        
        .hero-btn-primary {
            background: #0d6efd;
            border: 2px solid #0d6efd;
            color: white;
            padding: 14px 38px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 8px;
            margin: 0 8px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .hero-btn-primary:hover {
            background: #0b5ed7;
            border-color: #0b5ed7;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(13, 110, 253, 0.4);
            color: white;
        }
        
        .hero-btn-outline {
            background: transparent;
            border: 2px solid white;
            color: white;
            padding: 14px 38px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 8px;
            margin: 0 8px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .hero-btn-outline:hover {
            background: white;
            color: #0d6efd;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(255, 255, 255, 0.3);
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.2rem;
            }
            
            .hero-subtitle {
                font-size: 1rem;
            }
            
            .hero-btn-primary,
            .hero-btn-outline {
                padding: 12px 28px;
                font-size: 1rem;
                margin: 6px 4px;
                display: block;
                width: 100%;
                max-width: 280px;
                margin-left: auto;
                margin-right: auto;
            }
            
            .btn-wrapper {
                display: flex;
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>

<!-- Hero Section — Full Screen -->
<div class="hero-section">
    
    <!-- Background Video -->
    <video autoplay muted loop playsinline class="hero-video">
        <source src="assets/Video/bg.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>
    
    <!-- Dark Overlay -->
    <div class="hero-overlay"></div>
    
    <!-- Content -->
    <div class="hero-content">
        <h1 class="hero-title">Discover Your Perfect Career Path</h1>
        <p class="hero-subtitle">
            Take our advanced aptitude test, get expert counseling, and unlock learning materials tailored just for you.
        </p>
        <div class="btn-wrapper">
            <a href="register.php" class="hero-btn-primary">Get Started</a>
            <a href="login.php" class="hero-btn-outline">Login</a>
        </div>
    </div>
    
</div>

</body>
</html>