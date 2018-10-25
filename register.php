<?php
/**
 * Created by PhpStorm.
 * User: KoDusk
 * Date: 2/15/2018
 * Time: 11:01 AM
 */

include('includes/site.settings.php');
include('includes/functions.php');

session_start();
if(isset($_SESSION['LoggedIn']) && $_SESSION['LoggedIn']){
    header('location: index.php');
}

$email = stripslashes(isset($_POST['email']) ? $_POST['email'] : '');
$first_name = stripslashes(isset($_POST['first_name']) ? $_POST['first_name'] : '');
$last_name = stripslashes(isset($_POST['last_name']) ? $_POST['last_name'] : '');
$password = stripslashes(isset($_POST['password']) ? $_POST['password'] : '');
$confirm_password = stripslashes(isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '');

if(isset($_POST['submit'])){
    $errors = array();

    if(empty($email) || empty($first_name) || empty($last_name) || empty($password) || empty($confirm_password)){
        $errors [] = "Please fill in all fields!";
    }

    if(strlen($email) < 3 || strlen($email) > 50){
        $errors [] = "Please use a valid email address!";
    }

    if(strlen($first_name) < 3 || strlen($first_name) > 50){
        $errors [] = "Please use a valid first name!";
    }

    if(strlen($last_name) < 3 || strlen($last_name) > 50){
        $errors [] = "Please use a valid last name!";
    }

    if(!ctype_alpha($first_name)){
        $errors [] = "Please use a valid first name!";
    }

    if(!ctype_alpha($last_name)){
        $errors [] = "Please use a valid last name!";
    }

    if(strlen($email) < 3 || strlen($email) > 50){
        $errors [] = "Please use a valid email address!";
    }

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors [] = "Please use a valid email address!";
    }

    if(strlen($password) < 3 || strlen($password) > 50){
        $errors [] = "Please use a valid password!";
    }

    if($password != $confirm_password){
        $errors [] = "Passwords didn't match!";
    }



    if(count($errors) == 0){
        if(addUser($email, $first_name, $last_name, $password)){
            header('location: login.php');
        }else{
            $errors [] = "Email and/or Password doesn't exist!";
        }
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

    <title><?=$title_prefix.' - Register'; ?></title>

    <!-- Bootstrap core CSS-->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom fonts for this template-->
    <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- Custom styles for this template-->
    <link href="css/sb-admin.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
</head>

<body class="bg-dark">
<div class="container">
    <div class="card-register mx-auto mt-5">
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
        <div class="card">
        <div class="card-header">Register an Account</div>
        <div class="card-body">
            <form action="<?=$_SERVER['PHP_SELF'];?>" method="post">
                <div class="form-group">
                    <div class="form-row">
                        <div class="col-md-6">
                            <label for="fname">First name</label>
                            <input class="form-control" name="first_name" id="fname" type="text" aria-describedby="nameHelp" placeholder="Enter first name">
                        </div>
                        <div class="col-md-6">
                            <label for="lname">Last name</label>
                            <input class="form-control" name="last_name" id="lname" type="text" aria-describedby="nameHelp" placeholder="Enter last name">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input class="form-control" name="email" id="email" type="email" aria-describedby="emailHelp" placeholder="Enter email">
                </div>
                <div class="form-group">
                    <div class="form-row">
                        <div class="col-md-6">
                            <label for="password">Password</label>
                            <input class="form-control" name="password" id="password" type="password" placeholder="Password">
                        </div>
                        <div class="col-md-6">
                            <label for="confirm-password">Confirm password</label>
                            <input class="form-control" name="confirm_password" id="confirm-password" type="password" placeholder="Confirm password">
                        </div>
                    </div>
                </div>
                <input class="btn btn-primary btn-block" type="submit" name="submit" value="Register">
            </form>
            <div class="text-center">
                <a class="d-block small mt-3" href="login.php">Login</a>
            </div>
        </div>
        </div>
    </div>
</div>

<!-- Bootstrap core JavaScript-->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Core plugin JavaScript-->
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
</body>
</html>
