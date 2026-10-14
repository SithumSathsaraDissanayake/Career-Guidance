<?php
session_start();
include 'config/db.php';
require('fpdf/fpdf.php'); // FPDF Library Link කිරීම

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    exit("Access Denied");
}

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'FuturePath - System Analytics & Summary Report', 0, 1, 'C');
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 5, 'Generated Date: ' . date('Y-m-d H:i:s'), 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

// Table Header
$pdf->SetFillColor(15, 23, 42);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(60, 10, 'Metric Description', 1, 0, 'L', true);
$pdf->Cell(60, 10, 'Value', 1, 1, 'C', true);

// Table Body
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 11);

// ✅ Safe queries with error handling
$total_students = 0;
$total_appointments = 0;
$approved_payments = 0;

try {
    $total_students = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn() ?? 0;
    $total_appointments = $conn->query("SELECT COUNT(*) FROM appointments")->fetchColumn() ?? 0;
    $approved_payments = $conn->query("SELECT SUM(amount) FROM payments WHERE status = 'Approved'")->fetchColumn() ?? 0;
} catch (PDOException $e) {
    // If there is an error, leave it as 0.
}

$pdf->Cell(60, 10, 'Total Registered Students', 1);
$pdf->Cell(60, 10, $total_students, 1, 1, 'C');

$pdf->Cell(60, 10, 'Total Appointments', 1);
$pdf->Cell(60, 10, $total_appointments, 1, 1, 'C');

$pdf->Cell(60, 10, 'Total Revenue (LKR)', 1);
$pdf->Cell(60, 10, number_format($approved_payments, 2), 1, 1, 'C');

$pdf->Output('I', 'FuturePath_System_Report.pdf');
?>