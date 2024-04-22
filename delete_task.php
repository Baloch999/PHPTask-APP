<?php
global $conn;
include 'Includes/dbconnection.php';
session_start();

// Check if the user is logged in before allowing access to the task deletion page
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit();
}

$message = '';

// Fetch all tasks from the database
$sql = "SELECT id, name FROM tasks";
$result = $conn->query($sql);
$tasks = array();
while($row = $result->fetch_assoc()) {
    $tasks[] = $row;
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
    // CSRF token validation
    if (!hash_equals($_SESSION['token'], $_POST['token'])) {
        die('Request forgery detected');
    }

    $taskId = $_POST['taskId'];

    // Validate inputs
    if(empty($taskId)) {
        $message = "Task ID is required";
    } else {
        // Check if the task exists and if the user is authorized to delete it
        $sql = "SELECT user_id FROM tasks WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $taskId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['user_id'] != $_SESSION['id'] && $_SESSION['role'] != 'Admin') {
                $message = "You are not authorized to delete this task";
            } else {
                $sql = "DELETE FROM tasks WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $taskId);
                if ($stmt->execute()) {
                    $message = "Task deleted successfully";
                } else {
                    $message = "Error: " . $stmt->error;
                }
            }
        } else {
            $message = "Task not found";
        }
    }
}

// Generate a CSRF token to protect against CSRF attacks
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(16));
}
$token = $_SESSION['token'];

$conn->close();
?>
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>Delete Task</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">Project Management</a>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Home</a>
            </li>
        </ul>
    </div>
</nav>
<div class="container mt-5">
    <h2>Delete Task</h2>
    <?php if (!empty($message)): ?>
        <div class="alert alert-info">
            <?= $message; ?>
        </div>
    <?php endif; ?>
    <form method="post" action="">
        <div class="form-group">
            <label for="taskId">Task</label>
            <select class="form-control" id="taskId" name="taskId">
                <?php foreach($tasks as $task): ?>
                    <option value="<?= $task['id'] ?>"><?= $task['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <input type="hidden" name="token" value="<?= $token ?>">
        <button type="submit" class="btn btn-primary">Delete Task</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>