<?php
session_start();
include 'config/db.php';

// 🔐 Security check
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";

// 📥 Form submit වුනාම
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_academic'])) {
    $stream = trim($_POST['stream']);
    $gpa = floatval($_POST['gpa']);
    
    // Validation
    if (empty($stream)) {
        $msg = "<div class='alert alert-danger'>කරුණාකර ඔබේ stream එක තෝරන්න!</div>";
    } elseif ($gpa < 0 || $gpa > 4) {
        $msg = "<div class='alert alert-danger'>GPA එක 0 සහ 4 අතර විය යුතුයි!</div>";
    } else {
        try {
            // පරණ academic data delete කරන්න
            $delStmt = $conn->prepare("DELETE FROM student_academic WHERE user_id = :uid");
            $delStmt->execute(['uid' => $user_id]);
            
            // අලුත් data insert කරන්න
            $insStmt = $conn->prepare(
                "INSERT INTO student_academic (user_id, stream, gpa) 
                 VALUES (:uid, :stream, :gpa)"
            );
            $insStmt->execute([
                'uid' => $user_id,
                'stream' => $stream,
                'gpa' => $gpa
            ]);
            
            $msg = "<div class='alert alert-success'>✅ Academic information සාර්ථකව save කරන ලදී! <a href='recommendation_results.php' class='fw-bold'>Recommendations බලන්න</a></div>";
            
        } catch (PDOException $e) {
            $msg = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
        }
    }
}

// දැනට තියෙන academic data ලබාගන්න
$currentAcademic = null;
try {
    $stmt = $conn->prepare("SELECT stream, gpa FROM student_academic WHERE user_id = :uid");
    $stmt->execute(['uid' => $user_id]);
    $currentAcademic = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Ignore
}

// Stream options
$streamOptions = [
    'IT' => 'Information Technology (IT / ICT)',
    'Physical Science' => 'Physical Science',
    'Bio Science' => 'Bio Science',
    'Commerce' => 'Commerce',
    'Arts' => 'Arts',
    'Engineering' => 'Engineering',
    'Management' => 'Management'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FuturePath - Academic Info</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f3f4f6; font-family: 'Segoe UI', system-ui, sans-serif; }
        .card-custom { border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .stream-card {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.25s ease;
            margin-bottom: 10px;
        }
        .stream-card:hover {
            border-color: #0284c7;
            background-color: #f0f9ff;
            transform: translateY(-2px);
        }
        .stream-card.selected {
            border-color: #0284c7;
            background-color: #e0f2fe;
            box-shadow: 0 0 15px rgba(2, 132, 199, 0.2);
        }
        .stream-card .icon-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0284c7, #1d4ed8);
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark px-4 py-3">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="student_dashboard.php">
            <i class="bi bi-compass-fill text-info me-2"></i>FuturePath
        </a>
        <a href="student_dashboard.php" class="btn btn-outline-light btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Dashboard
        </a>
    </div>
</nav>

<div class="container py-5" style="max-width: 700px;">
    
    <div class="card card-custom p-4 mb-4">
        <div class="text-center mb-4">
            <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                 style="width: 80px; height: 80px; font-size: 2rem;">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
            <h3 class="fw-bold">My Academic Background</h3>
            <p class="text-muted">ඔබේ අධ්‍යාපන තොරතුරු ඇතුළත් කරන්න. මේවා career matching සඳහා භාවිතා වේ.</p>
        </div>

        <?php echo $msg; ?>

        <?php if ($currentAcademic): ?>
        <div class="alert alert-info">
            <strong>දැනට ඔබේ Academic Info:</strong><br>
            <i class="bi bi-bookmark-check-fill text-primary me-1"></i>
            Stream: <strong><?php echo htmlspecialchars($currentAcademic['stream']); ?></strong><br>
            <i class="bi bi-star-fill text-warning me-1"></i>
            GPA: <strong><?php echo htmlspecialchars($currentAcademic['gpa']); ?></strong>
        </div>
        <?php endif; ?>

        <form method="POST" action="add_academic.php">
            
            <div class="mb-4">
                <label class="form-label fw-bold mb-3">
                    <i class="bi bi-book text-success me-1"></i>
                    ඔබේ Stream එක තෝරන්න:
                </label>
                
                <div class="row g-3">
                    <?php foreach ($streamOptions as $value => $label): 
                        $isSelected = ($currentAcademic && $currentAcademic['stream'] === $value);
                    ?>
                    <div class="col-md-6">
                        <div class="stream-card <?php echo $isSelected ? 'selected' : ''; ?>" 
                             onclick="selectStream('<?php echo $value; ?>', this)">
                            <div class="icon-circle">
                                <i class="bi bi-bookmark-fill"></i>
                            </div>
                            <div class="fw-bold small"><?php echo $label; ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <input type="hidden" name="stream" id="streamInput" 
                       value="<?php echo $currentAcademic ? htmlspecialchars($currentAcademic['stream']) : ''; ?>" required>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">
                    <i class="bi bi-star-fill text-warning me-1"></i>
                    ඔබේ GPA (0 - 4 අතර):
                </label>
                <input 
                    type="number" 
                    name="gpa" 
                    class="form-control form-control-lg" 
                    step="0.01" 
                    min="0" 
                    max="4" 
                    placeholder="උදා: 3.50"
                    value="<?php echo $currentAcademic ? htmlspecialchars($currentAcademic['gpa']) : ''; ?>"
                    required>
                <small class="text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    ඔබේ GPA එක නොදන්නවා නම්, <strong>3.00</strong> ලෙස දාන්න.
                </small>
            </div>

            <button type="submit" name="save_academic" class="btn btn-success w-100 py-3 fw-bold rounded-3">
                <i class="bi bi-check-circle-fill me-1"></i> Save Academic Info
            </button>
        </form>
    </div>

    <div class="text-center">
        <a href="recommendation_results.php" class="btn btn-outline-primary">
            <i class="bi bi-stars me-1"></i> View My Recommendations
        </a>
    </div>

</div>

<script>
function selectStream(stream, element) {
    // සියලු cards වලින් 'selected' class එක අයින් කරන්න
    document.querySelectorAll('.stream-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Click කරපු එකට 'selected' class එක add කරන්න
    element.classList.add('selected');
    
    // Hidden input එකට value එක දාන්න
    document.getElementById('streamInput').value = stream;
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>