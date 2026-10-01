<?php
session_start();
include 'config/db.php'; // ඩේටාබේස් එක ලින්ක් කළා

// පරිශීලකයා ඇඩ්මින් කෙනෙක්ද කියා බැලීම
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// 📊 ඩෑෂ්බෝඩ් එකට අවශ්‍ය දත්ත ගණනය කිරීම් (Total Counts)
$total_students = 0;
$total_courses = 0;
$pending_appointments = 0;
$recent_appointments = []; 

try {
    // 1. මුළු සිසුන් ගණන (role = 'student')
    $student_stmt = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'student'");
    $total_students = $student_stmt ? $student_stmt->fetchColumn() : 0;

    // 2. මුළු පාඨමාලා ගණන
    $course_stmt = $conn->query("SELECT COUNT(*) FROM courses");
    $total_courses = $course_stmt ? $course_stmt->fetchColumn() : 0;

    // 3. දැනට Pending තියෙන ඇපොයින්ට්මන්ට් ගණන
    $app_stmt = $conn->query("SELECT COUNT(*) FROM appointments WHERE status = 'pending'");
    $pending_appointments = $app_stmt ? $app_stmt->fetchColumn() : 0;

    // 🔔 අලුත්ම ඇපොයින්ට්මන්ට් 5ක් ඩේටාබේස් එකෙන් ලබාගැනීම
    $recent_stmt = $conn->query("SELECT a.*, u.name AS student_name FROM appointments a 
                                 JOIN users u ON a.user_id = u.user_id 
                                 ORDER BY a.created_at DESC LIMIT 5");
    
    if ($recent_stmt) {
        $recent_appointments = $recent_stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    // Database Errors හසුරුවා ගැනීම
}

include 'includes/header.php';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
    html, body { height: 100%; margin: 0; background-color: #f8f9fa; }
    .main-wrapper { display: flex; flex-direction: column; min-height: 100vh; }
    .custom-navbar { background-color: #0f172a !important; padding: 12px 30px; }
    .custom-navbar .navbar-brand { font-weight: 700; font-size: 1.4rem; color: #ffffff !important; }
    .custom-navbar .nav-link { color: #ffffff !important; font-weight: 500; }
    .dashboard-container { display: flex; flex: 1; }
    .sidebar { width: 260px; background: #ffffff; box-shadow: 2px 0 10px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between; padding: 20px 15px; }
    .sidebar .nav-link { color: #495057; font-weight: 500; padding: 12px 15px; border-radius: 8px; margin-bottom: 5px; display: flex; align-items: center; gap: 12px; text-decoration: none; }
    .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #f1f5f9; color: #0f172a; border-left: 4px solid #0f172a; }
    .logout-section { border-top: 1px solid #eee; padding-top: 15px; }
    .main-content { flex: 1; padding: 30px; }
    .stat-card { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.02); transition: transform 0.2s; }
    .stat-card:hover { transform: translateY(-3px); }
</style>

<div class="main-wrapper">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg custom-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="admin_dashboard.php">FuturePath <span class="badge bg-danger fs-6 ms-2">Admin Panel</span></a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-4 gap-3">
                    <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Home</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        <nav class="sidebar">
            <div>
                <div class="text-center my-3">
                    <h6 class="mt-2 fw-bold text-dark"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Administrator'); ?></h6>
                    <span class="badge bg-danger-subtle text-danger border border-danger">Administrator</span>
                </div>
                <hr>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="admin_dashboard.php">
                            <i class="bi bi-grid-1x2-fill"></i> Overview Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_appointments.php">
                            <i class="bi bi-calendar-check-fill"></i> Appointments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_courses.php">
                            <i class="bi bi-book-half"></i> Manage Courses
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_jobs.php">
                            <i class="bi bi-briefcase-fill"></i> Manage Job Vacancies
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_payments.php">
                            <i class="bi bi-receipt"></i> Course Payments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_reports.php">
                            <i class="bi bi-file-earmark-bar-graph-fill"></i> System Reports
                        </a>
                    </li>
                </ul>
            </div>
            <div class="logout-section">
                <a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </nav>

        <!-- Main Content Area -->
        <main class="main-content">
            <div class="p-4 bg-white rounded-3 shadow-sm mb-4 border-start border-dark border-5">
                <h2 class="fw-bold text-dark m-0">Welcome Back, Admin! 👋</h2>
                <p class="text-muted m-0 mt-1">From here you can manage all student progress, courses, job vacancies, and appointments.</p>
            </div>

            <!-- Quick Management Navigation Links -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm rounded-4 p-3">
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-sliders me-2 text-primary"></i> Quick Access Shortcuts</h6>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="admin_payments.php" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-2">
                                <i class="bi bi-receipt me-1"></i> Manage Course Payments
                            </a>
                            <a href="admin_reports.php" class="btn btn-outline-success btn-sm rounded-pill px-3 py-2">
                                <i class="bi bi-graph-up-arrow me-1"></i> Analytics & System Reports (PDF)
                            </a>
                            <a href="manage_jobs.php" class="btn btn-outline-dark btn-sm rounded-pill px-3 py-2">
                                <i class="bi bi-briefcase me-1"></i> Manage Job Vacancies
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 📊 Statistical Summary Cards -->
            <div class="row g-4 mb-5">
                <div class="col-12 col-md-4">
                    <div class="card stat-card bg-primary text-white p-3">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase fw-bold opacity-75 small">Total Registered Students</h6>
                                <h2 class="fw-bold m-0 mt-1"><?php echo $total_students; ?></h2>
                            </div>
                            <i class="bi bi-people-fill fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card stat-card bg-success text-white p-3">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase fw-bold opacity-75 small">Active Skill Courses</h6>
                                <h2 class="fw-bold m-0 mt-1"><?php echo $total_courses; ?></h2>
                            </div>
                            <i class="bi bi-book fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card stat-card bg-warning text-dark p-3">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase fw-bold opacity-75 small">Pending Appointments</h6>
                                <h2 class="fw-bold m-0 mt-1"><?php echo $pending_appointments; ?></h2>
                            </div>
                            <i class="bi bi-hourglass-split fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 📋 Recent Appointments Table Section -->
            <div class="card border-0 shadow-sm p-4 rounded-3 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold text-dark m-0"><i class="bi bi-clock-history me-2 text-primary"></i> Recent Appointment Requests</h4>
                    <a href="manage_appointments.php" class="btn btn-outline-primary btn-sm fw-bold">View All</a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Student Name</th>
                                <th>Requested Counselor</th>
                                <th>Date</th>
                                <th>Time Slot</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($recent_appointments) > 0): ?>
                                <?php foreach ($recent_appointments as $app): ?>
                                    <tr>
                                        <td class="fw-bold text-dark"><?php echo htmlspecialchars($app['student_name']); ?></td>
                                        <td><?php echo htmlspecialchars($app['counselor_name'] ?? 'Counselor'); ?></td>
                                        <td><?php echo htmlspecialchars($app['appointment_date'] ?? $app['created_at']); ?></td>
                                        <td><?php echo isset($app['appointment_time']) ? date('h:i A', strtotime($app['appointment_time'])) : 'N/A'; ?></td>
                                        <td>
                                            <?php if (($app['status'] ?? '') == 'pending'): ?>
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            <?php elseif (($app['status'] ?? '') == 'approved'): ?>
                                                <span class="badge bg-success">Approved</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Completed</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No appointment requests have been received yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>