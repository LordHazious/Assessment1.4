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
if(isset($_SESSION['LoggedIn']) && $_SESSION['LoggedIn'] == true){
    header('location: index.php');
}

$email = stripslashes(isset($_POST['email']) ? $_POST['email'] : '');
$pass = stripslashes(isset($_POST['pass']) ? $_POST['pass'] : '');

if(isset($_POST['submit'])){
    $errors = array();

    if(empty($email) || empty($pass)){
        $errors [] = "Please enter your email and password!";
    }

    if(strlen($email) < 3 || strlen($email) > 50){
        $errors [] = "Please use a valid email address!";
    }

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors [] = "Please use a valid email address!";
    }

    if(strlen($pass) < 3 || strlen($pass) > 50){
        $errors [] = "Please use a valid password!";
    }

    if(count($errors) == 0){
        if(login($email, $pass)){
            session_start();
            if($_SESSION['LoggedIn'] == true){
                header('location: index.php');
            }
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

    <title><?=$title_prefix.' - Login'; ?></title>

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
    <div class="card-login mx-auto mt-5">
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
        <div class="card ">

            <div class="card-header">Login</div>
            <div class="card-body">
                <form action="<?=$_SERVER['PHP_SELF'];?>" method="post">
                    <div class="form-group">
                        <label for="email">Email address</label>
                        <input class="form-control" id="email" name="email" type="email" aria-describedby="emailHelp" placeholder="Email Address">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input class="form-control" id="password" name="pass" type="password" placeholder="Password">
                    </div>
                    <input class="btn btn-primary btn-block" type="submit" name="submit" value="Login">
                </form>
                <div class="text-center">
                    <a class="d-block small mt-3" href="register.php">Register an Account</a>
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
