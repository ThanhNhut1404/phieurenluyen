<?php
    require "../../models/getModel.php";

    $id_nguoi_dung = "";
    $ten_nguoi_dung = "";
    $chuc_vu_text = "Giảng viên";
    
    // Check session
    if(isset($_SESSION['gv'])){
        $id_nguoi_dung = $_SESSION['gv']->id_nguoi_dung;
        $chuc_vu_text = "Cố vấn học tập";
    } else if(isset($_SESSION['btdk'])){
        $id_nguoi_dung = $_SESSION['btdk']->id_nguoi_dung;
        $chuc_vu_text = "Bí thư đoàn khoa";
    } else {
        header("Location: ../../auth/action.php?req=dang-xuat&role=user");
        exit();
    }

    $avatar_header = "";
    $giangvien_info = $giangvien->giangvien__Get_By_Id($id_nguoi_dung);
    if($giangvien_info){
        $ten_nguoi_dung = $giangvien_info->ten_giang_vien;
    }
?>
<nav class="top-navbar">
    <div class="navbar-brand">
        <a href="index.php">
            <img src="../../assets/img/logo.png" alt="UniDRL" onerror="this.src='../../assets/img/tdc.png'">
        </a>
    </div>
    
    <div class="navbar-menu">
        
        <a href="index.php" class="nav-link">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-9.5Z" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <span>Trang chủ</span>
        </a>
        <?php
            require_once 'fetch_notifications.php';
            $unread_count = 0;
            foreach ($list_thong_bao as $tb) {
                if (isset($tb->is_read) && !$tb->is_read) $unread_count++;
            }
        ?>
        <a href="?page=thong-bao" class="nav-link" style="position: relative;">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 7h18s-3 0-3-7Z" stroke-width="2" stroke-linecap="round"/>
                <path d="M10 21a2 2 0 0 0 4 0" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <?php if ($unread_count > 0): ?>
                <span style="position: absolute; top: 12px; right: 8px; width: 8px; height: 8px; background-color: #ef4444; border-radius: 50%; box-shadow: 0 0 0 2px white;"></span>
            <?php endif; ?>
            <span>Thông báo</span>
        </a>
        
        <div class="user-profile dropdown">
            <a href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link">
                <span style="width: 35px; height: 35px; border-radius: 50%; background: #1d4ed8; display: flex; align-items: center; justify-content: center; overflow: hidden; margin-right: 0px;">
                    <?php if ($avatar_header): ?>
                        <img src="<?php echo $avatar_header; ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <i class="ri-user-3-fill" style="color: rgba(255,255,255,0.9); font-size: 26px; margin-top: 5px;"></i>
                    <?php endif; ?>
                </span>
                <span style="white-space: nowrap;"><?php echo $ten_nguoi_dung; ?> (<?php echo $chuc_vu_text; ?>)</span> 
                <svg class="user-caret" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M6 9l6 6 6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
            <div class="dropdown-menu dropdown-menu-right custom-dropdown">
                <a href="?page=thong-tin-ca-nhan" class="dropdown-item">
                    <i class="ri-profile-line"></i> Thông tin cá nhân
                </a>
                <a href="?page=doi-mat-khau" class="dropdown-item">
                    <i class="ri-lock-password-line"></i> Đổi mật khẩu
                </a>
                <div class="dropdown-divider"></div>
                <a href="../../auth/action.php?req=dang-xuat&role=user" class="dropdown-item">
                    <i class="ri-logout-box-r-line"></i> Đăng xuất
                </a>
            </div>
        </div>
    </div>
</nav>
