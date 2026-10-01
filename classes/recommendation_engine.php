<?php
class RecommendationEngine {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function calculateRecommendations($userId) {
        $testScores = $this->getAptitudeScores($userId);
        $userSkills = $this->getUserSkills($userId);
        $academicData = $this->getAcademicBackground($userId);

        $careerScores = [];
        $careers = $this->getAllCareers();

        foreach ($careers as $career) {
            $careerId = $career['career_id'];
            
            // 1. Aptitude Score (50%)
            $aptitudeScore = isset($testScores[$careerId]) ? $testScores[$careerId] : 0;
            
            // 2. Skills Score (30%)
            $skillScore = $this->matchSkills($userSkills, $career['required_skills']);
            
            // 3. Academic Score (20%)
            $academicScore = $this->matchAcademic($academicData, $career['field']);

            // Weighted Formula
            $finalScore = ($aptitudeScore * 0.5) + ($skillScore * 0.3) + ($academicScore * 0.2);

            $careerScores[] = [
                'career_id' => $careerId,
                'title' => $career['title'],
                'description' => $career['description'],
                'field' => $career['field'],
                'match_percentage' => round($finalScore, 2),
                // Breakdown එකත් එවන්න
                'aptitude_score' => round($aptitudeScore, 1),
                'skill_score' => round($skillScore, 1),
                'academic_score' => round($academicScore, 1)
            ];
        }

        usort($careerScores, function ($a, $b) {
            return $b['match_percentage'] <=> $a['match_percentage'];
        });

        return $careerScores;
    }

    private function getAptitudeScores($userId) {
        $stmt = $this->conn->prepare("SELECT career_id, score FROM aptitude_results WHERE user_id = :u");
        $stmt->execute(['u' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    private function getUserSkills($userId) {
        $stmt = $this->conn->prepare("SELECT skill_name FROM user_skills WHERE user_id = :u");
        $stmt->execute(['u' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    private function getAcademicBackground($userId) {
        $stmt = $this->conn->prepare("SELECT stream, gpa FROM student_academic WHERE user_id = :u");
        $stmt->execute(['u' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function getAllCareers() {
        $stmt = $this->conn->prepare("SELECT * FROM careers");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function matchSkills($userSkills, $requiredSkillsJson) {
        $required = json_decode($requiredSkillsJson, true) ?? [];
        if (empty($required)) return 0;

        // Normalize both arrays
        $userLower = array_map('strtolower', array_map('trim', $userSkills));
        $reqLower = array_map('strtolower', array_map('trim', $required));

        $matched = array_intersect($userLower, $reqLower);
        return (count($matched) / count($required)) * 100;
    }

    private function matchAcademic($academic, $careerField) {
        if (!$academic) return 50;
        
        $stream = strtolower(trim($academic['stream']));
        $field = strtolower(trim($careerField));
        
        if ($stream === $field) return 100;
        
        // Related fields
        $relatedFields = [
            'it' => ['it', 'ict', 'computer science', 'engineering'],
            'ict' => ['it', 'ict', 'computer science'],
            'physical science' => ['engineering', 'it'],
            'bio science' => ['healthcare', 'bio', 'medical', 'it'],
            'commerce' => ['management', 'business', 'it'],
            'arts' => ['design', 'arts', 'management', 'education'],
            'engineering' => ['engineering', 'it', 'physical science'],
            'management' => ['management', 'business', 'it'],
            'healthcare' => ['healthcare', 'bio science'],
            'education' => ['education', 'arts', 'management'],
            'design' => ['design', 'arts', 'it']
        ];
        
        if (isset($relatedFields[$stream]) && in_array($field, $relatedFields[$stream])) {
            return 80;
        }
        
        return 40;
    }
}