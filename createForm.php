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
        if ($field_type === 'dropdown' && empty($field_options)) {
            $message = "Dropdown options are required.";
        } else {
            $query = "INSERT INTO form_fields (field_name, field_type, is_required, field_options) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ssis', $field_name, $field_type, $is_required, $field_options);

            $message = $stmt->execute() ? "Field added successfully!" : "Error adding field. Please try again.";
            $stmt->close();
        }
    } else {
        $message = "All fields are required.";
    }
}

// Process delete field request with confirmation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_field']) && isset($_POST['confirm_delete'])) {
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="shortcut icon" href="image/bsitLogo2.png" type="image/x-icon">
    <link rel="stylesheet" href="css/createForm.css">
</head>
<body>
    <?php include 'adminSideBar.php'; ?>

    <div class="main-content">
        <div class="dashboard-header">
            <h1 class="dashboard-title">Create Enrollment Form</h1>
            <?php if (!empty($message)) : ?>
                <div class="message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
        </div>

        <div class="container">
            <div class="form-card">
                <h2 class="card-title">
                    <i class="fas fa-plus-circle"></i>
                    Add New Field
                </h2>
                <form action="" method="POST">
                    <div class="form-group">
                        <label for="field_name">Field Name</label>
                        <input type="text" name="field_name" id="field_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="field_type">Field Type</label>
                        <select name="field_type" id="field_type" class="form-control" onchange="toggleOptions()" required>
                            <option value="text">Text</option>
                            <option value="number">Number</option>
                            <option value="email">Email</option>
                            <option value="date">Date</option>
                            <option value="dropdown">Dropdown</option>
                        </select>
                    </div>
                    <div class="form-group" id="options-group" style="display: none;">
                        <label for="field_options">Dropdown Options</label>
                        <input type="text" name="field_options" id="field_options" class="form-control" 
                               placeholder="Enter options separated by commas">
                    </div>
                    <div class="form-group checkbox-group">
                        <input type="checkbox" name="is_required" id="is_required" value="1">
                        <label for="is_required">Required Field</label>
                    </div>
                    <button type="submit" name="add_field" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Add Field
                    </button>
                </form>
            </div>

            <div class="form-card">
    <h2 class="card-title">
        <i class="fas fa-list"></i>
        Existing Fields
    </h2>
    <div class="table-container">
        <table class="fields-table">
            <thead>
                <tr>
                    <th>Field Name</th>
                    <th>Type</th>
                    <th>Required</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td>
                            <?php echo htmlspecialchars($row['field_name']); ?>
                            <?php if ($row['field_type'] === 'dropdown') : ?>
                                <div class="text-sm text-gray-500">
                                    Options: <?php echo $row['field_options'] ? implode(', ', json_decode($row['field_options'])) : 'N/A'; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['field_type']); ?></td>
                        <td>
                            <span class="badge <?php echo $row['is_required'] ? 'badge-required' : 'badge-optional'; ?>">
                                <?php echo $row['is_required'] ? 'Required' : 'Optional'; ?>
                            </span>
                        </td>
                        <td>
                            <form action="" method="POST" style="display:inline;">
                                <input type="hidden" name="field_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="delete_field" class="btn btn-danger" onclick="return confirmDelete()">
                                    <i class="fas fa-trash"></i>
                                    Delete
                                </button>
                                <input type="hidden" name="confirm_delete" value="1">
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>




        </div>
    </div>

    <script>
        function toggleOptions() {
            const fieldType = document.getElementById('field_type').value;
            const optionsGroup = document.getElementById('options-group');
            optionsGroup.style.display = fieldType === 'dropdown' ? 'block' : 'none';
        }

        function confirmDelete() {
            return confirm("Are you sure you want to delete this field?");
        }
    </script>
</body>
</html>