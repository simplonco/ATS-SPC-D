<?php
session_start();
require_once 'class.user.php';
$user_home = new USER();
$admin = new USER();

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
    <title>Accenture | Administrateur</title>
    <?php require 'header.inc.php'; ?>
</head>

<body style="background:rgb(170, 17, 51)">
  <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
          <div class="container-fluid">
              <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"> <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </a>

              <a class="brand" id="app-name" href="#"><img src="./images/logo-accenture.png" alt="Logo Accenture"> eTélétravail Application </a>
              <div class="nav-collapse collapse">
                  <ul class="nav pull-right">
                      <li class="dropdown">
                          <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown"> <i class="icon-briefcase"></i>
                              <?php echo "Bienvenue Administrateur"; ?> <i class="caret"></i>
                          </a>
                          <ul class="dropdown-menu">
                              <li>
                                  <a tabindex="-1" href="admin.php?order=tbl_request.id&az=DESC">Toutes les demandes</a>
                                  <a tabindex="-1" href="logout.php">Se deconnecter</a>
                              </li>
                          </ul>
                      </li>
                  </ul>
                  <ul class="nav">
                      <li class="active">
                          <a href="http://www.accenture.com/fr-fr/" target="_blank">Accenture</a>
                      </li>

                      <li>
                          <a href="http://simplon.co/" target="_blank">SimplonCo</a>
                      </li>
                  </ul>
              </div>
              <!--/.nav-collapse -->
          </div>
      </div>
  </div>
    <div class="container" id="con2">
        <h2 class="form-request-heading">Toutes les demandes</h2>
        <hr />
        <div class="alert alert-info">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>UTILISATEURS
                        <a href="admin.php?order=tbl_users.userName&az=DESC"><i class="icon-arrow-down"></i></a>
                        <a href="admin.php?order=tbl_users.userName&az=ASC"><i class="icon-arrow-up"></i></a></th>
                        <th>DATE
                        <a href="admin.php?order=str_to_date(tbl_request.date,'%d-%m-%y')&az=DESC">
                        <i class="icon-arrow-down"></i></a>
                        <a href="admin.php?order=str_to_date(tbl_request.date,'%d-%m-%y')&az=ASC">
                        <i class="icon-arrow-up"></i></a>
                        </th>
                        <th>MESSAGE</th>
                        <th>VALIDER
                        <a href="admin.php?order=tbl_request.validate&az=DESC">
                        <i class="icon-arrow-down"></i></a>
                        <a href="admin.php?order=tbl_request.validate&az=ASC">
                        <i class="icon-arrow-up"></i></a>
                        </th>
                    </tr>
                </thead>

                <tbody>
                <?php
                if (empty($_GET['order']) && empty($_GET['az'])) {
                    $user->redirect("admin.php?order=tbl_request.id&az=DESC");
                }
                if (isset($_GET['order']) && isset($_GET['az'])){
                  $statusY = "Accepté";
                  $keyY = base64_encode($statusY);
                  $statusY = $keyY;

                  $statusN = "Refusé";
                  $keyN = base64_encode($statusN);
                  $statusN = $keyN;

                  $order = $_GET['order'];
                  $az = $_GET['az'];
                  // $order = "tbl_request.id";
                  // $az = "DESC";

                  $stmt = $db->query("SELECT tbl_request.id, tbl_request.date, tbl_request.message, tbl_request.validate, tbl_request.tokenCode As code, tbl_users.userName AS userN FROM tbl_request right JOIN tbl_users ON tbl_request.userid = tbl_users.userId ORDER BY $order $az");

                  while ($request = $stmt->fetch()) {
                    if (!empty($request['date'])) {
                        echo '<tr>';
                        echo '<td>'.$request['userN'].'</td>';
                        echo '<td width=100>'.$request['date'].'</td>';
                        echo '</td>';
                        echo '<td>'.$request['message'].'</td>';
                        echo '<td width=180>';
                        if ($request['validate'] == "Attendre") {
                            echo '<a class="btn btn-primary" id="btn-admin" href="verifyreq.php?id='.base64_encode($request['id']).'&code='.$request['code'].'&status='.$statusY.'"><span class="glyphicon glyphicon-pencil"></span> Confirmer</a>';
                            echo ' ';
                            echo '<a class="btn btn-danger" id="btn-admin" href="verifyreq.php?id='.base64_encode($request['id']).'&code='.$request['code'].'&status='.$statusN.'"><span class="glyphicon glyphicon-remove"></span> Refuser</a>';
                        } else {
                            if ($request['validate'] == "Accepté") {
                                echo '<strong style="color:blue">Accepté</strong>';
                            } else {
                                echo '<strong style="color:red">Refusé</strong>';
                            }
                        }
                        echo '</tr>';
                    }
                  }
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
