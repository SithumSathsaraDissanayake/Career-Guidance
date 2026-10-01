<?php
session_start();
include 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = '';
$error = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $job_title = trim($_POST['job_title']);
    $company_name = trim($_POST['company_name']);
    $category = trim($_POST['category']);
    $nvq_level = trim($_POST['nvq_level']);
    $description = trim($_POST['description']);
    $qualifications = trim($_POST['qualifications']);
    $closing_date = trim($_POST['closing_date']);

    if (!empty($job_title) && !empty($company_name) && !empty($category) && !empty($nvq_level)) {
        try {
            $sql = "INSERT INTO jobs (job_title, company_name, category, nvq_level, description, qualifications, closing_date) 
                    VALUES (:job_title, :company_name, :category, :nvq_level, :description, :qualifications, :closing_date)";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':job_title' => $job_title,
                ':company_name' => $company_name,
                ':category' => $category,
                ':nvq_level' => $nvq_level,
                ':description' => $description,
                ':qualifications' => $qualifications,
                ':closing_date' => $closing_date
            ]);

            $message = "Job vacancy posted successfully!";
        } catch (PDOException $e) {
            $error = "Error posting job: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post New Job Vacancy - FuturePath</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Post New Job Vacancy</h4>
                </div>
                <div class="card-body p-4">

                    <?php if (!empty($message)): ?>
                        <div class="alert alert-success"><?php echo $message; ?></div>
                    <?php endif; ?>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form action="add_job.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Job Title <span class="text-danger">*</span></label>
                            <input type="text" name="job_title" class="form-control" placeholder="e.g. Software Engineer" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Company Name <span class="text-danger">*</span></label>
                            <input type="text" name="company_name" class="form-control" placeholder="e.g. ABC Tech Solutions" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-select" required>
                                    <option value="">Select Category</option>
                                    <option value="IT Sector">IT Sector</option>
                                    <option value="Management">Management</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">NVQ Level Match <span class="text-danger">*</span></label>
                                <select name="nvq_level" class="form-select" required>
                                    <option value="">Select NVQ Level</option>
                                    <option value="NVQ Level 4">NVQ Level 4</option>
                                    <option value="NVQ Level 5">NVQ Level 5 (HNDIT)</option>
                                    <option value="NVQ Level 6">NVQ Level 6</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Job Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Enter brief overview of the role..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Qualifications & Requirements</label>
                            <textarea name="qualifications" class="form-control" rows="3" placeholder="Enter required skills and qualifications..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Closing Date</label>
                            <input type="date" name="closing_date" class="form-control">
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="view_jobs.php" class="btn btn-outline-secondary">View Vacancies</a>
                            <button type="submit" class="btn btn-primary px-4">Post Job</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>