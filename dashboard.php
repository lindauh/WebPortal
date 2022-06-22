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

session_start();

// ak nie sme prihlaseni presmeruje na prihlasenie sa
if (!isset($_SESSION['name'])) {
    header("Location: index.php");
} else {

    if ($_SESSION['type'] == 'google') {
        $stmt = $conn->prepare("SELECT time_stamp FROM logins 
                                JOIN accounts ON account_id = accounts.id 
                                WHERE account_id = :acc_id");
        $stmt->bindParam(":acc_id", $_SESSION['acc_id']);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $timestamps = $stmt->fetchAll();
    }

    if ($_SESSION['type'] == 'registracia') {

        $stmt = $conn->prepare("SELECT time_stamp FROM logins 
                                JOIN accounts ON account_id = accounts.id 
                                WHERE account_id = :acc_id");
        $stmt->bindParam(":acc_id", $_SESSION['user_id']);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $timestamps = $stmt->fetchAll();
    }

    $stmt = $conn->prepare("SELECT COUNT(logins.account_id), accounts.type FROM logins 
                                JOIN accounts ON logins.account_id=accounts.id 
                                WHERE accounts.type='google'");
    $googleCount = $stmt->execute();
    $googleCount = $stmt->fetch();

    $stmt = $conn->prepare("SELECT COUNT(logins.account_id), accounts.type FROM logins 
                                JOIN accounts ON logins.account_id=accounts.id 
                                WHERE accounts.type='registracia'");
    $stmt->execute();
    $registraciaCount = $stmt->fetch();
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
    <div class="container" id="dashContainer">
        <h3><?php echo $_SESSION['name']?>, you are logged in!</h3>

        <?php
        echo "You can log out here: ";
        ?>
        <a href="logout.php">Log out</a>
        <?php
        ?>

        <div id="buttonLogins">
            <button id="viewLogins" type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#exampleModal">View last logins</button>
        </div>

        <!-- Modalne okno pri prezerani logins -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Last logins</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table id="result-table" class="table">
                            <thead>
                                <tr>
                                    <th>Login time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($timestamps as $t) {
                                    echo "<tr><td>{$t['time_stamp']}</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                        <table id="result-table" class="table">
                            <thead>
                                <tr>
                                    <th>Statistics</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                echo "<tr><td>Form registration: " . $registraciaCount[0] . "</td></tr>";
                                echo "<tr><td>Google: " . $googleCount[0] . "</td></tr>";
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>