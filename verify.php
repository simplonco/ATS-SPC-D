<?php
require_once 'class.user.php';
$user = new USER();

if (empty($_GET['id']) && empty($_GET['code'])) {
    $user->redirect('index.php');
}

if (isset($_GET['id']) && isset($_GET['code'])) {
    $id = base64_decode($_GET['id']);
    $code = $_GET['code'];

    $statusY = 'Y';
    $statusN = 'N';

    $stmt = $user->runQuery('SELECT userID,userStatus FROM tbl_users WHERE userID=:uID AND tokenCode=:code LIMIT 1');
    $stmt->execute(array(':uID' => $id, ':code' => $code));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($stmt->rowCount() > 0) {
        if ($row['userStatus'] == $statusN) {
            $stmt = $user->runQuery('UPDATE tbl_users SET userStatus=:status WHERE userID=:uID');
            $stmt->bindparam(':status', $statusY);
            $stmt->bindparam(':uID', $id);
            $stmt->execute();

            $msg = "
                   <div class='alert alert-success'>
                     <button class='close' data-dismiss='alert'>&times;</button>
                     <strong>WoW !</strong>  Votre compte est maintenant activé: <a href='index.php'>Connectez-vous ici</a>
                   </div>
                   ";
        } else {
            $msg = "
                   <div class='alert alert-error'>
                     <button class='close' data-dismiss='alert'>&times;</button>
                     <strong>désolé !</strong>  Votre compte est déjà activé: <a href='index.php'>Connectez-vous ici</a>
                   </div>
                   ";
        }
    } else {
        $msg = "
               <div class='alert alert-error'>
                 <button class='close' data-dismiss='alert'>&times;</button>
                 <strong>désolé !</strong>  Aucun compte trouvé: <a href='signup.php'>Signup here</a>
               </div>
               ";
    }
}

?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Accenture | eTélétravail</title>
    <?php require 'header.inc.php'; ?>
</head>
<body id="login">
    <div class="container" id="con2">
    <?php
    if (isset($msg)) {
        echo $msg;
    }
    ?>
    </div>
    <!-- /container -->
    <?php require 'footer.inc.php'; ?>
    <?php require 'script.inc.php'; ?>
</body>

</html>
