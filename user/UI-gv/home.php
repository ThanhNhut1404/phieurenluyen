<?php
    $id_nguoi_dung = "";
    if(isset($_SESSION['gv'])){
        $id_nguoi_dung = $_SESSION['gv']->id_nguoi_dung;
    } else if(isset($_SESSION['btdk'])){
        $id_nguoi_dung = $_SESSION['btdk']->id_nguoi_dung;
    }

    $gv = $giangvien->giangvien__Get_By_Id($id_nguoi_dung);
    
    // Đếm số lớp quản lý (phancong)
    $so_lop = 0;
    $so_sinh_vien = 0;
    $phancong__Get_By_Id_Giang_Vien_All = $phancong->phancong__Get_By_Id_Giang_Vien_All($id_nguoi_dung);
    if ($phancong__Get_By_Id_Giang_Vien_All) {
        $so_lop = count($phancong__Get_By_Id_Giang_Vien_All);
        foreach ($phancong__Get_By_Id_Giang_Vien_All as $pc) {
            $sv_in_lop = $sinhvien->sinhvien__Get_By_Id_Lop_Hoc($pc->id_lop_hoc);
            if ($sv_in_lop) $so_sinh_vien += count($sv_in_lop);
        }
    }
    
    $trinh_do_str = "Chưa cập nhật";
    if ($gv && isset($gv->id_trinh_do)) {
        $td = $trinhdo->trinhdo__Get_By_Id($gv->id_trinh_do);
        if($td) $trinh_do_str = $td->ten_trinh_do;
    }
?>
<div class="dashboard-container">
    
    <div class="row">
        <!-- Thông tin giảng viên -->
        <div class="col-md-8">
            <div class="custom-card">
                <h3 class="card-title-custom">Thông tin giảng viên</h3>
                <div class="student-info-wrapper">
                    <div class="student-avatar" style="overflow: hidden; display: flex; align-items: center; justify-content: center;">
                        <i class="ri-user-3-fill" style="font-size: 80px; color: rgba(255,255,255,0.9); margin-top: 15px;"></i>
                    </div>
                    <div class="student-details">
                        <div class="student-name-title"><?php echo $gv->ten_giang_vien; ?></div>
                        
                        <p><span class="lbl">Mã GV:</span> <span class="val"><?php echo $gv->ma_giang_vien; ?></span></p>
                        <p><span class="lbl">Họ tên:</span> <span class="val"><?php echo $gv->ten_giang_vien; ?></span></p>
                        
                        <p><span class="lbl">Giới tính:</span> <span class="val"><?php echo $gv->gioi_tinh == 1 ? "Nam" : "Nữ"; ?></span></p>
                        <p><span class="lbl">Ngày sinh:</span> <span class="val"><?php echo date('d-m-Y', strtotime($gv->ngay_sinh)); ?></span></p>
                        
                        <p><span class="lbl">Email:</span> <span class="val"><?php echo $gv->email; ?></span></p>
                        <p><span class="lbl">Trạng thái:</span> <span class="val">Đang công tác</span></p>
                        
                        <p><span class="lbl">SĐT:</span> <span class="val"><?php echo $gv->so_dien_thoai_1; ?></span></p>
                        <p><span class="lbl">Trình độ:</span> <span class="val"><?php echo $trinh_do_str; ?></span></p>
                        
                        <p><span class="lbl">Địa chỉ LL:</span> <span class="val"><?php echo $gv->dia_chi_lien_lac; ?></span></p>
                        <p><span class="lbl">Địa chỉ TT:</span> <span class="val"><?php echo $gv->dia_chi_thuong_tru; ?></span></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thông báo -->
        <div class="col-md-4">
            <div class="custom-card" style="height: calc(100% - 10px);">
                <h3 class="card-title-custom d-flex justify-content-between align-items-center w-100">
                    <span>
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="width: 16px; height: 16px; color: #1d4ed8; stroke: currentColor; margin-right: 4px; vertical-align: -2px;">
                            <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 7h18s-3 0-3-7Z" stroke-width="2" stroke-linecap="round"/>
                            <path d="M10 21a2 2 0 0 0 4 0" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        Thông báo
                    </span>
                    <a href="?page=thong-bao" style="font-size: 0.85rem; font-weight: normal; color: #1d4ed8; text-decoration: none;">Xem chi tiết <i class="ri-arrow-right-s-line"></i></a>
                </h3>
                <div class="notification-list">
                    <?php if (count($list_thong_bao) > 0): ?>
                        <?php 
                        $count = 0;
                        foreach ($list_thong_bao as $tb): 
                            if ($count >= 4) break; // Chỉ hiển thị 4 thông báo mới nhất
                            $time = strtotime($tb->ngay_tao);
                            $month = "Th" . date('n', $time);
                            $day = date('d', $time);
                            $hour = date('H:i', $time);
                        ?>
                        <a href="?page=thong-bao" class="notification-item" style="<?php echo $tb->is_read ? 'opacity: 0.7;' : ''; ?>">
                            <div class="notif-date"><?php echo $month; ?><span><?php echo $day; ?></span></div>
                            <div class="notif-content">
                                <p class="notif-title" style="<?php echo !$tb->is_read ? 'font-weight: bold; color: #111827;' : ''; ?>">
                                    <?php echo htmlspecialchars($tb->tieu_de); ?>
                                </p>
                                <p class="notif-desc" style="font-size: 13px; color: #4b5563; margin-bottom: 6px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; line-height: 1.4;">
                                    <?php echo htmlspecialchars($tb->noi_dung); ?>
                                </p>
                                <p class="notif-meta"><?php echo htmlspecialchars($tb->nguoi_gui); ?> &bull; <?php echo $hour; ?></p>
                            </div>
                        </a>
                        <?php 
                            $count++;
                        endforeach; 
                        ?>
                    <?php else: ?>
                        <div class="text-center p-3 text-muted" style="font-size: 14px;">
                            Bạn không có thông báo nào.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions-row">
        <a href="?page=cham-diem" class="action-btn">
            <i class="ri-edit-box-line"></i>
            <span>Chấm điểm rèn luyện</span>
        </a>
    </div>

    <!-- Stats Row (Giảng viên) -->
    <div class="row">
        <div class="col-md-6">
            <div class="custom-card" style="height: calc(100% - 10px);">
                <div class="card-title-custom mb-3" style="border-bottom: 1px solid #e8ecf3;">
                    <span class="mb-0">Tổng số lớp phụ trách</span>
                </div>
                <div style="height: 120px; display: flex; align-items: center; justify-content: center; font-size: 48px; font-weight: bold; color: #1d4ed8;">
                    <?php echo $so_lop; ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="custom-card" style="height: calc(100% - 10px);">
                <div class="card-title-custom mb-3" style="border-bottom: 1px solid #e8ecf3;">
                    <span class="mb-0">Tổng số sinh viên phụ trách</span>
                </div>
                <div style="height: 120px; display: flex; align-items: center; justify-content: center; font-size: 48px; font-weight: bold; color: #1d4ed8;">
                    <?php echo $so_sinh_vien; ?>
                </div>
            </div>
        </div>
    </div>

</div>
