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

if(isset($_GET['remove'])){
    if(count($errors) == 0){
        if(!is_numeric($_GET['remove'])){
            $errors [] = "REMOVE MUST CONTAIN PID!";
        }

        if(removeCart($_SESSION['uid'], $_GET['remove'])){
            $success [] = "PRODUCT REMOVED FROM CART";
        }
    }
}

if(isset($_GET['pid']) && isset($_GET['qty'])){
    if($_GET['qty'] == 0){
        $errors [] = "Quantity can't be zero!";
    }

    if(!is_numeric($_GET['pid']) || !is_numeric($_GET['qty'])){
        $errors [] = "ERROR PID AND/OR UID MUST BE NUMERIC!";
    }

    if(!count($errors)) {
        if (updateCart($_SESSION['uid'], $_GET['pid'], $_GET['qty'])) {
            $success [] = "CART UPDATED";
        }
    }
}

if(isset($_GET['checkout'])){
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

    <title><?=$title_prefix.' - Cart'; ?></title>

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
            <li class="breadcrumb-item">
                <a href="shop.php">Shop</a>
            </li>
            <li class="breadcrumb-item active">Cart</li>
        </ol>
        <div class="row">
            <div class="col-12">
                <?php
                if(isset($errors)){
                    if(count($errors)){
                        echo "<div class=\"alert alert-danger alert-dismissable\"><a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">×</a>";
                        foreach($errors as $error){
                            echo "<ul><li>$error</li></ul>";
                        }
                        echo "</div>";
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
                        <i class="fa fa-shopping-basket"></i> Cart</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>Product</th>
                                    <th style="width: 10%">Quantity</th>
                                    <th style="text-align: right">Price</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $sub_total = 0;
                                $postage = 0;
                                foreach(getCart($_SESSION['uid']) as $cart){
                                    ?>
                                    <tr>
                                        <td><img src="img/shop/<?=$cart['image']; ?>" width="50" height="50"/> </td>
                                        <td><?=$cart['title']; ?></td>
                                        <td>
                                            <form action="<?=$_SERVER['PHP_SELF'];?>" method="get">
                                                <div class="input-group">
                                                    <input type="hidden" name="pid" value="<?=$cart['pid']; ?>" />
                                                    <input name="qty" class="form-control" type="text" value="<?=$cart['qty']; ?>" />
                                                    <div class="input-group-append">
                                                        <input type="submit" class="btn btn-outline-secondary" value="UPDATE">
                                                    </div>
                                                </div>
                                            </form>
                                        </td>
                                        <td class="text-right">$<?=$cart['price']*$cart['qty']; ?></td>
                                        <td class="text-right"><a href="?remove=<?=$cart['pid']; ?>" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> </a> </td>
                                    </tr>
                                    <?php
                                    $sub_total += $cart['price'] * $cart['qty'];
                                }
                                $total = $postage + $sub_total;
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row"></div>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <tr>
                            <td>Sub-Total</td>
                            <td class="text-right">$<?=$sub_total; ?></td>
                        </tr>
                        <tr>
                            <td>Shipping</td>
                            <td class="text-right"><?php if($postage == 0) echo "FREE"; else echo "$".$postage; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Total</strong></td>
                            <td class="text-right"><strong>$<?=$total; ?></strong></td>
                        </tr>
                    </table>
                </div>
                <div class="col-12 mb-3">
                    <div class="row">
                        <div class="col-sm-12  col-md-6">
                            <a href="shop.php" class="btn btn-lg btn-block btn-light">Continue Shopping</a>
                        </div>
                        <div class="col-sm-12 col-md-6 text-right">
                            <a href="?checkout" class="btn btn-lg btn-block btn-success">Checkout</a>
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