<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$top_career = null;
$recommendations = [];

// 🔍 Get the top career with the Recommendation Engine
try {
    require_once 'classes/recommendation_engine.php';
    $engine = new RecommendationEngine($conn);
    $recommendations = $engine->calculateRecommendations($user_id);
    
    if (!empty($recommendations)) {
        $top_career = $recommendations[0];
    }
} catch (Exception $e) {
    $top_career = null;
}

// 📚 Available Courses
$all_courses = [];
$recommended_courses = [];

try {
    // ✅ Take only courses relevant to the recommended career.
    if ($top_career && $top_career['match_percentage'] > 0) {
        $career_id = $top_career['career_id'];
        
        // ✅ Recommended courses (matching career_id)
        $rec_stmt = $conn->prepare("
            SELECT * FROM courses 
            WHERE career_id = :cid 
            ORDER BY course_id ASC
        ");
        $rec_stmt->execute([':cid' => $career_id]);
        $recommended_courses = $rec_stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // ✅ All other courses (except recommended ones)
    if ($top_career && !empty($recommended_courses)) {
        $other_stmt = $conn->prepare("
            SELECT * FROM courses 
            WHERE career_id != :cid OR career_id IS NULL
            ORDER BY career_id ASC, course_id ASC
        ");
        $other_stmt->execute([':cid' => $top_career['career_id']]);
        $all_courses = $other_stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $other_stmt = $conn->query("SELECT * FROM courses ORDER BY career_id ASC, course_id ASC");
        $all_courses = $other_stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $all_courses = [];
    $recommended_courses = [];
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
    
    /* ✅ Course Card */
    .course-card { 
        border: none; 
        border-radius: 14px; 
        transition: all 0.3s ease; 
        background: white;
        box-shadow: 0 4px 15px rgba(0,0,0,0.04);
        border-left: 4px solid #0d6efd;
        overflow: hidden;
    }
    .course-card:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 12px 25px rgba(0,0,0,0.1) !important;
    }
    
    /* ✅ Recommended Course Card — Highlight */
    .course-card.recommended {
        border-left: 4px solid #10b981;
        background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
        box-shadow: 0 4px 20px rgba(16, 185, 129, 0.15);
    }
    .course-card.recommended:hover {
        box-shadow: 0 12px 30px rgba(16, 185, 129, 0.25) !important;
    }
    
    /* ✅ Badges */
    .duration-badge {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        font-size: 0.75rem;
        padding: 5px 12px;
        border-radius: 20px;
        font-weight: 600;
    }
    .fee-badge {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        font-size: 0.75rem;
        padding: 5px 12px;
        border-radius: 20px;
        font-weight: 600;
    }
    .recommended-badge {
        background: linear-gradient(135deg, #10b981, #047857);
        color: white;
        font-size: 0.7rem;
        padding: 4px 10px;
        border-radius: 15px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    /* ✅ Course Title */
    .course-title {
        font-weight: 700;
        color: #1a1d20;
        font-size: 1.05rem;
        line-height: 1.4;
        margin-bottom: 10px;
    }
    
    /* ✅ Search Button */
    .btn-search {
        background: linear-gradient(135deg, #0d6efd, #0a58ca);
        border: none;
        color: white;
        font-weight: 600;
        padding: 9px 20px;
        border-radius: 8px;
        transition: all 0.25s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-size: 0.9rem;
    }
    .btn-search:hover {
        background: linear-gradient(135deg, #0a58ca, #084298);
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(13, 110, 253, 0.3);
        color: white;
    }
    
    /* ✅ Description */
    .course-desc {
        color: #6b7280;
        font-size: 0.85rem;
        line-height: 1.5;
    }
    
    /* ✅ Section Header */
    .section-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e5e7eb;
        margin-bottom: 20px;
    }
    .section-header .icon-box {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
    }
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

            <!-- 🎯 Recommendation Section -->
            <?php if ($top_career && $top_career['match_percentage'] > 0): ?>
                <div class="alert alert-success d-flex align-items-center rounded-3 p-3 mb-4 border-0 shadow-sm" role="alert">
                    <i class="bi bi-stars fs-4 me-3"></i>
                    <div>
                        Based on your test results, your recommended career path is 
                        <strong><?php echo htmlspecialchars($top_career['title']); ?></strong> 
                        (<?php echo $top_career['match_percentage']; ?>% match). 
                        We suggest starting with the courses below!
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-warning d-flex align-items-center rounded-3 p-3 mb-4 border-0 shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                    <div>
                        You haven't taken the Aptitude Test yet 
                        <a href="aptitude_test.php" class="alert-link fw-bold">Click Here!</a> 
                        Take the test first. Then you can see the courses that best suit you.
                    </div>
                </div>
            <?php endif; ?>

            <!-- ✅ SECTION 1: Recommended Courses (Highlight) -->
            <?php if (!empty($recommended_courses)): ?>
                <div class="section-header">
                    <div class="icon-box bg-success bg-opacity-10 text-success">
                        <i class="bi bi-award-fill"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark m-0">Recommended For You</h4>
                        <p class="text-muted small m-0">Courses tailored to your recommended career path</p>
                    </div>
                </div>
                
                <div class="row g-4 mb-5">
                    <?php foreach ($recommended_courses as $course): ?>
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="course-card recommended h-100 p-4 d-flex flex-column">
                                
                                <!-- Recommended Badge -->
                                <div class="mb-2">
                                    <span class="recommended-badge">
                                        <i class="bi bi-star-fill"></i> Recommended
                                    </span>
                                </div>
                                
                                <!-- Badges Row -->
                                <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
                                    <?php if (!empty($course['duration']) && $course['duration'] !== 'NULL'): ?>
                                        <span class="duration-badge">
                                            <i class="bi bi-clock-fill me-1"></i>
                                            <?php echo htmlspecialchars($course['duration']); ?>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($course['fee']) && $course['fee'] > 0): ?>
                                        <span class="fee-badge">
                                            <i class="bi bi-currency-exchange me-1"></i>
                                            Rs. <?php echo number_format($course['fee'], 0); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Course Title -->
                                <h5 class="course-title">
                                    <?php echo htmlspecialchars($course['course_name']); ?>
                                </h5>
                                
                                <!-- Course Description -->
                                <p class="course-desc flex-grow-1 mb-3">
                                    <?php echo htmlspecialchars($course['description']); ?>
                                </p>
                                
                                <!-- Search Button -->
                                <?php 
                                    $search_url = !empty($course['url']) && $course['url'] !== 'NULL' 
                                        ? $course['url'] 
                                        : 'https://www.google.com/search?q=' . urlencode($course['course_name']);
                                ?>
                                <a href="<?php echo htmlspecialchars($search_url); ?>" 
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   class="btn-search w-100 mt-2">
                                    <i class="bi bi-search"></i>
                                    Find This Course
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- ✅ SECTION 2: All Other Courses -->
            <div class="section-header">
                <div class="icon-box bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-collection-play-fill"></i>
                </div>
                <div>
                    <h4 class="fw-bold text-dark m-0">
                        <?php echo !empty($recommended_courses) ? 'Explore Other Courses' : 'Available Courses'; ?>
                    </h4>
                    <p class="text-muted small m-0">
                        <?php echo !empty($recommended_courses) ? 'Browse courses from other career paths' : 'All available courses'; ?>
                    </p>
                </div>
            </div>
            
            <div class="row g-4">
                <?php if (count($all_courses) > 0): ?>
                    <?php foreach ($all_courses as $course): ?>
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="course-card h-100 p-4 d-flex flex-column">
                                
                                <!-- Badges Row -->
                                <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
                                    <?php if (!empty($course['duration']) && $course['duration'] !== 'NULL'): ?>
                                        <span class="duration-badge">
                                            <i class="bi bi-clock-fill me-1"></i>
                                            <?php echo htmlspecialchars($course['duration']); ?>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($course['fee']) && $course['fee'] > 0): ?>
                                        <span class="fee-badge">
                                            <i class="bi bi-currency-exchange me-1"></i>
                                            Rs. <?php echo number_format($course['fee'], 0); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Course Title -->
                                <h5 class="course-title">
                                    <?php echo htmlspecialchars($course['course_name']); ?>
                                </h5>
                                
                                <!-- Course Description -->
                                <p class="course-desc flex-grow-1 mb-3">
                                    <?php echo htmlspecialchars($course['description']); ?>
                                </p>
                                
                                <!-- Search Button -->
                                <?php 
                                    $search_url = !empty($course['url']) && $course['url'] !== 'NULL' 
                                        ? $course['url'] 
                                        : 'https://www.google.com/search?q=' . urlencode($course['course_name']);
                                ?>
                                <a href="<?php echo htmlspecialchars($search_url); ?>" 
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   class="btn-search w-100 mt-2">
                                    <i class="bi bi-search"></i>
                                    Find This Course
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <?php if (empty($recommended_courses)): ?>
                        <div class="col-12">
                            <div class="alert alert-secondary text-center py-5">
                                <i class="bi bi-inbox fs-1 d-block mb-3 text-muted"></i>
                                <h5 class="text-muted">No courses available yet.</h5>
                                <p class="text-muted small mb-0">Please check back later.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>