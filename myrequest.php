<?php
session_start();
require_once 'class.user.php';
$user_home = new USER();
$req = new USER();

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

<?php require 'header.inc.php'; ?>

<body>
  <?php require 'navbar.inc.php'; ?>
    <div class="container" id="con2">
        <h2 class="form-request-heading">My Requests</h2>
        <hr />
        <div class="alert alert-info">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Validate</th>
                        <th>Message</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $stmt = $db->prepare('SELECT * FROM tbl_request WHERE userid = ?');
                $stmt->execute(array($userid));
                $item = $stmt->fetch();
                $stmt = $db->query('SELECT * FROM tbl_request WHERE userid ORDER BY id DESC');
                while ($request = $stmt->fetch()) {
                    echo '<tr>';
                    echo '<td>'.$request['date'].'</td>';
                    echo '<td>'.$request['validate'].'</td>';
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
