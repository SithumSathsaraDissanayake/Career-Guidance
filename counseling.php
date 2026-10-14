<?php
session_start();
include 'config/db.php';

// Checking if you are logged in as CGO / Counselor
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'counselor' && $_SESSION['user_role'] !== 'cgo')) {
    header("Location: login.php");
    exit();
}

$msg = "";
$user_id = $_SESSION['user_id'];
$cgo_name = $_SESSION['user_name'] ?? 'Counselor';

// ✅ Counselor_id = users.user_id in Appointments table
$counselor_id = $user_id;  // The user_id of the session is the same!
try {
    $cidStmt = $conn->prepare("SELECT counselor_id FROM counselors WHERE user_id = :uid LIMIT 1");
    $cidStmt->execute(['uid' => $user_id]);
    $cidResult = $cidStmt->fetch(PDO::FETCH_ASSOC);
    if ($cidResult) {
        $counselor_id = $cidResult['counselor_id'];
    }
} catch (PDOException $e) {
    // Silently fail
}

// The part where the CGO approves or rejects a student's appointment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $appointment_id = intval($_POST['appointment_id']);
    $status = $_POST['status'];
    
    // ✅ Validate status
    $allowed_status = ['approved', 'rejected', 'pending', 'completed'];
    if (!in_array($status, $allowed_status)) {
        $status = 'pending';
    }

    try {
        // ✅ Check if appointment belongs to this counselor
        $checkStmt = $conn->prepare("
            SELECT id FROM appointments 
            WHERE id = :aid 
            AND (
                counselor_id = :cid 
                OR counselor_name = :cname 
                OR counselor_name LIKE :cname_like
            )
        ");

        $checkStmt->execute([
            ':aid' => $appointment_id,
            ':cid' => $counselor_id,
            ':cname' => $cgo_name,
            ':cname_like' => $cgo_name . '%'
        ]);
        
        if ($checkStmt->fetch()) {
            // ✅ Update your appointment.
            $update_query = "UPDATE appointments SET status = :status WHERE id = :appointment_id";
            $stmt = $conn->prepare($update_query);
            $stmt->execute([
                'status' => $status,
                'appointment_id' => $appointment_id
            ]);

            $msg = "<div class='alert alert-success alert-dismissible fade show mb-4' role='alert'>
                        <i class='bi bi-check-circle-fill me-2'></i> Appointment status updated to " . htmlspecialchars($status) . "!
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                    </div>";
        } else {
            // ❌ Not their appointment
            $msg = "<div class='alert alert-danger mb-4'>
                        <i class='bi bi-exclamation-triangle-fill me-2'></i> 
                        You can only manage your own appointments!
                    </div>";
        }
    } catch (PDOException $e) {
        $msg = "<div class='alert alert-danger mb-4'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// ✅ Retrieving only relevant appointments from the database
$appointments = [];
try {
    $list_query = "SELECT a.*, u.name as student_name, u.email as student_email 
              FROM appointments a 
              LEFT JOIN users u ON a.user_id = u.user_id 
              WHERE a.counselor_id = :cid
                 OR a.counselor_name = :cname
                 OR a.counselor_name LIKE :cname_like
              ORDER BY 
                CASE 
                    WHEN a.status = 'pending' THEN 1
                    WHEN a.status = 'approved' THEN 2
                    ELSE 3
                END,
                a.appointment_date DESC";

$list_stmt = $conn->prepare($list_query);
$list_stmt->execute([
    'cid' => $counselor_id ?? 0,
    'cname' => $cgo_name,
    'cname_like' => $cgo_name . '%'
]);

    $appointments = $list_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $msg = "<div class='alert alert-danger mb-4'>Database Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CGO Counseling Dashboard - FuturePath</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body {
            background: #0f172a;
            color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        .sidebar {
            background-color: #1e293b;
            min-height: 100vh;
            border-right: 1px solid #334155;
        }
        .card-custom {
            background-color: #1e293b;
            border: 1px solid #334155;
            border-radius: 12px;
            color: #ffffff;
        }
        .table-custom { color: #f8fafc; }
        .table-custom th { background-color: #0f172a; color: #38bdf8; border-bottom: 2px solid #334155; }
        .table-custom td { border-color: #334155; background-color: transparent; color: #f8fafc; }
        .nav-link { color: #94a3b8; }
        .nav-link:hover, .nav-link.active { color: #38bdf8; background-color: #0f172a; border-radius: 8px; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-md-3 col-lg-2 sidebar p-3">
            <h4 class="text-info fw-bold mb-4 d-flex align-items-center gap-2">
                <i class="bi bi-person-badge-fill text-warning"></i> FuturePath
            </h4>
            <div class="mb-4">
                <small class="text-muted">CGO PORTAL</small>
                <div class="fw-bold text-light"><?php echo htmlspecialchars($cgo_name); ?></div>
            </div>
            <ul class="nav flex-column gap-2">
                <li class="nav-item">
                    <a class="nav-link" href="counselor_dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="counseling.php"><i class="bi bi-calendar-check me-2"></i> Counseling Sessions</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="my_students.php"><i class="bi bi-people-fill me-2"></i> My Students</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="counselor_profile.php"><i class="bi bi-person-circle me-2"></i> My Profile</a>
                </li>
                <li class="nav-item mt-4">
                    <a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
                </li>
            </ul>
        </div>

        <!-- Main Content Area -->
        <div class="col-md-9 col-lg-10 p-4">
            <?php echo $msg; ?>

            <div class="card card-custom p-4">
                <h4 class="fw-bold mb-4 text-warning d-flex align-items-center gap-2">
                    <i class="bi bi-calendar-check-fill"></i> Requested Student Appointments
                </h4>

                <div class="table-responsive">
                    <table class="table table-custom align-middle">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Student Email</th>
                                <th>Date</th>
                                <th>Time Slot</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($appointments)): ?>
                                <?php foreach ($appointments as $app): ?>
                                    <?php 
                                        $status = strtolower($app['status'] ?? 'pending');
                                        $student_id = $app['user_id'] ?? 0;
                                        $appointment_id = $app['id'] ?? 0;
                                    ?>
                                    <tr>
                                        <td class="fw-bold text-info"><?php echo htmlspecialchars($app['student_name'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($app['student_email'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($app['appointment_date'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($app['appointment_time'] ?? ''); ?></td>
                                        <td><span class="text-light opacity-75" style="font-size: 0.9rem;"><?php echo htmlspecialchars($app['reason'] ?? 'N/A'); ?></span></td>
                                        <td>
                                            <?php 
                                            if ($status === 'approved' || $status === 'confirmed') {
                                                echo "<span class='badge bg-success'>Approved</span>";
                                            } elseif ($status === 'rejected') {
                                                echo "<span class='badge bg-danger'>Rejected</span>";
                                            } elseif ($status === 'completed') {
                                                echo "<span class='badge bg-info'>Completed</span>";
                                            } else {
                                                echo "<span class='badge bg-warning text-dark'>Pending</span>";
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($status === 'approved' || $status === 'confirmed'): ?>
                                                <div class="d-flex gap-1">
                                                    <a href="video_call.php?appointment_id=<?php echo $appointment_id; ?>" class="btn btn-sm btn-primary">
                                                        <i class="bi bi-camera-video-fill"></i> Call
                                                    </a>
                                                    <a href="chat.php?receiver_id=<?php echo $student_id; ?>" class="btn btn-sm btn-info text-white">
                                                        <i class="bi bi-chat-dots-fill"></i> Chat
                                                    </a>
                                                </div>
                                            <?php elseif ($status === 'pending'): ?>
                                                <form method="POST" action="counseling.php" class="d-flex gap-2">
                                                    <input type="hidden" name="appointment_id" value="<?php echo $appointment_id; ?>">
                                                    <input type="hidden" name="update_status" value="1">
                                                    <button type="submit" name="status" value="approved" class="btn btn-sm btn-success">
                                                        <i class="bi bi-check-lg"></i> Approve
                                                    </button>
                                                    <button type="submit" name="status" value="rejected" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-x-lg"></i> Reject
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-muted small">No actions</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-light py-4">
                                        <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>
                                        No appointment requests found.
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