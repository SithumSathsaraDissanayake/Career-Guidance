<?php
session_start();
include 'config/db.php'; // ඩේටාබේස් එක ලින්ක් කළා

// පරිශීලකයා ඇඩ්මින් කෙනෙක්ද කියා බැලීම
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$msg = "";

// 🔄 ඇපොයින්ට්මන්ට් එකක තත්ත්වය (Status) වෙනස් කරන කොටස (Approve / Complete)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $appointment_id = intval($_GET['id']);
    $action = $_GET['action'];
    $new_status = "";

    if ($action === 'approve') {
        $new_status = 'approved';
    } elseif ($action === 'complete') {
        $new_status = 'completed';
    }

    if (!empty($new_status)) {
        try {
            $update_query = "UPDATE appointments SET status = :status WHERE id = :id";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->execute(['status' => $new_status, 'id' => $appointment_id]);
            
            $msg = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                        <i class='bi bi-check-circle-fill me-2'></i> Appointment status updated to <strong>" . ucfirst($new_status) . "</strong>!
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                    </div>";
        } catch (PDOException $e) {
            $msg = "<div class='alert alert-danger'>Error updating status: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}

// 🗂️ පද්ධතියේ තියෙන සියලුම ඇපොයින්ට්මන්ට් සිසුන්ගේ නම් සමඟ ඩේටාබේස් එකෙන් ලබාගැනීම
$all_appointments = [];
try {
    // JOIN එක LEFT JOIN බවට වෙනස් කළා
    $query = "SELECT a.*, u.name AS student_name, u.email AS student_email FROM appointments a 
              LEFT JOIN users u ON a.user_id = u.user_id 
              ORDER BY a.appointment_date DESC";
    $stmt = $conn->query($query);
    $all_appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $msg = "<div class='alert alert-danger'>Database Error: " . htmlspecialchars($e->getMessage()) . "</div>";
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
            <a class="navbar-brand" href="#">FuturePath <span class="badge bg-danger fs-6 ms-2">Admin Panel</span></a>
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
                    <li class="nav-item"><a class="nav-link active" href="manage_appointments.php"><i class="bi bi-calendar-check-fill"></i> Appointments</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_courses.php"><i class="bi bi-book-half"></i> Manage Courses</a></li>
                </ul>
            </div>
            <div class="logout-section">
                <a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <div class="p-4 bg-white rounded-3 shadow-sm mb-4 border-start border-danger border-5">
                <h2 class="fw-bold text-dark m-0">Manage Counseling Appointments 📅</h2>
                <p class="text-muted m-0 mt-1">Manage all tutoring sessions booked by students here.</p>
            </div>

            <?php echo $msg; ?>

            <!-- 📋 Appointments Table -->
            <div class="card manage-card p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Student Details</th>
                                <th>Assigned Counselor</th>
                                <th>Schedule Date & Time</th>
                                <th>Current Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($all_appointments)): ?>
                                <?php foreach ($all_appointments as $app): ?>
                                    <?php 
                                        $app_id = $app['id'] ?? ($app['appointment_id'] ?? 0);
                                        $status = strtolower($app['status'] ?? 'pending');
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($app['student_name'] ?? 'N/A'); ?></div>
                                            <small class="text-muted"><?php echo htmlspecialchars($app['student_email'] ?? ''); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($app['counselor_name'] ?? 'N/A'); ?></td>
                                        <td>
                                            <div class="fw-bold text-secondary"><i class="bi bi-calendar3 me-1"></i> <?php echo htmlspecialchars($app['appointment_date'] ?? ''); ?></div>
                                            <!-- Time Display එක Direct කළා -->
                                            <small class="text-muted"><i class="bi bi-clock me-1"></i> <?php echo htmlspecialchars($app['appointment_time'] ?? ''); ?></small>
                                        </td>
                                        <td>
                                            <?php if ($status === 'pending'): ?>
                                                <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> Pending</span>
                                            <?php elseif ($status === 'approved' || $status === 'confirmed'): ?>
                                                <span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> Approved</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><i class="bi bi-calendar-check"></i> Completed</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($status === 'pending'): ?>
                                                <a href="manage_appointments.php?action=approve&id=<?php echo $app_id; ?>" class="btn btn-sm btn-success fw-bold px-3"><i class="bi bi-check2"></i> Approve</a>
                                            <?php elseif ($status === 'approved' || $status === 'confirmed'): ?>
                                                <a href="manage_appointments.php?action=complete&id=<?php echo $app_id; ?>" class="btn btn-sm btn-primary fw-bold px-3"><i class="bi bi-flag-fill"></i> Mark Complete</a>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-light disabled px-3">No Actions</button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No appointments can be found in the system.</td>
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