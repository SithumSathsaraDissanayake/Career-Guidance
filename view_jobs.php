<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$nvq_filter = isset($_GET['nvq_level']) ? $_GET['nvq_level'] : '';

$jobs = [];

try {
    // 🔍 Database එකේ Columns (category, nvq_level) වලට ගැලපෙන පරිදි Query එක
    $sql = "SELECT * FROM jobs WHERE 1=1";
    
    if (!empty($category_filter)) {
        $sql .= " AND category = :category";
    }
    if (!empty($nvq_filter)) {
        $sql .= " AND nvq_level = :nvq_level";
    }
    
    $sql .= " ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    
    if (!empty($category_filter)) $stmt->bindValue(':category', $category_filter);
    if (!empty($nvq_filter)) $stmt->bindValue(':nvq_level', $nvq_filter);
    
    $stmt->execute();
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $jobs = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FuturePath - View Vacancies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        html, body { height: 100%; margin: 0; background-color: #f3f4f6; font-family: 'Segoe UI', system-ui, sans-serif; }
        .custom-navbar { background-color: #0f172a !important; padding: 15px 30px; }
        .custom-navbar .navbar-brand { font-weight: 800; font-size: 1.5rem; color: #38bdf8 !important; }
        .custom-navbar .nav-link { color: #9ca3af !important; font-weight: 500; }
        .custom-navbar .nav-link:hover { color: #ffffff !important; }
        .main-container { max-width: 1100px; margin: 40px auto; padding: 0 20px; }
        .job-card { background: white; border-radius: 14px; padding: 25px; box-shadow: 0 4px 10px rgba(0,0,0,0.02); border: 1px solid #e5e7eb; transition: transform 0.2s; }
        .job-card:hover { transform: translateY(-3px); }
        .filter-btn { border-radius: 20px; font-weight: 600; padding: 6px 16px; font-size: 0.9rem; }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <nav class="navbar navbar-expand-lg custom-navbar">
        <div class="container-fluid" style="max-width: 1200px; margin: 0 auto;">
            <a class="navbar-brand" href="student_dashboard.php"><i class="bi bi-compass-fill me-2"></i>FuturePath</a>
            <div class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav gap-2">
                    <li class="nav-item"><a class="nav-link" href="student_dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link active" href="view_jobs.php">Vacancies</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="main-container">
        
        <div class="p-4 bg-white rounded-3 shadow-sm mb-4 border-start border-success border-5">
            <h3 class="fw-bold text-dark m-0">Explore Career Vacancies & NVQ Matching 💼</h3>
            <p class="text-muted m-0 mt-1">ඔබේ NVQ සුදුසුකමට ගැළපෙන රැකියා ක්ෂේත්‍රයන් මෙතැනින් පෙරහන් (Filter) කර බලන්න.</p>
        </div>

        <div class="d-flex gap-2 flex-wrap mb-3">
            <span class="align-self-center fw-bold text-secondary me-2">Category:</span>
            <a href="view_jobs.php" class="btn filter-btn <?php echo empty($category_filter) ? 'btn-success' : 'btn-outline-secondary'; ?>">All</a>
            <a href="view_jobs.php?category=it&nvq_level=<?php echo $nvq_filter;?>" class="btn filter-btn <?php echo $category_filter === 'it' ? 'btn-success' : 'btn-outline-secondary'; ?>">IT Sector</a>
            <a href="view_jobs.php?category=management&nvq_level=<?php echo $nvq_filter;?>" class="btn filter-btn <?php echo $category_filter === 'management' ? 'btn-success' : 'btn-outline-secondary'; ?>">Management</a>
        </div>

        <div class="d-flex gap-2 flex-wrap mb-4">
            <span class="align-self-center fw-bold text-secondary me-2">NVQ Match:</span>
            <a href="view_jobs.php?category=<?php echo $category_filter; ?>" class="btn filter-btn <?php echo empty($nvq_filter) ? 'btn-dark' : 'btn-outline-dark'; ?>">Any Level</a>
            <a href="view_jobs.php?category=<?php echo $category_filter; ?>&nvq_level=NVQ Level 4" class="btn filter-btn <?php echo $nvq_filter === 'NVQ Level 4' ? 'btn-dark' : 'btn-outline-dark'; ?>">Level 4</a>
            <a href="view_jobs.php?category=<?php echo $category_filter; ?>&nvq_level=NVQ Level 5" class="btn filter-btn <?php echo $nvq_filter === 'NVQ Level 5' ? 'btn-dark' : 'btn-outline-dark'; ?>">Level 5 (HNDIT)</a>
            <a href="view_jobs.php?category=<?php echo $category_filter; ?>&nvq_level=NVQ Level 6" class="btn filter-btn <?php echo $nvq_filter === 'NVQ Level 6' ? 'btn-dark' : 'btn-outline-dark'; ?>">Level 6</a>
        </div>

        <div class="row g-4">
            <?php if (count($jobs) > 0): ?>
                <?php foreach ($jobs as $job): ?>
                    <div class="col-md-6">
                        <div class="job-card d-flex flex-column h-100">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h4 class="fw-bold text-dark m-0 fs-5"><?php echo htmlspecialchars($job['job_title']); ?></h4>
                                <span class="badge bg-dark text-white px-2.5 py-1.5 rounded-3 small">
                                    <i class="bi bi-award-fill me-1 text-warning"></i> <?php echo htmlspecialchars($job['nvq_level']); ?>
                                </span>
                            </div>
                            <h6 class="text-primary fw-semibold mb-3"><i class="bi bi-building me-1"></i> <?php echo htmlspecialchars($job['company_name']); ?></h6>
                            
                            <p class="text-muted small mb-3 flex-grow-1" style="white-space: pre-line;">
                                <strong>Description:</strong><br><?php echo htmlspecialchars($job['description']); ?>
                            </p>

                            <p class="text-muted small mb-4" style="white-space: pre-line;">
                                <strong>Qualifications:</strong><br><span class="text-dark fw-medium"><?php echo htmlspecialchars($job['qualifications'] ?? 'N/A'); ?></span>
                            </p>
                            
                            <hr class="mt-auto opacity-25">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-danger small fw-semibold">
                                    <i class="bi bi-calendar-x me-1"></i> Closing: <?php echo htmlspecialchars($job['closing_date'] ?? 'Not Specified'); ?>
                                </span>
                               <a href="apply_job.php?job_id=<?php echo $job['job_id']; ?>" class="btn btn-sm btn-primary px-4 rounded-2 fw-semibold">
    Apply Now <i class="bi bi-arrow-right ms-1"></i>
</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center text-muted py-5">
                    <i class="bi bi-briefcase-fill fs-1 d-block mb-2 text-light"></i>
                    තෝරාගත් සුදුසුකම් යටතේ දැනට කිසිදු රැකියා අවස්ථාවක් ඇතුළත් කර නොමැත.
                </div>
            <?php endif; ?>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>