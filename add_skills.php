<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";

// Save skills
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_skills'])) {
    $selectedSkills = $_POST['skills'] ?? [];
    
    if (empty($selectedSkills)) {
        $msg = "<div class='alert alert-warning'>කරුණාකර අවම වශයෙන් එක skill එකක්වත් තෝරන්න!</div>";
    } else {
        try {
            // පරණ skills delete කරන්න
            $delStmt = $conn->prepare("DELETE FROM user_skills WHERE user_id = :uid");
            $delStmt->execute(['uid' => $user_id]);
            
            // අලුත් skills insert කරන්න
            $insStmt = $conn->prepare(
                "INSERT INTO user_skills (user_id, skill_name) VALUES (:uid, :skill)"
            );
            
            foreach ($selectedSkills as $skill) {
                $insStmt->execute(['uid' => $user_id, 'skill' => $skill]);
            }
            
            $count = count($selectedSkills);
            $msg = "<div class='alert alert-success'>✅ Skills <strong>$count</strong> ක් සාර්ථකව save කරන ලදී! <a href='recommendation_results.php' class='fw-bold'>Recommendations බලන්න →</a></div>";
        } catch (PDOException $e) {
            $msg = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
        }
    }
}

// Categories සහ skills load කරන්න
$categories = [];
try {
    $stmt = $conn->query("
        SELECT sc.category_id, sc.category_name, sc.category_icon,
               sm.skill_id, sm.skill_name
        FROM skill_categories sc
        LEFT JOIN skills_master sm ON sc.category_id = sm.category_id
        ORDER BY sc.display_order, sm.skill_name
    ");
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $catId = $row['category_id'];
        if (!isset($categories[$catId])) {
            $categories[$catId] = [
                'name' => $row['category_name'],
                'icon' => $row['category_icon'],
                'skills' => []
            ];
        }
        if ($row['skill_id']) {
            $categories[$catId]['skills'][] = [
                'id' => $row['skill_id'],
                'name' => $row['skill_name']
            ];
        }
    }
} catch (PDOException $e) {
    die("Error loading skills: " . $e->getMessage());
}

// දැනට user ගේ skills ලබාගන්න
$currentSkills = [];
try {
    $stmt = $conn->prepare("SELECT skill_name FROM user_skills WHERE user_id = :uid");
    $stmt->execute(['uid' => $user_id]);
    $currentSkills = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Skills - FuturePath</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f3f4f6; font-family: 'Segoe UI', system-ui, sans-serif; }
        .card-custom { border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        
        .category-section { 
            background: white; 
            border-radius: 14px; 
            padding: 22px; 
            margin-bottom: 20px;
            border-left: 5px solid #0284c7;
        }
        .category-title { 
            font-weight: 700; 
            font-size: 1.15rem;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .skill-checkbox {
            display: none;
        }
        .skill-label {
            display: inline-block;
            padding: 8px 16px;
            margin: 4px;
            border: 2px solid #e5e7eb;
            border-radius: 25px;
            cursor: pointer;
            font-size: 0.88rem;
            font-weight: 500;
            transition: all 0.2s ease;
            user-select: none;
            background: #f9fafb;
        }
        .skill-label:hover {
            border-color: #0284c7;
            background: #f0f9ff;
            transform: translateY(-2px);
        }
        .skill-checkbox:checked + .skill-label {
            background: linear-gradient(135deg, #0284c7, #1d4ed8);
            color: white;
            border-color: #0284c7;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
        }
        
        .category-1 { border-left-color: #0ea5e9; }
        .category-2 { border-left-color: #8b5cf6; }
        .category-3 { border-left-color: #f59e0b; }
        .category-4 { border-left-color: #10b981; }
        .category-5 { border-left-color: #ef4444; }
        .category-6 { border-left-color: #ec4899; }
        
        .selection-counter {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: linear-gradient(135deg, #0284c7, #1d4ed8);
            color: white;
            padding: 15px 25px;
            border-radius: 50px;
            box-shadow: 0 10px 30px rgba(2, 132, 199, 0.4);
            font-weight: 600;
            z-index: 1000;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark px-4 py-3 sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="student_dashboard.php">
            <i class="bi bi-compass-fill text-info me-2"></i>FuturePath
        </a>
        <a href="student_dashboard.php" class="btn btn-outline-light btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Dashboard
        </a>
    </div>
</nav>

<div class="container py-4" style="max-width: 1000px;">
    
    <div class="card card-custom p-4 mb-4 text-center">
        <h2 class="fw-bold mb-2">🎯 Select Your Skills</h2>
        <p class="text-muted mb-0">ඔබට තියෙන skills තෝරන්න. මේවා ඔබට ගැළපෙනම career එක recommend කරන්න භාවිතා වේ.</p>
    </div>

    <?php echo $msg; ?>

    <form method="POST" action="add_skills.php" id="skillsForm">
        
        <?php foreach ($categories as $catId => $category): ?>
        <div class="category-section category-<?php echo $catId; ?>">
            <div class="category-title">
                <span style="font-size: 1.5rem;"><?php echo $category['icon']; ?></span>
                <?php echo htmlspecialchars($category['name']); ?> Skills
                <span class="badge bg-secondary ms-auto"><?php echo count($category['skills']); ?></span>
            </div>
            
            <div class="d-flex flex-wrap">
                <?php foreach ($category['skills'] as $skill): 
                    $isChecked = in_array($skill['name'], $currentSkills);
                ?>
                <input 
                    type="checkbox" 
                    class="skill-checkbox" 
                    name="skills[]" 
                    value="<?php echo htmlspecialchars($skill['name']); ?>"
                    id="skill_<?php echo $skill['id']; ?>"
                    <?php echo $isChecked ? 'checked' : ''; ?>>
                <label class="skill-label" for="skill_<?php echo $skill['id']; ?>">
                    <?php echo htmlspecialchars($skill['name']); ?>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <div class="text-center mb-5">
            <button type="submit" name="save_skills" class="btn btn-primary btn-lg px-5 py-3 rounded-pill fw-bold">
                <i class="bi bi-check-circle-fill me-2"></i> Save My Skills
            </button>
        </div>
    </form>
</div>

<div class="selection-counter" id="counter">
    <i class="bi bi-check2-square me-1"></i> <span id="count">0</span> skills selected
</div>

<script>
    const checkboxes = document.querySelectorAll('.skill-checkbox');
    const countEl = document.getElementById('count');
    
    function updateCount() {
        const count = document.querySelectorAll('.skill-checkbox:checked').length;
        countEl.textContent = count;
    }
    
    checkboxes.forEach(cb => cb.addEventListener('change', updateCount));
    updateCount();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>