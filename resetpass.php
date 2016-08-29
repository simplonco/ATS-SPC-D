<?php
require_once 'class.user.php';
$user = new USER();

if (empty($_GET['id']) && empty($_GET['code'])) {
    $user->redirect('index.php');
}

if (isset($_GET['id']) && isset($_GET['code'])) {
    $id = base64_decode($_GET['id']);
    $code = $_GET['code'];

    $stmt = $user->runQuery('SELECT * FROM tbl_users WHERE userID=:uid AND tokenCode=:token');
    $stmt->execute(array(':uid' => $id, ':token' => $code));
    $rows = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($stmt->rowCount() == 1) {
        if (isset($_POST['btn-reset-pass'])) {
            $pass = $_POST['pass'];
            $cpass = $_POST['confirm-pass'];

            if ($cpass !== $pass) {
                $msg = "<div class='alert alert-block'>
                        <button class='close' data-dismiss='alert'>&times;</button>
                        <strong>Sorry!</strong>  Mot de passe incorrect.
                        </div>";
            } else {
                $password = md5($cpass);
                $stmt = $user->runQuery('UPDATE tbl_users SET userPass=:upass WHERE userID=:uid');
                $stmt->execute(array(':upass' => $password, ':uid' => $rows['userID']));

                $msg = "<div class='alert alert-success'>
                        <button class='close' data-dismiss='alert'>&times;</button>
                        Mot de passe changé.
                        </div>";
                header('refresh:5;index.php');
            }
        }
    } else {
        $msg = "<div class='alert alert-success'>
                <button class='close' data-dismiss='alert'>&times;</button>
                Informations incorrectes, merci de réessayer
                </div>";
    }
}

?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
    <title>Accenture | Réinitialiser le mot de passe</title>
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
    <div class="container" id="con2>
        <div class='alert alert-success'>
            <strong>Bonjour !</strong>
            <?php echo $rows['userName'] ?> vous êtes ici pour réinitialiser votre mot de passe oublié.
        </div>
        <form class="form-signin" method="post">
            <h3 class="form-signin-heading">Réinitialiser le mot de passe.</h3>
            <hr />
            <?php
            if (isset($msg)) {
                echo $msg;
            }
            ?>
            <input type="password" class="input-block-level" placeholder="Nouveau mot de passe" name="pass" required />
            <input type="password" class="input-block-level" placeholder="Confirmer le nouveau mot de passe" name="confirm-pass" required />
            <hr />
            <button class="btn btn-large btn-primary" type="submit" name="btn-reset-pass">Réinitialisez votre mot de passe</button>
        </form>
    </div>
    <!-- /container -->
    <?php require 'footer.inc.php'; ?>
    <?php require 'script.inc.php'; ?>
</body>

</html>
