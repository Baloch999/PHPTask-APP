<?php
include 'Includes/dbconnection.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit();
}

$message = '';
$errors = array('oldPassword' => '', 'newPassword' => '');

// Define the password strength validation function using STRONG PASSWORD regex
function isPasswordStrong($password) {
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number    = preg_match('@[0-9]@', $password);
    $specialChars = preg_match('@[^\w]@', $password);

    if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
        return false;
    }

    return true;
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];

    // Check if old password is empty
    if(empty($oldPassword)){
        $errors['oldPassword'] = 'Old password is required';
    }

    // Check if new password is empty
    if(empty($newPassword)){
        $errors['newPassword'] = 'New password is required';
    }

    // If there are no errors, proceed with the rest of the code
    if(!array_filter($errors)){
        // Verify the old password
        $sql = "SELECT password FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $_SESSION['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Validate inputs
        if (password_verify($oldPassword, $user['password'])) {
            // Check if the new password is strong
            if (isPasswordStrong($newPassword)) {
                // Allow the user to change their password
                $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET password = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $newPasswordHash, $_SESSION['id']);
                if ($stmt->execute()) {
                    $message = "Password changed successfully";

                    // Update the is_password_changed flag
                    $sql = "UPDATE users SET is_password_changed = 1 WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $_SESSION['id']);
                    $stmt->execute();
                } else {
                    $message = "Error: " . $stmt->error;
                }
            } else {
                $message = "New password is not strong enough. It must be at least 8 characters long, and include at least one uppercase letter, one lowercase letter, one number, and one special character.";
            }
        } else {
            $message = "Incorrect old password";
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<head>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Project Management</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                </li>
            </ul>
        </div>
    </nav>
    <title>Change Password</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">Change Password</h2>
                </div>
                <div class="card-body">
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-info">
                            <?= $message; ?>
                        </div>
                    <?php endif; ?>
                    <form method="post" action="">
                        <div class="form-group">
                            <label for="oldPassword">Old Password</label>
                            <input type="password" class="form-control" id="oldPassword" name="oldPassword" required>
                            <div class="text-danger"><?php echo $errors['oldPassword']; ?></div>
                        </div>
                        <div class="form-group">
                            <label for="newPassword">New Password</label>
                            <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                            <div class="text-danger"><?php echo $errors['newPassword']; ?></div>
                        </div>
                        <button type="submit" class="btn btn-primary">Change Password</button>
                    </form>

                </div>
                <button class="btn-danger" onclick="location.href='index.php'" type="button">
                    Back to Home
                </button>
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>