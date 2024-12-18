<?php
session_start();
include 'db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: adminLogin.php'); // Redirect to admin login if not logged in
    exit();
}

// Process form submission to add field
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_field'])) {
    $field_name = trim($_POST['field_name']);
    $field_type = $_POST['field_type'];
    $is_required = isset($_POST['is_required']) ? 1 : 0;
    $field_options = !empty($_POST['field_options']) ? json_encode(explode(',', trim($_POST['field_options']))) : null;

    if (!empty($field_name) && !empty($field_type)) {
        $query = "INSERT INTO form_fields (field_name, field_type, is_required, field_options) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssis', $field_name, $field_type, $is_required, $field_options);

        $message = $stmt->execute() ? "Field added successfully!" : "Error adding field. Please try again.";
        $stmt->close();
    } else {
        $message = "All fields are required.";
    }
}

// Process delete field request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_field'])) {
    $field_id = intval($_POST['field_id']);
    $query = "DELETE FROM form_fields WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $field_id);

    $message = $stmt->execute() ? "Field deleted successfully!" : "Error deleting field.";
    $stmt->close();
}

// Fetch existing form fields
$query = "SELECT * FROM form_fields";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Enrollment Form</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/createForm.css">
    <link rel="shortcut icon" href="image/bsitLogo2.png" type="image/x-icon">
</head>
<body>
    <!-- Include Sidebar -->
    <?php include 'adminSideBar.php'; ?>

    <div class="main-content">
        <div class="container">
            <h1>Create Enrollment Form</h1>
            <?php if (!empty($message)) : ?>
                <div class="message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="field_name">Field Name:</label>
                    <input type="text" name="field_name" id="field_name" required>
                </div>
                <div class="form-group">
                    <label for="field_type">Field Type:</label>
                    <select name="field_type" id="field_type" onchange="toggleOptions()" required>
                        <option value="text">Text</option>
                        <option value="number">Number</option>
                        <option value="email">Email</option>
                        <option value="date">Date</option>
                        <option value="dropdown">Dropdown</option>
                    </select>
                </div>
                <div class="form-group" id="options-group" style="display: none;">
                    <label for="field_options">Dropdown Options (comma-separated):</label>
                    <input type="text" name="field_options" id="field_options" placeholder="e.g., Male,Female">
                </div>
                <div class="form-group checkbox">
                    <input type="checkbox" name="is_required" id="is_required" value="1">
                    <label for="is_required">Is Required?</label>
                </div>
                <button type="submit" name="add_field" class="btn">Add Field</button>
            </form>

            <h2>Existing Form Fields</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Field Name</th>
                        <th>Field Type</th>
                        <th>Options</th>
                        <th>Required</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['field_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['field_type']); ?></td>
                            <td><?php echo $row['field_options'] ? implode(', ', json_decode($row['field_options'])) : 'N/A'; ?></td>
                            <td><?php echo $row['is_required'] ? 'Yes' : 'No'; ?></td>
                            <td><?php echo $row['created_at']; ?></td>
                            <td>
                                <form action="" method="POST" style="display:inline;">
                                    <input type="hidden" name="field_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="delete_field" class="btn delete-btn">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        function toggleOptions() {
            const fieldType = document.getElementById('field_type').value;
            const optionsGroup = document.getElementById('options-group');
            optionsGroup.style.display = fieldType === 'dropdown' ? 'block' : 'none';
        }
    </script>
</body>
</html>
