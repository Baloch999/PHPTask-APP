<!DOCTYPE html>
<head>
    <title>ADMIN</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="wrapper" id="adminPage">
    <div class="sidebar bg-dark p-4">
        <h2 class="text-white text-center">Admin Panel</h2>
        <hr class="bg-dark">
        <div class="list-group">
            <div class="list-group">
                <a href="../index.php" class="list-group-item list-group-item-action bg-light custom-link">Dashboard</a>
                <a href="register.php" class="list-group-item list-group-item-action bg-light custom-link">Register New Users</a>
                <a href="users.php" class="list-group-item list-group-item-action bg-light custom-link">User Management</a>
            </div>
        </div>
    </div>
    <div class="content p-4">
        <div class="card">
            <div class="card-header text-center">
                <h2 class="text-dark"> Welcome, Administrator Remember to check reports!</h2>
            </div>
            <div class="card-body">
                <p class="card-text">1. Here you can Create Users and View Reports.</p>
                <p class="card-text">2. Use links above to navigate.</p>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>