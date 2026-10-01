<?php
session_start();
include 'config/db.php';
include 'classes/recommendation_engine.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$engine = new RecommendationEngine($conn);
$recommendations = $engine->calculateRecommendations($user_id);

// Top 8 විතරක් පෙන්නන්න
$recommendations = array_slice($recommendations, 0, 8);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Career Recommendations - FuturePath</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8fafc; font-family: 'Segoe UI', system-ui, sans-serif; }
        .card-custom { 
            border: none; 
            border-radius: 16px; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.04); 
            transition: all 0.3s;
        }
        .card-custom:hover { 
            transform: translateY(-4px); 
            box-shadow: 0 12px 28px rgba(0,0,0,0.1);
        }
        .match-badge { font-size: 0.85rem; padding: 8px 16px; border-radius: 20px; font-weight: 700; }
        .rank-badge {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.1rem;
            margin-right: 12px;
        }
        .rank-1 { background: linear-gradient(135deg, #fbbf24, #f59e0b); color: white; box-shadow: 0 4px 12px rgba(251,191,36,0.4); }
        .rank-2 { background: linear-gradient(135deg, #cbd5e1, #94a3b8); color: white; }
        .rank-3 { background: linear-gradient(135deg, #d97706, #b45309); color: white; }
        .rank-other { background: #e5e7eb; color: #6b7280; }
        
        .breakdown-item {
            font-size: 0.78rem;
            color: #6b7280;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-right: 12px;
        }
        .breakdown-item strong { color: #374151; }
    </style>
</head>
<body>

<div class="container py-5" style="max-width: 900px;">
    
    <div class="text-center mb-5">
        <h2 class="fw-bold text-dark">
            <i class="bi bi-stars text-warning me-2"></i>
            Your Personalized Career Matches
        </h2>
        <p class="text-muted">Aptitude Test, Skills සහ Academic දත්ත මත පදනම්ව ගණනය කරන ලද ප්‍රතිඵල.</p>
        
        <div class="d-flex justify-content-center gap-2 mt-3">
            <a href="add_skills.php" class="btn btn-outline-primary btn-sm rounded-pill">
                <i class="bi bi-lightning-charge me-1"></i> Update Skills
            </a>
            <a href="add_academic.php" class="btn btn-outline-success btn-sm rounded-pill">
                <i class="bi bi-mortarboard me-1"></i> Update Academic
            </a>
            <a href="aptitude_test.php" class="btn btn-outline-warning btn-sm rounded-pill">
                <i class="bi bi-journal-text me-1"></i> Retake Test
            </a>
        </div>
    </div>

    <?php if (!empty($recommendations)): ?>
        <?php foreach ($recommendations as $index => $item): 
            $rank = $index + 1;
            $rankClass = $rank <= 3 ? 'rank-' . $rank : 'rank-other';
            
            // Match badge color
            if ($item['match_percentage'] >= 70) {
                $badgeColor = 'bg-success';
            } elseif ($item['match_percentage'] >= 50) {
                $badgeColor = 'bg-primary';
            } elseif ($item['match_percentage'] >= 30) {
                $badgeColor = 'bg-warning text-dark';
            } else {
                $badgeColor = 'bg-secondary';
            }
        ?>
            <div class="card card-custom mb-4 p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex align-items-center">
                        <span class="rank-badge <?php echo $rankClass; ?>">#<?php echo $rank; ?></span>
                        <div>
                            <h4 class="fw-bold text-primary mb-0"><?php echo htmlspecialchars($item['title']); ?></h4>
                            <span class="badge bg-light text-dark small mt-1">
                                <i class="bi bi-tag me-1"></i><?php echo htmlspecialchars($item['field']); ?>
                            </span>
                        </div>
                    </div>
                    <span class="badge <?php echo $badgeColor; ?> match-badge">
                        <i class="bi bi-lightning-charge-fill me-1"></i><?php echo $item['match_percentage']; ?>%
                    </span>
                </div>
                
                <p class="text-secondary mb-3"><?php echo htmlspecialchars($item['description']); ?></p>
                
                <!-- Score Breakdown -->
                <div class="mb-3 p-3 bg-light rounded-3">
                    <div class="small fw-bold text-muted mb-2">
                        <i class="bi bi-bar-chart-fill me-1"></i> Score Breakdown:
                    </div>
                    <div class="d-flex flex-wrap">
                        <span class="breakdown-item">
                            <i class="bi bi-brain text-primary"></i>
                            Aptitude: <strong><?php echo $item['aptitude_score']; ?>%</strong> × 0.5
                        </span>
                        <span class="breakdown-item">
                            <i class="bi bi-lightning text-warning"></i>
                            Skills: <strong><?php echo $item['skill_score']; ?>%</strong> × 0.3
                        </span>
                        <span class="breakdown-item">
                            <i class="bi bi-mortarboard text-success"></i>
                            Academic: <strong><?php echo $item['academic_score']; ?>%</strong> × 0.2
                        </span>
                    </div>
                </div>
                
                <div class="progress mb-3" style="height: 10px;">
                    <div class="progress-bar <?php echo $badgeColor; ?>" role="progressbar" 
                         style="width: <?php echo $item['match_percentage']; ?>%;"></div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-2">
                    <a href="course_payment.php?career_id=<?php echo $item['career_id']; ?>" 
                       class="btn btn-outline-primary btn-sm rounded-pill px-4">
                        <i class="bi bi-book me-1"></i> Enroll Course
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
        
        <div class="alert alert-info text-center">
            <i class="bi bi-info-circle-fill me-1"></i>
            තවත් careers බලන්න <a href="#" class="fw-bold">සියලු careers බලන්න</a>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center p-5">
            <i class="bi bi-exclamation-triangle-fill fs-1 d-block mb-3"></i>
            <h5>තවමත් නිර්දේශ නොමැත!</h5>
            <p>කරුණාකර පළමුව Aptitude Test එක සම්පූර්ණ කරන්න.</p>
            <a href="aptitude_test.php" class="btn btn-primary rounded-pill px-4">
                Take Aptitude Test
            </a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>