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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Received Applications - FuturePath</title>
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
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-custom py-3">
    <div class="container">
        <a class="navbar-brand fw-bold text-info fs-4 d-flex align-items-center gap-2" href="company_dashboard.php">
            <i class="bi bi-building-fill text-warning"></i> 
            <span style="background: linear-gradient(to right, #38bdf8, #60a5fa); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">FuturePath</span> 
            <span class="badge bg-warning text-dark fs-6 ms-2">Company Portal</span>
        </a>
        <div class="d-flex align-items-center gap-3">
            <a href="company_dashboard.php" class="btn btn-outline-light btn-sm"><i class="bi bi-arrow-left"></i> Dashboard</a>
            <a href="logout.php" class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
    </div>
</nav>

<div class="container my-5">
    <div class="card card-custom p-4">
        <h4 class="fw-bold mb-4 text-warning d-flex align-items-center gap-2">
            <i class="bi bi-people-fill"></i> Received Job Applications
        </h4>

        <div class="table-responsive">
            <table class="table table-custom align-middle">
                <thead>
                    <tr>
                        <th>Job Title</th>
                        <th>Applicant Name</th>
                        <th>Email</th>
                        <th>Resume File</th>
                        <th>Applied Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        // Correct query using the 'applications' table from screenshot
                        $sql = "SELECT a.*, j.job_title 
                                FROM applications a
                                INNER JOIN jobs j ON a.job_id = j.job_id
                                LEFT JOIN companies c ON j.company_id = c.company_id
                                WHERE c.user_id = :user_id OR j.company_name = :company_name
                                ORDER BY a.application_id DESC";
                        
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([
                            'user_id' => $user_id,
                            'company_name' => $company_name
                        ]);
                        $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if ($applications) {
                            foreach ($applications as $app) {
                                echo "<tr>";
                                echo "<td class='fw-bold text-info'>" . htmlspecialchars($app['job_title']) . "</td>";
                                echo "<td>" . htmlspecialchars($app['applicant_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($app['applicant_email']) . "</td>";
                                
                                // CV Download Link linked to uploads/resumes/
                                if (!empty($app['resume_file'])) {
                                    echo "<td><a href='uploads/resumes/" . htmlspecialchars($app['resume_file']) . "' target='_blank' class='btn btn-sm btn-outline-info'><i class='bi bi-file-earmark-pdf'></i> View CV</a></td>";
                                } else {
                                    echo "<td><span class='text-muted'>No CV</span></td>";
                                }
                                
                                echo "<td>" . htmlspecialchars($app['applied_at'] ?? 'N/A') . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center text-muted py-4'>No job applications received yet.</td></tr>";
                        }
                    } catch (PDOException $e) {
                        echo "<tr><td colspan='5' class='text-center text-danger py-4'>Error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>