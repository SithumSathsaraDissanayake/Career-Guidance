<?php
session_start();
include 'config/db.php';

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_user'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    // ෆෝම් එකෙන් එන Role එක simple letters වලට හරවනවා
    $role = strtolower(trim($_POST['role'])); 
    
    // ෆෝම් එකෙන් cgo, officer, career guidance officer හෝ counselor මොකක් ආවත් එකම විදිහට සලකනවා
    if ($role === 'career guidance officer' || $role === 'officer' || $role === 'cgo' || $role === 'counselor') {
        $role = 'counselor';
    }

    try {
        // DB එකේ role එක 'counselor' හෝ 'cgo' විදිහට තිබුණත් හරියටම Select වෙන විදිහට Query එක හදා ඇත
        $query = "SELECT * FROM users WHERE email = :email AND (role = :role OR role = 'counselor' OR role = 'cgo') LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->execute(['email' => $email, 'role' => $role]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            // 🔀 Role එක අනුව අදාළ Dashboard එකට Redirect කිරීම
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
                exit();
            } elseif ($user['role'] === 'student') {
                header("Location: student_dashboard.php");
                exit();
            } elseif ($user['role'] === 'counselor' || $user['role'] === 'cgo' || $user['role'] === 'career guidance officer') {
                header("Location: counseling.php"); 
                exit();
            } elseif ($user['role'] === 'company') {
                header("Location: company_dashboard.php");
                exit();
            } else {
                header("Location: index.php");
                exit();
            }
        } else {
            $msg = "<div class='alert alert-danger text-center small py-2 mb-3'>Invalid Email, Password or Role selection!</div>";
        }
    } catch (PDOException $e) {
        $msg = "<div class='alert alert-danger text-center small py-2 mb-3'>Error: " . $e->getMessage() . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FuturePath - Choose Login Role</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body {
    /* 🎨 Professional Background — Gradient + Image */
    background: 
        linear-gradient(135deg, rgba(15, 23, 42, 0.92) 0%, rgba(30, 58, 138, 0.88) 50%, rgba(2, 132, 199, 0.85) 100%),
        url('https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=1920&q=80');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 20px;
    overflow-x: hidden;
    position: relative;
        }
        /* Animated gradient orbs */
body::before,
body::after {
    content: '';
    position: fixed;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.5;
    z-index: 0;
    animation: float 8s ease-in-out infinite;
    pointer-events: none;
}

body::before {
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, #0ea5e9, transparent);
    top: -100px;
    right: -100px;
}

body::after {
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, #7c3aed, transparent);
    bottom: -150px;
    left: -150px;
    animation-delay: -4s;
}

@keyframes float {
    0%, 100% { transform: translate(0, 0) scale(1); }
    50% { transform: translate(30px, -30px) scale(1.1); }
}

        .main-container {
    background: rgba(21, 24, 31, 0.85);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    width: 100%;
    max-width: 550px;
    border-radius: 24px;
    padding: 35px 30px;
    box-shadow: 
        0 25px 50px rgba(0, 0, 0, 0.5),
        0 0 0 1px rgba(255, 255, 255, 0.08),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
    color: #ffffff;
    position: relative;
    z-index: 10;
    animation: slideUp 0.6s ease-out;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

        .back-link {
            color: #9ca3af;
            text-decoration: none;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 25px;
            transition: color 0.2s;
        }
        .back-link:hover {
            color: #ffffff;
        }

        .brand-section {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }
        .brand-logo-icon {
            font-size: 2.2rem;
            color: #38bdf8;
        }
        .brand-text {
            font-weight: 800;
            font-size: 1.6rem;
            letter-spacing: 0.5px;
            background: linear-gradient(to right, #38bdf8, #60a5fa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .instruction-text {
            color: #9ca3af;
            font-size: 0.88rem;
            margin-bottom: 30px;
        }

        .role-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }

        .role-card {
    background: rgba(30, 36, 48, 0.7);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 2px solid rgba(56, 189, 248, 0.15);
    border-radius: 16px;
    padding: 20px 10px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 150px;
}

.role-card:hover {
    background: rgba(36, 44, 61, 0.9);
    border-color: rgba(56, 189, 248, 0.4);
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(2, 132, 199, 0.3);
}

        .icon-circle {
            width: 65px;
            height: 65px;
            background-color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            font-size: 1.8rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        
        .student-icon { color: #1d4ed8; }
        .counselor-icon { color: #059669; }
        .company-icon { color: #ea580c; }
        .admin-icon { color: #7c3aed; }

        .role-title {
            font-size: 0.88rem;
            font-weight: 600;
            color: #f3f4f6;
            line-height: 1.4;
            margin: 0;
        }

        .role-card.selected {
            border-color: #38bdf8;
            background-color: #243249;
            box-shadow: 0 0 15px rgba(56, 189, 248, 0.2);
        }

        .login-box {
            background-color: #1e2430;
            border-radius: 14px;
            padding: 20px;
            display: none;
            animation: fadeIn 0.4s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-control {
            background-color: #15181f !important;
            border: 1px solid #374151 !important;
            color: #ffffff !important;
            padding: 10px 15px;
            border-radius: 8px;
        }
        .form-control:focus {
            border-color: #38bdf8 !important;
            box-shadow: 0 0 8px rgba(56, 189, 248, 0.2) !important;
        }

        .btn-submit {
            background: linear-gradient(to right, #0284c7, #1d4ed8);
            border: none;
            color: white;
            font-weight: 600;
            padding: 12px;
            border-radius: 8px;
        }
        .btn-submit:hover {
            opacity: 0.9;
            color: white;
        }
    </style>
</head>
<body>

<div class="main-container">
    <a href="index.php" class="back-link">
        <i class="bi bi-chevron-left"></i> Homepage
    </a>

    <div class="brand-section">
        <i class="bi bi-compass-fill brand-logo-icon"></i>
        <span class="brand-text">FuturePath</span>
    </div>
    
    <p class="instruction-text">Click on the Card below to select the user type for login.</p>

    <?php echo $msg; ?>

    <div class="role-grid">
        <div class="role-card" id="studentCard" onclick="selectRole('student')">
            <div class="icon-circle">
                <i class="bi bi-mortarboard-fill student-icon"></i>
            </div>
            <p class="role-title">HNDIT Student /<br>Other Trainees</p>
        </div>

        <div class="role-card" id="counselorCard" onclick="selectRole('counselor')">
            <div class="icon-circle">
                <i class="bi bi-person-workspace counselor-icon"></i>
            </div>
            <p class="role-title">Career Guidance<br>Officer</p>
        </div>

        <div class="role-card" id="companyCard" onclick="selectRole('company')">
            <div class="icon-circle">
                <i class="bi bi-building-fill text-warning company-icon"></i>
            </div>
            <p class="role-title">Company / Other<br>Organization</p>
        </div>

        <div class="role-card" id="adminCard" onclick="selectRole('admin')">
            <div class="icon-circle">
                <i class="bi bi-shield-lock-fill admin-icon"></i>
            </div>
            <p class="role-title">Administrator</p>
        </div>
    </div>

    <div class="login-box" id="loginBox">
        <h5 class="fw-bold mb-3 text-center text-info" id="formTitle">Login</h5>
        <form action="login.php" method="POST">
            <input type="hidden" name="role" id="userRoleInput" value="">

            <div class="mb-3">
                <label class="form-label text-muted small fw-bold">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="enter your email" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted small fw-bold">Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" name="login_user" class="btn btn-submit w-100 mt-2">Sign In <i class="bi bi-box-arrow-in-right ms-1"></i></button>
        </form>
    </div>
</div>

<script>
function selectRole(role) {
    document.getElementById('userRoleInput').value = role;
    
    const loginBox = document.getElementById('loginBox');
    const formTitle = document.getElementById('formTitle');
    
    document.getElementById('studentCard').classList.remove('selected');
    document.getElementById('counselorCard').classList.remove('selected');
    document.getElementById('companyCard').classList.remove('selected');
    document.getElementById('adminCard').classList.remove('selected');

    if (role === 'student') {
        document.getElementById('studentCard').classList.add('selected');
        formTitle.innerHTML = "<i class='bi bi-mortarboard-fill me-2'></i>Student Portal Login";
    } else if (role === 'counselor') {
        document.getElementById('counselorCard').classList.add('selected');
        formTitle.innerHTML = "<i class='bi bi-person-workspace me-2'></i>Officer Portal Login";
    } else if (role === 'company') {
        document.getElementById('companyCard').classList.add('selected');
        formTitle.innerHTML = "<i class='bi bi-building-fill me-2'></i>Company Portal Login";
    } else if (role === 'admin') {
        document.getElementById('adminCard').classList.add('selected');
        formTitle.innerHTML = "<i class='bi bi-shield-lock-fill me-2'></i>FUTUREPATH Admin Login";
    }

    loginBox.style.display = 'block';
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>