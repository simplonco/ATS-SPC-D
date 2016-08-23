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
    <head>
        <title><?php echo $row['userEmail']; ?></title>
        <!-- Bootstrap -->
        <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
        <link href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
        <link href="assets/styles.css" rel="stylesheet" media="screen">

    </head>

    <body>
       <div>
        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container-fluid">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"> <span class="icon-bar"></span>
                     <span class="icon-bar"></span>
                     <span class="icon-bar"></span>
                    </a>

                    <a class="brand" href="#"><img src="./images/logo-accenture.png" alt="Logo Accenture"></a>
                    <div class="nav-collapse collapse">
                        <ul class="nav pull-right">
                            <li class="dropdown">
                                <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown"> <i class="icon-user"></i>
																	<?php echo $row['userEmail']; ?> <i class="caret"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
																			<a tabindex="-1" href="home.php">Add Request</a>
																			<a tabindex="-1" href="myrequest.php">My Requests</a>
																			<a tabindex="-1" href="logout.php">Logout</a>
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
        <div class="container">
						<h2 class="form-request-heading">My Requests</h2><hr />
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

                        echo '</td>';
                        echo '</tr>';
                    }

                      ?>
                   </tbody>
                </table>
            </div>
        </div> <!-- /container -->
        <footer class="footer">
            <p><img src="./images/tour-eiffel.png" alt="Tour Eiffel"> &copy; 2016 Accenture, Inc.</p>
        </footer>

        <script type="text/javascript" src="bootstrap/js/jquery-1.9.1.min.js"></script>
        <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="assets/scripts.js"></script>
    </body>
</html>
