<?php
/**
 * Created by PhpStorm.
 * User: KoDusk
 * Date: 3/17/2018
 * Time: 9:19 PM
 */

include('includes/site.settings.php');
include('includes/functions.php');
include('includes/blog.php');

session_start();
if(!$_SESSION['LoggedIn'] == true){
    header('location: login.php');
}

$errors = array();
$success = array();

if(isset($_POST['post_submit'])) {
    $post = $_POST['post'];
    $post = strip_tags($post, '<br><h2><a>');

    if(empty($post)){
        $errors [] = "Please enter a post!";
    }

    if(count($errors) == 0){
        $result = newPost($_SESSION['uid'], $post);
        if($result == true){
            $success [] = "Post Successful!";
        }else{
            $errors [] = $result;
        }
    }
}

if(isset($_GET['delete_post'])){
    if(check_admin($_SESSION['uid']) == true) {
        $pid = $_GET['delete_post'];

        if (empty($pid)) {
            $errors [] = "PID is empty!";
        }

        if(!is_numeric($pid)){
            $errors [] = "PID MUST BE NUMERIC";
        }

        if (count($errors) == 0) {
            $result = deletePost($pid);
            if ($result == true) {
                $success [] = "Post Deleted!";
            } else {
                $errors [] = $result;
            }
        }
    }
}

if(isset($_POST['comment_submit'])){
    $pid = $_POST['pid'];
    $comment = $_POST['comment'];
    $comment = strip_tags($comment, '<a>');

    if(empty($pid)){
        $errors [] = "error!";
    }

    if(!is_numeric($pid)){
        $errors [] = "PID MUST BE NUMERIC";
    }

    if(empty($comment)){
        $errors [] = "Comment Field is empty!";
    }

    if(count($errors) == 0){
        $result = newComment($_SESSION['uid'], $pid, $comment);
        if($result == true){
            $success [] = "Comment Successful!";
        }else{
            $errors [] = $result;
        }
    }

}

if(isset($_GET['delete_comment'])){
    if(check_admin($_SESSION['uid']) == true){
        $cid = $_GET['delete_comment'];

        if (empty($cid)) {
            $errors [] = "CID is empty!";
        }

        if (count($errors) == 0) {
            $result = deleteComment($cid);
            if ($result == true) {
                $success [] = "Comment Deleted!";
            } else {
                $errors [] = $result;
            }
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

    <title><?=$title_prefix.' - Blog'; ?></title>

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
            <li class="breadcrumb-item active">Blog Posts</li>
        </ol>
        <div class="row">
            <div class="col-12">
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
                <form action="<?=$_SERVER['PHP_SELF'];?>" method="post">
                    <div class="input-group mb-3">
                        <textarea name="post" class="form-control custom-control" rows="1" placeholder="New Post" style="min-height: 38px;"></textarea>
                        <div class="input-group-append">
                            <input type="submit" name="post_submit" class="btn btn-outline-secondary" value="Post">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?php
                foreach(getPosts() as $posts){
                    ?>
                    <div class="panel panel-white post panel-shadow mb-3">
                        <div class="post-heading">
                            <div class="pull-left image">
                                <img src="img/<?=getAvatar($posts['uid']); ?>" alt="user profile image">
                            </div>
                            <div class="pull-left ml-1 meta">
                                <div class="title h5">
                                    <b><?=showUser($posts['uid'])->first_name . " " . showUser($posts['uid'])->last_name;?></b>
                                </div>
                                <h6 class="text-muted small"><?php if($posts['minutes'] == false) echo date_format(date_create($posts['date_created']),'d/m/y g:i A'); else echo $posts['minutes'] . " minutes ago"; ?></h6>
                            </div>
                        </div>
                        <div class="post-description">
                            <p><?=$posts['post'];?></p>
                            <div class="stats">
                                <?php if(check_admin($_SESSION['uid']) == true) { ?><a href="?delete_post=<?=$posts['pid']; ?>" style="color:red;" class="btn btn-default stat-item small"><i class="fa fa-trash"></i> Delete</a> <?php } ?>
                            </div>
                        </div>
                        <div class="post-footer">
                            <form action="<?=$_SERVER['PHP_SELF'];?>" method="post">
                                <div class="input-group">
                                    <input type="hidden" name="pid" value="<?=$posts['pid']; ?>" />
                                    <input type="text" name="comment" class="form-control" placeholder="Add a comment">
                                    <div class="input-group-append">
                                        <input type="submit" name="comment_submit" class="btn btn-outline-secondary" value="Post">
                                    </div>
                                </div>
                            </form>
                            <ul class="comments-list">
                                <?php
                                foreach (getComments($posts['pid']) as $comments) {
                                    ?>
                                    <li class="comment">
                                        <div class="pull-left mr-1 mb-1">
                                            <img src="img/<?=getAvatar($comments['uid']); ?>" alt="user profile image">
                                        </div>
                                        <div class="comment-body">
                                            <div class="comment-heading">
                                                <h4 class="user"><?=showUser($comments['uid'])->first_name . " " . showUser($comments['uid'])->last_name;?></h4>
                                                <h5 class="time"><?php if($comments['minutes'] == false) echo date_format(date_create($comments['date_created']),'d/m/y g:i A'); else echo $comments['minutes'] . " minutes ago"; ?></h5>
                                                <?php if(check_admin($_SESSION['uid']) == true) { ?><a href="?delete_comment=<?=$comments['cid']; ?>" style="color:red;" class="btn btn-default stat-item smaller"><i class="fa fa-trash"></i> Delete</a> <?php } ?>
                                            </div>
                                            <p><?=$comments['comment'];?></p>
                                        </div>
                                    </li>
                                    <?php
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                    <?php
                }
                ?>
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
