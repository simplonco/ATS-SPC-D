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

<?php require 'header.inc.php'; ?>

<body>
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
                          <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown"> <i class="icon-briefcase"></i>
                              <?php echo "Welcome Admin"; ?> <i class="caret"></i>
                          </a>
                          <ul class="dropdown-menu">
                              <li>
                                  <a tabindex="-1" href="myrequest.php">All Requests</a>
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
    <div class="container" id="con2">
        <h2 class="form-request-heading">All Request</h2>
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
                        <th>USERS</th>
                        <th>DATE</th>
                        <th>VALIDATE</th>
                        <th>MESSAGE</th>
                        <th>REPLY</th>
                    </tr>
                </thead>

                <tbody>
                <?php
                // $stmt = $db->query('SELECT * FROM tbl_request WHERE userid ORDER BY id DESC');

                $statusY = "Accepted";
                $keyY = base64_encode($statusY);
                $statusY = $keyY;

                $statusN = "Not Accepted";
                $keyN = base64_encode($statusN);
                $statusN = $keyN;

                $stmt = $db->query('SELECT tbl_request.id, tbl_request.date, tbl_request.message, tbl_request.validate, tbl_request.tokenCode As code, tbl_users.userName AS userN FROM tbl_request right JOIN tbl_users ON tbl_request.userid = tbl_users.userId ORDER BY tbl_request.id DESC');

                while ($request = $stmt->fetch()) {
                    echo '<tr>';
                    echo '<td>'.$request['userN'].'</td>';
                    echo '<td>'.$request['date'].'</td>';
                    echo '<td>'.$request['validate'].'</td>';
                    echo '<td>'.$request['message'].'</td>';

                    echo '<td width=175>';
                    echo '<a class="btn btn-primary" href="verifyreq.php?id='.base64_encode($request['id']).'&code='.$request['code'].'&status='.$statusY.'"><span class="glyphicon glyphicon-pencil"></span> Confirm</a>';
                    echo ' ';
                    echo '<a class="btn btn-danger" href="verifyreq.php?id='.base64_encode($request['id']).'&code='.$request['code'].'&status='.$statusN.'"><span class="glyphicon glyphicon-remove"></span> Refuse</a>';
                    echo '</td>';

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
