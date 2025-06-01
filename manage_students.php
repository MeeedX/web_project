<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['add_student'])) {
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $password = $_POST['password'];

  
    $result = $conn->query("
        SELECT MIN(t1.student_id + 1) AS next_id
        FROM Student t1
        LEFT JOIN Student t2 ON t1.student_id + 1 = t2.student_id
        WHERE t2.student_id IS NULL
    ");

    $row = $result->fetch_assoc();
    $id = $row['next_id'] ?? 1;

   
    $check = $conn->query("SELECT * FROM Student WHERE student_id = $id");
    if ($check->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO Student (student_id, full_name, username, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $id, $full_name, $username, $password);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "<script>alert('Erreur : ID déjà utilisé.');</script>";
    }
}


if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']); 

    $stmt = $conn->prepare("DELETE FROM Student WHERE student_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        
        header("Location: manage_students.php");
        exit();
    } else {
        echo "Erreur lors de la suppression.";
    }

    $stmt->close();
}


$students = $conn->query("SELECT * FROM Student");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Students</title>
    <link rel="stylesheet" href="manage_students_style.css">

</head>
<body>
    <div class="form-container">
        <h2>Add New Student</h2>
        <form method="POST">
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="add_student">Add Student</button>
        </form>
    </div>

    <div class="student-list">
        <h2>Student List</h2>
        <table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Username</th>
            <th>Password</th> 
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $students->fetch_assoc()): ?>
            <tr>
                <td><?= $row['student_id'] ?></td>
                <td><?= htmlspecialchars($row['full_name']) ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['password']) ?></td> 
                <td>
                    <a href="edit_student.php?id=<?= $row['student_id'] ?>">Edit</a>
                    <a href="manage_students.php?delete=<?= $row['student_id'] ?>" onclick="return confirm('Delete this student?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

    </div>
</body>
</html>
