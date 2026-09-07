<?php
    $id_nguoi_dung = "";
    if(isset($_SESSION['gv'])){
        $id_nguoi_dung = $_SESSION['gv']->id_nguoi_dung;
    } else if(isset($_SESSION['btdk'])){
        $id_nguoi_dung = $_SESSION['btdk']->id_nguoi_dung;
    }

    // Xử lý upload ảnh đại diện
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_avatar') {
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['avatar'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array($ext, $allowed_ext)) {
                $upload_dir = '../../assets/img/avatars/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $filename = "gv_" . $id_nguoi_dung . "_" . time() . "." . $ext;
                $target_path = $upload_dir . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $target_path)) {
                    $giangvien->giangvien__Update_Avatar($id_nguoi_dung, $filename);
                    header("Location: ?page=thong-tin-ca-nhan&status=success&msg=" . urlencode("Cập nhật ảnh đại diện thành công!") . "&title=" . urlencode("Thành công!"));
                    exit;
                } else {
                    header("Location: ?page=thong-tin-ca-nhan&status=failed&msg=" . urlencode("Không thể tải lên tệp tin!") . "&title=" . urlencode("Thất bại!"));
                    exit;
                }
            } else {
                header("Location: ?page=thong-tin-ca-nhan&status=failed&msg=" . urlencode("Định dạng ảnh không hợp lệ (chỉ nhận jpg, png, gif, webp)!") . "&title=" . urlencode("Thất bại!"));
                exit;
            }
        }
    }

    $gv_info = $giangvien->giangvien__Get_By_Id($id_nguoi_dung);
    
    $trinh_do_str = "Chưa cập nhật";
    if ($gv_info && isset($gv_info->id_trinh_do)) {
        $td = $trinhdo->trinhdo__Get_By_Id($gv_info->id_trinh_do);
        if($td) $trinh_do_str = $td->ten_trinh_do;
    }

    // Avatar
    $has_avatar = false;
    $avatar_src = "";
    if (!empty($gv_info->anh_dai_dien) && file_exists("../../assets/img/avatars/" . $gv_info->anh_dai_dien)) {
        $avatar_src = "../../assets/img/avatars/" . $gv_info->anh_dai_dien;
        $has_avatar = true;
    }

    // Toast notification
    if (isset($_GET['status']) && isset($_GET['msg'])):
?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    if(window.Toast) {
        Toast.fire({
            icon: '<?= $_GET['status'] === 'success' ? 'success' : 'error' ?>',
            title: '<?= htmlspecialchars($_GET['msg'] ?? '') ?>'
        });
    }
});
</script>
<?php endif; ?>

<div class="dashboard-container">
    <div class="ketqua-card custom-card" style="margin-bottom: 20px;">
        <div class="ketqua-header">
            <h3 class="ketqua-title">Thông tin cá nhân</h3>
        </div>
        <div class="p-4">
            <div class="row">
                <!-- Left: Avatar -->
                <div class="col-md-2 text-center mb-4 mb-md-0">
                    <?php if ($has_avatar): ?>
                        <img src="<?=$avatar_src?>" alt="Avatar" style="width: 130px; height: 170px; object-fit: cover; border-radius: 6px; border: 1px solid #e8ecf3; padding: 2px;">
                    <?php else: ?>
                        <div style="width: 130px; height: 170px; border-radius: 6px; background: #1d4ed8; display: inline-flex; align-items: center; justify-content: center; overflow: hidden; margin: 0 auto; border: 1px solid #e8ecf3; padding: 2px;">
                            <i class="ri-user-3-fill" style="color: rgba(255,255,255,0.9); font-size: 80px; margin-top: 15px;"></i>
                        </div>
                    <?php endif; ?>

                    <div class="mt-2">
                        <form id="formAvatarGV" method="POST" enctype="multipart/form-data" style="display: none;">
                            <input type="hidden" name="action" value="update_avatar">
                            <input type="file" id="avatarUploadGV" name="avatar" accept="image/*" onchange="document.getElementById('formAvatarGV').submit();">
                        </form>
                        <a href="#" onclick="event.preventDefault(); document.getElementById('avatarUploadGV').click();" style="font-size: 0.85rem; color: #1d4ed8; font-weight: 600; text-decoration: none;">Cập nhật hình</a>
                    </div>
                </div>

                <!-- Right: Info -->
                <div class="col-md-10">
                    <div class="row" style="font-size: 0.95rem; color: #4b5563; line-height: 2.2;">
                        <?php if($gv_info): ?>
                        <!-- Column 1 -->
                        <div class="col-md-4">
                            <div><span style="color: #6b7280;">Mã GV:</span> <span style="font-weight: 600; color: #374151;"><?=$gv_info->ma_giang_vien?></span></div>
                            <div><span style="color: #6b7280;">Họ tên:</span> <span style="font-weight: 600; color: #374151;"><?=mb_strtoupper($gv_info->ten_giang_vien, 'UTF-8')?></span></div>
                            <div><span style="color: #6b7280;">Ngày sinh:</span> <span style="font-weight: 600; color: #374151;"><?=date('d/m/Y', strtotime($gv_info->ngay_sinh))?></span></div>
                            <div><span style="color: #6b7280;">Giới tính:</span> <span style="font-weight: 600; color: #374151;"><?=$gv_info->gioi_tinh == 1 ? 'Nam' : ($gv_info->gioi_tinh == 0 ? 'Nữ' : 'Khác')?></span></div>
                        </div>

                        <!-- Column 2 -->
                        <div class="col-md-4">
                            <div><span style="color: #6b7280;">Email:</span> <span style="font-weight: 600; color: #374151;"><?=$gv_info->email?></span></div>
                            <div><span style="color: #6b7280;">SĐT 1:</span> <span style="font-weight: 600; color: #374151;"><?=$gv_info->so_dien_thoai_1?></span></div>
                            <?php if(!empty($gv_info->so_dien_thoai_2)): ?>
                            <div><span style="color: #6b7280;">SĐT 2:</span> <span style="font-weight: 600; color: #374151;"><?=$gv_info->so_dien_thoai_2?></span></div>
                            <?php endif; ?>
                            <div><span style="color: #6b7280;">Trình độ:</span> <span style="font-weight: 600; color: #374151;"><?=$trinh_do_str?></span></div>
                        </div>

                        <!-- Column 3 -->
                        <div class="col-md-4">
                            <div><span style="color: #6b7280;">Đ/c liên lạc:</span> <span style="font-weight: 600; color: #374151;"><?=$gv_info->dia_chi_lien_lac?></span></div>
                            <div><span style="color: #6b7280;">Đ/c thường trú:</span> <span style="font-weight: 600; color: #374151;"><?=$gv_info->dia_chi_thuong_tru?></span></div>
                        </div>
                        <?php else: ?>
                            <div class="col-12 text-center text-muted">Không tìm thấy thông tin giảng viên.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
