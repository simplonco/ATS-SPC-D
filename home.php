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


if (isset($_POST['btn-request'])) {
    $date = trim($_POST['txtdate']);
    $message = trim($_POST['txtmessage']);
    $code = md5(uniqid(rand()));
    $WeekNumber = date('W', strtotime($date));



    $userid = $row['userID'];
    $username = $row['userName'];
    $db = new PDO('mysql:host=localhost;dbname=dbtest;charset=utf8mb4', 'root', 'root');


    $stmt = $db->prepare('SELECT * FROM tbl_request WHERE weekNum=:weekNum');
    $stmt->execute(array(':weekNum' => $WeekNumber));
    $stmt->fetch(PDO::FETCH_ASSOC);

    if ($stmt->rowCount() > 1) {
        $msg = "
                <div class='alert alert-error'>
                <button class='close' data-dismiss='alert'>&times;</button>
                <strong>désolé !</strong>  Vous avez plus de 2 DEMANDE cette semaine, S'il vous plaît essayer une autre semaine!
                </div>
                ";
    } else {
        $stmt = $db->prepare('INSERT INTO `tbl_request` (`date`, `weekNum`, `message`, `userid`, `tokenCode`) VALUES (:date, :weekNum, :message, :userid, :tokenCode)');
        $stmt->execute(array(':date' => $date, ':weekNum' => $WeekNumber, ':message' => $message, ':userid' => $userid, ':tokenCode' => $code));

        $stmt = $db->query('SELECT LAST_INSERT_ID()');
        $lastId = $stmt->fetch(PDO::FETCH_NUM);
        $lastId = $lastId[0];
        $id = $lastId;
        $key = base64_encode($id);
        $id = $key;

        $statusY = "Accepté";
        $keyY = base64_encode($statusY);
        $statusY = $keyY;

        $statusN = "Refusé";
        $keyN = base64_encode($statusN);
        $statusN = $keyN;

        // $mail = $row['userEmail'];
        $mail = '16accenture@gmail.com';
        $messages = "
                    Cher Madame,
                    <br /><br />
                    Je suis <b>$username</b>,<br />merci de confirmer ma demande pour ce jour: $date<br /><br />
                    Notes: $message<br/><br/>
                    <font color=blue>Pour accepter la demande, merci de cliquer sur le lien suivant</font>
                    <br />
                    <br />
                    <a href='http://localhost/ATS-SPC-D/verifyreq.php?id=$id&code=$code&status=$statusY'>cliquer ici pour accepter</a>
                    <br /><br />
                    <font color=red>Pour refuser la demande, merci de cliquer sur le lien suivant</font><br />
                    <br />
                    <a href='http://localhost/ATS-SPC-D/verifyreq.php?id=$id&code=$code&status=$statusN'>cliquer ici pour refuser</a>
                    <br /><br />
                    Merci,";

        $subject = 'Confirmation de la demande de teletravail';

        $req->send_mail($mail, $messages, $subject);
        $msg = "
                <div class='alert alert-success'>
                    <button class='close' data-dismiss='alert'>&times;</button>
                    Votre demande a bien été envoyée au département RH.
                    Vous aurez une réponse dans 24h, bonne fin de journée.
                </div>
                ";
    }
}
?>

<!DOCTYPE html>
<html class="no-js">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Accenture | eTélétravail</title>
    <?php require 'header.inc.php'; ?>
</head>
<body>
      <?php require 'navbar.inc.php'; ?>
        <div class="container" id="con">
            <?php
            if (isset($msg)) {
                echo $msg;
            }
            ?>

            <form class="form-request" method="post">
                <h3 class="form-request-heading">Demande de Télétravail</h3>
                <hr />
                <h5 class="form-label">Choisissez la date pour votre demande:</h5>
                <input type="text" class="input-block-level" id="datepicker" placeholder="Choisissez une date" name="txtdate" required />
                <h5 class="form-label">Expliquez pourquoi vous demandez ce jour de  télétravail:</h5>
                <input type="text" class="input-block-level" placeholder="Entrez votre message" name="txtmessage" required />

                <!-- TODO: <label>
                <input type="checkbox" id="sms" value="p1">
                High Priority!! (send a sms!!)
                </label> -->
                <hr />
                <button class="btn btn-large btn-warning" type="submit" name="btn-request">Envoyer</button>
            </form>
        </div>
        <!-- /container -->

        <?php require 'footer.inc.php'; ?>
        <?php require 'script.inc.php'; ?>
</body>

</html>
