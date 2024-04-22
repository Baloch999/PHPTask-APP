<?php
include '../Includes/dbconnection.php';
session_start();

$message = '';

// Check if the user is logged in and has the 'Admin' role before allowing access to the user registration page
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin') {
    $message = "Unauthorized access";
    exit();
}
 // Check if the user is logged in and has the 'Admin' role before allowing access to the user registration page
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];
    $role = htmlspecialchars($_POST['role']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    // Validate inputs and generate password hash if all inputs are valid
    if(empty($username) || empty($role) || empty($email)) {
        $message = "All fields are required";
    } elseif(!ctype_alnum($username) || strlen($username) < 5 || strlen($username) > 15) {
        $message = "Username must be alphanumeric and between 5 and 15 characters long";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format";
    } else {
        $password = password_hash('defaultPassword', PASSWORD_DEFAULT);

        // Prepare SQL statement and execute it
    }
    $sql = "INSERT INTO users (username, password, role, email) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $username, $password, $role, $email);
    if ($stmt->execute()) {
        $message = "New record created successfully";
    } else {
        $message = "Error: " . $stmt->error;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<head>
    <title>Update Task</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">Project Management Dashboard</a>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="../index.php">Home</a>
            </li>
        </ul>
    </div>
</nav>
<div class="container mt-5">
    <h2>Register</h2>
    <?php if (!empty($message)): ?>
        <div class="alert alert-info">
            <?= $message; ?>
        </div>
    <?php endif; ?>
    <form method="post" action="">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
            <button type="button" class="btn btn-secondary mt-2" id="generate">Generate Password</button>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="role">Role</label>
            <select class="form-control" id="role" name="role">
                <option value="Guest">Guest</option>
                <option value="User">User</option>
                <option value="Admin">Admin</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    document.getElementById('generate').addEventListener('click', function() {
        let chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+~`|}{[]\:;?><,./-=';
        let password = '';
        for (let i = 0; i < 15; i++) {
            let randomNumber = Math.floor(Math.random() * chars.length);
            password += chars.substring(randomNumber, randomNumber + 1);
        }
        document.getElementById('password').value = password;
    });
</script>
</body>
</>