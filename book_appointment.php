<?php
session_start();
include 'config/db.php'; // ඩේටාබේස් එක ලින්ක් කළා

// 🔐 පරිශීලකයා ශිෂ්‍යයෙක්ද කියා බැලීම
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_msg = "";
$error_msg = "";

// 📅 Appointment Form එක Submit කළ පසු
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_now'])) {
    $counselor_name = $_POST['counselor'];
    $appointment_date = $_POST['date'];
    $appointment_time = $_POST['time'];
    $reason = $_POST['reason'];
    $status = 'pending'; // 🛠️ ඩේටාබේස් එකේ පවතින විදිහට 'pending' (small letters) ලෙස වෙනස් කළා

    try {
        // appointments ටේබල් එකට දත්ත ඇතුළත් කිරීමේ Query එක
        $query = "INSERT INTO appointments (user_id, counselor_name, appointment_date, appointment_time, reason, status) 
                  VALUES (:user_id, :counselor, :date, :time, :reason, :status)";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':counselor', $counselor_name);
        $stmt->bindParam(':date', $appointment_date);
        $stmt->bindParam(':time', $appointment_time);
        $stmt->bindParam(':reason', $reason);
        $stmt->bindParam(':status', $status);

        if ($stmt->execute()) {
            $success_msg = "Your booking is successful! Wait for the consultant to confirm it.";
        } else {
            $error_msg = "An error occurred. Please try again.";
        }
    } catch (PDOException $e) {
        $error_msg = "Database Error: " . $e->getMessage();
    }
}

// 🗂️ මෙම ශිෂ්‍යයා දැනට දමා ඇති පැරණි Appointment විස්තර ලබාගැනීම
$my_appointments = [];
try {
    $query = "SELECT * FROM appointments WHERE user_id = :user_id ORDER BY appointment_date DESC";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $my_appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Error handling
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FuturePath - Book an Appointment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        html, body { height: 100%; margin: 0; background-color: #f3f4f6; font-family: 'Segoe UI', system-ui, sans-serif; }
        .custom-navbar { background-color: #0f172a !important; padding: 15px 30px; }
        .custom-navbar .navbar-brand { font-weight: 800; font-size: 1.5rem; color: #38bdf8 !important; }
        .custom-navbar .nav-link { color: #9ca3af !important; font-weight: 500; }
        .custom-navbar .nav-link:hover { color: #ffffff !important; }
        
        .main-container { max-width: 1100px; margin: 40px auto; padding: 0 20px; }
        .form-card { background: white; border-radius: 16px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); border: 1px solid #e5e7eb; }
        .history-card { background: white; border-radius: 16px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); border: 1px solid #e5e7eb; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg custom-navbar">
        <div class="container-fluid" style="max-width: 1200px; margin: 0 auto;">
            <a class="navbar-brand" href="student_dashboard.php"><i class="bi bi-compass-fill me-2"></i>FuturePath</a>
            <div class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav gap-2">
                    <li class="nav-item"><a class="nav-link" href="student_dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="aptitude_test.php">Aptitude Test</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="main-container">
        
        <?php if (!empty($success_msg)): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?php echo $success_msg; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $error_msg; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            
            <div class="col-md-5">
                <div class="form-card">
                    <h4 class="fw-bold text-dark mb-3"><i class="bi bi-calendar-plus text-primary me-2"></i>Book Appointment</h4>
                    <p class="text-muted small">Find a counselor and schedule a time that suits you.</p>
                    <hr>
                    
                    <form action="book_appointment.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">Select Counselor</label>
                            <select class="form-select py-2 rounded-2" name="counselor" required>
                                <option value="" selected disabled>Choose a mentor</option>
                                <option value="Mr. Sunil Perera (IT Sector)">Mr. Sunil Perera (IT Sector)</option>
                                <option value="Mrs. K. Silva (Management Sector)">Mrs. K. Silva (Management Sector)</option>
                                <option value="Dr. Rohan Dasanayaka (Engineering)">Dr. Rohan Dasanayaka (Engineering)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">Preferred Date</label>
                            <input type="date" class="form-select py-2 rounded-2" name="date" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">Time Slot</label>
                            <select class="form-select py-2 rounded-2" name="time" required>
                                <option value="" selected disabled>Choose a time</option>
                                <option value="09:00 AM - 10:00 AM">09:00 AM - 10:00 AM</option>
                                <option value="10:30 AM - 11:30 AM">10:30 AM - 11:30 AM</option>
                                <option value="02:00 PM - 03:00 PM">02:00 PM - 03:00 PM</option>
                                <option value="03:30 PM - 04:30 PM">03:30 PM - 04:30 PM</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-secondary">Briefly explain your career issue</label>
                            <textarea class="form-control rounded-2" name="reason" rows="3" placeholder="Briefly describe the issue you want to discuss..." required></textarea>
                        </div>

                        <button type="submit" name="book_now" class="btn btn-primary w-100 fw-bold py-2 rounded-2">Confirm Booking <i class="bi bi-chevron-right ms-1"></i></button>
                    </form>
                </div>
            </div>

            <div class="col-md-7">
                <div class="history-card">
                    <h4 class="fw-bold text-dark mb-3"><i class="bi bi-clock-history text-success me-2"></i>My Appointment Status</h4>
                    <p class="text-muted small">You can check the current status of your booked appointments here.</p>
                    <hr>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Counselor</th>
                                    <th>Date/Time</th>
                                    <th>Status</th>
                                    <th>Action</th> <!-- 👈 අලුතින් Action Column එකක් එකතු කළා -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($my_appointments) > 0): ?>
                                    <?php foreach ($my_appointments as $app): ?>
                                        <?php 
                                        // 🛠️ ඩේටාබේස් එකේ ඇති අගය කුඩා අකුරු (Lowercase) කර සංසන්දනය කිරීම
                                        $current_status = strtolower($app['status']); 
                                        
                                        // Counselor ගේ ID එක (ඔයාගේ Table එකේ `counselor_id` එකක් නැත්නම් default එකක් ගන්නවා)
                                        $counselor_id = isset($app['counselor_id']) ? $app['counselor_id'] : 1; 
                                        $appointment_id = isset($app['appointment_id']) ? $app['appointment_id'] : $app['id'];
                                        ?>
                                        <tr>
                                            <td><span class="fw-bold text-dark d-block"><?php echo htmlspecialchars($app['counselor_name']); ?></span></td>
                                            <td>
                                                <small class="text-secondary d-block"><i class="bi bi-calendar3 me-1"></i><?php echo htmlspecialchars($app['appointment_date']); ?></small>
                                                <small class="text-muted"><i class="bi bi-clock me-1"></i><?php echo htmlspecialchars($app['appointment_time']); ?></small>
                                            </td>
                                            <td>
                                                <?php if ($current_status == 'pending'): ?>
                                                    <span class="badge bg-warning text-dark px-2.5 py-1.5 rounded-pill fw-bold">Pending</span>
                                                <?php elseif ($current_status == 'approved' || $current_status == 'confirmed'): ?>
                                                    <span class="badge bg-success px-2.5 py-1.5 rounded-pill fw-bold">Approved</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger px-2.5 py-1.5 rounded-pill fw-bold">Rejected</span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- 🎯 Video Call සහ Chat බටන් එකතු කළ කොටස -->
                                            <td>
                                                <?php if ($current_status == 'approved' || $current_status == 'confirmed'): ?>
                                                    <div class="d-flex flex-column gap-1">
                                                        <a href="video_call.php?appointment_id=<?php echo $appointment_id; ?>" class="btn btn-sm btn-primary py-1 px-2 fw-semibold" style="font-size: 12px;">
                                                            <i class="bi bi-camera-video-fill me-1"></i> Join Call
                                                        </a>
                                                        <a href="chat.php?receiver_id=<?php echo $counselor_id; ?>" class="btn btn-sm btn-success py-1 px-2 fw-semibold" style="font-size: 12px;">
                                                            <i class="bi bi-chat-dots-fill me-1"></i> Chat
                                                        </a>
                                                    </div>
                                                <?php else: ?>
                                                    <small class="text-muted fs-7">N/A</small>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <i class="bi bi-calendar-x fs-2 d-block mb-2 text-light"></i>
                                            You have not booked any consultations yet.
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