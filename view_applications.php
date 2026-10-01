<?php
session_start();
include 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$applications = [];
$error = '';

try {
    // Fetch applications with related job details
    $sql = "SELECT applications.*, jobs.job_title, jobs.company_name 
            FROM applications 
            JOIN jobs ON applications.job_id = jobs.job_id 
            ORDER BY applications.applied_at DESC";
    
    $stmt = $conn->query($sql);
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching applications: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submitted Applications - FuturePath</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <?php include 'includes/navbar.php'; ?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Received Job Applications 📄</h2>
        <a href="manage_jobs.php" class="btn btn-outline-secondary">Manage Jobs</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#ID</th>
                            <th>Applicant Name</th>
                            <th>Email</th>
                            <th>Applied Job</th>
                            <th>Company</th>
                            <th>Applied Date</th>
                            <th class="text-center">Action / Resume</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($applications)): ?>
                            <?php foreach ($applications as $app): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($app['application_id']); ?></td>
                                    <td><strong><?php echo htmlspecialchars($app['applicant_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($app['applicant_email']); ?></td>
                                    <td><span class="badge bg-primary"><?php echo htmlspecialchars($app['job_title']); ?></span></td>
                                    <td><?php echo htmlspecialchars($app['company_name']); ?></td>
                                    <td><?php echo date('Y-m-d H:i', strtotime($app['applied_at'])); ?></td>
                                    <td class="text-center">
                                        <a href="uploads/resumes/<?php echo htmlspecialchars($app['resume_file']); ?>" 
                                           target="_blank" 
                                           class="btn btn-sm btn-success">
                                            <i class="bi bi-download"></i> Download CV
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No applications received yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>