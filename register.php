
<?php
include 'config.php';
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    if (strlen($username) < 3 || strlen($password) < 4) {
        $error = 'Username must be at least 3 characters and password at least 4 characters.';
    } else {
        $stmt = $conn->prepare('SELECT id FROM users WHERE username=?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows == 0) {
            $stmt->close();
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
            $stmt->bind_param('ss', $username, $hash);
            if ($stmt->execute()) {
                $success = 'Account created! You can now <a href="login.php">Login</a>.';
            } else {
                $error = 'Registration failed.';
            }
        } else {
            $error = 'Username already taken!';
        }
        $stmt->close();
    }
}
?>
<html>
<head>
    <title>Register | TO DO</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<div class="container">
    <h1>Register</h1>
    <?php if ($error) echo "<div class='error'>$error</div>"; ?>
    <?php if ($success) echo "<div class='success'>$success</div>"; ?>
    <form method="post">
        <input type="text" name="username" placeholder="Username" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>
        <input type="submit" value="Register">
    </form>
    <div><a class="link" href="login.php">Login</a></div>
</div>
</body>
</html>
