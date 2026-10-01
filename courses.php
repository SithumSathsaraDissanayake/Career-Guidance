<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_result = null;

// Aptitude Test Result ලබාගැනීම
try {
    $stmt = $conn->prepare("SELECT * FROM test_results WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 1");
    $stmt->execute(['user_id' => $user_id]);
    $user_result = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $user_result = null;
}

// 📚 Available Courses ලබාගැනීම
$all_courses = [];
try {
    $course_stmt = $conn->query("SELECT * FROM courses ORDER BY course_id DESC");
    $all_courses = $course_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $all_courses = [];
}

include 'includes/header.php';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
    html, body { height: 100%; margin: 0; background-color: #f8f9fa; }
    .main-wrapper { display: flex; flex-direction: column; min-height: 100vh; }
    .custom-navbar { background-color: #1a1d20 !important; padding: 12px 30px; }
    .custom-navbar .navbar-brand { font-weight: 700; font-size: 1.4rem; color: #ffffff !important; }
    .custom-navbar .nav-link { color: #ffffff !important; font-weight: 500; }
    .dashboard-container { display: flex; flex: 1; }
    .sidebar { width: 260px; background: #ffffff; box-shadow: 2px 0 10px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between; padding: 20px 15px; }
    .sidebar .nav-link { color: #495057; font-weight: 500; padding: 12px 15px; border-radius: 8px; margin-bottom: 5px; display: flex; align-items: center; gap: 12px; text-decoration: none; }
    .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #e9ecef; color: #0d6efd; }
    .logout-section { border-top: 1px solid #eee; padding-top: 15px; }
    .main-content { flex: 1; padding: 30px; }
    .course-card { border: none; border-radius: 12px; transition: transform 0.2s; background: white; }
    .course-card:hover { transform: translateY(-5px); box-shadow: 0 8px 15px rgba(0,0,0,0.06) !important; }
</style>

<div class="main-wrapper">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg custom-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">FuturePath</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-4 gap-3">
                    <li class="nav-item"><a class="nav-link" href="student_dashboard.php">Home</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div>
                <div class="text-center my-3">
                    <h6 class="mt-2 fw-bold text-dark"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Student'); ?></h6>
                    <span class="badge bg-light text-primary border border-primary">Student</span>
                </div>
                <hr>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="student_dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="aptitude_test.php"><i class="bi bi-journal-text"></i> Aptitude Test</a></li>
                    <li class="nav-item"><a class="nav-link active" href="courses.php"><i class="bi bi-book"></i> Skill Courses</a></li>
                    <li class="nav-item"><a class="nav-link" href="counseling.php"><i class="bi bi-calendar-event"></i> Counseling</a></li>
                </ul>
            </div>
            <div class="logout-section">
                <a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <div class="p-4 bg-white rounded-3 shadow-sm mb-4 border-start border-success border-5">
                <h2 class="fw-bold text-dark m-0">Skill Development Courses 📚</h2>
                <p class="text-muted m-0 mt-1">Take high-quality courses here to develop your skills.</p>
            </div>

            <!-- 🎯 Aptitude Test එක කරලා තියෙනවා නම් රෙකමන්ඩ් කරන කොටස -->
            <?php if (!empty($user_result)): ?>
                <div class="alert alert-success d-flex align-items-center rounded-3 p-3 mb-4" role="alert">
                    <i class="bi bi-stars fs-4 me-3"></i>
                    <div>
                        Based on your test results, your recommended career path is <strong><?php echo htmlspecialchars($user_result['category']); ?></strong>. We suggest starting with the courses below!
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-warning d-flex align-items-center rounded-3 p-3 mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                    <div>
                        You haven't taken the Aptitude Test yet <a href="aptitude_test.php" class="alert-link">Click Here ! </a> Take the test first. Then you can see the courses that best suit you.
                    </div>
                </div>
            <?php endif; ?>

            <!-- 🗂️ කෝස් ලිස්ට් එක පෙන්වන කොටස -->
            <h4 class="fw-bold text-dark mb-4 mt-2">Available Courses</h4>
            <div class="row g-4">
                <?php if (count($all_courses) > 0): ?>
                    <?php foreach ($all_courses as $course): ?>
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card h-100 shadow-sm p-3 course-card">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div>
                                        <span class="badge bg-success-subtle text-success mb-2"><i class="bi bi-clock"></i> <?php echo htmlspecialchars($course['duration'] ?? 'N/A'); ?></span>
                                        <h5 class="card-title fw-bold text-dark mb-2"><?php echo htmlspecialchars($course['course_name']); ?></h5>
                                        <p class="card-text text-muted small"><?php echo htmlspecialchars($course['description']); ?></p>
                                    </div>
                                    <div class="mt-3">
                                        <?php 
                                            $link = $course['url'] ?? $course['course_url'] ?? '#';
                                        ?>
                                        <a href="<?php echo htmlspecialchars($link); ?>" target="_blank" class="btn btn-success w-100">Start Learning <i class="bi bi-box-arrow-up-right ms-1"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-secondary text-center">No courses have been added yet.</div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>