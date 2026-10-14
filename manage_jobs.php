<?php
session_start();
include 'config/db.php';

// Admin check
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = '';
$error = '';

// Handle Delete Action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $job_id = intval($_GET['id']);
    try {
        $delete_stmt = $conn->prepare("DELETE FROM jobs WHERE job_id = :id");
        $delete_stmt->execute([':id' => $job_id]);
        $message = "Job vacancy deleted successfully!";
    } catch (PDOException $e) {
        $error = "Error deleting job: " . $e->getMessage();
    }
}

// Fetch all jobs
$jobs = [];
try {
    $stmt = $conn->query("SELECT * FROM jobs ORDER BY created_at DESC");
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching jobs: " . $e->getMessage();
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
    .manage-card { background: white; border-radius: 15px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.02); }
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
        <!-- Sidebar -->
        <nav class="sidebar">
            <div>
                <div class="text-center my-3">
                    <h6 class="mt-2 fw-bold text-dark"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?></h6>
                    <span class="badge bg-danger-subtle text-danger border border-danger">Administrator</span>
                </div>
                <hr>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="admin_dashboard.php"><i class="bi bi-grid-1x2-fill"></i> Overview Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_appointments.php"><i class="bi bi-calendar-check-fill"></i> Appointments</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_courses.php"><i class="bi bi-book-half"></i> Manage Courses</a></li>
                    <li class="nav-item"><a class="nav-link active" href="manage_jobs.php"><i class="bi bi-briefcase-fill"></i> Manage Job Vacancies</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin_payments.php"><i class="bi bi-receipt"></i> Course Payments</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin_reports.php"><i class="bi bi-file-earmark-bar-graph-fill"></i> System Reports</a></li>
                </ul>
            </div>
            <div class="logout-section">
                <a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <div class="p-4 bg-white rounded-3 shadow-sm mb-4 border-start border-dark border-5">
                <h2 class="fw-bold text-dark m-0">Manage Job Vacancies 💼</h2>
                <p class="text-muted m-0 mt-1">Post new jobs and manage existing job vacancies here.</p>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-4">
                <a href="add_job.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Post New Job
                </a>
                <a href="view_jobs.php" class="btn btn-outline-secondary">
                    <i class="bi bi-eye me-1"></i> View All Vacancies
                </a>
            </div>

            <?php if (!empty($message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card manage-card p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>#ID</th>
                                <th>Job Title</th>
                                <th>Company</th>
                                <th>Category</th>
                                <th>NVQ Level</th>
                                <th>Closing Date</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($jobs)): ?>
                                <?php foreach ($jobs as $job): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($job['job_id']); ?></td>
                                        <td><strong><?php echo htmlspecialchars($job['job_title']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($job['company_name']); ?></td>
                                        <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($job['category']); ?></span></td>
                                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($job['nvq_level']); ?></span></td>
                                        <td><?php echo htmlspecialchars($job['closing_date'] ?? 'N/A'); ?></td>
                                        <td class="text-center">
                                            <a href="edit_job.php?id=<?php echo $job['job_id']; ?>" class="btn btn-sm btn-warning me-1">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <a href="manage_jobs.php?action=delete&id=<?php echo $job['job_id']; ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Are you sure you want to delete this job vacancy?');">
                                                <i class="bi bi-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No job vacancies found.</td>
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