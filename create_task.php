<?php
include 'Includes/dbconnection.php';
session_start();

// Check if the user is logged in and has the 'User' or 'Admin' role before allowing access to the task creation page
if (!isset($_SESSION['loggedin']) || ($_SESSION['role'] != 'User' && $_SESSION['role'] != 'Admin')) {
    header('Location: login.php');
    exit();
}

$message = '';
$errors = array('taskName' => '', 'taskDescription' => '', 'taskStatus' => '', 'completionDate' => ''); // Initialize an errors array

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $taskName = $_POST['taskName'];
    $taskDescription = $_POST['taskDescription'];
    $taskStatus = $_POST['taskStatus'];
    $completionDate = $_POST['completionDate'];
    $userId = $_SESSION['id'];

    // Check if task name is empty
    if(empty($taskName)){
        $errors['taskName'] = 'Task name is required';
    }

    // Check if task description is empty
    if(empty($taskDescription)){
        $errors['taskDescription'] = 'Task description is required';
    }

    // Check if task status is empty
    if(empty($taskStatus)){
        $errors['taskStatus'] = 'Task status is required';
    }

    // Check if completion date is empty
    if(empty($completionDate)){
        $errors['completionDate'] = 'Completion date is required';
    }

    // If there are no errors, proceed with the rest of the code
    if(!array_filter($errors)){
        // Prepare SQL statement
        $sql = "INSERT INTO tasks (name, description, status, completion_date, user_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $taskName, $taskDescription, $taskStatus, $completionDate, $userId);

        // Execute SQL statement
        if ($stmt->execute()) {
            // Redirect to dashboard or display success message
            header('Location: index.php');
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>Create Task</title>
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
    <h2>Create a new task</h2>
    <form method="post" action="">
        <div class="form-group">
            <label for="taskName">Task name</label>
            <input type="text" class="form-control" id="taskName" name="taskName" required>
            <div class="text-danger"><?php echo $errors['taskName']; ?></div>
        </div>
        <div class="form-group">
            <label for="taskDescription">Task description</label>
            <textarea class="form-control" id="taskDescription" name="taskDescription" rows="3" required></textarea>
            <div class="text-danger"><?php echo $errors['taskDescription']; ?></div>
        </div>
        <div class="form-group">
            <label for="taskStatus">Task status</label>
            <select class="form-control" id="taskStatus" name="taskStatus">
                <option>Backlog</option>
                <option>Doing</option>
                <option>Done</option>
            </select>
            <div class="text-danger"><?php echo $errors['taskStatus']; ?></div>
        </div>
        <div class="form-group">
            <label for="completionDate">Completion Date</label>
            <input type="date" class="form-control" id="completionDate" name="completionDate">
            <div class="text-danger"><?php echo $errors['completionDate']; ?></div>
        </div>
        <button type="submit" class="btn btn-primary">Create Task</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>