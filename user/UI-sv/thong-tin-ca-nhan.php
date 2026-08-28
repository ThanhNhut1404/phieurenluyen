<?php
    // Xử lý upload ảnh
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
                $filename = "sv_" . $id_sinh_vien . "_" . time() . "." . $ext;
                $target_path = $upload_dir . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $target_path)) {
                    $sinhvien->sinhvien__Update_Avatar($id_sinh_vien, $filename);
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

    $sv_info = $sinhvien->sinhvien__Get_By_Id($id_sinh_vien);
    $lop_info = $lophoc->lophoc__Get_By_Id($sv_info->id_lop_hoc);
    
    // Fetch Khoa & Khóa
    $ten_khoa_hoc = "Chưa cập nhật";
    $ten_khoa = "Chưa cập nhật";
    
    if ($lop_info) {
        if ($lop_info->id_khoa_hoc) {
            $kh_info = $khoahoc->khoahoc__Get_By_Id($lop_info->id_khoa_hoc);
            if ($kh_info) $ten_khoa_hoc = $kh_info->ten_khoa_hoc;
        }
        if ($lop_info->id_nganh_hoc) {
            $nganh_info = $nganhhoc->nganhhoc__Get_By_Id($lop_info->id_nganh_hoc);
            if ($nganh_info && $nganh_info->id_khoa) {
                $khoa_info = $khoa->khoa__Get_By_Id($nganh_info->id_khoa);
                if ($khoa_info) $ten_khoa = $khoa_info->ten_khoa;
            }
        }
    }
    
    $chuc_vu_str = "Sinh viên";
    if ($sv_info->chuc_vu == 1) {
        $chuc_vu_str = "Lớp trưởng";
    } elseif ($sv_info->chuc_vu == 2) {
        $chuc_vu_str = "Bí thư chi đoàn";
    }
    
    // Avatar
    $has_avatar = false;
    $avatar_src = "";
    if (!empty($sv_info->anh_dai_dien) && file_exists("../../assets/img/avatars/" . $sv_info->anh_dai_dien)) {
        $avatar_src = "../../assets/img/avatars/" . $sv_info->anh_dai_dien;
        $has_avatar = true;
    }
?>

<div class="dashboard-container">
    <div class="ketqua-card custom-card" style="margin-bottom: 20px;">
        <div class="ketqua-header">
            <h3 class="ketqua-title">Thông tin sinh viên</h3>
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
                        <form id="formAvatar" method="POST" enctype="multipart/form-data" style="display: none;">
                            <input type="hidden" name="action" value="update_avatar">
                            <input type="file" id="avatarUpload" name="avatar" accept="image/*" onchange="document.getElementById('formAvatar').submit();">
                        </form>
                        <a href="#" onclick="event.preventDefault(); document.getElementById('avatarUpload').click();" style="font-size: 0.85rem; color: #1d4ed8; font-weight: 600; text-decoration: none;">Cập nhật hình</a>
                    </div>
                </div>

                <!-- Right: Info -->
                <div class="col-md-10">
                    <div class="row" style="font-size: 0.95rem; color: #4b5563; line-height: 2.2;">
                        <!-- Column 1 -->
                        <div class="col-md-3">
                            <div><span style="color: #6b7280;">MSSV:</span> <span style="font-weight: 600; color: #374151;"><?=$sv_info->ma_sinh_vien?></span></div>
                            <div><span style="color: #6b7280;">Họ tên:</span> <span style="font-weight: 600; color: #374151;"><?=mb_strtoupper($sv_info->ten_sinh_vien, 'UTF-8')?></span></div>
                            <div><span style="color: #6b7280;">Ngày sinh:</span> <span style="font-weight: 600; color: #374151;"><?=date('d/m/Y', strtotime($sv_info->ngay_sinh))?></span></div>
                            <div><span style="color: #6b7280;">Giới tính:</span> <span style="font-weight: 600; color: #374151;"><?=$sv_info->gioi_tinh == 1 ? 'Nam' : ($sv_info->gioi_tinh == 0 ? 'Nữ' : 'Khác')?></span></div>
                            <div><span style="color: #6b7280;">Email:</span> <span style="font-weight: 600; color: #374151;"><?=$sv_info->email?></span></div>
                        </div>

                        <!-- Column 2 -->
                        <div class="col-md-4">
                            <div><span style="color: #6b7280;">Khoa:</span> <span style="font-weight: 600; color: #374151;"><?=$ten_khoa?></span></div>
                            <div><span style="color: #6b7280;">Khóa:</span> <span style="font-weight: 600; color: #374151;"><?=$ten_khoa_hoc?></span></div>
                            <div><span style="color: #6b7280;">Lớp học:</span> <span style="font-weight: 600; color: #374151;"><?=$lop_info ? $lop_info->ten_lop_hoc : 'Chưa cập nhật'?></span></div>
                            <div><span style="color: #6b7280;">Chức vụ:</span> <span style="font-weight: 600; color: #374151;"><?=$chuc_vu_str?></span></div>
                        </div>

                        <!-- Column 3 -->
                        <div class="col-md-5">
                            <div><span style="color: #6b7280;">SĐT 1:</span> <span style="font-weight: 600; color: #374151;"><?=$sv_info->so_dien_thoai_1?></span></div>
                            <?php if(!empty($sv_info->so_dien_thoai_2)): ?>
                            <div><span style="color: #6b7280;">SĐT 2:</span> <span style="font-weight: 600; color: #374151;"><?=$sv_info->so_dien_thoai_2?></span></div>
                            <?php endif; ?>
                            <div><span style="color: #6b7280;">Đ/c liên lạc:</span> <span style="font-weight: 600; color: #374151;"><?=$sv_info->dia_chi_lien_lac?></span></div>
                            <div><span style="color: #6b7280;">Đ/c thường trú:</span> <span style="font-weight: 600; color: #374151;"><?=$sv_info->dia_chi_thuong_tru?></span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
