<?php 
    session_start();
    if(!isset($_SESSION['user'])){
        header('location: ../auth/');
        exit();
    }
    require "../models/getModel.php";

?>
<!-- header -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light" style="margin-left: 0 !important; padding-left: 8px !important;">
    <!-- Left navbar links -->
    <ul class="navbar-nav align-items-center">
        <!-- Quân sửa: Thêm logo và tên vai trò bên góc trái header -->
        <li class="nav-item d-flex align-items-center">
            <a class="nav-link d-flex align-items-center p-0" href="index.php" style="color: inherit; height: 38px;">
                <img height="34" src="../assets/img/logo.png" alt="Logo" class="mr-2" style="object-fit: contain;">
                <?php 
                $role_name = '';
                if (isset($_SESSION['lt'])) {
                    $role_name = 'Lớp trưởng';
                } elseif (isset($_SESSION['bt'])) {
                    $role_name = 'Bí thư chi đoàn';
                } elseif (isset($_SESSION['btdk'])) {
                    $role_name = 'Bí thư đoàn khoa';
                } elseif (isset($_SESSION['gv'])) {
                    $role_name = 'Cố vấn học tập';
                }
                if (!empty($role_name)): 
                ?>
                    <span class="font-weight-bold ml-2" style="font-size: 1.55rem; color: #0d3b66; font-family: 'Source Sans Pro', sans-serif;">
                        <?= $role_name ?>
                    </span>
                <?php endif; ?>
            </a>
        </li>
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
                    echo $dotchamdiem->dotchamdiem__Get_By_Id($id_dot)->ten_dot." - ".$dotchamdiem->dotchamdiem__Get_By_Id($id_dot)->ten_hoc_ky." - ".$dotchamdiem->dotchamdiem__Get_By_Id($id_dot)->ten_nam_hoc;
                    ?>
                <i class="fas fa-caret-down"></i>
            </a>

            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                <?php foreach($dotchamdiem->dotchamdiem__Get_All() as $item):?>
                <a href="?page=thong-ke&id_dot=<?=$item->id_dot?>" class="dropdown-item">

                    <?=$item->ten_dot." - ".$item->ten_hoc_ky." - ".$item->ten_nam_hoc?>
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