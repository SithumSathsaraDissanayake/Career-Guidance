<?php
// 1. මුළු ෆයිල් එකටම එකම එක පාරක් session_start() පාවිච්චි කිරීම
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. ඩේටාබේස් කනෙක්ෂන් එක ඇතුළත් කිරීම
include 'config/db.php'; 

// 3. භාෂාව (Language) වෙනස් කිරීමේ කෝඩ් එක
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = $_SESSION['lang'] ?? 'en'; // පෙරනිමියෙන් English වේ

// 4. පරිශීලකයා ශිෂ්‍යයෙක්ද කියා බැලීම (Security Check)
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: login.php");
    exit();
}

// 5. Job Vacancies (Companies දාලා තියෙන Jobs) ඩෑෂ්බෝඩ් එකේ පෙන්වීමට ඩේටාබේස් එකෙන් ලබාගැනීම
$job_vacancies = [];
try {
    $query = "SELECT * FROM jobs ORDER BY id DESC LIMIT 14"; 
    $stmt = $conn->query($query);
    $job_vacancies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // ඩේටාබේස් Error එකක් ආවොත් handle කිරීම
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FuturePath - Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        html, body { 
            height: 100%; 
            margin: 0; 
            background-color: var(--bs-body-bg); 
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif; 
            transition: background-color 0.3s ease, color 0.3s ease; 
        }
        .main-wrapper { display: flex; flex-direction: column; min-height: 100vh; }
        
        /* Smooth Dropdown Hover */
        .navbar-nav .dropdown:hover .dropdown-menu {
            display: block;
            margin-top: 0;
            animation: fadeIn 0.2s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Modern Professional Navbar */
        .custom-navbar { 
            background: linear-gradient(135deg, #1a1d20 0%, #2c3034 100%) !important; 
            padding: 14px 30px; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .custom-navbar .navbar-brand { font-weight: 800; font-size: 1.5rem; letter-spacing: 0.5px; color: #ffffff !important; }
        .custom-navbar .nav-link { color: rgba(255,255,255,0.85) !important; font-weight: 500; font-size: 0.95rem; transition: color 0.2s; }
        .custom-navbar .nav-link:hover { color: #38bdf8 !important; }
        
        /* Dashboard Container & Sidebar Styling (Dark/Light Responsive) */
        .dashboard-container { display: flex; flex: 1; }
        .sidebar { 
            width: 280px; 
            background-color: var(--bs-body-bg);
            border-right: 1px solid var(--bs-border-color);
            box-shadow: 4px 0 25px rgba(0,0,0,0.02); 
            display: flex; 
            flex-direction: column; 
            justify-content: space-between; 
            padding: 25px 20px; 
            z-index: 10;
        }
        .sidebar-menu { display: flex; flex-direction: column; flex-grow: 1; }
        
        .sidebar .nav-link { 
            color: var(--bs-body-color); 
            font-weight: 600; 
            padding: 12px 16px; 
            border-radius: 10px; 
            margin-bottom: 6px; 
            display: flex; 
            align-items: center; 
            gap: 14px; 
            text-decoration: none; 
            transition: all 0.25s ease;
        }
        .sidebar .nav-link i { font-size: 1.1rem; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { 
            background-color: rgba(13, 110, 253, 0.12);
            color: #0d6efd; 
            transform: translateX(4px);
        }
        .logout-section { border-top: 1px solid var(--bs-border-color); padding-top: 15px; }
        
        /* Main Content Area */
        .main-content { flex: 1; padding: 35px; background-color: var(--bs-body-bg); }
        
        /* Modern Cards Styling with Full Dark Mode Support */
        .dashboard-card { 
            border-radius: 16px; 
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1); 
            background-color: var(--bs-body-bg); 
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border: 1px solid var(--bs-border-color);
        }
        .dashboard-card:hover { 
            transform: translateY(-6px); 
            box-shadow: 0 16px 24px rgba(0,0,0,0.1) !important; 
        }
        
        .icon-box { 
            width: 58px; 
            height: 58px; 
            border-radius: 14px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 1.6rem; 
            margin-bottom: 18px; 
        }

        /* Welcome Banner Accent */
        .welcome-banner {
            background-color: var(--bs-body-bg);
            border: 1px solid var(--bs-border-color);
            border-left: 5px solid #0d6efd;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

<div class="main-wrapper">
    <!-- 🧭 Professional Top Navbar -->
    <nav class="navbar navbar-expand-lg custom-navbar sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <i class="bi bi-compass-fill text-primary"></i> FuturePath
            </a>
            <button class="navbar-toggler text-white border-0" type="button" data-bs-toggle="collapse" data-bs-target="#topNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="topNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-4 gap-2">
                    <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-house-door me-1"></i> Home</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="about.php" id="aboutDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-info-circle me-1"></i> About us
                        </a>
                        <ul class="dropdown-menu shadow-lg rounded-4 border-0 py-2 mt-2" aria-labelledby="aboutDropdown">
                            <li><a class="dropdown-item py-2" href="our_vision.php"><i class="bi bi-eye text-primary me-2"></i> Our Vision</a></li>
                            <li><a class="dropdown-item py-2" href="our_mission.php"><i class="bi bi-flag text-success me-2"></i> Our Mission</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header text-dark fw-bold">Contact Support</h6></li>
                            <li><span class="dropdown-item-text text-muted small"><i class="bi bi-telephone-fill me-2"></i> 0761195689</span></li>
                            <li><span class="dropdown-item-text text-muted small"><i class="bi bi-envelope-fill me-2"></i> dissanayakesms@gmail.com</span></li>
                            <li><a class="dropdown-item py-2 text-primary fw-semibold" href="https://facebook.com" target="_blank"><i class="bi bi-facebook me-2"></i> Facebook</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown hover-dropdown">
                        <a class="nav-link dropdown-toggle" href="career_guidance.php" id="careerDropdown" role="button">
                            <i class="bi bi-briefcase me-1"></i> Career guidance
                        </a>
                        <ul class="dropdown-menu shadow-lg rounded-4 border-0 py-2 mt-2" aria-labelledby="careerDropdown">
                            <li><a class="dropdown-item py-2" href="build_cv.php"><i class="bi bi-file-earmark-person text-primary me-2"></i> Build my CV</a></li>
                            <li><a class="dropdown-item py-2" href="aptitude_test.php"><i class="bi bi-puzzle text-warning me-2"></i> Psychological Career Test</a></li>
                            <li><a class="dropdown-item py-2" href="counseling.php"><i class="bi bi-calendar-check text-success me-2"></i> Book Sessions</a></li>
                        </ul>
                    </li>
                </ul>

                <ul class="navbar-nav ms-auto align-items-center gap-3">
                    <!-- Language Selector -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white bg-dark bg-opacity-50 px-3 py-2 rounded-pill border border-secondary border-opacity-25 small" href="#" id="langDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-translate me-1"></i> English
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg rounded-4 border-0 mt-2" aria-labelledby="langDropdown">
                            <li><a class="dropdown-item py-2" href="?lang=en">English</a></li>
                            <li><a class="dropdown-item py-2" href="?lang=si">සිංහල (Sinhala)</a></li>
                            <li><a class="dropdown-item py-2" href="?lang=ta">தமிழ் (Tamil)</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a href="login.php" class="nav-link px-3 btn btn-outline-light btn-sm rounded-pill text-white">Login</a></li>
                    <li class="nav-item"><a href="register.php" class="nav-link px-3 btn btn-primary btn-sm rounded-pill text-white">Register</a></li>
                    
                    <!-- Dark Mode Toggle Button -->
                    <li class="nav-item ms-1">
                        <button id="darkModeToggle" class="btn btn-dark bg-opacity-50 border border-secondary border-opacity-25 text-white rounded-circle p-2 shadow-sm" title="Toggle Theme" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                            <i id="darkModeIcon" class="bi bi-moon-fill"></i>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main App Body (Sidebar + Content) -->
    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        <nav class="sidebar">
            <div class="sidebar-menu">
                <div class="text-center my-3">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center shadow-inner" style="width: 70px; height: 70px; font-size: 1.8rem;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <h6 class="mt-3 fw-bold mb-1"><?php echo htmlspecialchars($_SESSION['user_name']); ?></h6>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-1 rounded-pill small">Student Scholar</span>
                </div>
                <hr class="opacity-25 my-4">
                <ul class="nav flex-column gap-1">
                    <li class="nav-item">
                        <a class="nav-link active" href="student_dashboard.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                <li class="nav-item">
                    <a class="nav-link" href="aptitude_test.php">
                        <i class="bi bi-journal-text"></i> Aptitude Test
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="add_skills.php">
                        <i class="bi bi-lightning-charge-fill"></i> My Skills
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="add_academic.php">
                        <i class="bi bi-mortarboard-fill"></i> Academic Info
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="courses.php">
                        <i class="bi bi-book"></i> Skill Courses
                    </a>
                </li>
                 <li class="nav-item">
                        <a class="nav-link" href="build_cv.php">
                            <i class="bi bi-file-earmark-person"></i> CV Builder
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_jobs.php">
                            <i class="bi bi-briefcase"></i> Job Vacancies
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="counseling.php">
                            <i class="bi bi-calendar-event"></i> Counseling
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="logout-section">
                <a class="nav-link text-danger" href="logout.php">
                    <i class="bi bi-box-arrow-right"></i> Logout System
                </a>
            </div>
        </nav>

        <!-- Main Content Area -->
        <main class="main-content">
            <!-- Welcome Banner -->
            <div class="p-4 welcome-banner mb-4">
                <h2 class="fw-bold m-0">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>! 👋</h2>
                <p class="text-muted m-0 mt-1">Welcome to the FuturePath Career Guidance System. Plan your tomorrow today with advanced tools.</p>
            </div>

                        <!-- Quick Action Links -->
            <div class="mb-4">
                <h3 class="fw-bold mb-3 fs-5 text-secondary">Quick Navigation</h3>
                <div class="row g-4">
                    
                    <!-- 1. Career Recommendations -->
                    <div class="col-md-6 col-lg-4">
                        <div class="dashboard-card h-100 p-4 rounded-4 d-flex flex-column border-start border-primary border-4">
                            <h5 class="fw-bold fs-6 mb-2">
                                <i class="bi bi-stars text-primary me-2"></i>Career Recommendations
                            </h5>
                            <p class="text-muted small flex-grow-1">Check out the best career tracks tailored uniquely for your skillset.</p>
                            <a href="recommendation_results.php" class="btn btn-outline-primary mt-3 rounded-pill btn-sm fw-semibold">
                                View Matches <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>

                    <!-- 2. My Skills -->
                    <div class="col-md-6 col-lg-4">
                        <div class="dashboard-card h-100 p-4 rounded-4 d-flex flex-column border-start border-primary border-4">
                            <h5 class="fw-bold fs-6 mb-2">
                                <i class="bi bi-lightning-charge-fill text-primary me-2"></i>My Skills
                            </h5>
                            <p class="text-muted small flex-grow-1">Add your skills to get better career recommendations.</p>
                            <a href="add_skills.php" class="btn btn-outline-primary mt-3 rounded-pill btn-sm fw-semibold">
                                Add Skills <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>

                    <!-- 3. Academic Info -->
                    <div class="col-md-6 col-lg-4">
                        <div class="dashboard-card h-100 p-4 rounded-4 d-flex flex-column border-start border-primary border-4">
                            <h5 class="fw-bold fs-6 mb-2">
                                <i class="bi bi-mortarboard-fill text-primary me-2"></i>Academic Info
                            </h5>
                            <p class="text-muted small flex-grow-1">Update your stream and GPA for accurate matching.</p>
                            <a href="add_academic.php" class="btn btn-outline-primary mt-3 rounded-pill btn-sm fw-semibold">
                                Update Info <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>

                    <!-- 4. Expert Appointments -->
                    <div class="col-md-6 col-lg-4">
                        <div class="dashboard-card h-100 p-4 rounded-4 d-flex flex-column border-start border-primary border-4">
                            <h5 class="fw-bold fs-6 mb-2">
                                <i class="bi bi-calendar-check text-primary me-2"></i>Expert Appointments
                            </h5>
                            <p class="text-muted small flex-grow-1">Book live advisory sessions and join interactive virtual guidance calls.</p>
                            <a href="book_appointment.php" class="btn btn-outline-primary mt-3 rounded-pill btn-sm fw-semibold">
                                Book Call <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>

                    <!-- 5. Course Payments -->
                    <div class="col-md-6 col-lg-4">
                        <div class="dashboard-card h-100 p-4 rounded-4 d-flex flex-column border-start border-primary border-4">
                            <h5 class="fw-bold fs-6 mb-2">
                                <i class="bi bi-credit-card text-primary me-2"></i>Course Payments
                            </h5>
                            <p class="text-muted small flex-grow-1">Upload course payment receipts and check validation status.</p>
                            <a href="course_payment.php" class="btn btn-outline-primary mt-3 rounded-pill btn-sm fw-semibold">
                                Make Payment <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>

                </div>
            </div>    

            <!-- Core Dashboard Feature Cards Grid -->
            <div class="row g-4 mb-4">
                <!-- 1. Aptitude Test Card -->
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="dashboard-card h-100 p-4">
                        <div class="d-flex flex-column justify-content-between h-100">
                            <div>
                                <div class="icon-box bg-primary bg-opacity-10 text-primary">
                                    <i class="bi bi-patch-question-fill"></i>
                                </div>
                                <h5 class="card-title fw-bold mb-2">1. Aptitude Test</h5>
                                <p class="card-text text-muted small">Take short smart evaluations to discover your innate talents and true industry interests.</p>
                            </div>
                            <a href="aptitude_test.php" class="btn btn-outline-primary w-100 mt-4 rounded-pill">Start Test <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>

                <!-- 2. Skill Courses Card -->
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="dashboard-card h-100 p-4">
                        <div class="d-flex flex-column justify-content-between h-100">
                            <div>
                                <div class="icon-box bg-success bg-opacity-10 text-success">
                                    <i class="bi bi-laptop-fill"></i>
                                </div>
                                <h5 class="card-title fw-bold mb-2">2. Skill Development</h5>
                                <p class="card-text text-muted small">Watch expert-curated modules and videos designed to maximize competencies.</p>
                            </div>
                            <a href="courses.php" class="btn btn-outline-success w-100 mt-4 rounded-pill">Explore Courses <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>

                <!-- 3. Career Counseling Card -->
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="dashboard-card h-100 p-4">
                        <div class="d-flex flex-column justify-content-between h-100">
                            <div>
                                <div class="icon-box bg-warning bg-opacity-10 text-warning">
                                    <i class="bi bi-chat-left-heart-fill"></i>
                                </div>
                                <h5 class="card-title fw-bold mb-2">3. Career Counseling</h5>
                                <p class="card-text text-muted small">Consult professional advisors directly to map out safe corporate pathways.</p>
                            </div>
                            <a href="counseling.php" class="btn btn-outline-warning w-100 mt-4 rounded-pill">Book Session <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Secondary Row: CV Builder & Job Vacancies -->
            <div class="row g-4">
                <!-- 4. CV Builder Card -->
                <div class="col-12 col-md-6">
                    <div class="dashboard-card h-100 p-4 border-start border-success border-4">
                        <div class="d-flex flex-column justify-content-between h-100">
                            <div>
                                <div class="icon-box bg-success bg-opacity-10 text-success">
                                    <i class="bi bi-file-earmark-person-fill"></i>
                                </div>
                                <h5 class="card-title fw-bold text-success mb-2">Professional CV Builder</h5>
                                <p class="card-text text-muted small">Compile your education history and technical skills instantly into standardized industry resume templates for PDF export.</p>
                            </div>
                            <a href="build_cv.php" class="btn btn-success text-white w-100 mt-4 rounded-pill shadow-sm">Create My CV <i class="bi bi-filetype-pdf ms-1"></i></a>
                        </div>
                    </div>
                </div>

                <!-- 5. Job Vacancies Card -->
                <div class="col-12 col-md-6">
                    <div class="dashboard-card h-100 p-4 border-start border-dark border-4">
                        <div class="d-flex flex-column justify-content-between h-100">
                            <div>
                                <div class="icon-box bg-dark bg-opacity-10 text-dark">
                                    <i class="bi bi-briefcase-fill"></i>
                                </div>
                                <h5 class="card-title fw-bold text-dark mb-2">NVQ Job Vacancies</h5>
                                <p class="card-text text-muted small">Browse exclusive vacancies posted by leading corporations matching your exact qualification bands and criteria.</p>
                            </div>
                            <a href="view_jobs.php" class="btn btn-dark w-100 mt-4 rounded-pill shadow-sm">Explore Vacancies <i class="bi bi-search ms-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<!-- Scripts & Theme Handler -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const toggleBtn = document.getElementById('darkModeToggle');
    const toggleIcon = document.getElementById('darkModeIcon');
    const htmlElement = document.documentElement;

    // LocalStorage preference loading
    const currentTheme = localStorage.getItem('theme') || 'light';
    htmlElement.setAttribute('data-bs-theme', currentTheme);
    updateIcon(currentTheme);

    toggleBtn.addEventListener('click', () => {
        let theme = htmlElement.getAttribute('data-bs-theme');
        let newTheme = theme === 'dark' ? 'light' : 'dark';
        
        htmlElement.setAttribute('data-bs-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateIcon(newTheme);
    });

    function updateIcon(theme) {
        if (theme === 'dark') {
            toggleIcon.classList.remove('bi-moon-fill');
            toggleIcon.classList.add('bi-sun-fill');
        } else {
            toggleIcon.classList.remove('bi-sun-fill');
            toggleIcon.classList.add('bi-moon-fill');
        }
    }
</script>
</body>
</html>