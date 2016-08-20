<?php
session_start();
require_once 'class.user.php';
$user_home = new USER();
$req = new USER();

if(!$user_home->is_logged_in())
{
	$user_home->redirect('index.php');
}

$stmt = $user_home->runQuery("SELECT * FROM tbl_users WHERE userID=:uid");
$stmt->execute(array(":uid"=>$_SESSION['userSession']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if(isset($_POST['btn-request']))
{
	$date = trim($_POST['txtdate']);
	$message = trim($_POST['txtmessage']);
	
   	$code = md5(uniqid(rand()));

    // DEBUG
    $userid = $row['userID'];
    $username = $row['userName']; // TODO: Fix that later !!!
    $db = new PDO('mysql:host=localhost;dbname=dbtest;charset=utf8mb4', 'root', 'root');
    // -------------------
    
    $stmt = $db->prepare("INSERT INTO `tbl_request` (`date`, `message`, `userid`) VALUES (:date, :message, :userid);");
    $stmt->execute(array(":date"=>$date, ":message"=>$message, ":userid"=>$userid));
    
    // DEBUG
    // $affected_rows = $stmt->rowCount();
    // echo "Request succeed: ".$affected_rows." rows affected!";
    // ---------------

	
			$mail = '16accenture@gmail.com';
			$messages = "					
						Dear Madame,
						<br /><br />
						I am $username,Please confirm my request for the day $date<br/>
                        Notes: $message<br/>
						To CONFIRM the request , just click following link<br />
						<br /><br />
						<a href='http://localhost/teletravail/verify.php?id=$id&code=$code'>Click HERE to Activate :)</a>
						<br /><br />
						Thanks,";
						
			$subject = "Confirm Request Teletravail";
						
			$req->send_mail($mail,$messages,$subject);	
			$msg = "
					<div class='alert alert-success'>
						<button class='close' data-dismiss='alert'>&times;</button>
						<strong>Success!</strong>  We've sent an email to the HR department.
                    it is took 24h to reply, have a great day.
			  		</div>
					";
}
?>

<!DOCTYPE html>
<html class="no-js">
    
    <head>
        <title><?php echo $row['userEmail']; ?></title>
        <!-- Bootstrap -->
        <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
        <link href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
        <link href="assets/styles.css" rel="stylesheet" media="screen">
        <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        
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
            <div class="col-md-4">
				<?php if(isset($msg)) echo $msg;  ?>
      <form class="form-signin" method="post">
        <h2 class="form-signin-heading">Request Teletravail</h2><hr />
        <input type="date" class="input-block-level" placeholder="Date" name="txtdate" required />
 
        <input type="text" class="input-block-level" placeholder="Enter a message" name="txtmessage" required />
        
     	<hr />
        <button class="btn btn-large btn-primary" type="submit" name="btn-request">Submit</button>
        
      </form>
        </div>
   
        
            <div class="alert alert-default col-md-8">
              
                
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
                    $stmt = $db->prepare("SELECT * FROM tbl_request WHERE userid = ?");
                    $stmt->execute(array($userid));
                    $item = $stmt->fetch();
                    $stmt = $db->query('SELECT * FROM tbl_request WHERE userid ORDER BY id DESC');
                    while($request = $stmt->fetch()) 
                        {
                            echo '<tr>';
                            echo '<td>'. $request['date'] . '</td>';
                            echo '<td>'. $request['validate'] . '</td>';
                            echo '<td>'. $request['message'] . '</td>';
                            
                            echo '</td>';
                            echo '</tr>';
                        }
    
                        // $stmt = $db->query('SELECT * FROM tbl_request WHERE userid = 10');
                        // $row_count = $stmt->rowCount();
                        // echo $row_count.' rows selected';
                     ?>
                   </tbody>
                </table>
               
              
            </div>
        
           
        </div> <!-- /container -->
        <footer class="footer">
            
            <p class="pull-left"><img src="./images/tour-eiffel.png" alt="Tour Eiffel"> &copy; 2016 Accenture, Inc.</p>
            
        </footer>
        
        
        <script type="text/javascript" src="bootstrap/js/jquery-1.9.1.min.js"></script>
        <script type="text/javascript" src="bootstrap/js/moment.js"></script>
        <script type="text/javascript" src="bootstrap/js/transition.js"></script>
        <script type="text/javascript" src="bootstrap/js/collapse.js"></script>
        <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="bootstrap/js/bootstrap-datepicker.js"></script>
        <script type="text/javascript" src="assets/scripts.js"></script>
    </body>

</html>