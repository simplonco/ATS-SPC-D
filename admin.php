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

// localhost db configuration
$db = new PDO('mysql:host=localhost;dbname=dbtest;charset=utf8mb4', 'root', 'root');

$nombre_de_msg_par_page = 11; // On met dans une variable le nombre de lines qu'on veut par page

// On récupère le nombre total de lines
$reponse=$db->query('SELECT COUNT(*) AS contenu FROM tbl_request');
$total_messages = $reponse->fetch();
$nombre_messages =$total_messages['contenu'];


// on détermine le nombre de pages
$nb_pages = ceil($nombre_messages / $nombre_de_msg_par_page);
// Maintenant, on va afficher les messages
// ---------------------------------------

if (isset($_GET['page']))
{
    $page = $_GET['page']; // On récupère le numéro de la page indiqué dans l'adresse (livredor.php?page=4)
}
else // La variable n'existe pas, c'est la première fois qu'on charge la page
{
    $page = 1; // On se met sur la page 1 (par défaut)
}

// On calcule le numéro du premier message qu'on prend pour le LIMIT de MySQL
$premierMessageAafficher = ($page - 1) * $nombre_de_msg_par_page;

?>

<!DOCTYPE html>
<html class="no-js">


<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
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
                                  <a tabindex="-1" href="admin.php?order=tbl_request.id&az=DESC&page=1">Toutes les demandes</a>
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
        <p align="right"><a class="btn btn-warning" href="generate_pdf.php" target="_blank">Generate PDF</a></p>
        <hr />
        <div class="alert alert-info">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Utilisateurs
                        <a href="admin.php?order=tbl_users.userName&az=DESC&page=<?php echo $page;?>"><i class="icon-arrow-down"></i></a>
                        <a href="admin.php?order=tbl_users.userName&az=ASC&page=<?php echo $page;?>"><i class="icon-arrow-up"></i></a></th>
                        <th>Date
                        <a href="admin.php?order=str_to_date(tbl_request.date,'%d-%m-%y')&az=DESC&page=<?php echo $page;?>">
                        <i class="icon-arrow-down"></i></a>
                        <a href="admin.php?order=str_to_date(tbl_request.date,'%d-%m-%y')&az=ASC&page=<?php echo $page;?>">
                        <i class="icon-arrow-up"></i></a>
                        </th>
                        <th>Message</th>
                        <th>Valider
                        <a href="admin.php?order=tbl_request.validate&az=DESC&page=<?php echo $page;?>">
                        <i class="icon-arrow-down"></i></a>
                        <a href="admin.php?order=tbl_request.validate&az=ASC&page=<?php echo $page;?>">
                        <i class="icon-arrow-up"></i></a>
                        </th>
                    </tr>
                </thead>

                <tbody>
                <?php
                if (empty($_GET['order']) && empty($_GET['az'])) {
                    $user->redirect("admin.php?order=tbl_request.id&az=DESC&page=<?php echo $page;?>");
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

                  $stmt = $db->query("SELECT tbl_request.id, tbl_request.date, tbl_request.message, tbl_request.validate, tbl_request.tokenCode As code, tbl_users.userName AS userN FROM tbl_request right JOIN tbl_users ON tbl_request.userid = tbl_users.userId ORDER BY $order $az LIMIT $premierMessageAafficher ,$nombre_de_msg_par_page");

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
            <div align="center">
              <?php
              // Puis on fait une boucle pour écrire les liens vers chacune des pages
              echo 'Page : ';
              for ($i = 1 ; $i <= $nb_pages ; $i++)
              {
                  echo '<a href="admin.php?order=tbl_request.id&az=DESC&page=' . $i . '">' . $i . '</a> ';
              }
              ?>
            </div>
        </div>
    </div>

    <!-- /container -->
    <?php require 'footer.inc.php'; ?>
    <?php require 'script.inc.php'; ?>
</body>
</html>
