<?php
session_start();
$host = "localhost";
$user = "root";
$password = "";
$dbname = "testDB";
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

$message = '';

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $sql = "INSERT INTO user (username, password) VALUES ('$username', '$password')";
    if ($conn->query($sql) === TRUE) {
        $message = "Neuer Benutzer erfolgreich registriert!";
    } else {
        $message = "Fehler: " . $sql . "<br>" . $conn->error;
    }
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $sql = "SELECT * FROM user WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $_SESSION['username'] = $username;
        $message = "Erfolgreich eingeloggt!";
    } else {
        $message = "Falscher Benutzername oder Passwort!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login und Registrierung</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f0f0f0; }
        .container { width: 300px; padding: 20px; background-color: white; margin: 50px auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        form { margin-bottom: 20px; }
        input[type=text], input[type=password], button { width: 100%; padding: 10px; margin-top: 10px; }
        .message { color: #333; text-align: center; margin-bottom: 20px; }
        .toggle { text-align: center; }
    </style>
</head>
<body>

<div class="container">
    <?php if (isset($_GET['action']) && $_GET['action'] == 'register'): ?>
        <form method="post">
            <input type="text" name="username" placeholder="Benutzername" required>
            <input type="password" name="password" placeholder="Passwort" required>
            <button type="submit" name="register">Registrieren</button>
        </form>
        <p class="message"><?php echo $message; ?></p>
        <p class="toggle">Sind Sie schon registriert? <a href="index.php">Hier zum Login.</a></p>
    <?php else: ?>
        <form method="post">
            <input type="text" name="username" placeholder="Benutzername" required>
            <input type="password" name="password" placeholder="Passwort" required>
            <button type="submit" name="login">Login</button>
        </form>
        <p class="message"><?php echo $message; ?></p>
        <p class="toggle">Sind Sie nicht registriert? <a href="index.php?action=register">Hier zum Registrieren.</a></p>
    <?php endif; ?>
</div>

</body>
</html>