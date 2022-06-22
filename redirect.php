<?php
// pripojenie na databazu
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

require_once("config.php");

require_once 'vendor/autoload.php';

$client = new Google\Client();
$client->setAuthConfig('client_secret');


if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    $client->setAccessToken($token['access_token']);

    // get profile info
    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();
    $email =  $google_account_info->email;
    $name =  $google_account_info->name;
    $googleId = $google_account_info->getId();

    $usersUsername = "google_acc_" . $googleId;

    require_once("config.php");

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // ci sa nachadza ten google ucet uz v userovi
        $sql = 'SELECT id FROM users WHERE username=?';
        $stmt = $conn->prepare($sql);
        $stmt->execute([$usersUsername]);
        $occurrence = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($occurrence) == 0) {
            $stmt = $conn->prepare("INSERT INTO users (username, email, name) VALUES (:username, :email, :name)");
            $stmt->bindParam(":username", $usersUsername);
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":email", $email);
            $stmt->execute();

            $user_id = $conn->lastInsertId();

            $stmt = $conn->prepare("INSERT INTO accounts (user_id, type, google_id) VALUES (" . $conn->lastInsertId() . ", 'google', '" . $googleId . "')");
            $stmt->execute();

            $stmt = $conn->prepare("INSERT INTO logins (account_id) VALUES (" . $conn->lastInsertId() . ")");
            $stmt->execute();

        } 
        if (count($occurrence) > 0) {
            $sql = 'SELECT id FROM users WHERE username=?';
            $stmt = $conn->prepare($sql);
            $stmt->execute([$usersUsername]);
            $googleUserId = $stmt->fetch();

            $sql = 'SELECT accounts.id FROM accounts JOIN users ON accounts.user_id = users.id WHERE accounts.user_id=?';
            $stmt = $conn->prepare($sql);
            $stmt->execute([$googleUserId[0]]);
            $googleUserAccId = $stmt->fetch();

            echo "google account id:  ";
            var_dump($googleUserAccId);

            $stmt = $conn->prepare("INSERT INTO logins (account_id) VALUES (?)");
            $stmt->execute([$googleUserAccId[0]]);
        }

        $stmt = $conn->prepare("SELECT accounts.id FROM accounts JOIN users ON accounts.user_id = users.id WHERE users.username = :usernamee");
        $stmt->bindParam(":usernamee", $usersUsername);
        $stmt->execute();
        $accountId = $stmt->fetch();


        session_start();
        $_SESSION['username'] = $usersUsername;
        $_SESSION['name'] = $name;
        $_SESSION['acc_id'] = $accountId["id"];
        $_SESSION['type'] = 'google';

        header("Location: dashboard.php");
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
}
