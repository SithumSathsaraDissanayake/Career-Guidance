<?php
session_start();
include 'config/db.php';

// Check if user is logged in and is a company
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'company') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$company_name = $_SESSION['user_name'];
$msg = "";

// Get company_id from companies table
$company_id = null;
try {
    $comp_stmt = $conn->prepare("SELECT company_id FROM companies WHERE user_id = :user_id LIMIT 1");
    $comp_stmt->execute(['user_id' => $user_id]);
    $company_data = $comp_stmt->fetch(PDO::FETCH_ASSOC);
    if ($company_data) {
        $company_id = $company_data['company_id'];
    }
} catch (PDOException $e) {
    // Handle database error if needed
}

// Handle Job Posting Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['post_job'])) {
    $job_title = trim($_POST['job_title']);
    $category = trim($_POST['category']);
    $nvq_level = trim($_POST['nvq_level']);
    $description = trim($_POST['description']);
    $qualifications = trim($_POST['qualifications']);
    $closing_date = $_POST['closing_date'];

    try {
        $sql = "INSERT INTO jobs (company_id, job_title, company_name, category, nvq_level, description, qualifications, closing_date) 
                VALUES (:company_id, :job_title, :company_name, :category, :nvq_level, :description, :qualifications, :closing_date)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'company_id' => $company_id,
            'job_title' => $job_title,
            'company_name' => $company_name,
            'category' => $category,
            'nvq_level' => $nvq_level,
            'description' => $description,
            'qualifications' => $qualifications,
            'closing_date' => $closing_date
        ]);

        $msg = "<div class='alert alert-success py-2 small alert-dismissible fade show' role='alert'>
                    <i class='bi bi-check-circle-fill me-1'></i> Job Vacancy Published Successfully!
                    <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                </div>";
    } catch (PDOException $e) {
        $msg = "<div class='alert alert-danger py-2 small alert-dismissible fade show' role='alert'>
                    <i class='bi bi-exclamation-triangle-fill me-1'></i> Error: " . htmlspecialchars($e->getMessage()) . "
                    <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                </div>";
    }
}

// Handle Job Deletion
if (isset($_GET['delete_job'])) {
    $delete_id = $_GET['delete_job'];
    try {
        $del_stmt = $conn->prepare("DELETE FROM jobs WHERE job_id = :job_id AND (company_id = :company_id OR company_name = :company_name)");
        $del_stmt->execute([
            'job_id' => $delete_id, 
            'company_id' => $company_id,
            'company_name' => $company_name
        ]);
        header("Location: company_dashboard.php");
        exit();
    } catch (PDOException $e) {
        $msg = "<div class='alert alert-danger py-2 small'>Could not delete vacancy.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FuturePath - Company Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body {
            background: radial-gradient(circle at 80% 20%, #1e40af 0%, #0284c7 35%, #1d4ed8 70%, #0369a1 100%);
            color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .navbar-custom {
            background-color: #15181f;
            border-bottom: 1px solid #2d3748;
        }

        .card-custom {
            background-color: #15181f;
            border: 1px solid #2d3748;
            border-radius: 16px;
            color: #ffffff;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }

        .form-control, .form-select {
            background-color: #1e2430 !important;
            border: 1px solid #374151 !important;
            color: #ffffff !important;
            padding: 10px 15px;
            border-radius: 8px;
        }
        .form-control:focus, .form-select:focus {
            border-color: #38bdf8 !important;
            box-shadow: 0 0 8px rgba(56, 189, 248, 0.2) !important;
        }

        .btn-custom {
            background: linear-gradient(to right, #ea580c, #f97316);
            color: white;
            border: none;
            font-weight: 600;
            border-radius: 8px;
        }
        .btn-custom:hover {
            opacity: 0.9;
            color: white;
        }

        .table-custom {
            color: #f8fafc;
        }
        .table-custom th {
            background-color: #1e2430;
            color: #38bdf8;
            border-bottom: 2px solid #374151;
        }
        .table-custom td {
            border-color: #2d3748;
            background-color: transparent;
            color: #f8fafc;
        }

        .form-control::placeholder {
            color: #9ca3af !important;
        }

        ::-webkit-calendar-picker-indicator {
            filter: invert(1);
        }
    </style>
</head>
<body>

<!-- Navigation Bar with Connected Pages -->
<nav class="navbar navbar-expand-lg navbar-custom py-3">
    <div class="container">
        <a class="navbar-brand fw-bold text-info fs-4 d-flex align-items-center gap-2" href="company_dashboard.php">
            <i class="bi bi-building-fill text-warning"></i> 
            <span style="background: linear-gradient(to right, #38bdf8, #60a5fa); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">FuturePath</span> 
            <span class="badge bg-warning text-dark fs-6 ms-2">Company Portal</span>
        </a>
        
        <button class="navbar-toggler navbar-dark" type="button" data-bs-toggle="collapse" data-bs-target="#companyNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="companyNavbar">
            <ul class="navbar-nav me-auto ms-4">
                <li class="nav-item">
                    <a class="nav-link text-light active" href="company_dashboard.php"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-light opacity-75" href="company_applications.php"><i class="bi bi-people-fill me-1"></i> Received Applications</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <span class="text-light small"><i class="bi bi-building me-1"></i> Company: <strong><?php echo htmlspecialchars($company_name); ?></strong></span>
                <a href="logout.php" class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </div>
    </div>
</nav>

<div class="container my-4">

    <?php echo $msg; ?>

    <!-- Quick Portal Links Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-custom p-3 d-flex flex-row align-items-center justify-content-between">
                <div>
                    <h6 class="fw-bold m-0 text-warning"><i class="bi bi-briefcase me-2"></i> Manage Job Applications</h6>
                    <small class="text-muted">Review students who applied for your listed vacancies.</small>
                </div>
                <a href="company_applications.php" class="btn btn-sm btn-outline-info rounded-pill px-3">
                    <i class="bi bi-eye-fill me-1"></i> View Applications
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Job Post Form -->
        <div class="col-lg-5">
            <div class="card card-custom p-4">
                <h5 class="fw-bold mb-3 text-warning d-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle-fill"></i> Post New Vacancy
                </h5>
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label small text-muted">Job Title</label>
                        <input type="text" name="job_title" class="form-control" placeholder="e.g. Trainee Software Engineer" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted">Category</label>
                        <select name="category" class="form-select" required>
                            <option value="IT Sector">IT Sector</option>
                            <option value="Management">Management</option>
                            <option value="Engineering">Engineering</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted">NVQ Level Required</label>
                        <select name="nvq_level" class="form-select" required>
                            <option value="NVQ Level 4">NVQ Level 4</option>
                            <option value="NVQ Level 5">NVQ Level 5</option>
                            <option value="NVQ Level 6">NVQ Level 6</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted">Job Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Brief details about the role..." required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted">Qualifications & Skills</label>
                        <textarea name="qualifications" class="form-control" rows="2" placeholder="e.g. PHP, Java, MySQL, OOP concepts"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted">Closing Date</label>
                        <input type="date" name="closing_date" class="form-control" required>
                    </div>

                    <button type="submit" name="post_job" class="btn btn-custom w-100 py-2 mt-2">
                        <i class="bi bi-send-fill me-1"></i> Publish Vacancy
                    </button>
                </form>
            </div>
        </div>

        <!-- Listed Jobs Table -->
        <div class="col-lg-7">
            <div class="card card-custom p-4">
                <h5 class="fw-bold mb-3 text-info d-flex align-items-center gap-2">
                    <i class="bi bi-briefcase-fill"></i> Posted Vacancies
                </h5>

                <div class="table-responsive">
                    <table class="table table-custom align-middle">
                        <thead>
                            <tr>
                                <th>Job Title</th>
                                <th>Category</th>
                                <th>NVQ Level</th>
                                <th>Closing Date</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                $stmt = $conn->prepare("SELECT * FROM jobs WHERE company_name = :c_name ORDER BY job_id DESC");
                                $stmt->execute(['c_name' => $company_name]);
                                $posted_jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                if ($posted_jobs) {
                                    foreach ($posted_jobs as $job) {
                                        echo "<tr>";
                                        echo "<td class='fw-bold'>" . htmlspecialchars($job['job_title']) . "</td>";
                                        echo "<td><span class='badge bg-primary'>" . htmlspecialchars($job['category']) . "</span></td>";
                                        echo "<td><span class='badge bg-secondary'>" . htmlspecialchars($job['nvq_level']) . "</span></td>";
                                        echo "<td>" . htmlspecialchars($job['closing_date']) . "</td>";
                                        echo "<td class='text-end'>";
                                        echo "<a href='company_dashboard.php?delete_job=" . $job['job_id'] . "' class='btn btn-outline-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this vacancy?\");'><i class='bi bi-trash'></i></a>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center text-muted py-4'>No vacancies posted yet.</td></tr>";
                                }
                            } catch (PDOException $e) {
                                echo "<tr><td colspan='5' class='text-center text-danger py-4'>Error loading jobs.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="text-end mt-3">
                    <a href="index.php" class="text-light small text-decoration-none"><i class="bi bi-arrow-left"></i> Back to Homepage</a>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>