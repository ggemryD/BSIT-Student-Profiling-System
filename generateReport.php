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
        /* Reset & Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', Arial, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .report-container {
            width: 100%;
            max-width: 600px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            padding: 25px;
            overflow: hidden;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #69bbbb;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
            color: #333333;
        }

        .header p {
            font-size: 14px;
            color: #777777;
            margin-top: 5px;
        }

        .info-group {
            margin-bottom: 15px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 8px;
        }

        .info-group p {
            font-size: 16px;
            color: #555555;
            margin: 0;
        }

        .info-group span {
            font-weight: 600;
            color: #333333;
        }

        .button-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .button-container button {
            flex: 1;
            max-width: 48%;
            background-color: #69bbbb;
            color: white;
            border: none;
            padding: 12px 15px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-align: center;
        }

        .button-container button:hover {
            background-color: #51a3a3;
        }

        .button-container .btn-secondary {
            background-color: #dc3545;
        }

        .button-container .btn-secondary:hover {
            background-color: #b52d3a;
        }

        @media (max-width: 768px) {
            .button-container button {
                max-width: 100%;
                margin-bottom: 10px;
            }

            .button-container button:last-child {
                margin-bottom: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Include Sidebar -->
    <?php include 'adminSideBar.php'; ?>

    <div class="report-container">
        <div class="header">
            <h1>Student Report</h1>
            <p>Generated on <?php echo date('F d, Y'); ?></p>
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
            <button class="btn-secondary" onclick="window.location.href='studentManagement.php'">Back</button>
        </div>
    </div>
</body>
</html>
