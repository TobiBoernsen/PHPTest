<?php
// Startet eine neue oder eine bestehende Session
session_start();

// Definiert die Verbindungsinformationen zur Datenbank
$host = "localhost";
$user = "root";
$password = "";
$dbname = "testDB";

// Stellt eine Verbindung zur Datenbank her
$conn = new mysqli($host, $user, $password, $dbname);

// Überprüft, ob die Datenbankverbindung erfolgreich war
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// Initialisiert eine Variable für Nachrichten an den Benutzer
$message = '';

// Verarbeitet die Registrierung
if (isset($_POST['register']) && isset($_POST['password']) && isset($_POST['password_repeat'])) {
    // Speichert Benutzername und Passwörter aus dem Formular
    $username = $_POST['username'];
    $password = $_POST['password'];
    $password_repeat = $_POST['password_repeat'];

   // Überprüft die Passwortkriterien
   if (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/', $password)) {
    $message = "Passwort muss mindestens 8 Zeichen lang sein und Großbuchstaben, Kleinbuchstaben und Zahlen enthalten.";
} elseif ($password !== $password_repeat) {
    $message = "Die Passwörter stimmen nicht überein.";
} else {
    // Prüft, ob der Benutzername bereits existiert
    $checkUser = $conn->query("SELECT * FROM user WHERE username='$username'");
    if ($checkUser->num_rows > 0) {
        $message = "Benutzername ist bereits vergeben.";
    } else {
        // Fügt den neuen Benutzer zur Datenbank hinzu, wenn die Überprüfungen erfolgreich waren
        $password_md5 = md5($password); // Verschlüsselt das Passwort mit MD5
        $sql = "INSERT INTO user (username, password) VALUES ('$username', '$password_md5')";
        if ($conn->query($sql) === TRUE) {
            $message = "Neuer Benutzer erfolgreich registriert!";
        } else {
            $message = "Fehler bei der Registrierung: " . $conn->error;
    }
}

// Verarbeitet den Login
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    // Überprüft, ob der Benutzer in der Datenbank existiert
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
        /* Einfache Styling-Anweisungen für die Benutzeroberfläche */
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
        <!-- Registrierungsformular -->
        <form method="post">
            <input type="text" name="username" placeholder="Benutzername" required>
            <input type="password" name="password" placeholder="Passwort" required>
            <input type="password" name="password_repeat" placeholder="Passwort wiederholen" required>
            <button type="submit" name="register">Registrieren</button>
        </form>
        <p class="message"><?php echo $message; ?></p>
        <p class="toggle">Sind Sie schon registiert? <a href="index.php">Hier zum Login.</a></p>
<?php else: ?>
<!-- Login-Formular -->
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
```
