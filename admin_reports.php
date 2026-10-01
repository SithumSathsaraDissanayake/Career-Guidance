<?php
session_start();
include 'config/db.php';

// Admin Authentication Check
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// 📈 Summary Analytics Data ලබාගැනීම
$total_students = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn();
$total_appointments = $conn->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
$approved_payments = $conn->query("SELECT SUM(amount) FROM payments WHERE status = 'Approved'")->fetchColumn() ?? 0;
$pending_payments = $conn->query("SELECT COUNT(*) FROM payments WHERE status = 'Pending'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FuturePath - Reports & Analytics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark">System Analytics & Reports</h3>
            <p class="text-muted mb-0">Check the overall progress and reports of the system here.</p>
        </div>
        <a href="export_pdf.php" class="btn btn-danger fw-bold rounded-2">
            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export PDF Report
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-5">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white rounded-4">
                <span class="text-muted small fw-semibold">Total Students</span>
                <h2 class="fw-bold text-primary mt-2 mb-0"><?php echo $total_students; ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white rounded-4">
                <span class="text-muted small fw-semibold">Total Appointments</span>
                <h2 class="fw-bold text-success mt-2 mb-0"><?php echo $total_appointments; ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white rounded-4">
                <span class="text-muted small fw-semibold">Total Revenue (LKR)</span>
                <h2 class="fw-bold text-dark mt-2 mb-0"><?php echo number_format($approved_payments, 2); ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white rounded-4">
                <span class="text-muted small fw-semibold">Pending Payments</span>
                <h2 class="fw-bold text-warning mt-2 mb-0"><?php echo $pending_payments; ?></h2>
            </div>
        </div>
    </div>
</div>

</body>
</html>