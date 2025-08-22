<?php
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];

// Add task
if (isset($_POST['add-task'])) {
    $desc = trim($_POST['desc']);
    if ($desc) {
        $stmt = $conn->prepare('INSERT INTO tasks (user_id, description) VALUES (?, ?)');
        $stmt->bind_param('is', $user_id, $desc);
        $stmt->execute();
        $stmt->close();
    }
    header('Location: index.php');
    exit;
}
// Complete task
if (isset($_GET['complete'])) {
    $tid = intval($_GET['complete']);
    $stmt = $conn->prepare('UPDATE tasks SET completed = 1 WHERE id = ? AND user_id = ?');
    $stmt->bind_param('ii', $tid, $user_id);
    $stmt->execute();
    $stmt->close();
    header('Location: index.php');
    exit;
}
// Delete task
if (isset($_GET['delete'])) {
    $tid = intval($_GET['delete']);
    $stmt = $conn->prepare('DELETE FROM tasks WHERE id = ? AND user_id = ?');
    $stmt->bind_param('ii', $tid, $user_id);
    $stmt->execute();
    $stmt->close();
    header('Location: index.php');
    exit;
}

// Fetch user tasks
$stmt = $conn->prepare('SELECT id, description, completed FROM tasks WHERE user_id = ? ORDER BY created_at DESC');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$tasks = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<html>
<head>
    <title>TO DO LIST</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<div class="container">
    <h1>TO DO LIST</h1>
    <div style="text-align:right"><a class="logout" href="logout.php">Logout</a></div>
    <form method="post" style="margin:1.3em 0;display:flex;gap:6px;">
        <input type="text" name="desc" placeholder="New task..." required>
        <input type="submit" name="add-task" value="Add">
    </form>
    <?php
    foreach ($tasks as $task) {
        $class = $task['completed'] ? 'task completed' : 'task';
        echo "<div class='$class'>";
        echo "<div class='desc'>" . htmlspecialchars($task['description']) . "</div>";
        if (!$task['completed']) {
            echo "<form method='get' style='display:inline'><button name='complete' value='{$task['id']}' title='Done!' style='background:transparent;border:0;cursor:pointer;font-size:1.3em;'>&#9989;</button></form>"; // tick
        }
        echo "<a href='?delete={$task['id']}' title='Delete' style='color:#c94c4c;font-size:1.15em;' onclick='return confirm('Delete this task?')'>&#10060;</a>";
        echo "</div>";
    }
    ?>
</div>
</body>
</html>
