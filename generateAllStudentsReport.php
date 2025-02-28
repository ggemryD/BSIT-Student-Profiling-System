<?php
// Include database connection
include 'db_connection.php';

// Fetch all student data from the database
$query = "
    SELECT s.id, s.first_name, s.last_name, 
           MAX(CASE WHEN d.field_name = 'Gender' THEN d.field_value END) AS gender,
           MAX(CASE WHEN d.field_name = 'Section' THEN d.field_value END) AS section,
           MAX(CASE WHEN d.field_name = 'Year_Level' THEN d.field_value END) AS year_level
    FROM students s
    LEFT JOIN student_details d ON s.id = d.student_id
    GROUP BY s.id, s.first_name, s.last_name
";
$result = $conn->query($query);

if (!$result) {
    die("Error fetching data: " . $conn->error);
}

// Set the content type to CSV for download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="students_report.csv"');

// Open PHP output stream to write data
$output = fopen('php://output', 'w');

// Add column headers to the CSV file
fputcsv($output, ['ID', 'First Name', 'Last Name', 'Gender', 'Section', 'Year Level']);

// Fetch each student record and write it to the CSV file
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['id'],
        $row['first_name'],
        $row['last_name'],
        $row['gender'] ?? 'Not Set',
        $row['section'] ?? 'Not Set',
        $row['year_level'] ?? 'Not Set'
    ]);
}

// Close the output stream
fclose($output);

// Close the database connection
$conn->close();

exit();
?>
