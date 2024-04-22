<?php
global $conn;
include 'Includes/dbconnection.php';
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];
        // Prepared statements to prevent SQL injections and Queries manipulation
    $sql = "SELECT id, password, role FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['role'] != $role) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid role for this user']); // error handling and input validation
        } elseif (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $username;
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid password']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid username']);
    }

    $conn->close();
    exit();
}
?>
<!DOCTYPE html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        #login-container {
            margin-top: 3%;
        }
    </style>
</head>
<body>
<div class="container" id="login-container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Please, Login in to Proceed. </div>
                <div class="card-body">
                    <form id="login-form">
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="role">Role:</label>
                            <select class="form-control" id="role" name="role">
                                <option value="User">User</option>
                                <option value="Admin">Admin</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Login</button>
                        <a href="index.php" type="submit" class="btn btn-primary">Guest Mode</a>
                    </form>
                    <div id="message"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#login-form').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: 'login.php',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        window.location.href = 'index.php';
                    } else {
                        $('#message').html('<div class="alert alert-danger">' + response.message + '</div>');
                    }
                }
            });
        });
    });
</script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>


