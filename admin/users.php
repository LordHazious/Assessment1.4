<?php
include('../includes/site.settings.php');
include('../includes/functions.php');

session_start();
if(!$_SESSION['LoggedIn'] == true){
    header('location: ../login.php');
}

if(check_admin($_SESSION['uid']) == false){
    header('location: ../index.php');
}

$editUser = isset($_GET['editUser']) ? $_GET['editUser'] : '';
$errors = array();

if(isset($_POST['cancel'])){
    header('location: '.$_SERVER['PHP_SELF']);
}

if(isset($_POST['delete'])){
    deleteUser($_POST['uid']);
}

if(isset($_POST['submit'])){
    $uid = stripslashes(isset($_POST['uid']) ? $_POST['uid'] : '');
    $email = stripslashes(isset($_POST['email']) ? $_POST['email'] : '');
    $first_name = stripslashes(isset($_POST['first_name']) ? $_POST['first_name'] : '');
    $last_name = stripslashes(isset($_POST['last_name']) ? $_POST['last_name'] : '');
    $password = stripslashes(isset($_POST['password']) ? $_POST['password'] : '');
    $confirm_password = stripslashes(isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '');
    $admin = stripslashes(isset($_POST['admin']) ? $_POST['admin'] : '');

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
        if(strlen($last_name) < 3 || strlen($last_name) > 50) {
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

    if(!empty($admin)){
        if($admin != 5){
            if($admin != 1) {
                $errors [] = "Admin can only be true or false!";
            }
        }
    }

    if(count($errors) == 0){
        $status = editUser($uid, $email, $first_name, $last_name, $admin, $password, $confirm_password);
        if($status != false){
            $success = $status;
        }else{
            $errors [] = "Changes Failed";
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

    <title><?=$title_prefix.' - Users'; ?></title>

    <!-- Bootstrap core CSS-->
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom fonts for this template-->
    <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- Page level plugin CSS-->
    <link href="../vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="../css/sb-admin.css" rel="stylesheet">
    <link href="../css/custom.css" rel="stylesheet">
</head>

<body class="fixed-nav sticky-footer bg-dark" id="page-top">

<?php include('../includes/views/admin-nav.php'); ?>

<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumbs-->
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="../index.php">Cody Codes</a>
            </li>
            <li class="breadcrumb-item">
                <a href="index.php">Admin</a>
            </li>
            <li class="breadcrumb-item active">Users</li>
        </ol>
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

        if(isset($success)){
            if(count($success)){
                foreach($success as $success_message) {
                    echo "<div class=\"alert alert-success alert-dismissable\"><a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">×</a>$success_message</div>";
                }
            }
        }
        ?>
        <div class="card mb-3">
            <div class="card-header">
                <i class="fa fa-table"></i> All Users</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>User ID</th>
                            <th>E-Mail</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Rank</th>
                            <th style="display:none;"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach(showAllUsers() as $row){
                            if($row['admin'] == 5) {
                                $adminStatus = 'Admin';
                            }else{
                                $adminStatus = 'User';
                            }
                            echo "<tr><td>".$row['uid']."</td><td>".$row['email']."</td><td>".$row['first_name']."</td><td>".$row['last_name']."</td><td>".$adminStatus."</td><td><a class=\"btn btn-info\" href=\"".$_SERVER['PHP_SELF']."?editUser=".$row['uid']."\">Edit</a></td></tr>";
                        }
                        ?>
                        </tbody>
                    </table>
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

    <!-- editUser Modal-->
    <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="<?=$_SERVER['PHP_SELF'];?>" method="post">
                <input type="hidden" name="uid" value="<?=$editUser; ?>" />
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="logoutModalLabel">User Editor</h5>
                        <input type="submit" name="cancel" class="close_button" value="×" />
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="email">Email address</label>
                            <input class="form-control" id="email" name="email" type="email" aria-describedby="emailHelp" placeholder="<?=showUser($editUser)->email;?>">
                        </div>
                        <div class="form-group">
                            <label for="password">First Name</label>
                            <input class="form-control" id="password" name="first_name" type="text" placeholder="<?=showUser($editUser)->first_name;?>">
                        </div>
                        <div class="form-group">
                            <label for="password">Last Name</label>
                            <input class="form-control" id="password" name="last_name" type="text" placeholder="<?=showUser($editUser)->last_name;?>">
                        </div>
                        <div class="form-group">
                            <label for="rank">Rank</label>
                            <select class="form-control" name="admin" id="rank">
                                <option value="5" <?php if(showUser($editUser)->admin == 5) echo "selected"; ?>>Admin</option>
                                <option value="1" <?php if(showUser($editUser)->admin != 5) echo "selected"; ?>>User</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input class="form-control" id="password" name="pass" type="password" placeholder="Password">
                        </div>
                        <div class="form-group">
                            <label for="password">Confirm Password</label>
                            <input class="form-control" id="password" name="confirm_pass" type="password" placeholder="Confirm Password">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="submit" name="cancel" class="btn btn-secondary" value="Cancel" />
                        <input type="submit" name="delete" class="btn btn-danger" value="Delete" />
                        <input type="submit" name="submit" class="btn btn-primary" value="Edit" />
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Bootstrap core JavaScript-->
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Core plugin JavaScript-->
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <!-- Page level plugin JavaScript-->
    <script src="../vendor/chart.js/Chart.min.js"></script>
    <script src="../vendor/datatables/jquery.dataTables.js"></script>
    <script src="../vendor/datatables/dataTables.bootstrap4.js"></script>
    <!-- Custom scripts for all pages-->
    <script src="../js/sb-admin.min.js"></script>
    <!-- Custom scripts for this page-->
    <script src="../js/sb-admin-datatables.min.js"></script>
    <script src="../js/sb-admin-charts.min.js"></script>
    <?php
    if(!empty($editUser)){
        echo "<script type=\"text/javascript\">$(window).on('load',function(){ $('#editUserModal').modal('show'); });</script>";
    }
    ?>
</div>
</body>
</html>
