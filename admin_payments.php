<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'config/db.php';

// Admin Auth Check
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Payment Status Update කිරීමේ කොටස
if (isset($_GET['action']) && isset($_GET['id'])) {
    $payment_id = $_GET['id'];
    $status = ($_GET['action'] == 'approve') ? 'Approved' : 'Rejected';
    
    try {
        $update_stmt = $conn->prepare("UPDATE payments SET status = :status WHERE payment_id = :id");
        $update_stmt->execute(['status' => $status, 'id' => $payment_id]);
        header("Location: admin_payments.php");
        exit();
    } catch (PDOException $e) {
        $error_msg = $e->getMessage();
    }
}

// Payments Data ලබාගැනීම
$payments = [];
try {
    $query = "SELECT p.*, u.name as student_name, c.course_name 
              FROM payments p 
              LEFT JOIN users u ON p.user_id = u.user_id 
              LEFT JOIN courses c ON p.course_id = c.course_id 
              ORDER BY p.payment_id DESC";
    $stmt = $conn->query($query);
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Payments Table එක නැතිනම් හෝ error එකක් ආවොත්
    $error_msg = $e->getMessage();
}

include 'includes/header.php';
?>

<div class="container my-4">
    <h3 class="fw-bold mb-4">Manage Course Payments</h3>

    <?php if (isset($error_msg)): ?>
        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($payments)): ?>
                        <?php foreach ($payments as $pay): ?>
                            <tr>
                                <td>#<?php echo $pay['payment_id']; ?></td>
                                <td><?php echo htmlspecialchars($pay['student_name'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($pay['course_name'] ?? 'N/A'); ?></td>
                                <td>LKR <?php echo number_format($pay['amount'] ?? 0, 2); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo ($pay['status'] == 'Approved') ? 'success' : (($pay['status'] == 'Rejected') ? 'danger' : 'warning'); ?>">
                                        <?php echo $pay['status'] ?? 'Pending'; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="admin_payments.php?action=approve&id=<?php echo $pay['payment_id']; ?>" class="btn btn-sm btn-success">Approve</a>
                                    <a href="admin_payments.php?action=reject&id=<?php echo $pay['payment_id']; ?>" class="btn btn-sm btn-danger">Reject</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-3">No payment records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>