<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'counselor' && $_SESSION['user_role'] !== 'cgo')) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$cgo_name = $_SESSION['user_name'] ?? 'Counselor';

// Get counselor_id
// ✅ Appointments table එකේ counselor_id = users.user_id
$counselor_id = $user_id;  // The user_id of the session is the same!
try {
    $cidStmt = $conn->prepare("SELECT counselor_id FROM counselors WHERE user_id = :uid LIMIT 1");
    $cidStmt->execute(['uid' => $user_id]);
    $cidResult = $cidStmt->fetch(PDO::FETCH_ASSOC);
    if ($cidResult) {
        $counselor_id = $cidResult['counselor_id'];
    }
} catch (PDOException $e) {}

// Statistics
$total_appointments = 0;
$pending_appointments = 0;
$approved_appointments = 0;
$total_students = 0;

    try {
    // ✅ Total Appointments — LIKE add
    $stmt = $conn->prepare("
        SELECT COUNT(*) FROM appointments 
        WHERE counselor_id = :cid 
           OR counselor_name = :cname 
           OR counselor_name LIKE :cname_like
    ");
    $stmt->execute([
        ':cid' => $counselor_id ?? 0,
        ':cname' => $cgo_name,
        ':cname_like' => $cgo_name . '%'
    ]);
    $total_appointments = $stmt->fetchColumn() ?? 0;
    
    // ✅ Pending — LIKE add
    $stmt = $conn->prepare("
        SELECT COUNT(*) FROM appointments 
        WHERE (counselor_id = :cid 
               OR counselor_name = :cname 
               OR counselor_name LIKE :cname_like)
          AND status = 'pending'
    ");
    $stmt->execute([
        ':cid' => $counselor_id ?? 0,
        ':cname' => $cgo_name,
        ':cname_like' => $cgo_name . '%'
    ]);
    $pending_appointments = $stmt->fetchColumn() ?? 0;
    
    // ✅ Approved — LIKE add
    $stmt = $conn->prepare("
        SELECT COUNT(*) FROM appointments 
        WHERE (counselor_id = :cid 
               OR counselor_name = :cname 
               OR counselor_name LIKE :cname_like)
          AND (status = 'approved' OR status = 'confirmed')
    ");
    $stmt->execute([
        ':cid' => $counselor_id ?? 0,
        ':cname' => $cgo_name,
        ':cname_like' => $cgo_name . '%'
    ]);
    $approved_appointments = $stmt->fetchColumn() ?? 0;
    
    // ✅ My Students — LIKE add
    $stmt = $conn->prepare("
        SELECT COUNT(DISTINCT user_id) FROM appointments 
        WHERE counselor_id = :cid 
           OR counselor_name = :cname 
           OR counselor_name LIKE :cname_like
    ");
    $stmt->execute([
        ':cid' => $counselor_id ?? 0,
        ':cname' => $cgo_name,
        ':cname_like' => $cgo_name . '%'
    ]);
    $total_students = $stmt->fetchColumn() ?? 0;
} catch (PDOException $e) {}

// Recent appointments
$recent = [];
try {
    $stmt = $conn->prepare("
    SELECT a.*, u.name as student_name 
    FROM appointments a 
    LEFT JOIN users u ON a.user_id = u.user_id 
    WHERE a.counselor_id = :cid 
       OR a.counselor_name = :cname 
       OR a.counselor_name LIKE :cname_like
    ORDER BY a.created_at DESC 
    LIMIT 5
");
$stmt->execute([
    ':cid' => $counselor_id ?? 0,
    ':cname' => $cgo_name,
    ':cname_like' => $cgo_name . '%'
]);
    $recent = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Counselor Dashboard - FuturePath</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background: #0f172a; color: #fff; font-family: 'Segoe UI', sans-serif; min-height: 100vh; }
        .sidebar { background-color: #1e293b; min-height: 100vh; border-right: 1px solid #334155; }
        .card-custom { background-color: #1e293b; border: 1px solid #334155;border-radius: 12px; color: #fff; transition: transform 0.2s; }
        .card-custom:hover { transform: translateY(-3px); }
        .nav-link { color: #94a3b8; }
        .nav-link:hover, .nav-link.active { color: #38bdf8; background-color: #0f172a; border-radius: 8px; }
        
        /* ✅ FIXED Table Colors — Black text on light background */
.table-custom th { 
    background-color: #0f172a; 
    color: #38bdf8; 
    border-bottom: 2px solid #334155; 
}
.table-custom td { 
    border-color: #334155; 
    color: #000000; 
    background-color: #ffffff;
}
/* Remove hover effect — no color change */
.table-custom tr:hover td {
    background-color: #ffffff;
    color: #000000;
}
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 sidebar p-3">
            <h4 class="text-info fw-bold mb-4 d-flex align-items-center gap-2">
                <i class="bi bi-person-badge-fill text-warning"></i> FuturePath
            </h4>
            <div class="mb-4">
                <small class="text-muted">CGO PORTAL</small>
                <div class="fw-bold text-light"><?php echo htmlspecialchars($cgo_name); ?></div>
            </div>
            <ul class="nav flex-column gap-2">
                <li class="nav-item"><a class="nav-link active" href="counselor_dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="counseling.php"><i class="bi bi-calendar-check me-2"></i> Counseling Sessions</a></li>
                <li class="nav-item"><a class="nav-link" href="my_students.php"><i class="bi bi-people-fill me-2"></i> My Students</a></li>
                <li class="nav-item"><a class="nav-link" href="counselor_profile.php"><i class="bi bi-person-circle me-2"></i> My Profile</a></li>
                <li class="nav-item mt-4"><a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
            </ul>
        </div>

        <div class="col-md-9 col-lg-10 p-4">
            <div class="p-4 card-custom mb-4">
                <h3 class="fw-bold m-0 text-white">Welcome back, <?php echo htmlspecialchars($cgo_name); ?>! 👋</h3>
                <p class="text-muted m-0 mt-1">Here's an overview of your counseling activities.</p>
            </div>

            <!-- Statistics Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card card-custom p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted text-uppercase">Total Appointments</small>
                                <h2 class="fw-bold text-info mt-2 mb-0"><?php echo $total_appointments; ?></h2>
                            </div>
                            <i class="bi bi-calendar-check-fill fs-1 text-info opacity-25"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-custom p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted text-uppercase">Pending</small>
                                <h2 class="fw-bold text-warning mt-2 mb-0"><?php echo $pending_appointments; ?></h2>
                            </div>
                            <i class="bi bi-hourglass-split fs-1 text-warning opacity-25"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-custom p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted text-uppercase">Approved</small>
                                <h2 class="fw-bold text-success mt-2 mb-0"><?php echo $approved_appointments; ?></h2>
                            </div>
                            <i class="bi bi-check-circle-fill fs-1 text-success opacity-25"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-custom p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted text-uppercase">My Students</small>
                                <h2 class="fw-bold text-primary mt-2 mb-0"><?php echo $total_students; ?></h2>
                            </div>
                            <i class="bi bi-people-fill fs-1 text-primary opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Appointments -->
            <div class="card card-custom p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold m-0 text-warning"><i class="bi bi-clock-history me-2"></i> Recent Appointments</h5>
                    <a href="counseling.php" class="btn btn-sm btn-outline-info">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom align-middle">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent)): ?>
                                <?php foreach ($recent as $app): ?>
                                    <?php $status = strtolower($app['status'] ?? 'pending'); ?>
                                    <tr>
                                        <td class="fw-bold text-info">
                                            <i class="bi bi-person-circle me-1"></i>
                                            <?php echo htmlspecialchars($app['student_name'] ?? 'N/A'); ?>
                                        </td>
                                        <td>
                                            <i class="bi bi-calendar3 me-1 text-info"></i>
                                            <?php echo htmlspecialchars($app['appointment_date'] ?? ''); ?>
                                        </td>
                                        <td>
                                            <i class="bi bi-clock me-1 text-info"></i>
                                            <?php echo htmlspecialchars($app['appointment_time'] ?? ''); ?>
                                        </td>
                                        <td>
                                            <?php if ($status === 'approved' || $status === 'confirmed'): ?>
                                                <span class="badge bg-success">Approved</span>
                                            <?php elseif ($status === 'rejected'): ?>
                                                <span class="badge bg-danger">Rejected</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center text-muted py-4">No appointments yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>