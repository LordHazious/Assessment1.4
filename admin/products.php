<?php
include('../includes/site.settings.php');
include('../includes/functions.php');
include('../includes/shop.php');

session_start();
if(!$_SESSION['LoggedIn'] == true){
    header('location: ../login.php');
}

if(!check_admin($_SESSION['uid'])){
    header('location: ../index.php');
}

$editProduct = isset($_GET['editProduct']) ? $_GET['editProduct'] : '';
$errors = array();

if(isset($_POST['cancel'])){
    header('location: '.$_SERVER['PHP_SELF']);
}

if(isset($_POST['delete'])){
    deleteProduct($_POST['pid']);
}

if(isset($_POST['addProduct'])){
    if($_FILES["product"]["size"] != 0) {
        $title = stripslashes(isset($_POST['title']) ? $_POST['title'] : '');
        $description = stripslashes(isset($_POST['description']) ? $_POST['description'] : '');
        $price = stripslashes(isset($_POST['price']) ? $_POST['price'] : '');


        $target_dir = "../img/shop/";
        $target_file_time = strtotime("now") . '_';
        $target_file = $target_dir . basename($target_file_time . $_FILES["product"]["name"]);
        $image = $target_file_time . $_FILES["product"]["name"];

        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["product"]["tmp_name"]);
        $image_width = $check[0];
        $image_height = $check[1];

        if ($image_width > 600 && $image_height > 600) {
            $errors [] = "Image size is bigger than 600x600";
        }

        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $errors [] = "Only JPG, JPEG, PNG & GIF files are allowed";
        }

        if (file_exists($target_file)) {
            $errors [] = "File name already exists";
        }

        if ($_FILES["product"]["size"] > 500000) {
            $errors [] = "Your file is too large.";
        }

        if (!move_uploaded_file($_FILES["product"]["tmp_name"], $target_file)) {
            $errors [] = "There was an error uploading your file.";
        }
    }else{
        $errors [] = "Please add a product image!";
    }

    if(count($errors) == 0){
        if(addProduct($title, $description, $price, $image)){
            $success = "Product Added";
        }else{
            $errors [] = "Failed to add product!";
        }
    }
}

if(isset($_POST['submit'])){
    $pid = stripslashes(isset($_POST['pid']) ? $_POST['pid'] : '');
    $title = stripslashes(isset($_POST['title']) ? $_POST['title'] : '');
    $description = stripslashes(isset($_POST['description']) ? $_POST['description'] : '');
    $price = stripslashes(isset($_POST['price']) ? $_POST['price'] : '');

    if($_FILES["image"]["size"] != 0) {
        $target_dir = "../img/shop/";
        $target_file_time = strtotime("now").'_';
        $target_file = $target_dir . basename($target_file_time.$_FILES["image"]["name"]);
        $image = $target_file_time.$_FILES["image"]["name"];

        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["image"]["tmp_name"]);
        $image_width = $check[0];
        $image_height = $check[1];

        if ($image_width > 600 && $image_height > 600) {
            $errors [] = "Image size is bigger than 600x600";
        }

        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
            $errors [] = "Only JPG, JPEG, PNG & GIF files are allowed";
        }

        if (file_exists($target_file)) {
            $errors [] = "File name already exists";
        }

        if ($_FILES["image"]["size"] > 500000) {
            $errors [] = "Your file is too large.";
        }

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $errors [] = "There was an error uploading your file.";
        }
    }else{
        $image = "";
    }

    if(count($errors) == 0){
        $status = editProduct($pid, $title, $description, $price, $image);
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

    <title><?=$title_prefix.' - Products'; ?></title>

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
            <li class="breadcrumb-item active">Products</li>
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
                <i class="fa fa-table"></i> All Products</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th style="display:none;"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach(getProducts() as $row){
                            echo "<tr><td><img src=\"http://".$_SERVER['HTTP_HOST']."/~s3681709/img/shop/".$row['image']."\" alt=\"image\" width=\"50\" height=\"50\"></td><td>".$row['title']."</td><td>".$row['description']."</td><td>$".$row['price']."</td><td><a class=\"btn btn-info\" href=\"".$_SERVER['PHP_SELF']."?editProduct=".$row['pid']."\">Edit</a></td></tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 mb-3">
        <div class="card">
            <div class="card-header">New Product</div>
            <div class="card-body">
                <form action="<?=$_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="product">Product Image</label>
                        <input class="form-control" id="product" name="product" type="file">
                    </div>
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input class="form-control" id="title" name="title" type="text" placeholder="Product Title">
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <input class="form-control" id="description" name="description" type="text" placeholder="Product Description">
                    </div>
                    <div class="form-group">
                        <label for="password">Price</label>
                        <input class="form-control" id="price" name="price" type="text" placeholder="Product Price">
                    </div>
                    <input type="submit" class="btn btn-primary" name="addProduct" value="Add Product">
                </form>
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
    <div class="modal fade" id="editProductModal" tabindex="-1" role="dialog" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="<?=$_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="pid" value="<?=$editProduct; ?>" />
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="logoutModalLabel">Product Editor</h5>
                        <input type="submit" name="cancel" class="close_button" value="×" />
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="product"><img src="../img/shop/<?=showProduct($editProduct)->image;?>" style="width: 100%;" alt="product"> <br/>Upload New Product Image</label>
                            <input class="form-control" id="product" name="image" type="file">
                        </div>
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input class="form-control" id="title" name="title" type="text" placeholder="<?=showProduct($editProduct)->title;?>">
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <input class="form-control" id="description" name="description" type="text" placeholder="<?=showProduct($editProduct)->description;?>">
                        </div>
                        <div class="form-group">
                            <label for="password">Price</label>
                            <input class="form-control" id="price" name="price" type="text" placeholder="<?=showProduct($editProduct)->price;?>">
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
    if(!empty($editProduct)){
        echo "<script type=\"text/javascript\">$(window).on('load',function(){ $('#editProductModal').modal('show'); });</script>";
    }
    ?>
</div>
</body>
</html>