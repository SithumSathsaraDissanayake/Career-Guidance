<?php
session_start();
include 'config/db.php';

// Counselor check
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'counselor' && $_SESSION['user_role'] !== 'cgo')) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$cgo_name = $_SESSION['user_name'] ?? 'Counselor';

// Get counselor_id
$counselor_id = $user_id;
try {
    $cidStmt = $conn->prepare("SELECT counselor_id FROM counselors WHERE user_id = :uid LIMIT 1");
    $cidStmt->execute(['uid' => $user_id]);
    $cidResult = $cidStmt->fetch(PDO::FETCH_ASSOC);
    if ($cidResult) {
        $counselor_id = $cidResult['counselor_id'];
    }
} catch (PDOException $e) {}

// Get students who have appointments with this counselor
$students = [];
try {
  $stmt = $conn->prepare("
    SELECT 
        u.user_id, 
        u.name, 
        u.email,
        COUNT(DISTINCT a.id) as appointment_count,
        MAX(a.appointment_date) as last_appointment,
        COUNT(DISTINCT CASE WHEN a.status = 'approved' OR a.status = 'confirmed' THEN a.id END) as approved_count
    FROM users u
    INNER JOIN appointments a ON a.user_id = u.user_id
    WHERE a.counselor_id = :cid
       OR a.counselor_name = :cname
       OR a.counselor_name LIKE :cname_like
    GROUP BY u.user_id, u.name, u.email
    ORDER BY last_appointment DESC
");
$stmt->execute([
    ':cid' => $counselor_id ?? 0,
    ':cname' => $cgo_name,
    ':cname_like' => $cgo_name . '%'
]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $students = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Students - FuturePath</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background: #0f172a; color: #fff; font-family: 'Segoe UI', sans-serif; min-height: 100vh; }
        .sidebar { background-color: #1e293b; min-height: 100vh; border-right: 1px solid #334155; }
        .card-custom { background-color: #1e293b; border: 1px solid #334155; border-radius: 12px; color: #fff; }
        .nav-link { color: #94a3b8; }
        .nav-link:hover, .nav-link.active { color: #38bdf8; background-color: #0f172a; border-radius: 8px; }
        .table-custom th { background-color: #0f172a; color: #38bdf8; border-bottom: 2px solid #334155; }
        .table-custom td { border-color: #334155; color: #f8fafc; }
        .table-custom td.email-cell { color: #94a3b8 !important; }  /* Light gray for emails */
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 sidebar p-3">
            <h4 class="text-info fw-bold mb-4 d-flex align-items-center gap-2">
                <i class="bi bi-person-badge-fill text-warning"></i> FuturePath
            </h4>
            <div class="mb-4">
                <small class="text-muted">CGO PORTAL</small>
                <div class="fw-bold text-light"><?php echo htmlspecialchars($cgo_name); ?></div>
            </div>
            <ul class="nav flex-column gap-2">
                <li class="nav-item"><a class="nav-link" href="counselor_dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="counseling.php"><i class="bi bi-calendar-check me-2"></i> Counseling Sessions</a></li>
                <li class="nav-item"><a class="nav-link active" href="my_students.php"><i class="bi bi-people-fill me-2"></i> My Students</a></li>
                <li class="nav-item"><a class="nav-link" href="counselor_profile.php"><i class="bi bi-person-circle me-2"></i> My Profile</a></li>
                <li class="nav-item mt-4"><a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 p-4">
            <div class="card card-custom p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h4 class="fw-bold text-warning m-0"><i class="bi bi-people-fill me-2"></i> My Students</h4>
                        <p class="text-muted small m-0 mt-1">Students who have booked appointments with you.</p>
                    </div>
                    <span class="badge bg-info text-dark fs-6"><?php echo count($students); ?> Students</span>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-custom align-middle">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Email</th>
                                <th>Total Appointments</th>
                                <th>Approved</th>
                                <th>Last Appointment</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($students)): ?>
                                <?php foreach ($students as $s): ?>
                                    <tr>
                                        <td class="fw-bold text-info">
                                            <i class="bi bi-person-circle me-1"></i>
                                            <?php echo htmlspecialchars($s['name']); ?>
                                        </td>
                                        <td class="email-cell"><?php echo htmlspecialchars($s['email']); ?></td>
                                        <td><span class="badge bg-primary"><?php echo $s['appointment_count']; ?></span></td>
                                        <td><span class="badge bg-success"><?php echo $s['approved_count']; ?></span></td>
                                        <td>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                <?php echo $s['last_appointment'] ? date('M d, Y', strtotime($s['last_appointment'])) : 'N/A'; ?>
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <a href="chat.php?receiver_id=<?php echo $s['user_id']; ?>" class="btn btn-sm btn-info text-white">
                                                <i class="bi bi-chat-dots-fill me-1"></i> Chat
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="bi bi-people fs-1 d-block mb-2"></i>
                                        No students yet. Students will appear here after they book appointments with you.
                                    </td>
                                </tr>
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