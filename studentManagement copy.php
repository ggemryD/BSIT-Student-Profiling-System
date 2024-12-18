<?php
// Start session and include database connection
session_start();
include 'db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: adminLogin.php'); // Redirect to login if not logged in
    exit();
}

// Fetch students' data
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management</title>
    <link rel="stylesheet" href="css/studentManagement.css"> <!-- Link your CSS file -->
    <link rel="shortcut icon" href="image/bsitLogo2.png" type="image/x-icon">

    <style>
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

    </style>

</head>
<body>

    <!-- Include Navbar -->
    <?php include 'adminSideBar.php'; ?>

    <div class="management-container">
        <h1>Student Management</h1>
        
        <!-- Add Student Form -->
        <div class="add-student-form">
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
            <h2>Student List</h2>
            <table>
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Gender</th>
                        <th>Section</th>
                        <th>Year Level</th>
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
                            <td>
                                <a href="viewStudent.php?id=<?php echo $row['id']; ?>" class="btn-view">View</a>
                                <a href="editStudent.php?id=<?php echo $row['id']; ?>" class="btn-edit">Edit</a>
                                <a href="deleteStudent.php?id=<?php echo $row['id']; ?>" 
                                   class="btn-delete" 
                                   onclick="return confirm('Are you sure you want to delete this student?');">
                                   Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="message">
            <?php echo $_SESSION['message']; ?>
            <?php unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>


</body>
</html>
