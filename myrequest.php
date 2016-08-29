<?php
session_start();
require_once 'class.user.php';
$user_home = new USER();

if (!$user_home->is_logged_in()) {
    $user_home->redirect('index.php');
}

$stmt = $user_home->runQuery('SELECT * FROM tbl_users WHERE userID=:uid');
$stmt->execute(array(':uid' => $_SESSION['userSession']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$userid = $row['userID'];
$username = $row['userName'];

$db = new PDO('mysql:host=localhost;dbname=dbtest;charset=utf8mb4', 'root', 'root');
?>

<!DOCTYPE html>
<html class="no-js">

<head>
  <meta charset="UTF-8">
    <title>Accenture | Se déconnecter</title>
    <?php require 'header.inc.php'; ?>
</head>
<body>
  <?php require 'navbar.inc.php'; ?>
    <div class="container" id="con2">
        <h2 class="form-request-heading">Mes demandes</h2>
        <hr />
        <!-- <script>

        var array = holidays(2016);
        for (var i = 0; i < array.length; i++) {
            array[i] = document.write(array[i]);
        }
        </script> -->

        <div class="alert alert-info">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Valider</th>
                        <th>Message</th>
                    </tr>
                </thead>

                <tbody>
                <?php
                $stmt = $db->query('SELECT * FROM tbl_request WHERE userid = '.$userid.' ORDER BY id DESC');
                while ($request = $stmt->fetch()) {
                    echo '<tr>';
                    echo '<td width=100>'.$request['date'].'</td>';
                    echo '<td width=100>';
                    if ($request['validate'] == "Attendre") {
                        echo '<strong style="color:green">Attendre</strong>';
                    } else {
                        if ($request['validate'] == "Accepté") {
                            echo '<strong style="color:blue">Accepté</strong>';
                        } else {
                            echo '<strong style="color:red">Refusé</strong>';
                        }
                    }

                    // echo '<td>'.$request['validate'].'</td>';
                    echo '<td>'.$request['message'].'</td>';
                    echo '</tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- /container -->
    <?php require 'footer.inc.php'; ?>
    <?php require 'script.inc.php'; ?>
</body>
</html>
