<?php
/**
 * Created by PhpStorm.
 * User: KoDusk
 * Date: 3/18/2018
 * Time: 4:09 PM
 */

include('includes/site.settings.php');
include('includes/functions.php');
include('includes/blog.php');

session_start();
if(!$_SESSION['LoggedIn'] == true){
    header('location: login.php');
}

$email = stripslashes(isset($_POST['email']) ? $_POST['email'] : '');
$first_name = stripslashes(isset($_POST['first_name']) ? $_POST['first_name'] : '');
$last_name = stripslashes(isset($_POST['last_name']) ? $_POST['last_name'] : '');
$password = stripslashes(isset($_POST['password']) ? $_POST['password'] : '');
$confirm_password = stripslashes(isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '');

if(isset($_POST['upload'])){
    $errors = array();

    $target_dir = "img/uploads/";
    $target_file_time = strtotime("now").'_';
    $target_file = $target_dir . basename($target_file_time.$_FILES["avatar"]["name"]);
    $image = $target_file_time.$_FILES["avatar"]["name"];

    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["avatar"]["tmp_name"]);
    $image_width = $check[0];
    $image_height = $check[1];

    if ($image_width > 60 && $image_height > 60) {
        $errors [] = "Image size is bigger than 60x60px";
    }

    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
        $errors [] = "Only JPG, JPEG, PNG & GIF files are allowed";
    }

    if (file_exists($target_file)) {
        $errors [] = "File name already exists";
    }

    if ($_FILES["avatar"]["size"] > 500000) {
        $errors [] = "Your file is too large.";
    }

    if (count($errors) == 0) {
        updateAvatar($_SESSION['uid'], $image);
        if (!move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
            $errors [] = "There was an error uploading your file.";
        }
    }
}

if(isset($_POST['submit'])){
    $errors = array();

    if(!empty($email)){
        if(strlen($email) < 3 || strlen($email) > 50){
            $errors [] = "Please use a valid email address!";
        }
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $errors [] = "Please use a valid email address!";
        }
    }

    if(!empty($first_name)) {
        if (strlen($first_name) < 3 || strlen($first_name) > 50) {
            $errors [] = "Please use a valid first name!";
        }
        if(!ctype_alpha($first_name)){
            $errors [] = "Please use a valid first name!";
        }
    }

    if(!empty($last_name)) {
        if (strlen($last_name) < 3 || strlen($last_name) > 50) {
            $errors [] = "Please use a valid last name!";
        }
        if(!ctype_alpha($last_name)){
            $errors [] = "Please use a valid last name!";
        }
    }

    if(!empty($password)) {
        if(strlen($password) < 3 || strlen($password) > 50){
            $errors [] = "Please use a valid password!";
        }

        if ($password != $confirm_password) {
            $errors [] = "Passwords didn't match!";
        }
    }

    if(count($errors) == 0){
        editUser($_SESSION['uid'], $email, $first_name, $last_name, null, $password, $confirm_password);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?=$title_prefix.' - Profile'; ?></title>

    <!-- Bootstrap core CSS-->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom fonts for this template-->
    <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- Page level plugin CSS-->
    <link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="css/sb-admin.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
</head>

<body class="fixed-nav sticky-footer bg-dark" id="page-top">

<?php include('includes/views/nav.php'); ?>

<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumbs-->
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="#"><?=$title_prefix; ?></a>
            </li>
            <li class="breadcrumb-item active">Profile</li>
        </ol>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">Edit Profile</div>
                    <div class="card-body">
                        <div class="row">
                            <!-- left column -->
                            <div class="col-md-3">
                                <div class="text-center">
                                    <form class="form-horizontal" action="<?=$_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label for="avatar"><img src="img/<?=getAvatar($_SESSION['uid']);?>" alt="avatar"> <br/>Upload New Avatar (60x60)</label>
                                            <input class="form-control" id="avatar" name="avatar" type="file">
                                        </div>
                                        <div class="form-group">
                                            <input type="submit" class="btn btn-primary" name="upload" value="Upload">
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- edit form column -->
                            <div class="col-md-9">
                                <?php
                                if(isset($errors)){
                                    if(count($errors)){
                                        echo "<div class=\"alert alert-danger alert-dismissable\"><a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">×</a>This form contains " . count($errors) . " errors<br/><div id=\"error_box\">";
                                        foreach($errors as $error){
                                            echo "<div id=\"error_explanation\"><ul><li>$error</li></ul></div>";
                                        }
                                        echo "</div></div>";
                                    }
                                }
                                ?>
                                <h3>Personal info</h3>

                                <form class="form-horizontal" action="<?=$_SERVER['PHP_SELF'];?>" method="post">
                                    <div class="form-group">
                                        <label for="email">Email address</label>
                                        <input class="form-control" id="email" name="email" type="email" aria-describedby="emailHelp" placeholder="<?=showUser($_SESSION['uid'])->email;?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="password">First Name</label>
                                        <input class="form-control" id="password" name="first_name" type="text" placeholder="<?=showUser($_SESSION['uid'])->first_name;?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="password">Last Name</label>
                                        <input class="form-control" id="password" name="last_name" type="text" placeholder="<?=showUser($_SESSION['uid'])->last_name;?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input class="form-control" id="password" name="pass" type="password" placeholder="Password">
                                    </div>
                                    <div class="form-group">
                                        <label for="password">Confirm Password</label>
                                        <input class="form-control" id="password" name="confirm_pass" type="password" placeholder="Confirm Password">
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-8">
                                            <input type="submit" class="btn btn-primary" name="submit" value="Save Changes">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.container-fluid-->
    <!-- /.content-wrapper-->
    <footer class="sticky-footer">
        <div class="container">
            <div class="text-center">
                <small>Copyright © <a href="http://www.codycodes.com.au/" target="_blank">Cody Codes</a> 2018</small>
            </div>
        </div>
    </footer>
    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fa fa-angle-up"></i>
    </a>
    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="<?=$_SERVER['PHP_SELF'];?>?logout">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <!-- Page level plugin JavaScript-->
    <script src="vendor/chart.js/Chart.min.js"></script>
    <script src="vendor/datatables/jquery.dataTables.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.js"></script>
    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin.min.js"></script>
    <!-- Custom scripts for this page-->
    <script src="js/sb-admin-datatables.min.js"></script>
    <script src="js/sb-admin-charts.min.js"></script>
</div>
</body>
</html>
