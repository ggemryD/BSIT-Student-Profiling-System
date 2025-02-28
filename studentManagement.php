<?php
// Start session and include database connection
session_start();
include 'db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: adminLogin.php'); // Redirect to login if not logged in
    exit();
}

// Fetch all students initially
$query = "
    SELECT s.id, s.first_name, s.last_name, 
           MAX(CASE WHEN d.field_name = 'Gender' THEN d.field_value END) AS gender,
           MAX(CASE WHEN d.field_name = 'Section' THEN d.field_value END) AS section,
           MAX(CASE WHEN d.field_name = 'Year_Level' THEN d.field_value END) AS year_level,
           MAX(CASE WHEN d.field_name = 'Enrollment_Status' THEN d.field_value END) AS enrollment_status
    FROM students s
    LEFT JOIN student_details d ON s.id = d.student_id
    GROUP BY s.id, s.first_name, s.last_name
";
$result = $conn->query($query);
if (!$result) {
    die("Error fetching data: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management</title>
    <link rel="stylesheet" href="css/studentManagement.css"> <!-- Link your CSS file -->
    <link rel="shortcut icon" href="image/bsitLogo2.png" type="image/x-icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- <style>
        .message {
            padding: 10px;
            margin: 10px 0;
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
            border-radius: 4px;
        }

        .message.error {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }

        .btn {
            display: inline-block;
            padding: 5px 10px;
            margin: 5px;
            text-decoration: none;
            color: #fff;
            border-radius: 5px;
            text-align: center;
        }

        .btn-add {
            background-color: #007bff;
            margin: 20px 0;
        }

        /* Add Student Form Popup */
        .add-student-form {
            display: none;
            position: fixed;
            top: 50%;
            left: 60%;
            transform: translate(-50%, -50%);
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            width: 400px;
            z-index: 1000;
        }

        .add-student-form input{
            width: 97%;
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .add-student-form button {
            display: inline-block;
            background-color: #004080;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .add-student-form button:hover {
            background-color: #003366;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #dc3545;
            color: #fff;
            padding: 5px 10px;
            border-radius: 50%;
            cursor: pointer;
        }

        .search-bar {
            margin: 20px 0;
            padding: 10px;
            width: 98%;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .student-table {
            margin-top: 20px;
        }
    </style> -->

</head>
<body>

    <!-- Include Navbar -->
    <?php include 'adminSideBar.php'; ?>

    <div class="management-container">
        <h1>Student Management</h1>

        <!-- Add Student Button 
        <a href="javascript:void(0);" class="btn btn-add" id="addStudentBtn">Add Student</a>

         Generate Report Button 
        <a href="generateAllStudentsReport.php" class="btn btn-report">Generate Report for All Students</a>

         Search Bar 
        <input type="text" id="searchBar" class="search-bar" placeholder="Search Student">-->

        <div class="actions-bar">
            <input type="text" id="searchBar" class="search-bar" placeholder="Search by name, gender, section, year level or status...">
            <a href="javascript:void(0);" class="btn btn-add" id="addStudentBtn">
                <i class="fas fa-plus"></i> Add Student
            </a>
            <a href="generateAllStudentsReport.php" class="btn btn-report">
                <i class="fas fa-file-alt"></i> Generate Report
            </a>
        </div>

        <!-- Add Student Form Popup -->
        <div class="add-student-form" id="addStudentForm">
            <button class="close-btn" id="closeBtn">&times;</button>
            <h2>Add New Student</h2>
            <form method="POST" action="addStudent.php">
                <label for="first_name">First Name:</label>
                <input type="text" name="first_name" required>

                <label for="last_name">Last Name:</label>
                <input type="text" name="last_name" required>

                <label for="email">Email:</label>
                <input type="email" name="email" required>

                <label for="password">Password:</label>
                <input type="password" name="password" required>

                <button type="submit">Add Student</button>
            </form>
        </div>

        <!-- Student Table -->
        <div class="student-table">
            <table id="studentTable">
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Gender</th>
                        <th>Section</th>
                        <th>Year Level</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['gender'] ?? 'Not Set'); ?></td>
                            <td><?php echo htmlspecialchars($row['section'] ?? 'Not Set'); ?></td>
                            <td><?php echo htmlspecialchars($row['year_level'] ?? 'Not Set'); ?></td>
                            <td><?php echo htmlspecialchars($row['enrollment_status'] ?? 'Not Set'); ?></td>
                            <td>
                                <a href="viewStudent.php?id=<?php echo $row['id']; ?>" class="btn btn-view">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="editStudent.php?id=<?php echo $row['id']; ?>" class="btn btn-edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="deleteStudent.php?id=<?php echo $row['id']; ?>" class="btn btn-delete" 
                                   onclick="return confirm('Are you sure you want to delete this student?');">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                                <a href="generateReport.php?id=<?php echo $row['id']; ?>" class="btn btn-report">
                                <i class='fas fa-file-alt'></i>
                            </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Show Add Student Form
        document.getElementById('addStudentBtn').addEventListener('click', () => {
            document.getElementById('addStudentForm').style.display = 'block';
        });

        // Close Add Student Form
        document.getElementById('closeBtn').addEventListener('click', () => {
            document.getElementById('addStudentForm').style.display = 'none';
        });

        document.getElementById('searchBar').addEventListener('input', function () {
            const searchQuery = this.value.trim(); // Get input value
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'fetchStudents.php?search=' + encodeURIComponent(searchQuery), true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    document.querySelector('#studentTable tbody').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        });
        

    </script>

</body>
</html>
