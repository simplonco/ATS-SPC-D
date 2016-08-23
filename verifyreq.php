<?php
require_once 'class.user.php';
$user = new USER();

if(empty($_GET['id']) && empty($_GET['code']))
{
	$user->redirect('index.php');
}


if(isset($_GET['id']) && isset($_GET['code']))
{
	$id = base64_decode($_GET['id']);
	$code = $_GET['code'];

	$statusY = "Y";
	$statusN = "N";

	$db = new PDO('mysql:host=localhost;dbname=dbtest;charset=utf8mb4', 'root', 'root');

	$stmt = $db->prepare("SELECT id,validate FROM tbl_request WHERE id=:id AND tokenCode=:code LIMIT 1");
	$stmt->execute(array(":id"=>$id,":code"=>$code));
	$ro=$stmt->fetch(PDO::FETCH_ASSOC);
	if($stmt->rowCount() > 0)
	{
		if($ro['validate']==$statusN)
		{
			$stmt = $db->prepare("UPDATE tbl_request SET validate=:validate WHERE id=:id");
			$stmt->bindparam(":validate",$statusY);
			$stmt->bindparam(":id",$id);
			$stmt->execute();

			$msg = "
		           <div class='alert alert-success'>
				   <button class='close' data-dismiss='alert'>&times;</button>
					  <strong>OK !</strong>  The REQUEST is Now Activated : <a href='index.php'>Thanks</a>
			       </div>
			       ";
		}
		else
		{
			$msg = "
		           <div class='alert alert-error'>
				   <button class='close' data-dismiss='alert'>&times;</button>
					  <strong>sorry !</strong>  Your REQUEST is allready Activated..  <a href='index.php'>OK</a>
			       </div>
			       ";
		}
	}
	else
	{
		$msg = "
		       <div class='alert alert-error'>
			   <button class='close' data-dismiss='alert'>&times;</button>
			   <strong>sorry !</strong>  No REQUEST Found : <a href='index.php'>Login here</a>
			   </div>
			   ";
	}
}

?>
<!DOCTYPE html>
<html>
  <head>
    <title>Confirm Request</title>
    <!-- Bootstrap -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
    <link href="assets/styles.css" rel="stylesheet" media="screen">

		 <script src="js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
  </head>
  <body id="login">
    <div class="container">
		<?php if(isset($msg)) { echo $msg; } ?>
    </div> <!-- /container -->
    <script src="vendors/jquery-1.9.1.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
  </body>
</html>
