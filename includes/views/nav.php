<!-- Navigation-->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
    <a class="navbar-brand" href=""><?=$site_name;?></a>
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav navbar-sidenav" id="main-nav">
            <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Dashboard">
                <a class="nav-link" href="index.php">
                    <i class="fa fa-fw fa-home"></i>
                    <span class="nav-link-text">Home</span>
                </a>
            </li>
            <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Blog">
                <a class="nav-link" href="blog.php">
                    <i class="fa fa-fw fa-th-large"></i>
                    <span class="nav-link-text">Blog</span>
                </a>
            </li>
            <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Shop">
                <a class="nav-link" href="shop.php">
                    <i class="fa fa-fw fa-dollar"></i>
                    <span class="nav-link-text">Shop</span>
                </a>
            </li>
            <?php
            if(check_admin($_SESSION['uid']) == true) {
                ?>
                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Admin Panel">
                    <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#adminPanel"
                       data-parent="#main-nav">
                        <i class="fa fa-fw fa-unlock"></i>
                        <span class="nav-link-text">Admin</span>
                    </a>
                    <ul class="sidenav-second-level collapse" id="adminPanel">
                        <li>
                            <a href="admin/users.php"><i class="fa fa-fw fa-users"></i> Users</a>
                        </li>
                        <li>
                            <a href="admin/products.php"><i class="fa fa-fw fa-dollar"></i> Products</a>
                        </li>
                    </ul>
                </li>
                <?php
            }
            ?>
        </ul>
        <ul class="navbar-nav sidenav-toggler">
            <li class="nav-item">
                <a class="nav-link text-center" id="sidenavToggler">
                    <i class="fa fa-fw fa-angle-left"></i>
                </a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="profile.php"><i class="fa fa-fw fa-user"></i>Profile</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="modal" data-target="#logoutModal"><i class="fa fa-fw fa-sign-out"></i>Logout</a>
            </li>
        </ul>
    </div>
</nav>