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
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="student_dashboard_style.css">
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar">
            <h2>Student Portal</h2>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="modules.php">My Modules</a></li> 
                <li><a href="quiz.php">Quiz</a></li>

                <li><a href="student_profile.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header>
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION["user"]["username"]); ?>!</h1>
            </header>
            <section class="content-section">
                <a href="modules.php" class="card-link">
                    <div class="card">
                        <h3>My Modules</h3>
                        <p>View all the modules you're enrolled in.</p>
                    </div>
                </a>
               
                <div class="card">
                    <h3><a href="student_profile.php" class="card-link">Profile</a></h3>
                    <p><a href="student_profile.php" class="card-link">View and update your personal information.</a></p>
                </div>
                
                <div class="card">
                    <h3><a href="quiz.php" class="card-link">Quiz</a></h3>
                    <p><a href="quiz.php" class="card-link">Challenge yourself with smart AI-powered quizzes.</a></p>
                </div>
            </section>
            
        </main>
    </div>
</body>
</html>
