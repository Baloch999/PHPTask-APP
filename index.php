<?php
global $conn;
include 'Includes/dbconnection.php';
session_start();

// Search query parameter for tasks search
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}
// Check if the user is logged in and has changed their password
if (isset($_SESSION['loggedin'])) {
    $sql = "SELECT is_password_changed FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $_SESSION['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user['is_password_changed'] == 0) {
        header('Location: change_password.php');
        exit();
    }
}

// Query to get the count of completed tasks
$sql = "SELECT COUNT(*) as count FROM tasks WHERE status = 'done'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$completedTasksCount = $row['count'];
?>
<!DOCTYPE html>
<html lang="eng">
<head>
    <title>Project Management Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="index-page">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">Project Management App</a>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav mr-auto">
            <?php if(isset($_SESSION['loggedin']) && $_SESSION['role'] == 'Admin'): ?>
                <li class="nav-item">
                <li class="nav-item mr-2">

                    <a class="nav-link btn btn-success" href="Admin/admin_page.php">Admin Panel</a>
                </li>
            <?php endif; ?>
            <?php if(isset($_SESSION['loggedin'])): ?>
                <li class="nav-item">
                    <a class="nav-link btn btn-danger" href="logout.php">Log-Out</a>
                </li>
            <?php endif; ?>
        </ul>
        <ul class="navbar-nav">
            <li class="nav-item ml-auto">
                <span class="navbar-text text-primary">
                    <?php if(isset($_SESSION['loggedin'])): ?>
                        Welcome, <?php echo $_SESSION['username']; ?>
                    <?php else: ?>
                        Welcome, Guest
                    <?php endif; ?>
                </span>
            </li>
        </ul>
    </div>
</nav>

<!-- Toast message -->
<div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-autohide="false">
    <div class="toast-header">
        <strong class="mr-auto">Task Completion Status</strong>
        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="toast-body">
        <?php echo $completedTasksCount; ?> tasks have been completed.
    </div>
</div>

<div class="container mt-5">
    <div class="col-md-4" id="sidebar">
        <div class="row">
            <div class="col-md-4">
                <div class="list-group">
                    <a href="index.php" class="list-group-item list-group-item-action active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    <?php if(isset($_SESSION['loggedin']) && ($_SESSION['role'] == 'User' || $_SESSION['role'] == 'Admin')): ?>
                        <a href="create_task.php" class="list-group-item list-group-item-action"><i class="fas fa-plus"></i> Create Task</a>
                    <?php endif; ?>
                    <?php if(isset($_SESSION['loggedin']) && $_SESSION['role'] == 'Admin'): ?>
                        <a href="Admin/update_task.php" class="list-group-item list-group-item-action"><i class="fas fa-edit"></i> Update Task</a>
                    <?php endif; ?>
                    <?php if (!isset($_SESSION['loggedin'])): ?>
                        <a href="login.php" class="list-group-item list-group-item-action"><i class="fas fa-sign-in-alt"></i> Login</a>
                    <?php endif; ?>
                    <?php if(isset($_SESSION['loggedin']) && ($_SESSION['role'] == 'User' || $_SESSION['role'] == 'Admin')): ?>
                        <a href="delete_task.php" class="list-group-item list-group-item-action"><i class="fas fa-trash"></i> Delete task</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-8">
                <div class="row">
                    <?php
                    $sql = "SELECT tasks.*, users.username AS assignee_name FROM tasks LEFT JOIN users ON tasks.user_id = users.id";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo '<div class="col-md-4">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">' . $row['name'] . '</h5>
                  <ul class="card-text">';
                            $descriptions = explode("\n", $row['description']); // Assuming each bullet point is separated by a newline
                            foreach ($descriptions as $description) {
                                echo '<li>' . $description . '</li>';
                            }
                            echo '</ul>
                  <p class="card-text-muted"><small class="text-muted">' . $row['status'] . '</small></p>
                  <p class="card-text-muted"><small class="text-muted">Assignee: ' . $row['assignee_name'] . '</small></p>
                </div>
              </div>
            </div>';
                        }
                    } else {
                        echo "No tasks found";
                    }
                    ?>
                </div>

            </div>
        </div>
    </div>
    <div class="container mt-5" style="position: fixed; right: 0; bottom: 0;">
        <label for="search-input"></label><input type="text" id="search-input" placeholder="Search tasks..." style="float: right;">
        <script>
            $(document).ready(function() {
                $('#search-input').on('keyup', function() {
                    let value = $(this).val().toLowerCase();
                    $('.card').filter(function() {
                        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                    });
                });
            });
        </script>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.toast').toast('show');
        });
    </script>
</body>
</html>