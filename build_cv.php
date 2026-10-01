<?php
session_start();
include 'config/db.php'; // ඩේටාබේස් එක ලින්ක් කළා

// 🔐 පරිශීලකයා ශිෂ්‍යයෙක්ද (Student) කියා බැලීම
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_msg = "";

// 🗂️ දැනටමත් ශිෂ්‍යයාගේ මූලික විස්තර (නම සහ ඊමේල්) users ටේබල් එකෙන් ලබාගැනීම
$student_name = "";
$student_email = "";
try {
    $stmt = $conn->prepare("SELECT name, email FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user_data) {
        $student_name = $user_data['name'];
        $student_email = $user_data['email'];
    }
} catch (PDOException $e) {
    // Error handling
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FuturePath - Professional CV Builder</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        html, body { height: 100%; margin: 0; background-color: #f3f4f6; font-family: 'Segoe UI', system-ui, sans-serif; }
        .custom-navbar { background-color: #0f172a !important; padding: 15px 30px; }
        .custom-navbar .navbar-brand { font-weight: 800; font-size: 1.5rem; color: #38bdf8 !important; }
        .custom-navbar .nav-link { color: #9ca3af !important; font-weight: 500; }
        .custom-navbar .nav-link:hover { color: #ffffff !important; }
        
        .main-container { max-width: 850px; margin: 40px auto; padding: 0 20px; }
        .cv-card { background: white; border-radius: 16px; padding: 35px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); border: 1px solid #e5e7eb; }
        .section-title { font-size: 1.1rem; font-weight: 700; color: #0f172a; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 0.5px; }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg custom-navbar">
        <div class="container-fluid" style="max-width: 1200px; margin: 0 auto;">
            <a class="navbar-brand" href="student_dashboard.php"><i class="bi bi-compass-fill me-2"></i>FuturePath</a>
            <div class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav gap-2">
                    <li class="nav-item"><a class="nav-link" href="student_dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link active" href="build_cv.php">CV Builder</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="main-container">
        
        <!-- CV Form Card -->
        <div class="cv-card">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h3 class="fw-bold text-dark m-0"><i class="bi bi-file-earmark-person-fill text-primary me-2"></i>Build Your Professional CV</h3>
                    <p class="text-muted small m-0 mt-1">ඔබේ වෘත්තීය තොරතුරු ඇතුළත් කර FuturePath ප්‍රමිතියෙන් යුතු CV එකක් සකසා ගන්න.</p>
                </div>
            </div>
            <hr class="mb-4">
            
            <!-- PDF එක හදන පිටුවට දත්ත යොමු කරන Form එක -->
            <form action="generate_pdf.php" method="POST" target="_blank">
                
                <!-- 1. தனிப்பட்ட விபரங்கள் (Personal Info) -->
                <div class="section-title text-primary"><i class="bi bi-person-fill me-2"></i>Personal Information</div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary">Full Name</label>
                        <input type="text" class="form-control" name="full_name" value="<?php echo htmlspecialchars($student_name); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary">Email Address</label>
                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($student_email); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary">Contact Number</label>
                        <input type="text" class="form-control" name="phone" placeholder="e.g., 0771234567" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary">Permanent Address</label>
                        <input type="text" class="form-control" name="address" placeholder="e.g., Colombo, Sri Lanka" required>
                    </div>
                </div>

                <!-- 2. අධ්‍යාපන සුදුසුකම් (Education) -->
                <div class="section-title text-success"><i class="bi bi-mortarboard-fill me-2"></i>Education & Qualifications</div>
                <div class="mb-4">
                    <label class="form-label small fw-bold text-secondary">Highest Educational Qualification / Diploma</label>
                    <textarea class="form-control" name="education" rows="3" placeholder="e.g., Higher National Diploma in Information Technology (HNDIT) - Advanced Technological Institute (SLIATE)" required></textarea>
                </div>

                <!-- 3. තාක්ෂණික හා වෙනත් කුසලතා (Skills) -->
                <div class="section-title text-warning"><i class="bi bi-lightning-charge-fill me-2"></i>Skills & Technologies</div>
                <div class="mb-4">
                    <label class="form-label small fw-bold text-secondary">Key Skills (Comma separated)</label>
                    <input type="text" class="form-control" name="skills" placeholder="e.g., Java, PHP, Web Development, SQL, OOP Concepts" required>
                    <div class="form-text text-muted">කුසලතා එකින් එක වෙන් කිරීමට කමා ( , ) ලකුණ භාවිතා කරන්න.</div>
                </div>

                <!-- 4. සේවා පළපුරුද්ද හෝ ප්‍රොජෙක්ට්ස් (Experience / Projects) -->
                <div class="section-title text-danger"><i class="bi bi-briefcase-fill me-2"></i>Projects & Experience</div>
                <div class="mb-4">
                    <label class="form-label small fw-bold text-secondary">Academic Projects or Work Experience</label>
                    <textarea class="form-control" name="experience" rows="4" placeholder="e.g., Developed a Web-Based Career Guidance and Skill Development System using PHP and MySQL. Handled database optimization and front-end design." required></textarea>
                </div>

                <!-- CV Generation Button -->
                <button type="submit" class="btn btn-dark w-100 fw-bold py-2.5 rounded-3 fs-5 mt-2">
                    Generate & Download CV (PDF) <i class="bi bi-filetype-pdf ms-1 text-danger"></i>
                </button>
            </form>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>