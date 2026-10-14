<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$appointment_id = isset($_GET['appointment_id']) ? intval($_GET['appointment_id']) : 0;
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'] ?? 'student';
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User';

// ✅ Check appointment access
$has_access = false;
$appointment_info = null;

if ($appointment_id > 0) {
    try {
        if ($user_role === 'student') {
            // Student: Checking if their appointment is + approved.
            $checkStmt = $conn->prepare("
                SELECT id, user_id, counselor_name, appointment_date, appointment_time, status 
                FROM appointments 
                WHERE id = :aid 
                  AND user_id = :uid 
                  AND (status = 'approved' OR status = 'confirmed')
            ");
            $checkStmt->execute(['aid' => $appointment_id, 'uid' => $user_id]);
        } else {
            // Counselor/Admin: Also looking at the approved appointment.
            $checkStmt = $conn->prepare("
                SELECT id, user_id, counselor_name, appointment_date, appointment_time, status 
                FROM appointments 
                WHERE id = :aid 
                  AND (status = 'approved' OR status = 'confirmed')
            ");
            $checkStmt->execute(['aid' => $appointment_id]);
        }
        
        $appointment_info = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($appointment_info) {
            $has_access = true;
        }
    } catch (PDOException $e) {
        $has_access = false;
    }
}

// If no access, show alert and redirect
if (!$has_access) {
    echo "<script>
        alert('You do not have access to this video call. Please ensure the appointment is approved.');
        window.location.href = 'student_dashboard.php';
    </script>";
    exit();
}

// Unique Room Name
$room_name = "FuturePath_Counseling_Room_" . $appointment_id;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FuturePath - Live Video Counseling</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body, html { height: 100%; margin: 0; background-color: #0f172a; overflow: hidden; }
        #meet-container { width: 100%; height: calc(100vh - 60px); }
    </style>
</head>
<body>

    <!-- Header Bar -->
    <div class="d-flex justify-content-between align-items-center px-4 py-2 bg-dark text-white border-bottom border-secondary" style="height: 60px;">
        <h5 class="m-0 text-info fw-bold">
            <i class="bi bi-camera-video-fill me-2"></i>Counseling Session #<?php echo $appointment_id; ?>
            <small class="text-white-50 ms-2">
                <?php echo htmlspecialchars($appointment_info['counselor_name']); ?> 
                | <?php echo htmlspecialchars($appointment_info['appointment_date']); ?>
            </small>
        </h5>
        <a href="counseling.php" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to end this call?');">
            <i class="bi bi-telephone-x-fill me-1"></i> Exit Call
        </a>
    </div>

    <!-- Jitsi Video Call Container -->
    <div id="meet-container"></div>

    <!-- Jitsi External API -->
    <script src="https://meet.jit.si/external_api.js"></script>
    <script>
        const domain = 'meet.jit.si';
        const options = {
            roomName: '<?php echo $room_name; ?>',
            width: '100%',
            height: '100%',
            parentNode: document.querySelector('#meet-container'),
            userInfo: {
                displayName: '<?php echo htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8'); ?>'
            },
            configOverwrite: {
                startWithAudioMuted: false,
                startWithVideoMuted: false,
                prejoinPageEnabled: false
            }
        };
        const api = new JitsiMeetExternalAPI(domain, options);
    </script>
</body>
</html>