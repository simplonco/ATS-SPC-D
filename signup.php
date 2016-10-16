<?php
session_start();
require_once 'class.user.php';

$reg_user = new USER();

if ($reg_user->is_logged_in() != '') {
    $reg_user->redirect('home.php');
}

if (isset($_POST['btn-signup'])) {
    $uname = trim($_POST['txtuname']);
    $email = trim($_POST['txtemail']);
    $upass = trim($_POST['txtpass']);
    $code = md5(uniqid(rand()));

    $stmt = $reg_user->runQuery('SELECT * FROM tbl_users WHERE userEmail=:email_id');
    $stmt->execute(array(':email_id' => $email));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($stmt->rowCount() > 0) {
        $msg = "
                <div class='alert alert-error'>
                <button class='close' data-dismiss='alert'>&times;</button>
                  <strong>désolé !</strong>  email existe déjà, S'il vous plaît essayer un autre.
                </div>
                ";
    } else {
        if ($reg_user->register($uname, $email, $upass, $code)) {
            $id = $reg_user->lasdID();
            $key = base64_encode($id);
            $id = $key;

            $message = "
                        <img src='http://www.accenture.com/t00010101T000000__w__/fr-fr/_acnmedia/Accenture/Dev/ComponentImages/logo-accenture.png' alt='Accenture'>
                        <br /><br />
                        Bonjour $uname,
                        <br /><br />
                        Bienvenue a la demande de teletravail Accenture!<br/>
                        Pour terminer votre inscription s'il vous plait, cliquez simplement sur le lien suivant<br/>
                        <br /><br />
                        <a href='http://localhost/ATS-SPC-D/verify.php?id=$id&code=$code'>Cliquez ici pour activer.</a>
                        <br /><br />
                        Merci,";

            $subject = 'Confirmer inscription';

            $reg_user->send_mail($email, $message, $subject);
            $msg = "
                    <div class='alert alert-success'>
                        <button class='close' data-dismiss='alert'>&times;</button>
                        Nous avons envoyé un courriel à $email.
                        S'il vous plait cliquer sur le lien de confirmation dans l'e-mail pour créer votre compte.
                    </div>
                    ";
        } else {
            echo 'désolé , Requête n a pas pu exécuter...';
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Accenture | S'enregistrer</title>
        <?php require 'header.inc.php'; ?>
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script src="js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
</head>

<body id="login">
  <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
          <a class="brand" id="app-name" href="#"><img src="./images/logo-accenture.png" alt="Logo Accenture"> eTélétravail Application </a>
      </div>
  </div>

    <div class="container" id="con2">
        <?php if (isset($msg)) {
            echo $msg;
        } ?>
        <form class="form-signin" method="post">

            <h2 class="form-signin-heading">S'enregistrer</h2>
            <hr />
            <input type="text" class="input-block-level" placeholder="Nom d'utilisateur" name="txtuname" required />
            <input type="email" class="input-block-level" placeholder="Adresse e-mail" name="txtemail" required />
            <input type="password" class="input-block-level" placeholder="Mot de passe" name="txtpass" required />
            <hr />
            <button class="btn btn-large btn-primary" type="submit" name="btn-signup">S'enregistrer</button>
            <a href="index.php" style="float:right;" class="btn btn-large">Se connecter</a>
        </form>
    </div>
    <!-- /container -->
    <?php require 'footer.inc.php'; ?>
    <?php require 'script.inc.php'; ?>
</body>

</html>
