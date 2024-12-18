<?php
include 'db_connection.php';

// Get the search query from the GET parameter
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Fetch filtered students based on the search query
$query = "
    SELECT s.id, s.first_name, s.last_name, 
           MAX(CASE WHEN d.field_name = 'Gender' THEN d.field_value END) AS gender,
           MAX(CASE WHEN d.field_name = 'Section' THEN d.field_value END) AS section,
           MAX(CASE WHEN d.field_name = 'Year_Level' THEN d.field_value END) AS year_level
    FROM students s
    LEFT JOIN student_details d ON s.id = d.student_id
    WHERE s.first_name LIKE '%$search%' OR s.last_name LIKE '%$search%'
    GROUP BY s.id, s.first_name, s.last_name
";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['gender'] ?? 'Not Set') . "</td>";
        echo "<td>" . htmlspecialchars($row['section'] ?? 'Not Set') . "</td>";
        echo "<td>" . htmlspecialchars($row['year_level'] ?? 'Not Set') . "</td>";
        echo "<td>
                <a href='viewStudent.php?id=" . $row['id'] . "' class='btn btn-view'>
                    <i class='fas fa-eye'></i>
                </a>
                <a href='editStudent.php?id=" . $row['id'] . "' class='btn btn-edit'>
                    <i class='fas fa-edit'></i>
                </a>
                <a href='deleteStudent.php?id=" . $row['id'] . "' class='btn btn-delete' 
                   onclick='return confirm(\"Are you sure you want to delete this student?\");'>
                    <i class='fas fa-trash-alt'></i>
                </a>
                <a href='generateReport.php?id=" . $row['id'] . "' class='btn btn-report' target='_blank'>
                    <i class='fas fa-file-alt'></i>
                </a>
            </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6'>No students found.</td></tr>";
}
?>
