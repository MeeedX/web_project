<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

require_once "db_connect.php";
$username = $_SESSION["user"]["username"];
$sql = "SELECT student_id, full_name, username FROM Student WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Profile</title>
    <link rel="stylesheet" href="student_profile_style.css">
</head>
<body>
<div class="dashboard">
   
    <main class="main-content">
        <header>
            <h1>Student Profile</h1>
        </header>
        <section class="content-section">
            <div class="card">
                <h3>Full Name</h3>
                <p><?php echo htmlspecialchars($student['full_name']); ?></p>
            </div>
            <div class="card">
                <h3>Username</h3>
                <p><?php echo htmlspecialchars($student['username']); ?></p>
            </div>
            <div class="card">
                <h3>Student ID</h3>
                <p><?php echo htmlspecialchars($student['student_id']); ?></p>
            </div>
        </section>
    </main>
</div>
</body>
</html>
