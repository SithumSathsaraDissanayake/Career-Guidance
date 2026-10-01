<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user_role = $_SESSION['user_role'] ?? 'student'; // Default fallback to student
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 py-3 mb-4 shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="#">FuturePath</a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-2">
                
                <?php if ($user_role === 'student'): ?>
                    <!-- Student Specific Links -->
                    <li class="nav-item">
                        <a class="nav-link" href="student_dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="aptitude_test.php">Aptitude Test</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="courses.php">Skill Courses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_jobs.php">View Vacancies</a>
                    </li>

                <?php elseif ($user_role === 'admin' || $user_role === 'company'): ?>
                    <!-- Admin / Company Specific Links -->
                    <li class="nav-item">
                        <a class="nav-link" href="add_job.php">Post Job</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_jobs.php">Manage Jobs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_applications.php">View Applications</a>
                    </li>
                <?php endif; ?>

            </ul>

            <div class="d-flex align-items-center gap-3">
                <span class="text-white">
                    Logged in as: <strong><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></strong> 
                    (<span class="text-warning"><?php echo ucfirst($user_role); ?></span>)
                </span>
                <a href="logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
            </div>
        </div>
    </div>
</nav>