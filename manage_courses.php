<?php
session_start();
include 'config/db.php'; // ඩේටාබේස් එක ලින්ක් කළා

// පරිශීලකයා ඇඩ්මින් කෙනෙක්ද කියා බැලීම
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$msg = "";

// ➕ 1. අලුත් කෝස් එකක් එකතු කිරීම (Add Course)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_course'])) {
    $course_name = $_POST['course_name'];
    $description = $_POST['description'];
    $duration = $_POST['duration'];
    $course_url = $_POST['course_url'];

    try {
        $insert_query = "INSERT INTO courses (course_name, description, duration, course_url) 
                         VALUES (:course_name, :description, :duration, :course_url)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->execute([
            'course_name' => $course_name,
            'description' => $description,
            'duration' => $duration,
            'course_url' => $course_url
        ]);

        $msg = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                    <i class='bi bi-check-circle-fill me-2'></i> New course added successfully!
                    <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                </div>";
    } catch (PDOException $e) {
        $msg = "<div class='alert alert-danger'>Error adding course: " . $e->getMessage() . "</div>";
    }
}

// ❌ 2. කෝස් එකක් ඉවත් කිරීම (Delete Course)
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    try {
        $delete_query = "DELETE FROM courses WHERE id = :id";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->execute(['id' => $delete_id]);

        $msg = "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
                    <i class='bi bi-trash-fill me-2'></i> Course removed successfully!
                    <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                </div>";
    } catch (PDOException $e) {
        $msg = "<div class='alert alert-danger'>Error deleting course: " . $e->getMessage() . "</div>";
    }
}

// 🗂️ පද්ධතියේ තියෙන සියලුම කෝස් ලිස්ට් එක ඩේටාබේස් එකෙන් ලබාගැනීම
$all_courses = [];
try {
    $query = "SELECT * FROM courses ORDER BY course_id DESC";
    $stmt = $conn->query($query);
    $all_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage();
}

include 'includes/header.php';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
    html, body { height: 100%; margin: 0; background-color: #f8f9fa; }
    .main-wrapper { display: flex; flex-direction: column; min-height: 100vh; }
    .custom-navbar { background-color: #0f172a !important; padding: 12px 30px; }
    .custom-navbar .navbar-brand { font-weight: 700; font-size: 1.4rem; color: #ffffff !important; }
    .custom-navbar .nav-link { color: #ffffff !important; font-weight: 500; }
    .dashboard-container { display: flex; flex: 1; }
    .sidebar { width: 260px; background: #ffffff; box-shadow: 2px 0 10px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between; padding: 20px 15px; }
    .sidebar .nav-link { color: #495057; font-weight: 500; padding: 12px 15px; border-radius: 8px; margin-bottom: 5px; display: flex; align-items: center; gap: 12px; text-decoration: none; }
    .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #f1f5f9; color: #0f172a; border-left: 4px solid #0f172a; }
    .logout-section { border-top: 1px solid #eee; padding-top: 15px; }
    .main-content { flex: 1; padding: 30px; }
    .manage-card { background: white; border-radius: 15px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.02); }
</style>

<div class="main-wrapper">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg custom-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">FuturePath <span class="badge bg-danger fs-6 ms-2">Admin Panel</span></a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-4 gap-3">
                    <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Home</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div>
                <div class="text-center my-3">
                    <h6 class="mt-2 fw-bold text-dark"><?php echo htmlspecialchars($_SESSION['user_name']); ?></h6>
                    <span class="badge bg-danger-subtle text-danger border border-danger">Administrator</span>
                </div>
                <hr>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="admin_dashboard.php"><i class="bi bi-grid-1x2-fill"></i> Overview Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_appointments.php"><i class="bi bi-calendar-check-fill"></i> Appointments</a></li>
                    <li class="nav-item"><a class="nav-link active" href="manage_courses.php"><i class="bi bi-book-half"></i> Manage Courses</a></li>
                </ul>
            </div>
            <div class="logout-section">
                <a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <div class="p-4 bg-white rounded-3 shadow-sm mb-4 border-start border-primary border-5">
                <h2 class="fw-bold text-dark m-0">Manage Skill Courses 📚</h2>
                <p class="text-muted m-0 mt-1">Add new courses to the system and manage existing courses from here.</p>
            </div>

            <?php echo $msg; ?>

            <div class="row g-4">
                <!-- 📝 1. Add Course Form -->
                <div class="col-12 col-lg-4">
                    <div class="card manage-card p-4">
                        <h4 class="fw-bold text-dark mb-4"><i class="bi bi-plus-circle-fill text-primary me-2"></i>Add New Course</h4>
                        <form action="manage_courses.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Course Name</label>
                                <input type="text" class="form-control py-2" name="course_name" placeholder="e.g., Python Bootcamp" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Duration</label>
                                <input type="text" class="form-control py-2" name="duration" placeholder="e.g., 3 Months" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Course Reference URL</label>
                                <input type="url" class="form-control py-2" name="course_url" placeholder="https://example.com" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Brief Description</label>
                                <textarea class="form-control" name="description" rows="3" placeholder="Enter short course overview..." required></textarea>
                            </div>
                            <button type="submit" name="add_course" class="btn btn-primary w-100 fw-bold py-2 mt-2 shadow-sm">Add Course <i class="bi bi-save ms-1"></i></button>
                        </form>
                    </div>
                </div>

                <!-- 📋 2. Existing Courses List -->
                <div class="col-12 col-lg-8">
                    <div class="card manage-card p-4 h-100">
                        <h4 class="fw-bold text-dark mb-4"><i class="bi bi-collection-play-fill text-primary me-2"></i>Active Courses List</h4>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Course Name & Details</th>
                                        <th>Duration</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($courses)): ?>
    <?php foreach ($courses as $course): ?>
        <tr>
            <td>
                <strong><?php echo htmlspecialchars($course['course_name']); ?></strong><br>
                <small class="text-muted"><?php echo htmlspecialchars($course['description']); ?></small><br>
                
                <?php if (!empty($course['url'])): ?>
                    <a href="<?php echo htmlspecialchars($course['url']); ?>" target="_blank" class="small text-decoration-none mt-1 d-inline-block">
                        <i class="bi bi-link-45deg"></i> Visit Link
                    </a>
                <?php endif; ?>
            </td>
            
            <td>
                <?php echo !empty($course['duration']) ? htmlspecialchars($course['duration']) : 'N/A'; ?>
            </td>

            <td>
                <a href="manage_courses.php?delete=<?php echo $course['course_id']; ?>" 
                   class="btn btn-sm btn-outline-danger" 
                   onclick="return confirm('Are you sure you want to delete this course?')">
                   Delete
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="3" class="text-center">There are no courses in the system yet.</td>
    </tr>
<?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>