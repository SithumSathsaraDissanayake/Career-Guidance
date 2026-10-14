<?php
session_start();

// 🔐 Checking whether data came via POST as a security measure
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: build_cv.php");
    exit();
}

// ✅ FIX: Retrieving data (without escaping — then escaping in output)
$full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : 'N/A';
$email = isset($_POST['email']) ? trim($_POST['email']) : 'N/A';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : 'N/A';
$address = isset($_POST['address']) ? trim($_POST['address']) : 'N/A';
$education = isset($_POST['education']) ? trim($_POST['education']) : 'N/A';
$skills = isset($_POST['skills']) ? trim($_POST['skills']) : '';
$experience = isset($_POST['experience']) ? trim($_POST['experience']) : 'N/A';

// Allocating skills separated by commas (,) into an array
$skills_array = !empty($skills) ? explode(',', $skills) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV - <?php echo htmlspecialchars($full_name); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body { background-color: #ffffff; color: #334155; font-family: 'Segoe UI', system-ui, sans-serif; font-size: 14px; line-height: 1.6; }
        .cv-wrapper { max-width: 800px; margin: 0 auto; padding: 40px; }
        
        /* Header Style */
        .cv-header { border-bottom: 3px solid #0f172a; padding-bottom: 20px; margin-bottom: 30px; }
        .cv-name { font-size: 2.2rem; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 1px; }
        .cv-contact-info { font-size: 0.9rem; color: #64748b; margin-top: 5px; }
        .cv-contact-info span { margin-right: 15px; }
        
        /* Section Style */
        .cv-section { margin-bottom: 25px; }
        .cv-section-title { font-size: 1.1rem; font-weight: 700; color: #0f172a; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; margin-bottom: 12px; }
        
        /* Skills Tags */
        .skill-tag { display: inline-block; background-color: #f1f5f9; color: #334155; padding: 4px 12px; border-radius: 6px; font-weight: 500; font-size: 0.85rem; margin-right: 6px; margin-bottom: 6px; border: 1px solid #e2e8f0; }
        
        /* 🖨️ PDF Print Settings */
        @media print {
            body { background-color: #ffffff; padding: 0; }
            .cv-wrapper { padding: 20px; max-width: 100%; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="bg-light p-3 border-bottom text-center no-print">
        <p class="m-0 small text-muted mb-2">
            Your professional CV is ready! Click the button below to save it as a PDF.
            (Select <strong>Save as PDF</strong> as the destination)
        </p>
        <button onclick="window.print()" class="btn btn-dark btn-sm fw-bold px-4 rounded-2">
            <i class="bi bi-printer-fill me-2 text-danger"></i> Print / Save as PDF
        </button>
        <a href="build_cv.php" class="btn btn-outline-secondary btn-sm ms-2 rounded-2">
            <i class="bi bi-pencil me-1"></i>Edit Details
        </a>
    </div>

    <div class="cv-wrapper">
        
        <!-- Header -->
        <div class="cv-header">
            <div class="cv-name"><?php echo htmlspecialchars($full_name); ?></div>
            <div class="cv-contact-info d-flex flex-wrap">
                <span>
                    <i class="bi bi-envelope-fill me-1 text-secondary"></i> 
                    <?php echo htmlspecialchars($email); ?>
                </span>
                <span>
                    <i class="bi bi-telephone-fill me-1 text-secondary"></i> 
                    <?php echo htmlspecialchars($phone); ?>
                </span>
                <span>
                    <i class="bi bi-geo-alt-fill me-1 text-secondary"></i> 
                    <?php echo htmlspecialchars($address); ?>
                </span>
            </div>
        </div>

        <!-- Education -->
        <div class="cv-section">
            <div class="cv-section-title">Education & Academic Qualifications</div>
            <div style="white-space: pre-line;" class="text-dark">
                <?php echo htmlspecialchars($education); ?>
            </div>
        </div>

        <!-- Skills -->
        <div class="cv-section">
            <div class="cv-section-title">Key Skills & Technologies</div>
            <div>
                <?php if (count($skills_array) > 0): ?>
                    <?php foreach ($skills_array as $skill): ?>
                        <span class="skill-tag"><?php echo htmlspecialchars(trim($skill)); ?></span>
                    <?php endforeach; ?>
                <?php else: ?>
                    <span class="text-muted">No skills specified.</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Experience -->
        <div class="cv-section">
            <div class="cv-section-title">Projects & Professional Experience</div>
            <div style="white-space: pre-line;" class="text-dark">
                <?php echo htmlspecialchars($experience); ?>
            </div>
        </div>

    </div>

    <script>
        window.onload = function() {
            // Delay the Print Dialog for a moment
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>

</body>
</html>