<?php
session_start();
require_once 'class.user.php';
$user = new USER();

if ($user->is_logged_in() != '') {
    $user->redirect('home.php');
}

if (isset($_POST['btn-submit'])) {
    $email = $_POST['txtemail'];

    $stmt = $user->runQuery('SELECT userID FROM tbl_users WHERE userEmail=:email LIMIT 1');
    $stmt->execute(array(':email' => $email));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($stmt->rowCount() == 1) {
        $id = base64_encode($row['userID']);
        $code = md5(uniqid(rand()));

        $stmt = $user->runQuery('UPDATE tbl_users SET tokenCode=:token WHERE userEmail=:email');
        $stmt->execute(array(':token' => $code, 'email' => $email));

        $message = "
                  <img src='http://www.accenture.com/t00010101T000000__w__/fr-fr/_acnmedia/Accenture/Dev/ComponentImages/logo-accenture.png' alt='Accenture'>
                  <br /><br />
                   Bonjour , $email
                   <br /><br />
                   Nous avons recu votre demande de réinitialisation de mot de passe,
                   cliquer sur le lien suivant, sinon ignorer ce mail,
                   <br /><br />
                   Cliquer sur le lien suivant pour réinitialiser votre mot de passe.
                   <br /><br />
                   <a href='http://localhost/ATS-SPC-D/resetpass.php?id=$id&code=$code'>Cliquer ici pour réinitialiser votre mot de passe</a>
                   <br /><br />
                   Merci.
                   ";
        $subject = 'Reinitialiser le mot de passe';

        $user->send_mail($email, $message, $subject);

        $msg = "<div class='alert alert-success'>
                <button class='close' data-dismiss='alert'>&times;</button>
                Nous vous avons envoyé un mail $email.
                Cliquez sur le lien du mail pour créer un nouveau mot de passe.
                </div>";
    } else {
        $msg = "<div class='alert alert-danger'>
                <button class='close' data-dismiss='alert'>&times;</button>
                <strong>Désolé!</strong>  Cet email n'existe pas.
                </div>";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
    <title>Accenture | Mot de passe oublié</title>
        <?php require 'header.inc.php'; ?>
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
</head>

<body id="login">
  <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
          <a class="brand" id="app-name" href="#"><img src="./images/logo-accenture.png" alt="Logo Accenture"> eTélétravail Application </a>
      </div>
  </div>

    <div class="container" id="con2">

        <form class="form-signin" method="post">
            <h3 class="form-signin-heading">Mot de passe oublié</h3>
            <hr />
            <?php
            if (isset($msg)) {
                echo $msg;
            } else {
            ?>
            <div class='alert alert-info'>
              Entrez votre adresse email. Vous recevrez un lien pour créerun nouveau mot de passe par mail.

            </div>
            <?php
            }
            ?>
            <input type="email" class="input-block-level" placeholder="Email address" name="txtemail" required />
            <hr />
            <button class="btn btn-danger btn-primary" type="submit" name="btn-submit">Créez un nouveau mot de passe</button>
        </form>
    </div>
    <!-- /container -->
    <?php require 'script.inc.php'; ?>
</body>

</html>
