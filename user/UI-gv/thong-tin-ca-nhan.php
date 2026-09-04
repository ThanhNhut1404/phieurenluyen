<?php
    $id_nguoi_dung = "";
    if(isset($_SESSION['gv'])){
        $id_nguoi_dung = $_SESSION['gv']->id_nguoi_dung;
    } else if(isset($_SESSION['btdk'])){
        $id_nguoi_dung = $_SESSION['btdk']->id_nguoi_dung;
    }

    $gv_info = $giangvien->giangvien__Get_By_Id($id_nguoi_dung);
    
    $trinh_do_str = "Chưa cập nhật";
    if ($gv_info && isset($gv_info->id_trinh_do)) {
        $td = $trinhdo->trinhdo__Get_By_Id($gv_info->id_trinh_do);
        if($td) $trinh_do_str = $td->ten_trinh_do;
    }
?>

<div class="dashboard-container">
    <div class="ketqua-card custom-card" style="margin-bottom: 20px;">
        <div class="ketqua-header">
            <h3 class="ketqua-title">Thông tin cá nhân</h3>
        </div>
        <div class="p-4">
            <div class="row">
                <!-- Left: Avatar (Placeholder only since DB doesn't support avatar for GV yet) -->
                <div class="col-md-2 text-center mb-4 mb-md-0">
                    <div style="width: 130px; height: 170px; border-radius: 6px; background: #1d4ed8; display: inline-flex; align-items: center; justify-content: center; overflow: hidden; margin: 0 auto; border: 1px solid #e8ecf3; padding: 2px;">
                        <i class="ri-user-3-fill" style="color: rgba(255,255,255,0.9); font-size: 80px; margin-top: 15px;"></i>
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
