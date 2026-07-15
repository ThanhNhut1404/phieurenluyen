<!-- header -->
<?php 
    session_start();
    if(!isset($_SESSION['user'])){
        header('location: ../auth/');
        exit();
    }
    require "../models/getModel.php";

?>
<!-- Navbar -->
<nav class="ml-0 main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <!-- <li class="nav-item dropdown">
            <a class="nav-link" href="#">
                <img width="30" src="../assets/img/logo.png" alt="">
            </a>
        </li> -->
    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-chart-pie"></i>
                <?php
                    $id_dot = $dotchamdiem->dotchamdiem__Get_Last()->id_dot;
                    if(isset($_GET['id_dot'])){
                        $id_dot = $_GET['id_dot'];
                    }
                    echo $dotchamdiem->dotchamdiem__Get_By_Id($id_dot)->ten_dot." - ".$dotchamdiem->dotchamdiem__Get_By_Id($id_dot)->ten_hoc_ky."(".$dotchamdiem->dotchamdiem__Get_By_Id($id_dot)->ten_nam_hoc.")";
                    ?>
                <i class="fas fa-caret-down"></i>
            </a>

            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                <?php foreach($dotchamdiem->dotchamdiem__Get_All() as $item):?>
                <a href="?page=thong-ke&id_dot=<?=$item->id_dot?>" class="dropdown-item">
                    <i class="fas fa-file"></i>

                    <?=$item->ten_dot." - ".$item->ten_hoc_ky."(".$item->ten_nam_hoc.")"?>
                </a>
                <?php endforeach; ?>
            </div>
        </li>

        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-user"></i>
                Tài khoản
                <i class="fas fa-caret-down"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                <a href="?page=quan-ly-tai-khoan" class="dropdown-item">
                    <i class="fas fa-user-cog"></i>
                    Đổi mật khẩu
                </a>
                <div class="dropdown-divider"></div>
                <a href="../auth/action.php?req=dang-xuat" class="dropdown-item">
                    <i class="fas fa-sign-out-alt"></i>
                    Đăng xuất
                </a>
            </div>
        </li>


    </ul>
</nav>
<!-- /.navbar -->