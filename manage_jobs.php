<?php
session_start();
include 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Job Vacancies - FuturePath</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'includes/navbar.php'; ?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Job Vacancies</h2>
        <div>
            <a href="add_job.php" class="btn btn-primary">+ Post New Job</a>
            <a href="view_jobs.php" class="btn btn-outline-secondary">View All Vacancies</a>
        </div>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
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
                                        <a href="edit_job.php?id=<?php echo $job['job_id']; ?>" class="btn btn-sm btn-warning me-1">Edit</a>
                                        <a href="manage_jobs.php?action=delete&id=<?php echo $job['job_id']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete this job vacancy?');">Delete</a>
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
    </div>
</div>

<script href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>