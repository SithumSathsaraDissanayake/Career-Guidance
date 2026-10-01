<?php
session_start();
include 'config/db.php'; // ඩේටාබේස් එක නිවැරදිව සම්බන්ධ කළා

// 🔐 පරිශීලකයා ශිෂ්‍යයෙක්ද කියා බැලීම
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$show_results = false;
$suggested_career = "";
$career_description = "";

// 🌐 භාෂාව තෝරාගැනීම (Default: English)
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'en';
if (!in_array($lang, ['en', 'si', 'ta'])) {
    $lang = 'en';
}

// 📊 ශිෂ්‍යයා උත්තර ටික Submit කළ පසු ක්‍රියාත්මක වන කොටස
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_test'])) {
    $scores = [
        'it' => 0,
        'engineering' => 0,
        'management' => 0,
        'design' => 0
    ];

    // ප්‍රශ්න 10යේ පිළිතුරු එකතු කර ලකුණු ගණනය කිරීම
    for ($i = 1; $i <= 10; $i++) {
        if (isset($_POST["q$i"])) {
            $answer = $_POST["q$i"];
            if (array_key_exists($answer, $scores)) {
                $scores[$answer]++;
            }
        }
    }

    // වැඩිම ලකුණු ලැබී ඇති ක්ෂේත්‍රය සෙවීම
    arsort($scores);
    $top_career = key($scores);

    // 💡 ප්‍රතිඵලය අනුව Career Path එක තීරණය කිරීම (භාෂාව අනුව)
    if ($lang == 'si') {
        if ($top_career == 'it') {
            $suggested_career = "Information Technology & Software Engineering 💻";
            $career_description = "ඔබට තර්කානුකූලව සිතීමේ සහ ගැටලු විසඳීමේ (Problem Solving) ඉහළ හැකියාවක් ඇත. මෘදුකාංග ඉංජිනේරු විද්‍යාව (Software Engineering), දත්ත විශ්ලේෂණය (Data Science), හෝ වෙබ් සංවර්ධනය (Web Development) වැනි ක්ෂේත්‍ර ඔබට වඩාත්ම ගැළපේ.";
        } elseif ($top_career == 'engineering') {
            $suggested_career = "Engineering & Technical Fields 🛠️";
            $career_description = "ඔබ උපකරණ ක්‍රියාකාරීත්වය, තාක්ෂණික නිර්මාණ සහ භෞතික පද්ධති පිළිබඳව උනන්දුවක් දක්වයි. සිවිල්, යාන්ත්‍රික, හෝ විද්‍යුත් ඉංජිනේරු ක්ෂේත්‍ර මෙන්ම රොබොටික්ස් වැනි නවීන තාක්ෂණික අංශ ඔබට ගැළපේ.";
        } elseif ($top_career == 'management') {
            $suggested_career = "Business Management & Entrepreneurship 📊";
            $career_description = "ඔබ සන්නිවේදනයට, නායකත්වයට සහ සැලසුම් සකස් කිරීමට දක්ෂයෙකි. ව්‍යාපාර කළමනාකරණය (Business Administration), මානව සම්පත් (HR), අලෙවිකරණය (Marketing) හෝ තමන්ගේම ව්‍යාපාරයක් ඇරඹීම (Entrepreneurship) ඔබට සුදුසුය.";
        } else {
            $suggested_career = "Creative Arts & Digital Design 🎨";
            $career_description = "ඔබ ඉතා නිර්මාණශීලී, අලුත් විදිහට හිතන කෙනෙකි. UI/UX Design, Graphic Design, Digital Marketing, හෝ බහුමාධ්‍ය (Multimedia) ක්ෂේත්‍ර ඔස්සේ ඔබට ඉතා ඉහළ ගමනක් යා හැක.";
        }
    } elseif ($lang == 'ta') {
        if ($top_career == 'it') {
            $suggested_career = "Information Technology & Software Engineering 💻";
            $career_description = "நீங்கள் தர்க்கரீதியாக சிந்திக்கவும் பிரச்சினைகளைத் தீர்க்கவும் அதிக திறன் கொண்டவர். மென்பொருள் பொறியியல் (Software Engineering), தரவு அறிவியல் (Data Science), அல்லது வலை மேம்பாடு (Web Development) போன்ற துறைகள் உங்களுக்கு மிகவும் பொருத்தமானவை.";
        } elseif ($top_career == 'engineering') {
            $suggested_career = "Engineering & Technical Fields 🛠️";
            $career_description = "நீங்கள் சாதனங்களின் செயல்பாடு, தொழில்நுட்ப வடிவமைப்புகள் மற்றும் இயற்பியல் அமைப்புகளில் ஆர்வம் காட்டுகிறீர்கள். சிவில், இயந்திர, அல்லது மின் பொறியியல் துறைகள் மற்றும் ரோபோட்டிக்ஸ் போன்ற நவீன தொழில்நுட்பப் பிரிவுகள் உங்களுக்கு பொருத்தமானவை.";
        } elseif ($top_career == 'management') {
            $suggested_career = "Business Management & Entrepreneurship 📊";
            $career_description = "நீங்கள் தொடர்பு, தலைமைத்துவம் மற்றும் திட்டமிடலில் திறமையானவர். வணிக மேலாண்மை (Business Administration), மனித வளம் (HR), சந்தைப்படுத்தல் (Marketing) அல்லது சொந்த வணிகத்தைத் தொடங்குதல் (Entrepreneurship) உங்களுக்கு பொருத்தமானது.";
        } else {
            $suggested_career = "Creative Arts & Digital Design 🎨";
            $career_description = "நீங்கள் மிகவும் படைப்பாற்றல் மிக்கவர், புதிய வழியில் சிந்திக்கும் நபர். UI/UX Design, Graphic Design, Digital Marketing, அல்லது Multimedia துறைகள் மூலம் நீங்கள் மிக உயர்ந்த நிலைக்குச் செல்ல முடியும்.";
        }
    } else {
        if ($top_career == 'it') {
            $suggested_career = "Information Technology & Software Engineering 💻";
            $career_description = "You have a high ability to think logically and solve problems. Fields like Software Engineering, Data Science, or Web Development are best suited for you.";
        } elseif ($top_career == 'engineering') {
            $suggested_career = "Engineering & Technical Fields 🛠️";
            $career_description = "You are interested in how devices work, technical designs, and physical systems. Civil, Mechanical, or Electrical Engineering as well as modern technical fields like Robotics are suitable for you.";
        } elseif ($top_career == 'management') {
            $suggested_career = "Business Management & Entrepreneurship 📊";
            $career_description = "You are skilled in communication, leadership, and planning. Business Administration, Human Resources (HR), Marketing, or starting your own business (Entrepreneurship) are suitable for you.";
        } else {
            $suggested_career = "Creative Arts & Digital Design 🎨";
            $career_description = "You are very creative and think in new ways. You can go very far through fields like UI/UX Design, Graphic Design, Digital Marketing, or Multimedia.";
        }
    }

        
        $show_results = true;

// 💾 SAVE TO DATABASE
try {
    $categoryToCareerId = [
        'it' => 1,
        'engineering' => 8,
        'management' => 11,
        'design' => 17
    ];
    
    $deleteStmt = $conn->prepare("DELETE FROM aptitude_results WHERE user_id = :uid");
    $deleteStmt->execute(['uid' => $_SESSION['user_id']]);
    
    $insertStmt = $conn->prepare(
        "INSERT INTO aptitude_results (user_id, career_id, score) 
         VALUES (:uid, :cid, :score)"
    );
    
    foreach ($scores as $category => $score) {
        if (isset($categoryToCareerId[$category])) {
            $normalizedScore = min(100, $score * 10);
            $insertStmt->execute([
                'uid' => $_SESSION['user_id'],
                'cid' => $categoryToCareerId[$category],
                'score' => $normalizedScore
            ]);
        }
    }
    
    // Skills save
    $skillMap = [
        'it' => ['Programming', 'Web Development', 'Database Management'],
        'engineering' => ['AutoCAD', 'Technical Drawing', 'Structural Analysis'],
        'management' => ['Accounting', 'Financial Analysis', 'Project Management'],
        'design' => ['Graphic Design', 'UI/UX Design', 'Digital Illustration']
    ];
    
    $delSkills = $conn->prepare("DELETE FROM user_skills WHERE user_id = :uid");
    $delSkills->execute(['uid' => $_SESSION['user_id']]);
    
    $insSkill = $conn->prepare(
        "INSERT INTO user_skills (user_id, skill_name) VALUES (:uid, :skill)"
    );
    
    arsort($scores);
    $topCategories = array_slice(array_keys($scores), 0, 2);
    
    foreach ($topCategories as $cat) {
        if (isset($skillMap[$cat])) {
            foreach ($skillMap[$cat] as $skill) {
                $insSkill->execute([
                    'uid' => $_SESSION['user_id'],
                    'skill' => $skill
                ]);
            }
        }
    }
    
} catch (PDOException $e) {
    error_log("Aptitude save error: " . $e->getMessage());
}

// ... save code ...

    // 💾 SAVE TO DATABASE - මෙතන ඉඳන් අලුත් code එක
    try {
        $categoryToCareerId = [
    'it' => 1,           // Software Engineer
    'engineering' => 8,  // Civil Engineer
    'management' => 11,  // Business Analyst
    'design' => 17       // Graphic Designer
];
        
        // පරණ aptitude results delete කරන්න (re-take කරන විට)
        $deleteStmt = $conn->prepare("DELETE FROM aptitude_results WHERE user_id = :uid");
        $deleteStmt->execute(['uid' => $_SESSION['user_id']]);
        
        // අලුත් results insert කරන්න
        $insertStmt = $conn->prepare(
            "INSERT INTO aptitude_results (user_id, career_id, score) 
             VALUES (:uid, :cid, :score)"
        );
        
        foreach ($scores as $category => $score) {
            if (isset($categoryToCareerId[$category])) {
                // Test එකේ ප්‍රශ්න 10යි → score 0-10 අතර
                // එය 0-100 දක්වා normalize කරන්න (× 10)
                $normalizedScore = min(100, $score * 10);
                
                $insertStmt->execute([
                    'uid' => $_SESSION['user_id'],
                    'cid' => $categoryToCareerId[$category],
                    'score' => $normalizedScore
                ]);
            }
        }
        
        // 🎯 AUTO-SAVE user skills from test categories
       $skillMap = [
    'it' => ['java', 'php', 'sql', 'problem solving'],
    'engineering' => ['python', 'sql', 'excel', 'statistics'],
    'management' => ['networking', 'linux', 'security', 'python'],
    'design' => ['html', 'css', 'javascript', 'creativity']

        ];
        
        // පරණ skills delete කරන්න
        $delSkills = $conn->prepare("DELETE FROM user_skills WHERE user_id = :uid");
        $delSkills->execute(['uid' => $_SESSION['user_id']]);
        
        // අලුත් skills insert කරන්න (top 2 categories වලින්)
        $insSkill = $conn->prepare(
            "INSERT INTO user_skills (user_id, skill_name) VALUES (:uid, :skill)"
        );
        
        arsort($scores); // වැඩිම scores ඉස්සරහට
        $topCategories = array_slice(array_keys($scores), 0, 2);
        
        foreach ($topCategories as $cat) {
            if (isset($skillMap[$cat])) {
                foreach ($skillMap[$cat] as $skill) {
                    $insSkill->execute([
                        'uid' => $_SESSION['user_id'],
                        'skill' => $skill
                    ]);
                }
            }
        }
        
        // 🎓 Academic data save කරන්න (default "IT", 3.0 GPA)
        // Profile page එකක් හදලා පසුව update කරන්න පුළුවන්
        $checkAcademic = $conn->prepare(
            "SELECT id FROM student_academic WHERE user_id = :uid"
        );
        $checkAcademic->execute(['uid' => $_SESSION['user_id']]);
        
        if (!$checkAcademic->fetch()) {
            $insAcademic = $conn->prepare(
                "INSERT INTO student_academic (user_id, stream, gpa) 
                 VALUES (:uid, 'IT', 3.0)"
            );
            $insAcademic->execute(['uid' => $_SESSION['user_id']]);
        }
        
    } catch (PDOException $e) {
        // Error log කරන්න, ඒත් user ට පෙන්නන්න එපා
        error_log("Aptitude save error: " . $e->getMessage());
    }
    // 💾 SAVE END - අලුත් code එක මෙතනින් ඉවරයි
}

// 🌐 භාෂාවට අනුව ප්‍රශ්න සහ පිළිතුරු
$questions = [];

if ($lang == 'si') {
    $questions = [
        1 => [
            'question' => 'ඔබ වඩාත්ම ප්‍රිය කරන්නේ කුමන ආකාරයේ ගැටලු විසඳීමටද?',
            'answers' => [
                'it' => 'ගණිතමය හෝ පරිගණක කේතයන්හි (Code) ඇති ගැටලු විසඳීම',
                'management' => 'මිනිසුන් අතර ඇතිවන ගැටලු සහ ව්‍යාපාරික ගැටලු විසඳීම',
                'design' => 'නිර්මාණශීලී අදහස් හෝ මෝස්තර පිළිබඳ ගැටලු විසඳීම'
            ]
        ],
        2 => [
            'question' => 'ඔබ නිදහස් වේලාවකදී වැඩිපුරම කිරීමට කැමති කුමක්ද?',
            'answers' => [
                'engineering' => 'විද්‍යුත් උපකරණ හෝ යාන්ත්‍රික මෙවලම් ගලවා සකස් කිරීම',
                'it' => 'අලුත් ඇප්ස් (Apps), වෙබ් අඩවි හෝ තාක්ෂණික තොරතුරු සෙවීම',
                'design' => 'ඡායාරූපකරණය, චිත්‍ර ඇඳීම හෝ වීඩියෝ සංස්කරණය කිරීම'
            ]
        ],
        3 => [
            'question' => 'සමූහ ව්‍යාපෘතියකදී (Group Project) ඔබ නිතරම භාරගන්නා භූමිකාව කුමක්ද?',
            'answers' => [
                'management' => 'කණ්ඩායම මෙහෙයවීම සහ වැඩ කොටස් බෙදා දීම (Leader)',
                'it' => 'ප්‍රධාන තාක්ෂණික වැඩ කොටස හෝ ගණනය කිරීම් සිදු කිරීම',
                'design' => 'ප්‍රොජෙක්ට් එකේ Presentation එක ලස්සනට නිර්මාණය කිරීම'
            ]
        ],
        4 => [
            'question' => 'අලුත් වෙබ් අඩවියක් දුටු විට ඔබේ අවධානය මුලින්ම යොමු වන්නේ කුමකටද?',
            'answers' => [
                'design' => 'එහි ඇති වර්ණ, පෙනුම සහ ලස්සන (Layout & Visuals)',
                'it' => 'එහි වේගය සහ එය ක්‍රියාකරන ආකාරය (Features & Speed)',
                'management' => 'එම වෙබ් අඩවියෙන් කරන ව්‍යාපාරය හෝ සේවාව කුමක්ද යන්න'
            ]
        ],
        5 => [
            'question' => 'පරිගණක භාෂා (Java, Python, PHP) ඉගෙන ගැනීමට ඔබ තුළ උනන්දුවක් පවතීද?',
            'answers' => [
                'it' => 'ඔව්, ඉතාමත් කැමතියි',
                'management' => 'නැත, මම වඩා කැමති කළමනාකරණ අංශයටයි'
            ]
        ],
        6 => [
            'question' => 'යන්ත්‍ර සූත්‍ර හෝ ගොඩනැගිලි සැලසුම් (Blueprints) පිළිබඳව හැදෑරීමට ඔබ කැමතිද?',
            'answers' => [
                'engineering' => 'ඔව්, ඒ පිළිබඳ තාක්ෂණික දැනුමට මම ප්‍රිය කරමි',
                'design' => 'නැත, මම වඩා කැමති කලාත්මක නිර්මාණ කිරීමටයි'
            ]
        ],
        7 => [
            'question' => 'සමාජ මාධ්‍ය (Social Media) සඳහා ලෝගෝ (Logos) සහ පෝස්ටර් නිර්මාණයට ඔබ දක්ෂද?',
            'answers' => [
                'design' => 'ඔව්, මට ඒ සඳහා හොඳ නිර්මාණශීලී හැකියාවක් ඇත',
                'it' => 'නැත, මම කැමති එහි පසුබිම් කේතකරණයටයි (Coding)'
            ]
        ],
        8 => [
            'question' => 'නව තාක්ෂණික උපකරණයක් වෙළඳපොලට ආ විට ඔබ මුලින්ම කරන්නේ කුමක්ද?',
            'answers' => [
                'it' => 'එහි Operating System එක සහ Specs පිළිබඳව සෙවීම',
                'management' => 'එහි මිල, වෙළඳපල ඉල්ලුම සහ ලාභය පිළිබඳව සිතීම'
            ]
        ],
        9 => [
            'question' => 'අනාගතයේදී ඔබ වීමට වඩාත්ම කැමති කවුරුන් වීමටද?',
            'answers' => [
                'management' => 'ප්‍රධාන කළමනාකාරවරයෙක් (Manager) හෝ ව්‍යාපාරිකයෙක්',
                'it' => 'ප්‍රධාන මෘදුකාංග ඉංජිනේරුවරයෙක් (Tech Lead/Engineer)'
            ]
        ],
        10 => [
            'question' => 'ඔබ වැඩක් කිරීමට වඩාත්ම කැමති කුමනාකාර පරිසරයකද?',
            'answers' => [
                'management' => 'මිනිසුන් සමඟ නිතරම අදහස් හුවමාරු කරගත හැකි කාර්යාලයක',
                'engineering' => 'වැඩබිමක (Field) හෝ පර්යේෂණාගාරයක'
            ]
        ]
    ];
} elseif ($lang == 'ta') {
    $questions = [
        1 => [
            'question' => 'எந்த வகையான பிரச்சினைகளைத் தீர்ப்பதை நீங்கள் மிகவும் விரும்புகிறீர்கள்?',
            'answers' => [
                'it' => 'கணித அல்லது கணினி குறியீட்டில் (Code) உள்ள பிரச்சினைகளைத் தீர்ப்பது',
                'management' => 'மக்களிடையே உள்ள பிரச்சினைகள் மற்றும் வணிகப் பிரச்சினைகளைத் தீர்ப்பது',
                'design' => 'படைப்பாற்றல் யோசனைகள் அல்லது வடிவமைப்புகள் பற்றிய பிரச்சினைகளைத் தீர்ப்பது'
            ]
        ],
        2 => [
            'question' => 'உங்கள் ஓய்வு நேரத்தில் நீங்கள் மிகவும் விரும்பிச் செய்வது எது?',
            'answers' => [
                'engineering' => 'மின்னணு சாதனங்கள் அல்லது இயந்திர கருவிகளை பிரித்து சரிசெய்வது',
                'it' => 'புதிய செயலிகள் (Apps), இணையதளங்கள் அல்லது தொழில்நுட்ப தகவல்களைத் தேடுவது',
                'design' => 'புகைப்படம் எடுத்தல், ஓவியம் வரைதல் அல்லது வீடியோ எடிட்டிங் செய்வது'
            ]
        ],
        3 => [
            'question' => 'குழு திட்டத்தில் (Group Project) நீங்கள் அடிக்கடி எடுக்கும் பங்கு என்ன?',
            'answers' => [
                'management' => 'குழுவை வழிநடத்துதல் மற்றும் வேலைகளைப் பங்கிட்டுக் கொடுத்தல் (Leader)',
                'it' => 'முக்கிய தொழில்நுட்ப வேலைப்பகுதி அல்லது கணக்கீடுகளைச் செய்வது',
                'design' => 'திட்டத்தின் Presentation-ஐ அழகாக வடிவமைப்பது'
            ]
        ],
        4 => [
            'question' => 'புதிய இணையதளத்தைப் பார்க்கும்போது உங்கள் கவனம் முதலில் எதில் செலுத்தப்படுகிறது?',
            'answers' => [
                'design' => 'அதன் நிறங்கள், தோற்றம் மற்றும் அழகு (Layout & Visuals)',
                'it' => 'அதன் வேகம் மற்றும் அது செயல்படும் விதம் (Features & Speed)',
                'management' => 'அந்த இணையதளம் மூலம் செய்யப்படும் வணிகம் அல்லது சேவை என்ன என்பது'
            ]
        ],
        5 => [
            'question' => 'கணினி மொழிகளை (Java, Python, PHP) கற்க உங்களுக்கு ஆர்வம் உள்ளதா?',
            'answers' => [
                'it' => 'ஆம், மிகவும் விரும்புகிறேன்',
                'management' => 'இல்லை, நான் மேலாண்மைத் துறையை விரும்புகிறேன்'
            ]
        ],
        6 => [
            'question' => 'இயந்திர வரைபடங்கள் அல்லது கட்டிட வரைபடங்களை (Blueprints) படிப்பதில் நீங்கள் விருப்பப்படுகிறீர்களா?',
            'answers' => [
                'engineering' => 'ஆம், அது பற்றிய தொழில்நுட்ப அறிவை நான் விரும்புகிறேன்',
                'design' => 'இல்லை, நான் கலைநயமிக்க படைப்புகளை உருவாக்குவதை விரும்புகிறேன்'
            ]
        ],
        7 => [
            'question' => 'சமூக ஊடகங்களுக்கு (Social Media) லோகோக்கள் (Logos) மற்றும் சுவரொட்டிகளை வடிவமைக்க நீங்கள் திறமையானவரா?',
            'answers' => [
                'design' => 'ஆம், அதற்கு எனக்கு நல்ல படைப்பாற்றல் திறன் உள்ளது',
                'it' => 'இல்லை, நான் அதன் பின்னணி குறியீட்டை (Coding) விரும்புகிறேன்'
            ]
        ],
        8 => [
            'question' => 'புதிய தொழில்நுட்ப சாதனம் சந்தைக்கு வரும்போது நீங்கள் முதலில் செய்வது என்ன?',
            'answers' => [
                'it' => 'அதன் Operating System மற்றும் Specs பற்றி தேடுவது',
                'management' => 'அதன் விலை, சந்தை தேவை மற்றும் லாபம் பற்றி சிந்திப்பது'
            ]
        ],
        9 => [
            'question' => 'எதிர்காலத்தில் நீங்கள் யாராக விரும்புகிறீர்கள்?',
            'answers' => [
                'management' => 'தலைமை மேலாளர் (Manager) அல்லது தொழில்முனைவோர்',
                'it' => 'தலைமை மென்பொருள் பொறியாளர் (Tech Lead/Engineer)'
            ]
        ],
        10 => [
            'question' => 'நீங்கள் வேலை செய்ய மிகவும் விரும்பும் சூழல் எது?',
            'answers' => [
                'management' => 'மக்களுடன் அடிக்கடி கருத்துக்களைப் பரிமாறிக்கொள்ளக்கூடிய அலுவலகத்தில்',
                'engineering' => 'களத்தில் (Field) அல்லது ஆய்வகத்தில்'
            ]
        ]
    ];
} else {
    // English (Default)
    $questions = [
        1 => [
            'question' => 'What type of problems do you most enjoy solving?',
            'answers' => [
                'it' => 'Solving problems in mathematics or computer code',
                'management' => 'Solving problems between people and business problems',
                'design' => 'Solving problems related to creative ideas or designs'
            ]
        ],
        2 => [
            'question' => 'What do you most enjoy doing in your free time?',
            'answers' => [
                'engineering' => 'Taking apart and fixing electronic devices or mechanical tools',
                'it' => 'Exploring new apps, websites, or technical information',
                'design' => 'Photography, drawing, or video editing'
            ]
        ],
        3 => [
            'question' => 'What role do you usually take in a group project?',
            'answers' => [
                'management' => 'Leading the team and distributing tasks (Leader)',
                'it' => 'Doing the main technical part or calculations',
                'design' => 'Designing the project presentation beautifully'
            ]
        ],
        4 => [
            'question' => 'When you see a new website, what catches your attention first?',
            'answers' => [
                'design' => 'Its colors, appearance, and visuals (Layout & Visuals)',
                'it' => 'Its speed and how it works (Features & Speed)',
                'management' => 'What business or service the website offers'
            ]
        ],
        5 => [
            'question' => 'Are you interested in learning programming languages (Java, Python, PHP)?',
            'answers' => [
                'it' => 'Yes, I really enjoy it',
                'management' => 'No, I prefer the management field'
            ]
        ],
        6 => [
            'question' => 'Do you like studying blueprints or building plans?',
            'answers' => [
                'engineering' => 'Yes, I enjoy technical knowledge about it',
                'design' => 'No, I prefer creating artistic designs'
            ]
        ],
        7 => [
            'question' => 'Are you good at designing logos and posters for social media?',
            'answers' => [
                'design' => 'Yes, I have good creative skills for it',
                'it' => 'No, I prefer the background coding'
            ]
        ],
        8 => [
            'question' => 'When a new tech device comes to the market, what do you do first?',
            'answers' => [
                'it' => 'Search about its Operating System and Specs',
                'management' => 'Think about its price, market demand, and profit'
            ]
        ],
        9 => [
            'question' => 'What do you most want to become in the future?',
            'answers' => [
                'management' => 'A top Manager or Entrepreneur',
                'it' => 'A lead Software Engineer (Tech Lead/Engineer)'
            ]
        ],
        10 => [
            'question' => 'What kind of environment do you most prefer to work in?',
            'answers' => [
                'management' => 'An office where you can constantly exchange ideas with people',
                'engineering' => 'A field or a laboratory'
            ]
        ]
    ];
}

// 🌐 භාෂාවට අනුව UI පෙළ
$ui_text = [];

if ($lang == 'si') {
    $ui_text = [
        'title' => 'FuturePath - වෘත්තීය කුසලතා පරීක්ෂණය',
        'nav_dashboard' => 'ප්‍රධාන මෙනුව',
        'nav_aptitude' => 'කුසලතා පරීක්ෂණය',
        'header' => 'වෘත්තීය කුසලතා පරීක්ෂණය 📝',
        'subheader' => 'පහත ප්‍රශ්න වලට ඔබට වඩාත්ම ගැළපෙන පිළිතුර තෝරන්න. සියලුම ප්‍රශ්න වලට පිළිතුරු සැපයීම අනිවාර්ය වේ.',
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
        'subheader' => 'கீழே உள்ள கேள்விகளுக்கு உங்களுக்கு மிகவும் பொருத்தமான பதிலைத் தேர்ந்தெடுக்கவும். அனைத்து கேள்விகளுக்கும் பதிலளிப்பது கட்டாயமாகும்.',
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
        'subheader' => 'Choose the answer that best suits you for the following questions. Answering all questions is mandatory.',
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
                <div class="d-flex gap-3">
                    <a href="student_dashboard.php" class="btn btn-outline-light rounded-pill px-4"><i class="bi bi-arrow-left me-1"></i> <?php echo $ui_text['back_dashboard']; ?></a>
                    <a href="book_appointment.php" class="btn btn-info text-dark fw-bold rounded-pill px-4"><?php echo $ui_text['discuss_counselor']; ?> <i class="bi bi-chat-dots-fill ms-1"></i></a>
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