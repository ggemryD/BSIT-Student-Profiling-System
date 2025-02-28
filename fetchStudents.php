<?php
// Include database connection
include 'db_connection.php';

// Retrieve and sanitize the search query
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search = $conn->real_escape_string($search);

// Main query to fetch and sort student details
$query = "
    SELECT 
        s.id, 
        s.first_name, 
        s.last_name,
        MAX(CASE WHEN d.field_name = 'Gender' THEN d.field_value END) AS gender,
        MAX(CASE WHEN d.field_name = 'Section' THEN d.field_value END) AS section,
        MAX(CASE WHEN d.field_name = 'Year_Level' THEN d.field_value END) AS year_level,
        MAX(CASE WHEN d.field_name = 'Enrollment_Status' THEN d.field_value END) AS enrollment_status
    FROM students s
    LEFT JOIN student_details d ON s.id = d.student_id
";

// If there's a search query
if (!empty($search)) {
    // If the search query matches a section pattern (e.g., '1A', '2B')
    if (preg_match("/^[1-4][A-Za-z]$/", $search)) {
        $query .= " WHERE EXISTS (
            SELECT 1 FROM student_details d2 
            WHERE d2.student_id = s.id 
            AND d2.field_name = 'Section' 
            AND LOWER(TRIM(d2.field_value)) = LOWER('$search')
        )";
    } else {
        // For general search across multiple fields
        $query .= " WHERE (
            s.first_name LIKE '%$search%'
            OR s.last_name LIKE '%$search%'
            OR EXISTS (
                SELECT 1 FROM student_details d2 
                WHERE d2.student_id = s.id 
                AND (
                    (d2.field_name = 'Year_Level' AND d2.field_value LIKE '%$search%')
                    OR (d2.field_name = 'Gender' AND LOWER(TRIM(d2.field_value)) = LOWER('$search'))
                    OR (d2.field_name = 'Enrollment_Status' AND LOWER(TRIM(d2.field_value)) = LOWER('$search'))
                )
            )
        )";
    }
}

// Complete the query with GROUP BY and ORDER BY
$query .= "
    GROUP BY s.id, s.first_name, s.last_name
    ORDER BY s.last_name ASC, s.first_name ASC
";

$result = $conn->query($query);

// Check for results and generate table rows
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['first_name']) . "</td>
                <td>" . htmlspecialchars($row['last_name']) . "</td>
                <td>" . htmlspecialchars($row['gender'] ?? 'Not Set') . "</td>
                <td>" . htmlspecialchars($row['section'] ?? 'Not Set') . "</td>
                <td>" . htmlspecialchars($row['year_level'] ?? 'Not Set') . "</td>
                <td>" . htmlspecialchars($row['enrollment_status'] ?? 'Not Set') . "</td>
                <td>
                    <a href='viewStudent.php?id=" . $row['id'] . "' class='btn btn-view'>
                        <i class='fas fa-eye'></i>
                    </a>
                    <a href='editStudent.php?id=" . $row['id'] . "' class='btn btn-edit'>
                        <i class='fas fa-edit'></i>
                    </a>
                    <a href='deleteStudent.php?id=" . $row['id'] . "' class='btn btn-delete' 
                       onclick=\"return confirm('Are you sure you want to delete this student?');\">
                        <i class='fas fa-trash-alt'></i>
                    </a>
                    <a href='generateReport.php?id=" . $row['id'] . "' class='btn btn-report'>
                        <i class='fas fa-file-alt'></i>
                    </a>
                </td>
            </tr>";
    }
} else {
    echo "<tr><td colspan='7'>No students found</td></tr>";
}

$conn->close();
?>