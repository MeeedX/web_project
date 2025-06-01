<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

require_once "db_connect.php";

$studentId = $_SESSION["user"]["student_id"];

// Ajouter un module avec texte
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_module"])) {
    $moduleName = trim($_POST["module_name"]);
    $text = trim($_POST["text"]);

    if (!empty($moduleName) && !empty($text)) {
        $stmt = $conn->prepare("INSERT INTO Module (module_name) VALUES (?)");
        $stmt->bind_param("s", $moduleName);
        $stmt->execute();
        $moduleId = $stmt->insert_id;

        $stmt2 = $conn->prepare("INSERT INTO Note (student_id, module_id, text) VALUES (?, ?, ?)");
        $stmt2->bind_param("iis", $studentId, $moduleId, $text);
        $stmt2->execute();
    }
}

// Modifier un module
if (isset($_POST["edit_module"])) {
    $noteId = $_POST["note_id"];
    $moduleName = $_POST["module_name"];
    $text = $_POST["text"];

    $stmt = $conn->prepare("UPDATE Module m JOIN Note n ON m.module_id = n.module_id SET m.module_name = ?, n.text = ? WHERE n.note_id = ? AND n.student_id = ?");
    $stmt->bind_param("ssii", $moduleName, $text, $noteId, $studentId);
    $stmt->execute();
}

// Supprimer un module
if (isset($_POST["delete_module"])) {
    $noteId = $_POST["note_id"];
    $stmt = $conn->prepare("DELETE FROM Note WHERE note_id = ? AND student_id = ?");
    $stmt->bind_param("ii", $noteId, $studentId);
    $stmt->execute();
}

$query = "SELECT n.note_id, m.module_name, n.text
          FROM Note n
          JOIN Module m ON n.module_id = m.module_id
          WHERE n.student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $studentId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Modules</title>
    <link rel="stylesheet" href="modules_style.css">
</head>
<body>
    <div class="module-container">
        <h1>My Modules</h1>

        <form method="POST" class="module-form">
            <input type="text" name="module_name" placeholder="Module Name" required>
            <textarea name="text" placeholder="Write something about this module..." required></textarea>
            <button type="submit" name="add_module">Add Module</button>
        </form>

        <div class="modules-grid">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="module-card">
                    <form method="POST" class="edit-form">
                        <input type="hidden" name="note_id" value="<?= $row['note_id']; ?>">
                        <input type="text" name="module_name" value="<?= htmlspecialchars($row['module_name']); ?>" required>
                        <textarea name="text" required><?= htmlspecialchars($row['text']); ?></textarea>
                        <button type="submit" name="edit_module">Edit</button>
                        <button type="submit" name="delete_module" onclick="return confirm('Delete this module?')">Delete</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
