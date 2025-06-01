<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin_dashboard_style.css">
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar">
            <h2>Dashboard</h2>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="students_list.php">Students</a></li> 
                
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header>
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION["user"]["username"]); ?>!</h1>
            </header>
            <section class="content-section">
                <a href="manage_students.php" class="card-link">
                    <div class="card">
                        <h3>Manage Students</h3>
                        <p>Add, edit, or delete student profiles.</p>
                    </div>
                </a>
                <div class="card">
                    <h3><a href="students_list.php" class="card-link">Students</a></h3>
                    <p><a href="students_list.php" class="card-link">Studet list.</a></p>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
