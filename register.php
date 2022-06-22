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

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $kandidatUser = $_POST['username'];

    // ci sa nachadza ten google ucet uz v userovi
    $sql = 'SELECT id FROM users WHERE username=?';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$kandidatUser]);

    $occurrence = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($occurrence) == 0) {

        $stmt = $conn->prepare("INSERT INTO users (username, email, name, surname) VALUES (?,?,?,?)");
        $stmt->execute([$_POST['username'], $_POST['email'], $_POST['name'], $_POST['surname']]);

        $user_id = $conn->lastInsertId();

        $stmt = $conn->prepare("INSERT INTO accounts (user_id, type, password) VALUES (" . $conn->lastInsertId() . ", 'registracia', :password)");
        $psw_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt->bindParam(":password", $psw_hash);
        $stmt->execute();

        $stmt = $conn->prepare("INSERT INTO logins (account_id) VALUES (" . $conn->lastInsertId() . ")");
        $stmt->execute();

        session_start();
        $_SESSION['username'] = $_POST['username'];
        $_SESSION['name'] = $_POST['name'];
        $_SESSION['surname'] = $_POST['surname'];
        $_SESSION['type'] = 'registracia';

        header("Location: dashboard.php");
    }
    if (count($occurrence) > 0) {
        echo $kandidatUser . "is already in use. Try some other.";
    }
}
?>


<!DOCTYPE html>
<html lang="sk">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <link href="css/style.css" rel="stylesheet">
    <title>Assignment 3</title>
</head>

<body>
    <h1>Registracia</h1>
    <div class="container">

        <form action="register.php" method="post">
            <div class="container">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" placeholder="Enter username" name="username" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" placeholder="Enter name" name="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="surname">Surname</label>
                    <input type="text" placeholder="Enter surname" name="surname" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="email">Enter your email</label>
                    <input type="email" id="email" name="email" size="30" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" placeholder="Enter password" name="password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">Register</button>

                <div class="form-check">
                    <label>
                        <input type="checkbox" checked="checked" name="remember" class="form-check-input">Remember me
                    </label>
                </div>
            </div>
        </form>
        <div>
            <span>Already a member? <a href="index.php">Log in.</a></span>
        </div>
    </div>
</body>
</html>