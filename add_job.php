<?php
session_start();
include 'config/db.php';

// ✅ Allow BOTH company AND admin
$user_role = $_SESSION['user_role'] ?? '';
if (!isset($_SESSION['user_id']) || ($user_role !== 'company' && $user_role !== 'admin')) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$company_name = $_SESSION['user_name'];
$message = '';
$error = '';

// ✅ Form values (preserve on error)
$form_job_title = '';
$form_category = '';
$form_nvq_level = '';
$form_description = '';
$form_qualifications = '';
$form_closing_date = '';
$form_company_name = '';

// Get company_id (only for company role)
$company_id = null;
if ($user_role === 'company') {
    try {
        $comp_stmt = $conn->prepare("SELECT company_id FROM companies WHERE user_id = :user_id LIMIT 1");
        $comp_stmt->execute(['user_id' => $user_id]);
        $company_data = $comp_stmt->fetch(PDO::FETCH_ASSOC);
        if ($company_data) {
            $company_id = $company_data['company_id'];
        }
    } catch (PDOException $e) {}
}

// ✅ Get all companies for admin dropdown
$all_companies = [];
if ($user_role === 'admin') {
    try {
        $comp_list_stmt = $conn->query("SELECT company_id, company_name FROM companies ORDER BY company_name ASC");
        $all_companies = $comp_list_stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {}
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ✅ Capture form values
    $form_job_title = trim($_POST['job_title'] ?? '');
    $form_category = trim($_POST['category'] ?? '');
    $form_nvq_level = trim($_POST['nvq_level'] ?? '');
    $form_description = trim($_POST['description'] ?? '');
    $form_qualifications = trim($_POST['qualifications'] ?? '');
    $form_closing_date = trim($_POST['closing_date'] ?? '');
    
    // ✅ Admin ට company name manually select කරන්න පුළුවන්
    if ($user_role === 'admin') {
        $form_company_name = trim($_POST['company_name'] ?? '');
        $selected_company_id = intval($_POST['selected_company_id'] ?? 0);
        
        // If admin selected a company from dropdown, get its name
        if ($selected_company_id > 0) {
            try {
                $name_stmt = $conn->prepare("SELECT company_name FROM companies WHERE company_id = :cid LIMIT 1");
                $name_stmt->execute([':cid' => $selected_company_id]);
                $name_result = $name_stmt->fetch(PDO::FETCH_ASSOC);
                if ($name_result) {
                    $form_company_name = $name_result['company_name'];
                    $company_id = $selected_company_id;
                }
            } catch (PDOException $e) {}
        }
    } else {
        // Company role — use session company_name
        $form_company_name = $company_name;
    }

    if (!empty($form_job_title) && !empty($form_category) && !empty($form_nvq_level) && !empty($form_company_name)) {
        try {
            $sql = "INSERT INTO jobs (company_id, job_title, company_name, category, nvq_level, description, qualifications, closing_date) 
                    VALUES (:company_id, :job_title, :company_name, :category, :nvq_level, :description, :qualifications, :closing_date)";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':company_id' => $company_id,
                ':job_title' => $form_job_title,
                ':company_name' => $form_company_name,
                ':category' => $form_category,
                ':nvq_level' => $form_nvq_level,
                ':description' => $form_description,
                ':qualifications' => $form_qualifications,
                ':closing_date' => $form_closing_date
            ]);

            // ✅ Success + Redirect (prevent duplicate on reload)
            $_SESSION['job_post_success'] = "Job vacancy posted successfully!";
            header("Location: add_job.php");
            exit();
            
        } catch (PDOException $e) {
            $error = "Error posting job: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}

// ✅ Show success message from session
if (isset($_SESSION['job_post_success'])) {
    $message = $_SESSION['job_post_success'];
    unset($_SESSION['job_post_success']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post New Job Vacancy - FuturePath</title>
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
        .form-select option {
            background-color: #1e2430;
            color: #ffffff;
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
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(234, 88, 12, 0.3);
        }
        .form-control::placeholder {
            color: #9ca3af !important;
        }
        ::-webkit-calendar-picker-indicator {
            filter: invert(1);
        }
        
        /* ✅ Admin info banner */
        .admin-banner {
            background: linear-gradient(135deg, rgba(220, 38, 38, 0.15), rgba(239, 68, 68, 0.1));
            border: 1px solid #dc2626;
            border-left: 5px solid #dc2626;
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .admin-banner i {
            color: #ef4444;
            font-size: 1.5rem;
        }
        .admin-banner .text {
            color: #fca5a5;
            font-size: 0.9rem;
        }
        .admin-banner .text strong {
            color: #ffffff;
        }
    </style>
</head>
<body>

<!-- Navbar — Role-based -->
<nav class="navbar navbar-expand-lg navbar-custom py-3">
    <div class="container">
        <a class="navbar-brand fw-bold text-info fs-4 d-flex align-items-center gap-2" href="<?php echo $user_role === 'admin' ? 'admin_dashboard.php' : 'company_dashboard.php'; ?>">
            <i class="bi bi-building-fill text-warning"></i> 
            <span style="background: linear-gradient(to right, #38bdf8, #60a5fa); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">FuturePath</span> 
            <span class="badge <?php echo $user_role === 'admin' ? 'bg-danger' : 'bg-warning text-dark'; ?> fs-6 ms-2">
                <?php echo $user_role === 'admin' ? 'Admin Panel' : 'Company Portal'; ?>
            </span>
        </a>
        <div class="d-flex align-items-center gap-3">
            <a href="<?php echo $user_role === 'admin' ? 'admin_dashboard.php' : 'company_dashboard.php'; ?>" class="btn btn-outline-light btn-sm">
                <i class="bi bi-arrow-left"></i> Dashboard
            </a>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>

<div class="container my-5" style="max-width: 800px;">
    
    <!-- ✅ Admin Info Banner -->
    <?php if ($user_role === 'admin'): ?>
        <div class="admin-banner">
            <i class="bi bi-shield-lock-fill"></i>
            <div class="text">
                <strong>Admin Mode:</strong> You can post jobs on behalf of any company. Select a company from the dropdown below or type a custom company name.
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($message)): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card card-custom p-4">
        <h4 class="fw-bold mb-4 text-warning d-flex align-items-center gap-2">
            <i class="bi bi-file-earmark-plus-fill"></i> Post a New Vacancy
        </h4>
        
        <form action="add_job.php" method="POST">
            
            <!-- ✅ Company Name (different for admin & company) -->
            <?php if ($user_role === 'admin'): ?>
                <!-- Admin: Dropdown + Custom input -->
                <div class="mb-3">
                    <label class="form-label small text-light">Select Company <span class="text-danger">*</span></label>
                    <select name="selected_company_id" class="form-select" id="companySelect">
                        <option value="0">— Select from registered companies —</option>
                        <?php foreach ($all_companies as $comp): ?>
                            <option value="<?php echo $comp['company_id']; ?>">
                                <?php echo htmlspecialchars($comp['company_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label small text-light">Or Type Custom Company Name <span class="text-danger">*</span></label>
                    <input type="text" name="company_name" id="customCompanyName" class="form-control" 
                           placeholder="e.g. ABC Tech Solutions" 
                           value="<?php echo htmlspecialchars($form_company_name); ?>">
                    <small class="text-muted">Leave blank if you selected a company above</small>
                </div>
            <?php else: ?>
                <!-- Company: Show own name (read-only) -->
                <div class="mb-3">
                    <label class="form-label small text-light">Company Name</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($company_name); ?>" readonly>
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label small text-light">Job Title / Position <span class="text-danger">*</span></label>
                <input type="text" name="job_title" class="form-control" 
                       placeholder="e.g. Associate Software Engineer" 
                       value="<?php echo htmlspecialchars($form_job_title); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label small text-light">Category <span class="text-danger">*</span></label>
                <select name="category" class="form-select" required>
                    <option value="" disabled <?php echo empty($form_category) ? 'selected' : ''; ?>>Select Category</option>
                    <option value="IT Sector" <?php echo $form_category === 'IT Sector' ? 'selected' : ''; ?>>💻 IT Sector</option>
                    <option value="Engineering" <?php echo $form_category === 'Engineering' ? 'selected' : ''; ?>>🏗️ Engineering</option>
                    <option value="Management" <?php echo $form_category === 'Management' ? 'selected' : ''; ?>>💼 Management</option>
                    <option value="Healthcare" <?php echo $form_category === 'Healthcare' ? 'selected' : ''; ?>>🏥 Healthcare</option>
                    <option value="Construction" <?php echo $form_category === 'Construction' ? 'selected' : ''; ?>>🏢 Construction</option>
                    <option value="Design" <?php echo $form_category === 'Design' ? 'selected' : ''; ?>>🎨 Design</option>
                    <option value="Agriculture" <?php echo $form_category === 'Agriculture' ? 'selected' : ''; ?>>🌾 Agriculture</option>
                    <option value="Education" <?php echo $form_category === 'Education' ? 'selected' : ''; ?>>📚 Education</option>
                    <option value="Other" <?php echo $form_category === 'Other' ? 'selected' : ''; ?>>📌 Other</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label small text-light">NVQ Level Required <span class="text-danger">*</span></label>
                <select name="nvq_level" class="form-select" required>
                    <option value="NVQ Level 3" <?php echo $form_nvq_level === 'NVQ Level 3' ? 'selected' : ''; ?>>NVQ Level 3 (Certificate)</option>
                    <option value="NVQ Level 4" <?php echo $form_nvq_level === 'NVQ Level 4' ? 'selected' : ''; ?>>NVQ Level 4 (Certificate)</option>
                    <option value="NVQ Level 5" <?php echo $form_nvq_level === 'NVQ Level 5' ? 'selected' : ''; ?>>NVQ Level 5 (Diploma / HNDIT)</option>
                    <option value="NVQ Level 6" <?php echo $form_nvq_level === 'NVQ Level 6' ? 'selected' : ''; ?>>NVQ Level 6 (Higher Diploma / Degree)</option>
                    <option value="NVQ Level 7" <?php echo $form_nvq_level === 'NVQ Level 7' ? 'selected' : ''; ?>>NVQ Level 7 (Postgraduate)</option>
                    <option value="Any Level" <?php echo $form_nvq_level === 'Any Level' ? 'selected' : ''; ?>>Any Level (No specific requirement)</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label small text-light">Job Description <span class="text-danger">*</span></label>
                <textarea name="description" class="form-control" rows="4" 
                          placeholder="Brief overview of the role and responsibilities..." required><?php echo htmlspecialchars($form_description); ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label small text-light">Qualifications & Skills</label>
                <textarea name="qualifications" class="form-control" rows="3" 
                          placeholder="e.g. PHP, Java, MySQL, Communication skills..."><?php echo htmlspecialchars($form_qualifications); ?></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label small text-light">Closing Date <span class="text-danger">*</span></label>
                <input type="date" name="closing_date" class="form-control" 
                       min="<?php echo date('Y-m-d'); ?>" 
                       value="<?php echo htmlspecialchars($form_closing_date); ?>" required>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <a href="<?php echo $user_role === 'admin' ? 'admin_dashboard.php' : 'company_dashboard.php'; ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </a>
                <button type="submit" class="btn btn-custom px-4">
                    <i class="bi bi-send-fill me-1"></i> Publish Vacancy
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- ✅ Admin company dropdown logic -->
<?php if ($user_role === 'admin'): ?>
<script>
    const companySelect = document.getElementById('companySelect');
    const customInput = document.getElementById('customCompanyName');
    
    if (companySelect && customInput) {
        companySelect.addEventListener('change', function() {
            if (this.value !== '0') {
                // Company selected → clear custom input + make optional
                customInput.value = '';
                customInput.required = false;
                customInput.disabled = false;
                customInput.style.opacity = '0.5';
                customInput.placeholder = 'Company selected from dropdown above';
            } else {
                // No selection → allow custom input
                customInput.required = true;
                customInput.disabled = false;
                customInput.style.opacity = '1';
                customInput.placeholder = 'e.g. ABC Tech Solutions';
            }
        });
        
        // Initial state
        if (companySelect.value === '0') {
            customInput.required = true;
        }
    }
</script>
<?php endif; ?>

</body>
</html>