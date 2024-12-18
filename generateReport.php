<?php
// Start session and include database connection
session_start();
include 'db_connection.php';

// Validate the student ID
if (!isset($_GET['id'])) {
    die("Invalid request.");
}
$student_id = intval($_GET['id']);

// Fetch student's basic information
$query_student = "SELECT first_name, last_name FROM students WHERE id = $student_id";
$result_student = $conn->query($query_student);
if ($result_student->num_rows == 0) {
    die("Student not found.");
}
$student = $result_student->fetch_assoc();

// Fetch dynamic fields and their values
$query_details = "
    SELECT field_name, field_value 
    FROM student_details 
    WHERE student_id = $student_id
";
$result_details = $conn->query($query_details);
if (!$result_details) {
    die("Error fetching details: " . $conn->error);
}

// Store details in an associative array
$details = [];
while ($row = $result_details->fetch_assoc()) {
    $details[$row['field_name']] = $row['field_value'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .report-container {
            width: 90%;
            max-width: 600px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 14px;
            color: #555;
        }

        .info-group {
            margin-bottom: 15px;
        }

        .info-group p {
            margin: 0;
            font-size: 16px;
            color: #333;
            line-height: 1.5;
        }

        .info-group span {
            font-weight: bold;
            color: #555;
        }

        .button-container {
            text-align: center;
            margin-top: 20px;
        }

        .button-container button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .button-container button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="report-container">
        <div class="header">
            <h1>Student Report</h1>
            <p>Generated Report</p>
        </div>

        <div class="info-group">
            <p><span>First Name:</span> <?php echo htmlspecialchars($student['first_name']); ?></p>
        </div>

        <div class="info-group">
            <p><span>Last Name:</span> <?php echo htmlspecialchars($student['last_name']); ?></p>
        </div>

        <?php foreach ($details as $field_name => $field_value): ?>
        <div class="info-group">
            <p><span><?php echo htmlspecialchars($field_name); ?>:</span> <?php echo htmlspecialchars($field_value); ?></p>
        </div>
        <?php endforeach; ?>

        <div class="button-container">
            <button onclick="window.print()">Print</button>
        </div>
    </div>
</body>
</html>
