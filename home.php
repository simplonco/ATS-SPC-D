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

if (isset($_POST['btn-request'])) {
    $date = trim($_POST['txtdate']);
    $message = trim($_POST['txtmessage']);
    $code = md5(uniqid(rand()));
    $WeekNumber = date('W', strtotime($date));

    $userid = $row['userID'];
    $username = $row['userName'];
    $db = new PDO('mysql:host=localhost;dbname=dbtest;charset=utf8mb4', 'root', 'root');

    $stmt = $db->prepare('SELECT * FROM tbl_request WHERE weekNum=:weekNum');
    $stmt->execute(array(':weekNum' => $WeekNumber));
    $stmt->fetch(PDO::FETCH_ASSOC);

    if ($stmt->rowCount() > 1) {
        $msg = "
                <div class='alert alert-error'>
                <button class='close' data-dismiss='alert'>&times;</button>
                <strong>Sorry !</strong>  You have more than 2 REQUEST this week , Please Try another WEEK!
                </div>
                ";
    } else {
        $stmt = $db->prepare('INSERT INTO `tbl_request` (`date`, `weekNum`, `message`, `userid`, `tokenCode`) VALUES (:date, :weekNum, :message, :userid, :tokenCode)');
        $stmt->execute(array(':date' => $date, ':weekNum' => $WeekNumber, ':message' => $message, ':userid' => $userid, ':tokenCode' => $code));

        $stmt = $db->query('SELECT LAST_INSERT_ID()');
        $lastId = $stmt->fetch(PDO::FETCH_NUM);
        $lastId = $lastId[0];
        $id = $lastId;
        $key = base64_encode($id);
        $id = $key;

        $statusY = "Accepted";
        $keyY = base64_encode($statusY);
        $statusY = $keyY;

        $statusN = "Not Accepted";
        $keyN = base64_encode($statusN);
        $statusN = $keyN;

        $mail = '16accenture@gmail.com';
        $messages = "
                    Dear Madame,
                    <br /><br />
                    I am $username,<br />Please confirm my request for the day: $date<br /><br />
                    Notes: $message<br/>
                    To CONFIRM the request , just click following link<br />
                    <br />
                    <a href='http://localhost/ATS-SPC-D/verifyreq.php?id=$id&code=$code&status=$statusY'>Click HERE to Confirm</a>
                    <br /><br />
                    To REFUSE the request , just click following link<br />
                    <br />
                    <a href='http://localhost/ATS-SPC-D/verifyreq.php?id=$id&code=$code&status=$statusN'>Click HERE to Refuse</a>
                    <br /><br />
                    Thanks,";

        $subject = 'Confirm Request Teletravail';

        $req->send_mail($mail, $messages, $subject);
        $msg = "
                <div class='alert alert-success'>
                    <button class='close' data-dismiss='alert'>&times;</button>
                    <strong>Success!</strong> We've sent an email to the HR department.
                    it is took 24h to reply, have a great day.
                </div>
                ";
    }
}
?>

<!DOCTYPE html>
<html class="no-js">

<?php require 'header.inc.php'; ?>

<body>
      <?php require 'navbar.inc.php'; ?>
        <div class="container" id="con">
            <?php
            if (isset($msg)) {
                echo $msg;
            }
            ?>

            <form class="form-request" method="post">
                <h3 class="form-request-heading">Request Teletravail</h3>
                <hr />
                <h5 class="form-label">Choose your request date:</h5>
                <input type="text" class="input-block-level" id="datepicker" placeholder="Choose a date" name="txtdate" readonly required />
                <h5 class="form-label">Explain why you request the day for teletravail:</h5>
                <input type="text" class="input-block-level" placeholder="Enter a message" name="txtmessage" required />

                <!-- TODO: <label>
                <input type="checkbox" id="sms" value="p1">
                High Priority!! (send a sms!!)
                </label> -->
                <hr />
                <button class="btn btn-large btn-warning" type="submit" name="btn-request">Submit</button>
            </form>
        </div>
        <!-- /container -->

        <?php require 'footer.inc.php'; ?>
        <?php require 'script.inc.php'; ?>
</body>

</html>
