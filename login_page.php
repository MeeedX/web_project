<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    
    $stmt = $conn->prepare("SELECT * FROM Administrator WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $resultAdmin = $stmt->get_result();

    $stmt2 = $conn->prepare("SELECT * FROM Student WHERE username = ? AND password = ?");
    $stmt2->bind_param("ss", $username, $password);
    $stmt2->execute();
    $resultStudent = $stmt2->get_result();

    if ($resultAdmin->num_rows === 1) {
        $_SESSION["user"] = $resultAdmin->fetch_assoc();
        header("Location: admin_dashboard.php");
        exit();
    } elseif ($resultStudent->num_rows === 1) {
        $_SESSION["user"] = $resultStudent->fetch_assoc();
        header("Location: student_dashboard.php");
        exit();
    } else {
        $error = "Incorrect username or password.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LoginPage</title>
    <link rel="stylesheet" href="login_page_style.css">
</head>
<body>
    <div class="login-container">
        <h2>LoginPage</h2>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <div class="remember-me">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Remember me</label>
            </div>
            <button type="submit">Sign In</button>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        </form>
    </div>
</body>
</html>
