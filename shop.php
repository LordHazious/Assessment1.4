<?php
/**
 * Created by PhpStorm.
 * User: KoDusk
 * Date: 4/11/2018
 * Time: 3:16 PM
 */

include('includes/site.settings.php');
include('includes/functions.php');
include('includes/shop.php');

session_start();

$errors = array();
$success = array();

if(!$_SESSION['LoggedIn'] == true){
    header('location: login.php');
}

if(isset($_GET['cart_add'])){
    if(addCart($_SESSION['uid'], $_GET['cart_add']) == true){
        $success [] = "PRODUCT ADDED TO <a href=\"cart.php\">CART</a>";
    }else if(addCart($_SESSION['uid'], $_GET['cart_add']) == false){
        $errors [] = "PRODUCT ALREADY IN CART";
    }else{
        die(addCart($_SESSION['uid'], $_GET['cart_add']));
    }
}

if(isset($_GET['buy_now'])){
    $errors [] = "PAYMENT GATEWAY NOT SETUP!";
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

    <title><?=$title_prefix.' - Shop'; ?></title>

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
                <a href="index.php"><?=$title_prefix; ?></a>
            </li>
            <li class="breadcrumb-item active">Shop</li>
            <li class="breadcrumb-item">
                <a href="cart.php">Cart</a>
            </li>
        </ol>

        <div class="col-lg-12">
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
            <form action="<?=$_SERVER['PHP_SELF'];?>" method="get">
                <div class="input-group mb-3">
                    <input type="text" name="search" class="form-control custom-control" placeholder="Search"/>
                    <div class="input-group-append">
                        <input type="submit" class="btn btn-outline-secondary" value="Search">
                    </div>
                </div>
            </form>
            <div class="row">
                <?php
                if(empty($_GET['search'])) {
                    foreach (getProducts() as $product) {
                        ?>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card h-100">
                                <img class="card-img-top" style="max-height: 500px; max-width 100%;" src="img/shop/<?= $product['image']; ?>" alt="">
                                <div class="card-body">
                                    <h4 class="card-title">
                                        <?= $product['title']; ?>
                                    </h4>
                                    <h5>$<?= $product['price']; ?></h5>
                                    <p class="card-text"><?= $product['description']; ?></p>
                                </div>
                                <div class="card-footer">
                                    <a class="btn btn-link" href="?cart_add=<?= $product['pid']; ?>"><i
                                                class="fa fa-shopping-cart"></i> Add to cart</a>
                                    <a class="btn btn-link" href="?buy_now"><i class="fa fa-dollar"></i> Buy now</a>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }else{
                    foreach (productSearch($_GET['search']) as $product) {
                        ?>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card h-100">
                                <img class="card-img-top" src="img/shop/<?= $product['image']; ?>" alt="">
                                <div class="card-body">
                                    <h4 class="card-title">
                                        <?= $product['title']; ?>
                                    </h4>
                                    <h5>$<?= $product['price']; ?></h5>
                                    <p class="card-text"><?= $product['description']; ?></p>
                                </div>
                                <div class="card-footer">
                                    <a class="btn btn-link" href="?cart_add=<?= $product['pid']; ?>"><i class="fa fa-shopping-cart"></i> Add to cart</a>
                                    <a class="btn btn-link" href="?buy_now"><i class="fa fa-dollar"></i> Buy now</a>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
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