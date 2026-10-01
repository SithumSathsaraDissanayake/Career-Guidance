<?php
session_start();
include 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if Job ID is provided
if (!isset($_GET['job_id']) || empty($_GET['job_id'])) {
    header("Location: view_jobs.php");
    exit();
}

$job_id = intval($_GET['job_id']);
$message = '';
$error = '';

// Fetch Job Details
try {
    $stmt = $conn->prepare("SELECT * FROM jobs WHERE job_id = :job_id");
    $stmt->execute([':job_id' => $job_id]);
    $job = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$job) {
        header("Location: view_jobs.php");
        exit();
    }
} catch (PDOException $e) {
    $error = "Error fetching job details: " . $e->getMessage();
}

// Handle Application Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $applicant_name = trim($_POST['applicant_name']);
    $applicant_email = trim($_POST['applicant_email']);
    $user_id = $_SESSION['user_id'];

    // File Upload Handling
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
        $allowed_types = ['pdf', 'doc', 'docx'];
        $file_name = $_FILES['resume']['name'];
        $file_tmp = $_FILES['resume']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_types)) {
            $new_file_name = "CV_" . $user_id . "_" . time() . "." . $file_ext;
            $upload_dir = "uploads/resumes/";

            // Create directory if not exists
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $destination = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_tmp, $destination)) {
                try {
                    $sql = "INSERT INTO applications (job_id, user_id, applicant_name, applicant_email, resume_file) 
                            VALUES (:job_id, :user_id, :applicant_name, :applicant_email, :resume_file)";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([
                        ':job_id' => $job_id,
                        ':user_id' => $user_id,
                        ':applicant_name' => $applicant_name,
                        ':applicant_email' => $applicant_email,
                        ':resume_file' => $new_file_name
                    ]);

                    $message = "Your application has been submitted successfully!";
                } catch (PDOException $e) {
                    $error = "Database Error: " . $e->getMessage();
                }
            } else {
                $error = "Failed to upload CV file. Please try again.";
            }
        } else {
            $error = "Invalid file format. Only PDF, DOC, and DOCX files are allowed.";
        }
    } else {
        $error = "Please upload your CV/Resume.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Job - FuturePath</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Job Application Form</h4>
                </div>
                <div class="card-body p-4">

                    <div class="bg-light p-3 rounded mb-4 border">
                        <h5 class="text-primary mb-1"><?php echo htmlspecialchars($job['job_title']); ?></h5>
                        <p class="text-muted mb-0"><strong>Company:</strong> <?php echo htmlspecialchars($job['company_name']); ?> | <strong>NVQ Level:</strong> <?php echo htmlspecialchars($job['nvq_level']); ?></p>
                    </div>

                    <?php if (!empty($message)): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                        <a href="view_jobs.php" class="btn btn-outline-primary">Back to Vacancies</a>
                    <?php else: ?>

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>

                        <form action="apply_job.php?job_id=<?php echo $job_id; ?>" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="applicant_name" class="form-control" required placeholder="Enter your full name">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="applicant_email" class="form-control" required placeholder="Enter your email address">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Upload Resume / CV (PDF or DOC) <span class="text-danger">*</span></label>
                                <input type="file" name="resume" class="form-control" accept=".pdf,.doc,.docx" required>
                                <small class="text-muted">Max file size allowed. Formats: PDF, DOC, DOCX</small>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <a href="view_jobs.php" class="btn btn-outline-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary px-4">Submit Application</button>
                            </div>
                        </form>

                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>