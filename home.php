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
		$WeekNumber = date("W", strtotime($date));

    $userid = $row['userID'];
    $username = $row['userName'];
    $db = new PDO('mysql:host=localhost;dbname=dbtest;charset=utf8mb4', 'root', 'root');

		$stmt = $db->prepare("SELECT * FROM tbl_request WHERE weekNum=:weekNum");
		$stmt->execute(array(":weekNum"=>$WeekNumber));
		$stmt->fetch(PDO::FETCH_ASSOC);

		if($stmt->rowCount() > 1)
			{
				$msg = "
							<div class='alert alert-error'>
						<button class='close' data-dismiss='alert'>&times;</button>
							<strong>Sorry !</strong>  You have more than 2 REQUEST this week , Please Try another day
						</div>
						";
			}
			else
				{
		    $stmt = $db->prepare("INSERT INTO `tbl_request` (`date`, `weekNum`, `message`, `userid`, `tokenCode`) VALUES (:date, :weekNum, :message, :userid, :tokenCode)");
		    $stmt->execute(array(":date"=>$date, ":weekNum"=>$WeekNumber, ":message"=>$message, ":userid"=>$userid, ":tokenCode"=>$code));


		    // DEBUG
		    //  $affected_rows = $stmt->rowCount();
		    //  echo "Request succeed: ".$affected_rows." rows affected!";
		    // ---------------

				$stmt = $db->query("SELECT LAST_INSERT_ID()");
				$lastId = $stmt->fetch(PDO::FETCH_NUM);
				$lastId = $lastId[0];
				$id = $lastId;
				$key = base64_encode($id);
				$id = $key;

					$mail = '16accenture@gmail.com';
					$messages = "
								Dear Madame,
								<br /><br />
								I am $username,<br />Please confirm my request for the day: $date<br/><br />
		                        Notes: $message<br/>
								To CONFIRM the request , just click following link<br />
								<br /><br />
								<a href='http://localhost/teletravail/verifyreq.php?id=$id&code=$code'>Click HERE to Activate :)</a>
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
		}
?>

<!DOCTYPE html>
<html class="no-js">
    <head>
        <title><?php echo $row['userEmail']; ?></title>
        <!-- Bootstrap -->
        <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
        <link href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
				 <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
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
        <div class="container" id="con">

						<?php if(isset($msg)) echo $msg;  ?>

			      <form class="form-request" method="post">
			        <h2 class="form-request-heading">Request Teletravail</h2>
							<hr />
			        <input type="text" class="input-block-level" id="datepicker" placeholder="Choose a date" name="txtdate" required />

			        <input type="text" class="input-block-level" placeholder="Enter a message" name="txtmessage" required />

							<!-- <label>
					      <input type="checkbox" id="sms" value="p1">
					      High Priority!! (send a sms!!)
					    </label> -->
			     		<hr />
			        <button class="btn btn-large btn-primary" type="submit" name="btn-request">Submit</button>

			      </form>

        </div> <!-- /container -->
        <footer class="footer">
            <p><img src="./images/tour-eiffel.png" alt="Tour Eiffel"> &copy; 2016 Accenture, Inc.</p>
        </footer>

        <script type="text/javascript" src="bootstrap/js/jquery-1.9.1.min.js"></script>
				<!-- <script src="https://code.jquery.com/jquery-1.12.4.js"></script> -->
				<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
        <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="assets/scripts.js"></script>

		</body>
</html>
