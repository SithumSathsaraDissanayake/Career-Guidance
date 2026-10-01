<?php
session_start();
include 'config/db.php'; // ඩේටාබේස් එක නිවැරදිව ලින්ක් කළා

$msg = "";

// 📥 Register ප්‍රොසෙස් එක (POST Request)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_user'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 🛑 මූලික වැරදි පරීක්ෂා කිරීම් (Validations)
    if ($password !== $confirm_password) {
        $msg = "<div class='alert alert-danger text-center small py-2 mb-3'>Passwords do not match!</div>";
    } else {
        try {
            // 🔍 1. ඊමේල් එක දැනටමත් පද්ධතියේ තියෙනවාදැයි බැලීම
            $check_query = "SELECT user_id FROM users WHERE email = :email LIMIT 1";
            $check_stmt = $conn->prepare($check_query);
            $check_stmt->execute(['email' => $email]);
            
            if ($check_stmt->fetch()) {
                $msg = "<div class='alert alert-danger text-center small py-2 mb-3'>Email is already registered!</div>";
            } else {
                // 🔑 2. මුරපදය Hash කිරීම (Security)
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // 📝 3. දත්ත ඩේටාබේස් එකට ඇතුළත් කිරීම (users table)
                $insert_query = "INSERT INTO users (name, email, role, password) VALUES (:name, :email, :role, :password)";
                $insert_stmt = $conn->prepare($insert_query);
                $insert_stmt->execute([
                    'name' => $name,
                    'email' => $email,
                    'role' => $role,
                    'password' => $hashed_password
                ]);

                // 🏢 4. Role එක 'company' නම්, companies table එකටත් දත්ත ඇතුළත් කිරීම
                if ($role === 'company') {
                    $user_id = $conn->lastInsertId();
                    $company_name = $name; // Register වෙද්දී දාපු name එක company_name ලෙස ගනී
                    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : null;
                    $website = isset($_POST['website']) ? trim($_POST['website']) : null;

                    $comp_query = "INSERT INTO companies (user_id, company_name, phone, website) VALUES (:user_id, :company_name, :phone, :website)";
                    $comp_stmt = $conn->prepare($comp_query);
                    $comp_stmt->execute([
                        'user_id' => $user_id,
                        'company_name' => $company_name,
                        'phone' => $phone,
                        'website' => $website
                    ]);
                }

                $msg = "<div class='alert alert-success text-center small py-2 mb-3'>Registration successful! <a href='login.php' class='text-dark fw-bold text-decoration-none'>Login Now</a></div>";
            }
        } catch (PDOException $e) {
            $msg = "<div class='alert alert-danger text-center small py-2 mb-3'>Error: " . $e->getMessage() . "</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FuturePath - Create an Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
            
            /* 🎨 Professional Background — Gradient + Image Pattern */
            background: 
                linear-gradient(135deg, rgba(15, 23, 42, 0.92) 0%, rgba(30, 58, 138, 0.88) 50%, rgba(2, 132, 199, 0.85) 100%),
                url('https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
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
            background: radial-gradient(circle, #1d4ed8, transparent);
            bottom: -150px;
            left: -150px;
            animation-delay: -4s;
        }
        
        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(30px, -30px) scale(1.1); }
        }

        /* Glassmorphism Card */
        .main-container {
            background: rgba(21, 24, 31, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            width: 100%;
            max-width: 480px;
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
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 20px;
            transition: all 0.2s;
        }
        .back-link:hover { color: #38bdf8; transform: translateX(-3px); }

        .brand-section {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 5px;
        }
        .brand-logo-icon {
            font-size: 2.2rem;
            color: #38bdf8;
            animation: pulse 3s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        .brand-text {
            font-weight: 800;
            font-size: 1.6rem;
            letter-spacing: 0.5px;
            background: linear-gradient(to right, #38bdf8, #60a5fa, #a78bfa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .instruction-text {
            color: #9ca3af;
            font-size: 0.88rem;
            margin-bottom: 25px;
        }

        /* Form Box */
        .form-box {
            background: rgba(30, 36, 48, 0.7);
            border: 1px solid rgba(56, 189, 248, 0.15);
            border-radius: 16px;
            padding: 25px;
        }

        .form-control, .form-select {
            background-color: rgba(21, 24, 31, 0.8) !important;
            border: 1px solid #374151 !important;
            color: #ffffff !important;
            padding: 11px 15px;
            border-radius: 10px;
            transition: all 0.25s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #38bdf8 !important;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.15) !important;
            background-color: rgba(21, 24, 31, 1) !important;
        }
        .form-control::placeholder { color: #6b7280; }

        .form-select option {
            background-color: #15181f;
            color: #ffffff;
        }
        
        .form-label {
            color: #cbd5e1;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .btn-submit {
            background: linear-gradient(135deg, #0284c7, #1d4ed8, #7c3aed);
            background-size: 200% 200%;
            border: none;
            color: white;
            font-weight: 700;
            padding: 13px;
            border-radius: 10px;
            transition: all 0.3s;
            letter-spacing: 0.5px;
        }
        .btn-submit:hover {
            background-position: 100% 0;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(29, 78, 216, 0.4);
        }
        
        .card-title {
            color: #38bdf8;
            font-weight: 700;
            letter-spacing: 0.5px;
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
    
    <p class="instruction-text">Create your account by filling out the details below.</p>

    <?php echo $msg; ?>

    <div class="form-box">
        <h5 class="fw-bold mb-4 text-center text-info"><i class="bi bi-person-plus-fill me-2"></i>Create an Account</h5>
        
        <form action="register.php" method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold">Full Name</label>
                <input type="text" name="name" class="form-control" placeholder="John Doe" required>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="example@mail.com" required>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold">User Role</label>
                <select name="role" class="form-select" required>
                    <option value="" disabled selected>Select your identity</option>
                    <option value="student">HNDIT Student / Other Trainee</option>
                    <option value="counselor">Career Guidance Officer</option>
                    <option value="company">Company / Other Organization</option>
                    <option value="admin">Administrator</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold">Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" name="register_user" class="btn btn-submit w-100">Register <i class="bi bi-check-circle ms-1"></i></button>
        </form>

        <div class="text-center mt-3">
            <small class="text-muted">Already have an account? <a href="login.php" class="text-info text-decoration-none fw-bold">Login here</a></small>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>