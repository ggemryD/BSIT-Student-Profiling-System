<?php
require('fpdf/fpdf.php'); // Include FPDF library
include 'db_connection.php';

// Get student ID from query string
if (!isset($_GET['id'])) {
    die("Student ID not provided.");
}

$student_id = intval($_GET['id']);

// Fetch student data
$query = "
    SELECT s.first_name, s.last_name, 
           MAX(CASE WHEN d.field_name = 'Gender' THEN d.field_value END) AS gender,
           MAX(CASE WHEN d.field_name = 'Section' THEN d.field_value END) AS section,
           MAX(CASE WHEN d.field_name = 'Year_Level' THEN d.field_value END) AS year_level
    FROM students s
    LEFT JOIN student_details d ON s.id = d.student_id
    WHERE s.id = $student_id
    GROUP BY s.first_name, s.last_name
";
$result = $conn->query($query);

if ($result->num_rows == 0) {
    die("No student found with ID $student_id.");
}

$student = $result->fetch_assoc();

// Create PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

$pdf->Cell(0, 10, 'Student Report', 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 10, 'First Name:', 1);
$pdf->Cell(0, 10, $student['first_name'], 1, 1);

$pdf->Cell(50, 10, 'Last Name:', 1);
$pdf->Cell(0, 10, $student['last_name'], 1, 1);

$pdf->Cell(50, 10, 'Gender:', 1);
$pdf->Cell(0, 10, $student['gender'] ?? 'Not Set', 1, 1);

$pdf->Cell(50, 10, 'Section:', 1);
$pdf->Cell(0, 10, $student['section'] ?? 'Not Set', 1, 1);

$pdf->Cell(50, 10, 'Year Level:', 1);
$pdf->Cell(0, 10, $student['year_level'] ?? 'Not Set', 1, 1);

// Output PDF
$pdf->Output('D', 'Student_Report_' . $student['first_name'] . '_' . $student['last_name'] . '.pdf');
?>
