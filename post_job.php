<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'company' && $_SESSION['user_role'] !== 'admin')) {
    header("Location: login.php");
    exit();
}

$company_id = $_SESSION['user_id'];
$success_msg = "";
$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['post_job'])) {
    $job_title = $_POST['job_title'];
    $job_category = $_POST['job_category'];
    $nvq_level = $_POST['nvq_level']; // අලුතින් එකතු කල කොටස
    $description = $_POST['description'];
    $qualifications = $_POST['qualifications'];
    $closing_date = $_POST['closing_date'];

    try {
        $query = "INSERT INTO jobs (company_id, job_title, job_category, nvq_level, description, qualifications, closing_date) 
                  VALUES (:company_id, :job_title, :job_category, :nvq_level, :description, :qualifications, :closing_date)";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':company_id', $company_id);
        $stmt->bindParam(':job_title', $job_title);
        $stmt->bindParam(':job_category', $job_category);
        $stmt->bindParam(':nvq_level', $nvq_level);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':qualifications', $qualifications);
        $stmt->bindParam(':closing_date', $closing_date);

        if ($stmt->execute()) {
            $success_msg = "රැකියා පුරප්පාඩුව NVQ මට්ටම සමඟ සාර්ථකව පද්ධතියට ඇතුළත් කරන ලදී!";
        } else {
            $error_msg = "යම් දෝෂයක් සිදු විය. කරුණාකර නැවත උත්සාහ කරන්න.";
        }
    } catch (PDOException $e) {
        $error_msg = "Database Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FuturePath - Post a Job Vacancy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        html, body { height: 100%; margin: 0; background-color: #f3f4f6; font-family: 'Segoe UI', system-ui, sans-serif; }
        .custom-navbar { background-color: #0f172a !important; padding: 15px 30px; }
        .custom-navbar .navbar-brand { font-weight: 800; font-size: 1.5rem; color: #38bdf8 !important; }
        .custom-navbar .nav-link { color: #9ca3af !important; font-weight: 500; }
        .custom-navbar .nav-link:hover { color: #ffffff !important; }
        .main-container { max-width: 700px; margin: 40px auto; padding: 0 20px; }
        .form-card { background: white; border-radius: 16px; padding: 35px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); border: 1px solid #e5e7eb; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg custom-navbar">
        <div class="container-fluid" style="max-width: 1200px; margin: 0 auto;">
            <a class="navbar-brand" href="#"><i class="bi bi-building-fill text-info me-2"></i>FuturePath Corporate</a>
            <div class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav gap-2">
                    <li class="nav-item"><a class="nav-link active" href="post_job.php">Post a Job</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php"><span class="btn btn-sm btn-outline-danger px-3 rounded-pill">Logout</span></a></li>
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

        <div class="form-card">
            <h3 class="fw-bold text-dark mb-2"><i class="bi bi-file-earmark-plus-fill text-primary me-2"></i>Post a New Vacancy</h3>
            <p class="text-muted small mb-4">ඔබේ ආයතනයේ පුරප්පාඩු NVQ මට්ටම් සමඟ ශිෂ්‍ය ප්‍රජාව වෙත ඉදිරිපත් කරන්න.</p>
            <hr class="mb-4">
            
            <form action="post_job.php" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-semibold text-secondary">Job Title / Position</label>
                    <input type="text" class="form-control py-2 rounded-2" name="job_title" placeholder="e.g., Associate Software Engineer" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold text-secondary">Job Category</label>
                    <select class="form-select py-2 rounded-2" name="job_category" required>
                        <option value="" selected disabled>ක්ෂේත්‍රය තෝරන්න</option>
                        <option value="it">Information Technology (IT)</option>
                        <option value="engineering">Engineering & Technical</option>
                        <option value="management">Business & Management</option>
                        <option value="design">Creative Arts & Design</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold text-secondary">Required NVQ Level</label>
                    <select class="form-select py-2 rounded-2" name="nvq_level" required>
                        <option value="Any" selected>Any NVQ Level (විශේෂ මට්ටමක් අවශ්‍ය නොවේ)</option>
                        <option value="NVQ Level 3">NVQ Level 3 (Certificate)</option>
                        <option value="NVQ Level 4">NVQ Level 4 (Certificate)</option>
                        <option value="NVQ Level 5">NVQ Level 5 (Diploma / HNDIT)</option>
                        <option value="NVQ Level 6">NVQ Level 6 (Higher Diploma / Degree)</option>
                        <option value="NVQ Level 7">NVQ Level 7 (Postgraduate)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold text-secondary">Job Description</label>
                    <textarea class="form-control rounded-2" name="description" rows="4" placeholder="රැකියාවේ විස්තරය..." required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold text-secondary">Required Qualifications & Skills</label>
                    <textarea class="form-control rounded-2" name="qualifications" rows="3" placeholder="සුදුසුකම්..." required></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary">Closing Date</label>
                    <input type="date" class="form-control py-2 rounded-2" name="closing_date" min="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <button type="submit" name="post_job" class="btn btn-primary w-100 fw-bold py-2.5 rounded-2 fs-5">Publish Vacancy <i class="bi bi-send-fill ms-1"></i></button>
            </form>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>