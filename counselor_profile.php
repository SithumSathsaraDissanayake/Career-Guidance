<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'counselor' && $_SESSION['user_role'] !== 'cgo')) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'Counselor';
$msg = "";

// Save profile
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_profile'])) {
    $specialization = trim($_POST['specialization']);
    $bio = trim($_POST['bio']);
    $phone = trim($_POST['phone']);
    $experience = intval($_POST['experience_years']);
    
    try {
        // Check if profile exists
        $checkStmt = $conn->prepare("SELECT counselor_id FROM counselors WHERE user_id = :uid");
        $checkStmt->execute(['uid' => $user_id]);
        
        if ($checkStmt->fetch()) {
            // Update
            $stmt = $conn->prepare("
                UPDATE counselors 
                SET specialization = :spec, bio = :bio, phone = :phone, experience_years = :exp 
                WHERE user_id = :uid
            ");
        } else {
            // Insert
            $stmt = $conn->prepare("
                INSERT INTO counselors (user_id, specialization, bio, phone, experience_years) 
                VALUES (:uid, :spec, :bio, :phone, :exp)
            ");
        }
        
        $stmt->execute([
            'uid' => $user_id,
            'spec' => $specialization,
            'bio' => $bio,
            'phone' => $phone,
            'exp' => $experience
        ]);
        
        $msg = "<div class='alert alert-success alert-dismissible fade show'>
                    <i class='bi bi-check-circle-fill me-2'></i> Profile saved successfully!
                    <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                </div>";
    } catch (PDOException $e) {
        $msg = "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// Get current profile
$profile = null;
try {
    $stmt = $conn->prepare("SELECT * FROM counselors WHERE user_id = :uid");
    $stmt->execute(['uid' => $user_id]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - FuturePath</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background: #0f172a; color: #fff; min-height: 100vh; font-family: 'Segoe UI', sans-serif; }
        .sidebar { background-color: #1e293b; min-height: 100vh; border-right: 1px solid #334155; }
        .card-custom { background-color: #1e293b; border: 1px solid #334155; border-radius: 12px; color: #fff; }
        .nav-link { color: #94a3b8; }
        .nav-link:hover, .nav-link.active { color: #38bdf8; background-color: #0f172a; border-radius: 8px; }
        .form-control, .form-select { background-color: #0f172a !important; border: 1px solid #334155 !important; color: #fff !important; padding: 10px 15px; }
        .form-control:focus { border-color: #38bdf8 !important; box-shadow: 0 0 8px rgba(56,189,248,0.2) !important; }
        .form-control::placeholder { color: #6b7280 !important; }
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
                <div class="fw-bold text-light"><?php echo htmlspecialchars($user_name); ?></div>
            </div>
            <ul class="nav flex-column gap-2">
                <li class="nav-item"><a class="nav-link" href="counselor_dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="counseling.php"><i class="bi bi-calendar-check me-2"></i> Counseling Sessions</a></li>
                <li class="nav-item"><a class="nav-link" href="my_students.php"><i class="bi bi-people-fill me-2"></i> My Students</a></li>
                <li class="nav-item"><a class="nav-link active" href="counselor_profile.php"><i class="bi bi-person-circle me-2"></i> My Profile</a></li>
                <li class="nav-item mt-4"><a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
            </ul>
        </div>

        <div class="col-md-9 col-lg-10 p-4">
            <?php echo $msg; ?>
            
            <div class="card card-custom p-4" style="max-width: 800px;">
                <h4 class="fw-bold mb-3 text-warning"><i class="bi bi-person-circle me-2"></i> My Profile</h4>
                <p class="text-muted small mb-4">This information will be visible to students when they book appointments.</p>
                <hr>
                
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small text-light">Full Name (Read-only)</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user_name); ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-light">Phone Number</label>
                            <input type="text" name="phone" class="form-control" 
                                   value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>"
                                   placeholder="e.g. 0771234567">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small text-light">Specialization</label>
                            <input type="text" name="specialization" class="form-control" 
                                   value="<?php echo htmlspecialchars($profile['specialization'] ?? ''); ?>"
                                   placeholder="e.g. IT Sector / Management / Engineering">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-light">Years of Experience</label>
                            <input type="number" name="experience_years" class="form-control"
                                   placeholder="Years of Experience" 
                                   value="<?php echo htmlspecialchars($profile['experience_years'] ?? 0); ?>"
                                   min="0" max="50">
                        </div>
                        <div class="col-12">
                            <label class="form-label small text-light">Bio / About Me</label>
                            <textarea name="bio" class="form-control" rows="5" 
                                      placeholder="Tell students about your experience and how you can help them..."><?php echo htmlspecialchars($profile['bio'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    
                    <button type="submit" name="save_profile" class="btn btn-info text-white fw-bold mt-4 px-4">
                        <i class="bi bi-save me-1"></i> Save Profile
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>