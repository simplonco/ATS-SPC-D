<?php
require_once 'class.user.php';
$user = new USER();

if (empty($_GET['id']) && empty($_GET['code']) && empty($_GET['status'])) {
    $user->redirect('index.php');
}

if (isset($_GET['id']) && isset($_GET['code']) && isset($_GET['status'])) {
    $id = base64_decode($_GET['id']);
    $code = $_GET['code'];
    $status = base64_decode($_GET['status']);

    $statusW = "Waiting";

    $close = 'JavaScript:window.close()';

    $db = new PDO('mysql:host=localhost;dbname=dbtest;charset=utf8mb4', 'root', 'root');

    $stmt = $db->prepare('SELECT id,validate FROM tbl_request WHERE id=:id AND tokenCode=:code LIMIT 1');
    $stmt->execute(array(':id' => $id, ':code' => $code));
    $ro = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($stmt->rowCount() > 0) {
        if ($ro['validate']==$statusW) {
            $stmt = $db->prepare('UPDATE tbl_request SET validate=:validate WHERE id=:id');
            $stmt->bindparam(':validate', $status);
            $stmt->bindparam(':id', $id);
            $stmt->execute();

            $msg = "
                    <div class='alert alert-success'>
                      <button class='close' data-dismiss='alert'>&times;</button>
                      <strong>OK !</strong>  The REQUEST is Now have a reply.. <a href=$close>Close</a>
                    </div>
                   ";
        } else {
            $msg = "
                   <div class='alert alert-error'>
                     <button class='close' data-dismiss='alert'>&times;</button>
                     <strong>sorry !</strong>  Your REQUEST is allready Activated.. <a href=$close>Close</a>
                   </div>
                   ";
        }
    } else {
        $msg = "
               <div class='alert alert-error'>
                 <button class='close' data-dismiss='alert'>&times;</button>
                 <strong>sorry !</strong>  No REQUEST Found.. <a href=$close>Close</a>
               </div>
               ";
    }
}

?>
<!DOCTYPE html>
<html>

<?php require 'header.inc.php'; ?>

<body id="login">
    <div class="container">
    <?php
    if (isset($msg)) {
        echo $msg;
    }
    ?>
    </div>
    <!-- /container -->
    <?php require 'footer.inc.php'; ?>
    <?php require 'script.inc.php'; ?>
</body>

</html>
