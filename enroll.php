<?php
// Start session and include database connection
session_start();
include 'db_connection.php';

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header('Location: studentLogin.php'); // Redirect to student login if not logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_SESSION['student_id'];
    $errors = [];

    foreach ($_POST as $field_name => $field_value) {
        if (empty($field_value)) {
            $errors[$field_name] = "This field is required.";
        } else {
            // Match field_name with the form_fields table
            $query = "INSERT INTO student_details (student_id, field_name, field_value) 
                      VALUES (?, ?, ?) 
                      ON DUPLICATE KEY UPDATE field_value = VALUES(field_value)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('iss', $student_id, $field_name, $field_value);
            if (!$stmt->execute()) {
                $errors[$field_name] = "Failed to save field: $field_name.";
            }
            $stmt->close();
        }
    }

    if (empty($errors)) {
        $message = "Enrollment form submitted successfully!";
    }
}

// Fetch form fields
$query = "SELECT * FROM form_fields";
$result = $conn->query($query);
$form_fields = $result->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll Now</title>
    <link rel="stylesheet" href="css/enroll.css"> <!-- Link to your student CSS -->
    <link rel="shortcut icon" href="image/bsitLogo2.png" type="image/x-icon">

</head>
<body>
    <!-- Include Navbar -->
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h1>Enrollment Form</h1>
        <?php if (!empty($message)) : ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <form action="" method="POST">
    <?php foreach ($form_fields as $field): ?>
        <div class="form-group">
            <label for="<?php echo htmlspecialchars($field['field_name']); ?>">
                <?php echo htmlspecialchars($field['field_name']); ?>
                <?php if ($field['is_required']): ?><span class="error"> *</span><?php endif; ?>
            </label>
            <?php if ($field['field_type'] === 'dropdown'): ?>
                <select name="<?php echo htmlspecialchars($field['field_name']); ?>" id="<?php echo htmlspecialchars($field['field_name']); ?>" required>
                    <option value="">-- Select an option --</option>
                    <?php foreach (json_decode($field['field_options']) as $option): ?>
                        <option value="<?php echo htmlspecialchars($option); ?>"><?php echo htmlspecialchars($option); ?></option>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <input type="<?php echo htmlspecialchars($field['field_type']); ?>" name="<?php echo htmlspecialchars($field['field_name']); ?>" id="<?php echo htmlspecialchars($field['field_name']); ?>" <?php echo $field['is_required'] ? 'required' : ''; ?>>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    <button type="submit" class="btn">Submit Form</button>
</form>
    </div>
</body>
</html>
