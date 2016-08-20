<?php
session_start();
require_once 'class.user.php';

$user_home = new USER();

if(!$user_home->is_logged_in())
{
	$user_home->redirect('index.php');
}


$stmt = $user_home->runQuery("SELECT * FROM tbl_users WHERE userID=:uid");
$stmt->execute(array(":uid"=>$_SESSION['userSession']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$dateError = $messageError = "";

    if(!empty($_POST)) 
    {
        $date        = checkInput($_POST['date']);
        $message     = checkInput($_POST['message']);
        
        
        if(empty($date)) 
        {
            $dateError = 'Ce champ ne peut pas être vide';
            $isSuccess = false;
        }
        if(empty($message)) 
        {
            $messageError = 'Ce champ ne peut pas être vide';
            $isSuccess = false;
        } 
        
        if($isSuccess) 
        {
            $db = Database::connect();
            $statement = $db->prepare("INSERT INTO request (date,message) values(?, ?)");
            $statement->execute(array($date,$message));
            Database::disconnect();
            header("Location: index.php");
        }
    }

    function checkInput($data) 
    {
      $data = trim($data);
      $data = stripslashes($data);
      $data = htmlspecialchars($data);
      return $data;
    }
?>


<!DOCTYPE html>
<html class="no-js">
    
    <head>
        <title><?php echo $row['userEmail']; ?></title>
        <!-- Bootstrap -->
        <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
        <link href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
        <link href="bootstrap/css/bootstrap-datepicker3.standalone.css" rel="stylesheet" media="screen">
        <link href="assets/styles.css" rel="stylesheet" media="screen">
        <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        
    </head>
    
    <body>
       <div class="container-fluid">
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
                                <a href="add.php">Request teletravail</a>
                            </li>
                            <!--<li class="dropdown">
                                <a href="#" data-toggle="dropdown" class="dropdown-toggle">Tutorials <b class="caret"></b>

                                </a>
                                <ul class="dropdown-menu" id="menu1">
                                    <li><a href="">PHP OOP</a></li>
                                    <li><a href="">PHP PDO</a></li>
                                </ul>
                            </li>-->
                            <li>
                                <a href="">My request</a>
                            </li>
                            
                            
                        </ul>
                    </div>
                    <!--/.nav-collapse -->
                </div>
            </div>
        </div>
        
            
                <h2 class="page-header">Add Day</h2>
                <?php if(isset($msg)) echo $msg;  ?>
                <form class="form-request" method="post">
                    <div class="form-group">
                        <label>Choose Date</label>
                            <div class="input-group date" id="sandbox-container">
                            <input type="text" class="form-control" id="date" name="date" required /><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                            </div>
                        </div>
                    <div class="form-group">
                        <label>Message</label>
                        <input type="text" class="form-control" id="message" name="message" placeholder="message" required />
                    </div>
                    <button type="submit" class="btn btn-default" name="btn-request">Submit</button>
                    
                </form>
            
       
           
        <footer class="footer">
            
            <p class="pull-left"><img src="./images/tour-eiffel.png" alt="Tour Eiffel"> &copy; 2016 Accenture, Inc.</p>
            
          </footer>
        
        <!--/.fluid-container-->
        <script type="text/javascript" src="bootstrap/js/jquery-1.9.1.min.js"></script>
        <script type="text/javascript" src="bootstrap/js/moment.js"></script>
        <script type="text/javascript" src="bootstrap/js/transition.js"></script>
        <script type="text/javascript" src="bootstrap/js/collapse.js"></script>
        <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="bootstrap/js/bootstrap-datepicker.js"></script>
        <script type="text/javascript" src="assets/scripts.js"></script>
        
        
    </body>

</html>