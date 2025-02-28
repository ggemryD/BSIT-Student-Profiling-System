<?php
session_start();
include 'db_connection.php';

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header('Location: studentLogin.php');
    exit();
}

$student_id = $_SESSION['student_id'];

// Check if the form is locked for the specific student
$query = "SELECT form_locked FROM students WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $student_id);
$stmt->execute();
$stmt->bind_result($form_locked);
$stmt->fetch();
$stmt->close();

if (!$form_locked) {
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $errors = [];

        foreach ($_POST as $field_name => $field_value) {
            if (empty($field_value)) {
                $errors[$field_name] = "This field is required.";
            } else {
                // Insert or update field data for the specific student
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
            // Lock the form for this specific student
            $update_query = "UPDATE students SET form_locked = 1 WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param('i', $student_id);
            if ($stmt->execute()) {
                $stmt->close();
                header('Location: home.php'); // Redirect to home page
                exit();
            } else {
                $message = "Form submission successful, but failed to lock the form.";
            }
        }
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
    <link rel="stylesheet" href="css/enroll.css">
    <link rel="shortcut icon" href="image/bsitLogo2.png" type="image/x-icon">
</head>
<body>

    <div class="container">
        <h1>Student Information Form</h1>

        <!-- <?php if (!empty($message)) : ?>
            -- Display success or locked message --
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?> -->

        <?php if ($form_locked) : ?>
            <!-- <p class="message">You have already submitted this form. It is now locked.</p> -->
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php else : ?>
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
        <?php endif; ?>
    </div>
</body>
</html>
