<!DOCTYPE html>
<html lang="en"
<head>
    <title>Users</title>
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

</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
<?php
include '../Includes/dbconnection.php';
session_start();

// Check if the user is logged in and is an admin
if(!isset($_SESSION['loggedin']) || $_SESSION['role'] != 'Admin') {
    echo "You do not have permission to access this page.";
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the user ID from the POST request
    $user_id = $_POST['user_id'];

    // Prepare a SQL statement to delete the user
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    echo "<div class='alert alert-success'>User has been deleted.</div>";
}

// Get all users from the database
$sql = "SELECT id, username FROM users";
$result = $conn->query($sql);

echo "<table class='table table-striped'>";
echo "<thead class='thead-dark'><tr><th>Username</th><th>Action</th></tr></thead>";
echo "<tbody>";
while($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['username'] . "</td>";
    echo "<td>
                <form method='POST' action='users.php'>
                    <input type='hidden' name='user_id' value='" . $row['id'] . "'>
                    <input type='submit' class='btn btn-danger' value='Delete'>
                </form>
              </td>";
    echo "</tr>";
}
echo "</tbody>";
echo "</table>";
?>
