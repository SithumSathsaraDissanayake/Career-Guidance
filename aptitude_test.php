<?php
session_start();
include 'config/db.php';

// 🔐 Checking if the user is a student
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$show_results = false;
$suggested_career = "";
$career_description = "";

// 🌐 Language selection
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'en';
if (!in_array($lang, ['en', 'si', 'ta'])) {
    $lang = 'en';
}

// 📊 After the student submits the answers
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_test'])) {
    $scores = [
        'it' => 0,
        'engineering' => 0,
        'management' => 0,
        'healthcare' => 0,
        'construction' => 0,
        'design' => 0,
        'agriculture' => 0,
        'education' => 0
    ];

    // ප්‍Adding up the answers to the 20 questions and calculating the score
    for ($i = 1; $i <= 20; $i++) {
        if (isset($_POST["q$i"])) {
            $answer = $_POST["q$i"];
            if (array_key_exists($answer, $scores)) {
                $scores[$answer]++;
            }
        }
    }

    // Find the field with the highest score
    arsort($scores);
    $top_career = key($scores);

    // 💡 Determining the Career Path Based on the Result (Language-wise)
    if ($lang == 'si') {
        if ($top_career == 'it') {
            $suggested_career = "Information Technology & Software Engineering 💻";
            $career_description = "ඔබට තර්කානුකූලව සිතීමේ සහ ගැටලු විසඳීමේ ඉහළ හැකියාවක් ඇත. මෘදුකාංග ඉංජිනේරු විද්‍යාව, දත්ත විශ්ලේෂණය, හෝ වෙබ් සංවර්ධනය වැනි ක්ෂේත්‍ර ඔබට වඩාත්ම ගැළපේ.";
        } elseif ($top_career == 'engineering') {
            $suggested_career = "Engineering & Technical Fields 🛠️";
            $career_description = "ඔබ උපකරණ ක්‍රියාකාරීත්වය, තාක්ෂණික නිර්මාණ සහ භෞතික පද්ධති පිළිබඳව උනන්දුවක් දක්වයි. සිවිල්, යාන්ත්‍රික, හෝ විද්‍යුත් ඉංජිනේරු ක්ෂේත්‍ර ඔබට ගැළපේ.";
        } elseif ($top_career == 'management') {
            $suggested_career = "Business Management & Entrepreneurship 📊";
            $career_description = "ඔබ සන්නිවේදනයට, නායකත්වයට සහ සැලසුම් සකස් කිරීමට දක්ෂයෙකි. ව්‍යාපාර කළමනාකරණය, මානව සම්පත්, අලෙවිකරණය හෝ ව්‍යාපාරයක් ඇරඹීම ඔබට සුදුසුය.";
        } elseif ($top_career == 'healthcare') {
            $suggested_career = "Healthcare & Medical Services 🏥";
            $career_description = "ඔබට මිනිසුන්ට උපකාර කිරීමට සහ රෝගී සත්කාර කිරීමට උනන්දුවක් ඇත. හෙද සේවය, වෛද්‍ය රසායනාගාර, හෝ සෞඛ්‍ය පරිපාලන ක්ෂේත්‍ර ඔබට ගැළපේ.";
        } elseif ($top_career == 'construction') {
            $suggested_career = "Construction & Infrastructure 🏗️";
            $career_description = "ඔබ ගොඩනැගිලි ඉදිකිරීම්, ප්‍රමාණ සමීක්ෂණය සහ ඉදිකිරීම් කළමනාකරණයට උනන්දුවක් දක්වයි. Quantity Surveyor, Site Supervisor හෝ Construction Manager වෘත්තීය මාවත් ඔබට ගැළපේ.";
        } elseif ($top_career == 'agriculture') {
            $suggested_career = "Agriculture & Agribusiness 🌾";
            $career_description = "ඔබ කෘෂිකර්මය, බෝග වගාව සහ ගොවිපල කළමනාකරණයට උනන්දුවක් දක්වයි. කෘෂිකර්ම නිලධාරී, ගොවිපල කළමනාකරු හෝ කෘෂි ව්‍යාපාරික ක්ෂේත්‍ර ඔබට ගැළපේ.";
        } elseif ($top_career == 'education') {
            $suggested_career = "Education & Teaching 📚";
            $career_description = "ඔබ දැනුම බෙදාගැනීමට සහ අනිත් අයට උගන්වන්නට උනන්දුවක් දක්වයි. ගුරු වෘත්තිය, අධ්‍යාපන උපදේශනය හෝ පුහුණු ක්ෂේත්‍ර ඔබට ගැළපේ.";
        } else {
            $suggested_career = "Creative Arts & Digital Design 🎨";
            $career_description = "ඔබ ඉතා නිර්මාණශීලී, අලුත් විදිහට හිතන කෙනෙකි. UI/UX Design, Graphic Design, Digital Marketing, හෝ Multimedia ක්ෂේත්‍ර ඔස්සේ ඔබට ඉහළ ගමනක් යා හැක.";
        }
    } elseif ($lang == 'ta') {
        if ($top_career == 'it') {
            $suggested_career = "Information Technology & Software Engineering 💻";
            $career_description = "நீங்கள் தர்க்கரீதியாக சிந்திக்கவும் பிரச்சினைகளைத் தீர்க்கவும் அதிக திறன் கொண்டவர். மென்பொருள் பொறியியல், தரவு அறிவியல், அல்லது வலை மேம்பாடு போன்ற துறைகள் உங்களுக்கு பொருத்தமானவை.";
        } elseif ($top_career == 'engineering') {
            $suggested_career = "Engineering & Technical Fields 🛠️";
            $career_description = "நீங்கள் சாதனங்களின் செயல்பாடு, தொழில்நுட்ப வடிவமைப்புகள் மற்றும் இயற்பியல் அமைப்புகளில் ஆர்வம் காட்டுகிறீர்கள். சிவில், இயந்திர, அல்லது மின் பொறியியல் போன்ற துறைகள் பொருத்தமானவை.";
        } elseif ($top_career == 'management') {
            $suggested_career = "Business Management & Entrepreneurship 📊";
            $career_description = "நீங்கள் தொடர்பு, தலைமைத்துவம் மற்றும் திட்டமிடலில் திறமையானவர். வணிக மேலாண்மை, மனித வளம், சந்தைப்படுத்தல் அல்லது சொந்த வணிகத்தைத் தொடங்குதல் பொருத்தமானது.";
        } elseif ($top_career == 'healthcare') {
            $suggested_career = "Healthcare & Medical Services 🏥";
            $career_description = "மக்களுக்கு உதவுவதிலும் நோயாளி பராமரிப்பிலும் உங்களுக்கு ஆர்வம் உள்ளது. செவிலியம், மருத்துவ ஆய்வகம் அல்லது சுகாதார நிர்வாகத் துறைகள் பொருத்தமானவை.";
        } elseif ($top_career == 'construction') {
            $suggested_career = "Construction & Infrastructure 🏗️";
            $career_description = "கட்டுமானம், அளவு ஆய்வு மற்றும் கட்டுமான மேலாண்மையில் உங்களுக்கு ஆர்வம் உள்ளது. Quantity Surveyor, Site Supervisor அல்லது Construction Manager பாதைகள் பொருத்தமானவை.";
        } elseif ($top_career == 'agriculture') {
            $suggested_career = "Agriculture & Agribusiness 🌾";
            $career_description = "விவசாயம், பயிர் வளர்ப்பு மற்றும் பண்ணை நிர்வாகத்தில் உங்களுக்கு ஆர்வம் உள்ளது. விவசாய அதிகாரி, பண்ணை மேலாளர் அல்லது விவசாய வணிகத் துறைகள் பொருத்தமானவை.";
        } elseif ($top_career == 'education') {
            $suggested_career = "Education & Teaching 📚";
            $career_description = "அறிவைப் பகிர்ந்து கொள்வதிலும் மற்றவர்களுக்குக் கற்பிப்பதிலும் உங்களுக்கு ஆர்வம் உள்ளது. ஆசிரியர், கல்வி ஆலோசகர் அல்லது பயிற்சித் துறைகள் பொருத்தமானவை.";
        } else {
            $suggested_career = "Creative Arts & Digital Design 🎨";
            $career_description = "நீங்கள் மிகவும் படைப்பாற்றல் மிக்கவர். UI/UX Design, Graphic Design, Digital Marketing, அல்லது Multimedia துறைகள் மூலம் மிக உயர்ந்த நிலைக்குச் செல்ல முடியும்.";
        }
    } else {
        if ($top_career == 'it') {
            $suggested_career = "Information Technology & Software Engineering 💻";
            $career_description = "You have a high ability to think logically and solve problems. Fields like Software Engineering, Data Science, or Web Development are best suited for you.";
        } elseif ($top_career == 'engineering') {
            $suggested_career = "Engineering & Technical Fields 🛠️";
            $career_description = "You are interested in how devices work, technical designs, and physical systems. Civil, Mechanical, or Electrical Engineering are suitable for you.";
        } elseif ($top_career == 'management') {
            $suggested_career = "Business Management & Entrepreneurship 📊";
            $career_description = "You are skilled in communication, leadership, and planning. Business Administration, HR, Marketing, or Entrepreneurship are suitable for you.";
        } elseif ($top_career == 'healthcare') {
            $suggested_career = "Healthcare & Medical Services 🏥";
            $career_description = "You are interested in helping people and providing patient care. Nursing, Medical Laboratory, or Healthcare Administration are suitable for you.";
        } elseif ($top_career == 'construction') {
            $suggested_career = "Construction & Infrastructure 🏗️";
            $career_description = "You are interested in construction, quantity surveying, and construction management. Quantity Surveyor, Site Supervisor, or Construction Manager paths are suitable for you.";
        } elseif ($top_career == 'agriculture') {
            $suggested_career = "Agriculture & Agribusiness 🌾";
            $career_description = "You are interested in agriculture, crop management, and farm management. Agricultural Officer, Farm Manager, or Agribusiness roles are suitable for you.";
        } elseif ($top_career == 'education') {
            $suggested_career = "Education & Teaching 📚";
            $career_description = "You are interested in sharing knowledge and teaching others. Teaching, Educational Counseling, or Training roles are suitable for you.";
        } else {
            $suggested_career = "Creative Arts & Digital Design 🎨";
            $career_description = "You are very creative and think in new ways. UI/UX Design, Graphic Design, Digital Marketing, or Multimedia are suitable for you.";
        }
    }

    $show_results = true;

    // 💾 SAVE TO DATABASE
    try {
        // Category → Career ID Map
        $categoryToCareerId = [
            'it' => 1,
            'engineering' => 2,
            'management' => 3,
            'healthcare' => 4,
            'construction' => 5,
            'design' => 6,
            'agriculture' => 7,
            'education' => 8
        ];
        
        $deleteStmt = $conn->prepare("DELETE FROM aptitude_results WHERE user_id = :uid");
        $deleteStmt->execute(['uid' => $_SESSION['user_id']]);
        
        $insertStmt = $conn->prepare("
            INSERT INTO aptitude_results (user_id, career_id, score) 
            VALUES (:uid, :cid, :score)
        ");
        
        foreach ($scores as $category => $score) {
            if (isset($categoryToCareerId[$category])) {
                $normalizedScore = min(100, $score * 5);  // 20 questions → score 0-20 → × 5 = 0-100
                
                $insertStmt->execute([
                    'uid' => $_SESSION['user_id'],
                    'cid' => $categoryToCareerId[$category],
                    'score' => $normalizedScore
                ]);
            }
        }
        
        // 💾 Skills save
        $skillMap = [
            'it' => ['Programming', 'Web Development', 'Database Management'],
            'engineering' => ['AutoCAD', 'Technical Drawing', 'Structural Analysis'],
            'management' => ['Accounting', 'Financial Analysis', 'Project Management'],
            'healthcare' => ['Patient Care', 'Medical Laboratory Techniques', 'First Aid'],
            'construction' => ['Quantity Surveying', 'Construction Technology', 'Surveying'],
            'design' => ['Graphic Design', 'UI/UX Design', 'Digital Illustration'],
            'agriculture' => ['Crop Management', 'Farm Management', 'Irrigation Techniques'],
            'education' => ['Lesson Planning', 'Classroom Management', 'Educational Technology']
        ];
        
        $delSkills = $conn->prepare("DELETE FROM user_skills WHERE user_id = :uid");
        $delSkills->execute(['uid' => $_SESSION['user_id']]);
        
        $insSkill = $conn->prepare("INSERT INTO user_skills (user_id, skill_name) VALUES (:uid, :skill)");
        
        arsort($scores);
        $topCategories = array_slice(array_keys($scores), 0, 2);
        
        foreach ($topCategories as $cat) {
            if (isset($skillMap[$cat])) {
                foreach ($skillMap[$cat] as $skill) {
                    $insSkill->execute(['uid' => $_SESSION['user_id'], 'skill' => $skill]);
                }
            }
        }
        
        // Academic save
        $checkAcademic = $conn->prepare("SELECT id FROM student_academic WHERE user_id = :uid");
        $checkAcademic->execute(['uid' => $_SESSION['user_id']]);
        
        if (!$checkAcademic->fetch()) {
            $insAcademic = $conn->prepare("
                INSERT INTO student_academic (user_id, stream, gpa) 
                VALUES (:uid, 'IT', 3.0)
            ");
            $insAcademic->execute(['uid' => $_SESSION['user_id']]);
        }
        
    } catch (PDOException $e) {
        error_log("Aptitude save error: " . $e->getMessage());
    }
}

// 🌐 Questions and answers by language
$questions = [];

if ($lang == 'si') {
    // SINHALA — 20 ප්‍රශ්න
    $questions = [
        1 => ['question' => 'ඔබ වඩාත්ම ප්‍රිය කරන්නේ කුමන ආකාරයේ ගැටලු විසඳීමටද?', 'answers' => ['it' => 'ගණිතමය හෝ පරිගණක කේතයන්හි ගැටලු', 'management' => 'මිනිසුන් අතර ඇතිවන ගැටලු සහ ව්‍යාපාරික ගැටලු', 'design' => 'නිර්මාණශීලී අදහස් හෝ මෝස්තර පිළිබඳ ගැටලු']],
        2 => ['question' => 'ඔබ නිදහස් වේලාවකදී වැඩිපුරම කිරීමට කැමති කුමක්ද?', 'answers' => ['engineering' => 'විද්‍යුත් උපකරණ හෝ යාන්ත්‍රික මෙවලම් ගලවා සකස් කිරීම', 'it' => 'අලුත් ඇප්ස්, වෙබ් අඩවි හෝ තාක්ෂණික තොරතුරු සෙවීම', 'design' => 'ඡායාරූපකරණය, චිත්‍ර ඇඳීම හෝ වීඩියෝ සංස්කරණය']],
        3 => ['question' => 'සමූහ ව්‍යාපෘතියකදී ඔබ නිතරම භාරගන්නා භූමිකාව කුමක්ද?', 'answers' => ['management' => 'කණ්ඩායම මෙහෙයවීම සහ වැඩ කොටස් බෙදා දීම', 'it' => 'ප්‍රධාන තාක්ෂණික වැඩ කොටස හෝ ගණනය කිරීම්', 'design' => 'ප්‍රොජෙක්ට් එකේ Presentation එක ලස්සනට නිර්මාණය කිරීම']],
        4 => ['question' => 'අලුත් වෙබ් අඩවියක් දුටු විට ඔබේ අවධානය මුලින්ම යොමු වන්නේ කුමකටද?', 'answers' => ['design' => 'එහි ඇති වර්ණ, පෙනුම සහ ලස්සන', 'it' => 'එහි වේගය සහ එය ක්‍රියාකරන ආකාරය', 'management' => 'එම වෙබ් අඩවියෙන් කරන ව්‍යාපාරය හෝ සේවාව']],
        5 => ['question' => 'පරිගණක භාෂා (Java, Python, PHP) ඉගෙන ගැනීමට ඔබ තුළ උනන්දුවක් පවතීද?', 'answers' => ['it' => 'ඔව්, ඉතාමත් කැමතියි', 'management' => 'නැත, මම වඩා කැමති කළමනාකරණ අංශයටයි', 'design' => 'නැත, මම වඩා කැමති නිර්මාණශීලී වැඩ වලටයි']],
        6 => ['question' => 'යන්ත්‍ර සූත්‍ර හෝ ගොඩනැගිලි සැලසුම් පිළිබඳව හැදෑරීමට ඔබ කැමතිද?', 'answers' => ['engineering' => 'ඔව්, තාක්ෂණික දැනුමට මම ප්‍රිය කරමි', 'construction' => 'ඔව්, ඉදිකිරීම් කටයුතු වලට මම කැමතියි', 'design' => 'නැත, මම වඩා කැමති කලාත්මක නිර්මාණ කිරීමටයි']],
        7 => ['question' => 'සමාජ මාධ්‍ය සඳහා ලෝගෝ සහ පෝස්ටර් නිර්මාණයට ඔබ දක්ෂද?', 'answers' => ['design' => 'ඔව්, මට හොඳ නිර්මාණශීලී හැකියාවක් ඇත', 'it' => 'නැත, මම කැමති පසුබිම් කේතකරණයටයි', 'management' => 'නැත, මම කැමති අලෙවිකරණයටයි']],
        8 => ['question' => 'නව තාක්ෂණික උපකරණයක් වෙළඳපොලට ආ විට ඔබ මුලින්ම කරන්නේ කුමක්ද?', 'answers' => ['it' => 'එහි Operating System සහ Specs සෙවීම', 'management' => 'එහි මිල, ඉල්ලුම සහ ලාභය ගැන සිතීම', 'engineering' => 'එහි Hardware ක්‍රියාකරන ආකාරය ගැන සිතීම']],
        9 => ['question' => 'අනාගතයේදී ඔබ වීමට වඩාත්ම කැමති කවුරුන් වීමටද?', 'answers' => ['management' => 'ප්‍රධාන කළමනාකාරවරයෙක් හෝ ව්‍යාපාරිකයෙක්', 'it' => 'ප්‍රධාන මෘදුකාංග ඉංජිනේරුවරයෙක්', 'healthcare' => 'වෛද්‍යවරයෙක් හෝ හෙදියක්']],
        10 => ['question' => 'ඔබ වැඩක් කිරීමට වඩාත්ම කැමති කුමනාකාර පරිසරයකද?', 'answers' => ['management' => 'මිනිසුන් සමඟ අදහස් හුවමාරු කරගත හැකි කාර්යාලයක', 'engineering' => 'වැඩබිමක හෝ පර්යේෂණාගාරයක', 'healthcare' => 'රෝහලක හෝ රෝගී සත්කාර ස්ථානයක']],
        11 => ['question' => 'රෝගී හෝ තුවාල ලැබූ මිනිසුන්ට උපකාර කිරීම ගැන ඔබට හැඟෙන්නේ කෙසේද?', 'answers' => ['healthcare' => 'ඉතා කැමතියි — මට මිනිසුන්ට උපකාර කිරීමට ඕන', 'management' => 'මම කළමනාකරණය හරහා උපකාර කිරීමට කැමතියි', 'construction' => 'මම යටිතල පහසුකම් ඉදිකිරීමෙන් උපකාර කිරීමට කැමතියි']],
        12 => ['question' => 'ගොඩනැගිලි සඳහා මිනුම් ගැනීම සහ පිරිවැය ඇස්තමේන්තු කිරීම ගැන උනන්දුද?', 'answers' => ['construction' => 'ඔව්, ප්‍රමාණ සමීක්ෂණය මට කැමතියි', 'engineering' => 'ඔව්, ඒත් නිර්මාණ පැත්තට කැමතියි', 'management' => 'ඔව්, ඒත් ව්‍යාපාර කළමනාකරණයට කැමතියි']],
        13 => ['question' => 'රටා හඳුනා ගැනීම සඳහා විශාල දත්ත ප්‍රමාණයක් විශ්ලේෂණය කිරීම ඔබට කැමතිද?', 'answers' => ['it' => 'ඔව්, දත්ත සමඟ වැඩ කිරීම මට කැමතියි', 'management' => 'ඔව්, ව්‍යාපාරික තීරණ සඳහා', 'healthcare' => 'ඔව්, වෛද්‍ය පර්යේෂණ සඳහා']],
        14 => ['question' => 'වෘත්තීය ජීවිතයේදී ඔබ වඩාත්ම වින්දනය කරන්නේ කුමක්ද?', 'answers' => ['engineering' => 'යන්ත්‍ර හෝ පද්ධති නිර්මාණය කිරීම', 'construction' => 'ඉදිකිරීම් ව්‍යාපෘති කළමනාකරණය කිරීම', 'design' => 'දෘශ්‍ය අන්තර්ගත නිර්මාණය කිරීම']],
        15 => ['question' => 'ඔබට ලෝකයේ එක් ප්‍රධාන ගැටලුවක් විසඳන්න පුළුවන් නම්, එය කුමක්ද?', 'answers' => ['healthcare' => 'රෝග සුව කිරීම සහ සෞඛ්‍ය සේවා වැඩිදියුණු කිරීම', 'engineering' => 'තිරසාර යටිතල පහසුකම් ගොඩනැගීම', 'it' => 'තාක්ෂණය සංවර්ධනය කිරීම']],
        16 => ['question' => 'ඔබට ගොවිපලක වැඩ කිරීමට කැමතිද?', 'answers' => ['agriculture' => 'ඔව්, කෘෂිකර්මයට මම බෙහෙවින් කැමතියි', 'construction' => 'නැත, ඉදිකිරීම් ක්ෂේත්‍රයට මම කැමතියි', 'design' => 'නැත, මම නිර්මාණශීලී වැඩ වලට කැමතියි']],
        17 => ['question' => 'අනිත් අයට දේවල් උගන්වන්නට ඔබ කැමතිද?', 'answers' => ['education' => 'ඔව්, මම ගුරු වෘත්තියට කැමතියි', 'management' => 'ඔව්, ඒත් මම පුහුණු ක්ෂේත්‍රයට කැමතියි', 'healthcare' => 'නැත, මම රෝගී සත්කාරයට කැමතියි']],
        18 => ['question' => 'බෝග වගා කිරීම සහ පස සකස් කිරීම ගැන ඔබ උනන්දුද?', 'answers' => ['agriculture' => 'ඔව්, ගොවිතැනට මම කැමතියි', 'engineering' => 'නැත, යන්ත්‍ර සූත්‍ර වලට කැමතියි', 'it' => 'නැත, තාක්ෂණයට කැමතියි']],
        19 => ['question' => 'පන්ති කාමරයක ගුරුවරයෙක් ලෙස වැඩ කිරීමට ඔබ කැමතිද?', 'answers' => ['education' => 'ඔව්, ගුරු වෘත්තියට මම කැමතියි', 'management' => 'නැත, ව්‍යාපාර කළමනාකරණයට කැමතියි', 'healthcare' => 'නැත, සෞඛ්‍ය සේවයට කැමතියි']],
        20 => ['question' => 'ඔබේ අනාගත වෘත්තිය ලෙස වඩාත්ම කැමති කුමක්ද?', 'answers' => ['agriculture' => 'කෘෂිකර්ම නිලධාරී හෝ ගොවිපල කළමනාකරු', 'education' => 'ගුරුවරයෙක් හෝ අධ්‍යාපන උපදේශකයෙක්', 'it' => 'මෘදුකාංග ඉංජිනේරුවරයෙක්']]
    ];
} elseif ($lang == 'ta') {
    // TAMIL — 20 கேள்விகள்
    $questions = [
        1 => ['question' => 'எந்த வகையான பிரச்சினைகளைத் தீர்ப்பதை விரும்புகிறீர்கள்?', 'answers' => ['it' => 'கணித அல்லது கணினி குறியீட்டு பிரச்சினைகள்', 'management' => 'மக்கள் மற்றும் வணிக பிரச்சினைகள்', 'design' => 'படைப்பாற்றல் யோசனைகள்']],
        2 => ['question' => 'ஓய்வு நேரத்தில் விரும்பிச் செய்வது எது?', 'answers' => ['engineering' => 'மின்னணு சாதனங்களை பிரித்து சரிசெய்வது', 'it' => 'புதிய செயலிகள், இணையதளங்களை ஆராய்வது', 'design' => 'புகைப்படம், ஓவியம் அல்லது வீடியோ எடிட்டிங்']],
        3 => ['question' => 'குழு திட்டத்தில் எடுக்கும் பங்கு என்ன?', 'answers' => ['management' => 'குழுவை வழிநடத்துதல் (Leader)', 'it' => 'முக்கிய தொழில்நுட்ப வேலைப்பகுதி', 'design' => 'Presentation-ஐ அழகாக வடிவமைப்பது']],
        4 => ['question' => 'புதிய இணையதளத்தில் கவனம் முதலில் எதில்?', 'answers' => ['design' => 'நிறங்கள், தோற்றம் (Layout & Visuals)', 'it' => 'வேகம் மற்றும் செயல்பாடு', 'management' => 'வணிகம் அல்லது சேவை']],
        5 => ['question' => 'கணினி மொழிகளை கற்க ஆர்வம் உள்ளதா?', 'answers' => ['it' => 'ஆம், மிகவும் விரும்புகிறேன்', 'management' => 'இல்லை, மேலாண்மையை விரும்புகிறேன்', 'design' => 'இல்லை, படைப்பாற்றலை விரும்புகிறேன்']],
        6 => ['question' => 'இயந்திர வரைபடங்களை படிப்பதில் விருப்பம் உள்ளதா?', 'answers' => ['engineering' => 'ஆம், தொழில்நுட்ப அறிவை விரும்புகிறேன்', 'construction' => 'ஆம், கட்டுமான வேலைகளை விரும்புகிறேன்', 'design' => 'இல்லை, கலைநயமிக்க படைப்புகளை விரும்புகிறேன்']],
        7 => ['question' => 'சமூக ஊடகங்களுக்கு லோகோக்களை வடிவமைக்க திறமையானவரா?', 'answers' => ['design' => 'ஆம், நல்ல படைப்பாற்றல் திறன் உள்ளது', 'it' => 'இல்லை, பின்னணி குறியீட்டை விரும்புகிறேன்', 'management' => 'இல்லை, சந்தைப்படுத்தலை விரும்புகிறேன்']],
        8 => ['question' => 'புதிய தொழில்நுட்ப சாதனம் வரும்போது முதலில் செய்வது?', 'answers' => ['it' => 'Operating System மற்றும் Specs பற்றி தேடுவது', 'management' => 'விலை, தேவை மற்றும் லாபம் பற்றி சிந்திப்பது', 'engineering' => 'Hardware எவ்வாறு செயல்படுகிறது என்பதை சிந்திப்பது']],
        9 => ['question' => 'எதிர்காலத்தில் யாராக விரும்புகிறீர்கள்?', 'answers' => ['management' => 'தலைமை மேலாளர் அல்லது தொழில்முனைவோர்', 'it' => 'தலைமை மென்பொருள் பொறியாளர்', 'healthcare' => 'மருத்துவர் அல்லது செவிலியர்']],
        10 => ['question' => 'வேலை செய்ய விரும்பும் சூழல் எது?', 'answers' => ['management' => 'அலுவலகத்தில் மக்களுடன்', 'engineering' => 'களத்தில் அல்லது ஆய்வகத்தில்', 'healthcare' => 'மருத்துவமனையில்']],
        11 => ['question' => 'நோயுற்ற மக்களுக்கு உதவுவது பற்றி எப்படி உணருகிறீர்கள்?', 'answers' => ['healthcare' => 'மிகவும் ஆர்வம் — நேரடியாக உதவ விரும்புகிறேன்', 'management' => 'நிர்வாகம் மூலம் உதவ விரும்புகிறேன்', 'construction' => 'உள்கட்டமைப்பு மூலம் உதவ விரும்புகிறேன்']],
        12 => ['question' => 'கட்டிடங்களுக்கான அளவீடு மற்றும் செலவு மதிப்பீட்டில் ஆர்வம்?', 'answers' => ['construction' => 'ஆம், அளவு ஆய்வு எனக்கு பிடிக்கும்', 'engineering' => 'ஆம், வடிவமைப்பு பக்கத்தை விரும்புகிறேன்', 'management' => 'ஆம், வணிக மேலாண்மையை விரும்புகிறேன்']],
        13 => ['question' => 'பெரிய தரவுகளை பகுப்பாய்வு செய்வதை விரும்புகிறீர்களா?', 'answers' => ['it' => 'ஆம், தரவுகளுடன் வேலை செய்வது பிடிக்கும்', 'management' => 'ஆம், வணிக முடிவுகளுக்கு', 'healthcare' => 'ஆம், மருத்துவ ஆராய்ச்சிக்கு']],
        14 => ['question' => 'தொழில் வாழ்க்கையில் மிகவும் அனுபவிக்கும் விஷயம்?', 'answers' => ['engineering' => 'இயந்திரங்களை வடிவமைத்து கட்டுவது', 'construction' => 'கட்டுமான திட்டங்களை நிர்வகிப்பது', 'design' => 'காட்சி உள்ளடக்கத்தை உருவாக்குவது']],
        15 => ['question' => 'உலகின் ஒரு பிரச்சினையை தீர்க்க முடிந்தால்?', 'answers' => ['healthcare' => 'நோய்களை குணப்படுத்துதல்', 'engineering' => 'நிலையான உள்கட்டமைப்பு', 'it' => 'தொழில்நுட்பத்தை உருவாக்குதல்']],
        16 => ['question' => 'பண்ணையில் வேலை செய்ய விரும்புகிறீர்களா?', 'answers' => ['agriculture' => 'ஆம், விவசாயத்தை விரும்புகிறேன்', 'construction' => 'இல்லை, கட்டுமானத்தை விரும்புகிறேன்', 'design' => 'இல்லை, படைப்பாற்றலை விரும்புகிறேன்']],
        17 => ['question' => 'மற்றவர்களுக்கு கற்பிக்க விரும்புகிறீர்களா?', 'answers' => ['education' => 'ஆம், ஆசிரியர் தொழிலை விரும்புகிறேன்', 'management' => 'ஆம், பயிற்சி துறையை விரும்புகிறேன்', 'healthcare' => 'இல்லை, நோயாளி பராமரிப்பை விரும்புகிறேன்']],
        18 => ['question' => 'பயிர் வளர்ப்பு மற்றும் மண் தயாரிப்பில் ஆர்வம்?', 'answers' => ['agriculture' => 'ஆம், விவசாயத்தை விரும்புகிறேன்', 'engineering' => 'இல்லை, இயந்திரங்களை விரும்புகிறேன்', 'it' => 'இல்லை, தொழில்நுட்பத்தை விரும்புகிறேன்']],
        19 => ['question' => 'வகுப்பறையில் ஆசிரியராக வேலை செய்ய விரும்புகிறீர்களா?', 'answers' => ['education' => 'ஆம், ஆசிரியர் தொழிலை விரும்புகிறேன்', 'management' => 'இல்லை, வணிக மேலாண்மையை விரும்புகிறேன்', 'healthcare' => 'இல்லை, சுகாதார சேவையை விரும்புகிறேன்']],
        20 => ['question' => 'எதிர்கால தொழிலாக விரும்புவது எது?', 'answers' => ['agriculture' => 'விவசாய அதிகாரி அல்லது பண்ணை மேலாளர்', 'education' => 'ஆசிரியர் அல்லது கல்வி ஆலோசகர்', 'it' => 'மென்பொருள் பொறியாளர்']]
    ];
} else {
    // ENGLISH — 20 QUESTIONS
    $questions = [
        1 => ['question' => 'What type of problems do you most enjoy solving?', 'answers' => ['it' => 'Solving problems in mathematics or computer code', 'management' => 'Solving problems between people and business problems', 'design' => 'Solving problems related to creative ideas or designs']],
        2 => ['question' => 'What do you most enjoy doing in your free time?', 'answers' => ['engineering' => 'Taking apart and fixing electronic devices or mechanical tools', 'it' => 'Exploring new apps, websites, or technical information', 'design' => 'Photography, drawing, or video editing']],
        3 => ['question' => 'What role do you usually take in a group project?', 'answers' => ['management' => 'Leading the team and distributing tasks (Leader)', 'it' => 'Doing the main technical part or calculations', 'design' => 'Designing the project presentation beautifully']],
        4 => ['question' => 'When you see a new website, what catches your attention first?', 'answers' => ['design' => 'Its colors, appearance, and visuals', 'it' => 'Its speed and how it works', 'management' => 'What business or service the website offers']],
        5 => ['question' => 'Are you interested in learning programming languages (Java, Python, PHP)?', 'answers' => ['it' => 'Yes, I really enjoy it', 'management' => 'No, I prefer the management field', 'design' => 'No, I prefer creative design work']],
        6 => ['question' => 'Do you like studying blueprints or building plans?', 'answers' => ['engineering' => 'Yes, I enjoy technical knowledge about it', 'construction' => 'Yes, I love construction and building work', 'design' => 'No, I prefer creating artistic designs']],
        7 => ['question' => 'Are you good at designing logos and posters for social media?', 'answers' => ['design' => 'Yes, I have good creative skills for it', 'it' => 'No, I prefer the background coding', 'management' => 'No, I prefer marketing and business strategy']],
        8 => ['question' => 'When a new tech device comes to the market, what do you do first?', 'answers' => ['it' => 'Search about its Operating System and Specs', 'management' => 'Think about its price, market demand, and profit', 'engineering' => 'Think about how the hardware works internally']],
        9 => ['question' => 'What do you most want to become in the future?', 'answers' => ['management' => 'A top Manager or Entrepreneur', 'it' => 'A lead Software Engineer', 'healthcare' => 'A doctor, nurse, or healthcare professional']],
        10 => ['question' => 'What kind of environment do you most prefer to work in?', 'answers' => ['management' => 'An office where you can exchange ideas with people', 'engineering' => 'A field or a laboratory', 'healthcare' => 'A hospital or patient care facility']],
        11 => ['question' => 'How do you feel about helping sick or injured people?', 'answers' => ['healthcare' => 'Very interested — I want to help people directly', 'management' => 'I prefer to help through management', 'construction' => 'I prefer to help by building infrastructure']],
        12 => ['question' => 'Are you interested in measuring and estimating costs for buildings?', 'answers' => ['construction' => 'Yes, quantity surveying and cost estimation interest me', 'engineering' => 'Yes, but I prefer the design and technical side', 'management' => 'Yes, but I prefer general business management']],
        13 => ['question' => 'Do you enjoy analyzing large amounts of data to find patterns?', 'answers' => ['it' => 'Yes, I love working with data and finding insights', 'management' => 'Yes, especially for business decisions', 'healthcare' => 'Yes, especially for medical research']],
        14 => ['question' => 'What would you enjoy most in a professional career?', 'answers' => ['engineering' => 'Designing and building machines or systems', 'construction' => 'Managing construction projects on-site', 'design' => 'Creating visual content and branding']],
        15 => ['question' => 'If you could solve one major world problem, what would it be?', 'answers' => ['healthcare' => 'Curing diseases and improving healthcare', 'engineering' => 'Building sustainable infrastructure and clean energy', 'it' => 'Developing technology to connect and help people']],
        16 => ['question' => 'Would you enjoy working on a farm?', 'answers' => ['agriculture' => 'Yes, I love agriculture and farming', 'construction' => 'No, I prefer the construction field', 'design' => 'No, I prefer creative work']],
        17 => ['question' => 'Do you like teaching or helping others learn?', 'answers' => ['education' => 'Yes, I want to be a teacher or educator', 'management' => 'Yes, but I prefer training roles in business', 'healthcare' => 'No, I prefer patient care']],
        18 => ['question' => 'Are you interested in growing crops or managing farmland?', 'answers' => ['agriculture' => 'Yes, agriculture and farming interest me', 'engineering' => 'No, I prefer engineering', 'it' => 'No, I prefer technology']],
        19 => ['question' => 'Would you like to work in a classroom as a teacher?', 'answers' => ['education' => 'Yes, I would love to be a teacher', 'management' => 'No, I prefer business management', 'healthcare' => 'No, I prefer healthcare services']],
        20 => ['question' => 'What is your ideal future career?', 'answers' => ['agriculture' => 'Agricultural Officer or Farm Manager', 'education' => 'Teacher or Educational Counselor', 'it' => 'Software Engineer or Tech Lead']]
    ];
}

// 🌐 UI text according to language
$ui_text = [];
if ($lang == 'si') {
    $ui_text = [
        'title' => 'FuturePath - වෘත්තීය කුසලතා පරීක්ෂණය',
        'nav_dashboard' => 'ප්‍රධාන මෙනුව',
        'nav_aptitude' => 'කුසලතා පරීක්ෂණය',
        'header' => 'වෘත්තීය කුසලතා පරීක්ෂණය 📝',
        'subheader' => 'පහත ප්‍රශ්න 20ට ඔබට වඩාත්ම ගැළපෙන පිළිතුර තෝරන්න.',
        'submit_btn' => 'ඉදිරිපත් කර මගේ වෘත්තීය මාවත බලන්න',
        'result_badge' => 'ඔබේ ප්‍රතිඵලය',
        'back_dashboard' => 'උපකරණ පුවරුවට ආපසු',
        'discuss_counselor' => 'උපදේශක සමඟ සාකච්ඡා කරන්න',
        'select_language' => 'භාෂාව තෝරන්න'
    ];
} elseif ($lang == 'ta') {
    $ui_text = [
        'title' => 'FuturePath - தொழில் திறன் தேர்வு',
        'nav_dashboard' => 'டாஷ்போர்டு',
        'nav_aptitude' => 'திறன் தேர்வு',
        'header' => 'தொழில் திறன் தேர்வு 📝',
        'subheader' => 'கீழே உள்ள 20 கேள்விகளுக்கு பொருத்தமான பதிலைத் தேர்ந்தெடுக்கவும்.',
        'submit_btn' => 'சமர்ப்பித்து எனது தொழில் பாதையைப் பார்க்கவும்',
        'result_badge' => 'உங்கள் முடிவு',
        'back_dashboard' => 'டாஷ்போர்டுக்குத் திரும்பு',
        'discuss_counselor' => 'ஆலோசகருடன் கலந்துரையாடுங்கள்',
        'select_language' => 'மொழியைத் தேர்ந்தெடுக்கவும்'
    ];
} else {
    $ui_text = [
        'title' => 'FuturePath - Career Aptitude Test',
        'nav_dashboard' => 'Dashboard',
        'nav_aptitude' => 'Aptitude Test',
        'header' => 'Career Aptitude Test 📝',
        'subheader' => 'Choose the answer that best suits you for the following 20 questions.',
        'submit_btn' => 'Submit & View My Career Path',
        'result_badge' => 'Your Test Result',
        'back_dashboard' => 'Back to Dashboard',
        'discuss_counselor' => 'Discuss with Counselor',
        'select_language' => 'Select Language'
    ];
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $ui_text['title']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        html, body { height: 100%; margin: 0; background-color: #f3f4f6; font-family: 'Segoe UI', system-ui, sans-serif; }
        .custom-navbar { background-color: #0f172a !important; padding: 15px 30px; }
        .custom-navbar .navbar-brand { font-weight: 800; font-size: 1.5rem; color: #38bdf8 !important; }
        .custom-navbar .nav-link { color: #9ca3af !important; font-weight: 500; }
        .custom-navbar .nav-link:hover { color: #ffffff !important; }
        .test-container { max-width: 800px; margin: 40px auto; padding: 0 20px; }
        .question-card { background: white; border-radius: 14px; padding: 25px; box-shadow: 0 4px 10px rgba(0,0,0,0.02); border: 1px solid #e5e7eb; }
        .form-check-input:checked { background-color: #0284c7; border-color: #0284c7; }
        .form-check-label { cursor: pointer; color: #374151; font-weight: 500; }
        .result-box { background: linear-gradient(135deg, #1e1b4b 0%, #311042 100%); color: white; border-radius: 20px; padding: 35px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); animation: fadeInUp 0.4s ease; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .lang-selector { display: flex; gap: 8px; }
        .lang-selector .btn { border-radius: 8px; font-weight: 600; font-size: 0.85rem; padding: 6px 14px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg custom-navbar">
        <div class="container-fluid" style="max-width: 1200px; margin: 0 auto;">
            <a class="navbar-brand" href="student_dashboard.php"><i class="bi bi-compass-fill me-2"></i>FuturePath</a>
            <div class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav gap-2 align-items-center">
                    <li class="nav-item"><a class="nav-link" href="student_dashboard.php"><?php echo $ui_text['nav_dashboard']; ?></a></li>
                    <li class="nav-item"><a class="nav-link active" href="aptitude_test.php"><?php echo $ui_text['nav_aptitude']; ?></a></li>
                    <li class="nav-item ms-3">
                        <div class="lang-selector">
                            <a href="?lang=en" class="btn btn-sm <?php echo $lang == 'en' ? 'btn-info text-dark' : 'btn-outline-light'; ?>">EN</a>
                            <a href="?lang=si" class="btn btn-sm <?php echo $lang == 'si' ? 'btn-info text-dark' : 'btn-outline-light'; ?>">සිං</a>
                            <a href="?lang=ta" class="btn btn-sm <?php echo $lang == 'ta' ? 'btn-info text-dark' : 'btn-outline-light'; ?>">தமிழ்</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="test-container">
        
        <?php if ($show_results): ?>
            <div class="result-box mb-5">
                <span class="badge bg-info mb-2 text-dark fw-bold"><i class="bi bi-trophy-fill me-1"></i> <?php echo $ui_text['result_badge']; ?></span>
                <h2 class="fw-bold text-info"><?php echo $suggested_career; ?></h2>
                <p class="mt-3 fs-5 text-white-50" style="line-height: 1.6;"><?php echo $career_description; ?></p>
                <hr class="my-4 opacity-25">
                <div class="d-flex gap-3 flex-wrap">
                    <a href="student_dashboard.php" class="btn btn-outline-light rounded-pill px-4"><i class="bi bi-arrow-left me-1"></i> <?php echo $ui_text['back_dashboard']; ?></a>
                    <a href="book_appointment.php" class="btn btn-info text-dark fw-bold rounded-pill px-4"><?php echo $ui_text['discuss_counselor']; ?> <i class="bi bi-chat-dots-fill ms-1"></i></a>
                    <a href="recommendation_results.php" class="btn btn-success text-white fw-bold rounded-pill px-4">View Recommendations <i class="bi bi-stars ms-1"></i></a>
                </div>
            </div>
        <?php endif; ?>

        <div class="p-4 bg-white rounded-3 shadow-sm mb-4 border-start border-info border-5">
            <h3 class="fw-bold text-dark m-0"><?php echo $ui_text['header']; ?></h3>
            <p class="text-muted m-0 mt-1"><?php echo $ui_text['subheader']; ?></p>
        </div>

        <form action="aptitude_test.php?lang=<?php echo $lang; ?>" method="POST">
            
            <div class="d-flex flex-column gap-4">
                
                <?php foreach ($questions as $num => $q): ?>
                <div class="question-card">
                    <h5 class="fw-bold text-dark mb-3"><?php echo $num . '. ' . $q['question']; ?></h5>
                    <?php 
                    $first = true;
                    foreach ($q['answers'] as $value => $label): 
                    ?>
                    <div class="form-check <?php echo $first ? '' : 'mt-2'; ?>">
                        <input class="form-check-input" type="radio" name="q<?php echo $num; ?>" value="<?php echo $value; ?>" id="q<?php echo $num . $value; ?>" <?php echo $first ? 'required' : ''; ?>>
                        <label class="form-check-label" role="button" for="q<?php echo $num . $value; ?>"><?php echo $label; ?></label>
                    </div>
                    <?php 
                    $first = false;
                    endforeach; 
                    ?>
                </div>
                <?php endforeach; ?>

                <button type="submit" name="submit_test" class="btn btn-primary w-100 fw-bold py-3 fs-5 rounded-3 shadow-sm my-4"><?php echo $ui_text['submit_btn']; ?> <i class="bi bi-magic ms-1"></i></button>

            </div>
        </form>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>