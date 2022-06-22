<?php

/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

// include your composer dependencies
require_once 'vendor/autoload.php';

$client = new Google\Client();
$client->setAuthConfig('client_secret');

$redirect_uri = 'https://xyz.php';

$client->addScope("email");
$client->addScope("profile");

$client->setRedirectUri($redirect_uri);
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
    <h1>Welcome back!</h1>
    <h3>Zápočet</h3>
    <div class="container">

        <form action="login.php" method="post">
            <div class="container">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" placeholder="Enter username" name="username" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="psw">Password</label>
                    <input type="password" placeholder="Enter password" name="password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">Login</button>

                <div class="form-check">
                    <label>
                        <input type="checkbox" checked="checked" name="remember" class="form-check-input">Remember me
                    </label>
                </div>
            </div>
        </form>

        <div class="container">
            <span>Don't have an account yet? <a href="register.php">Sign in</a></span>
            <br><br>
            <?php
            echo "<a href='" . $client->createAuthUrl() . "'>Google Login</a>";
            ?>
        </div>
    </div>
</body>
</html>