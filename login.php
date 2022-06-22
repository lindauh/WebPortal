<?php
// pripojenie na databazu
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

require_once("config.php");


try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}


if (isset($_POST['username'])) {
    $stmt = $conn->prepare("SELECT * FROM users 
                            JOIN accounts ON users.id = accounts.user_id 
                            WHERE username = :username AND `type` = 'registracia'");

   if (password_verify($_POST['password'], $user['password'])) {
        $stmt = $conn->prepare("INSERT INTO logins (account_id) VALUES (" . $user["id"] . ")");
        $stmt->execute();

        session_start();
        
        $_SESSION['username'] = $user['username'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['surname'] = $user['surname'];
        $_SESSION['user_id'] = $user["id"];
        $_SESSION['type'] = 'registracia';
 
        header("Location: dashboard.php");
   }/* else {
        echo 'Invalid password!';
    }*/
}
