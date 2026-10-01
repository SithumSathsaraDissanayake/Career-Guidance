<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;
$msg = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_slip'])) {
    $amount = $_POST['amount'];
    
    // Slip Image Upload Handling
    if (isset($_FILES['slip_image']) && $_FILES['slip_image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
        $filename = $_FILES['slip_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $new_filename = "SLIP_" . time() . "_" . rand(1000, 9999) . "." . $ext;
            $upload_dir = "uploads/slips/";

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            if (move_uploaded_file($_FILES['slip_image']['tmp_filename'], $upload_dir . $new_filename)) {
                $stmt = $conn->prepare("INSERT INTO payments (user_id, course_id, amount, slip_image, status) VALUES (:u, :c, :a, :img, 'Pending')");
                $stmt->execute([
                    'u' => $user_id,
                    'c' => $course_id,
                    'a' => $amount,
                    'img' => $new_filename
                ]);
                $msg = "බැංකු රිසිට්පත සාර්ථකව යොමු කරන ලදී. පරිපාලකගේ අනුමැතිය ලැබුණු පසු කෝස් එක ලබාගත හැක.";
            } else {
                $error = "ෆයිල් එක Upload කිරීමට අපොහොසත් විය.";
            }
        } else {
            $error = "කරුණාකර JPG, PNG හෝ PDF ෆෝමැට් එකෙන් රිසිට්පත ලබාදෙන්න.";
        }
    } else {
        $error = "කරුණාකර බැංකු රිසිට්පත තෝරන්න.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FuturePath - Course Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5" style="max-width: 550px;">
    <div class="card shadow-sm border-0 rounded-4 p-4">
        <h4 class="fw-bold text-dark mb-3">Course Fee Payment</h4>
        <p class="text-muted small">පහත දක්වා ඇති බැංකු ගිණුමට මුදල් තැන්පත් කර රිසිට්පත Upload කරන්න.</p>
        
        <div class="alert alert-info rounded-3 small">
            <strong>Bank Details:</strong><br>
            Bank Name: Commercial Bank<br>
            Account No: 8009123456<br>
            Account Name: FuturePath Education
        </div>

        <?php if ($msg): ?>
            <div class="alert alert-success"><?php echo $msg; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label fw-semibold">Amount (LKR)</label>
                <input type="number" name="amount" class="form-control" value="2500.00" readonly>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Upload Deposit Slip Image</label>
                <input type="file" name="slip_image" class="form-control" accept="image/*,.pdf" required>
            </div>

            <button type="submit" name="upload_slip" class="btn btn-primary w-100 fw-bold py-2 rounded-2">
                Submit Slip
            </button>
        </form>
    </div>
</div>

</body>
</html>