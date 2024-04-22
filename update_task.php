<?php
include '../Includes/dbconnection.php';
session_start();

// Check if the user is logged in and has the 'User' or 'Admin' role before allowing access to the task creation page
if (!isset($_SESSION['loggedin']) || ($_SESSION['role'] != 'User' && $_SESSION['role'] != 'Admin')) {
    header('Location: login.php');
    exit();
}
$message = '';

if(!isset($_SESSION['loggedin']) || $_SESSION['role'] != 'Admin') {
    header('Location: login.php');
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $taskId = $_POST['taskId'];
    $taskName = $_POST['taskName'];
    $taskDescription = $_POST['taskDescription'];
    $taskStatus = $_POST['taskStatus'];

    // Validate inputs
    if(empty($taskName) || empty($taskDescription) || empty($taskStatus)) {
        $message = "All fields are required";
    } else {
        // Check if the user has permission to edit this task
        $sql = "SELECT user_id FROM tasks WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $taskId);
        $stmt->execute();
        $result = $stmt->get_result();
        $task = $result->fetch_assoc();

        if ($_SESSION['role'] != 'Admin' && $_SESSION['id'] != $task['user_id']) {
            $message = "You do not have permission to edit this task";
        } else {
            $sql = "UPDATE tasks SET name = ?, description = ?, status = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $taskName, $taskDescription, $taskStatus, $taskId);
            if ($stmt->execute()) {
                $message = "Task updated successfully";
            } else {
                $message = "Error: " . $stmt->error;
            }
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html>
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
    <h2>Update Task</h2>
    <?php if (!empty($message)): ?>
        <div class="alert alert-info">
            <?= $message; ?>
        </div>
    <?php endif; ?>
    <form method="post" action="">
        <div class="form-group">
            <label for="taskId">Task ID</label>
            <input type="number" class="form-control" id="taskId" name="taskId" required>
        </div>
        <div class="form-group">
            <label for="taskName">Task Name</label>
            <input type="text" class="form-control" id="taskName" name="taskName" required>
        </div>
        <div class="form-group">
            <label for="taskDescription">Task Description</label>
            <textarea class="form-control" id="taskDescription" name="taskDescription" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label for="taskStatus">Task Status</label>
            <select class="form-control" id="taskStatus" name="taskStatus">
                <option>Open</option>
                <option>In progress</option>
                <option>Completed</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Task</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>