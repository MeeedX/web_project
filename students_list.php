<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

$sql = "SELECT student_id, full_name, username FROM Student";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Students List</title>
    <link rel="stylesheet" href="students_list_style.css">
</head>
<body>
    <div class="container">
        <h2>Students List</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Username</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($row["student_id"]); ?></td>
                    <td><?php echo htmlspecialchars($row["full_name"]); ?></td>
                    <td><?php echo htmlspecialchars($row["username"]); ?></td>
                </tr>
                <?php
                    endwhile;
                else:
                ?>
                <tr><td colspan="3">No students found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
