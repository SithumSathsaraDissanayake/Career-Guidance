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

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_jobs.php");
    exit();
}

$job_id = intval($_GET['id']);

// Handle Form Update
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
            $sql = "UPDATE jobs SET 
                    job_title = :job_title, 
                    company_name = :company_name, 
                    category = :category, 
                    nvq_level = :nvq_level, 
                    description = :description, 
                    qualifications = :qualifications, 
                    closing_date = :closing_date 
                    WHERE job_id = :job_id";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':job_title' => $job_title,
                ':company_name' => $company_name,
                ':category' => $category,
                ':nvq_level' => $nvq_level,
                ':description' => $description,
                ':qualifications' => $qualifications,
                ':closing_date' => $closing_date,
                ':job_id' => $job_id
            ]);

            $message = "Job vacancy updated successfully!";
        } catch (PDOException $e) {
            $error = "Error updating job: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}

// Fetch existing job data
try {
    $stmt = $conn->prepare("SELECT * FROM jobs WHERE job_id = :job_id");
    $stmt->execute([':job_id' => $job_id]);
    $job = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$job) {
        header("Location: manage_jobs.php");
        exit();
    }
} catch (PDOException $e) {
    $error = "Error fetching job details: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Job Vacancy - FuturePath</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Edit Job Vacancy</h4>
                    <a href="manage_jobs.php" class="btn btn-sm btn-outline-dark">Back to Manage Jobs</a>
                </div>
                <div class="card-body p-4">

                    <?php if (!empty($message)): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                    <?php endif; ?>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <form action="edit_job.php?id=<?php echo $job_id; ?>" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Job Title <span class="text-danger">*</span></label>
                            <input type="text" name="job_title" class="form-control" value="<?php echo htmlspecialchars($job['job_title']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Company Name <span class="text-danger">*</span></label>
                            <input type="text" name="company_name" class="form-control" value="<?php echo htmlspecialchars($job['company_name']); ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-select" required>
                                    <option value="IT Sector" <?php echo ($job['category'] == 'IT Sector') ? 'selected' : ''; ?>>IT Sector</option>
                                    <option value="Management" <?php echo ($job['category'] == 'Management') ? 'selected' : ''; ?>>Management</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">NVQ Level Match <span class="text-danger">*</span></label>
                                <select name="nvq_level" class="form-select" required>
                                    <option value="NVQ Level 4" <?php echo ($job['nvq_level'] == 'NVQ Level 4') ? 'selected' : ''; ?>>NVQ Level 4</option>
                                    <option value="NVQ Level 5" <?php echo ($job['nvq_level'] == 'NVQ Level 5') ? 'selected' : ''; ?>>NVQ Level 5 (HNDIT)</option>
                                    <option value="NVQ Level 6" <?php echo ($job['nvq_level'] == 'NVQ Level 6') ? 'selected' : ''; ?>>NVQ Level 6</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Job Description</label>
                            <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($job['description']); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Qualifications & Requirements</label>
                            <textarea name="qualifications" class="form-control" rows="3"><?php echo htmlspecialchars($job['qualifications'] ?? ''); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Closing Date</label>
                            <input type="date" name="closing_date" class="form-control" value="<?php echo htmlspecialchars($job['closing_date'] ?? ''); ?>">
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="manage_jobs.php" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-warning px-4">Update Vacancy</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>