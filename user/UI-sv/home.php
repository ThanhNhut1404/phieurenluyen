<?php
    $id_sinh_vien = "";
    if(isset($_SESSION['sv'])){
        $id_sinh_vien = $_SESSION['sv']->id_nguoi_dung;
    } else if(isset($_SESSION['lt'])){
        $id_sinh_vien = $_SESSION['lt']->id_nguoi_dung;
    } else if(isset($_SESSION['bt'])){
        $id_sinh_vien = $_SESSION['bt']->id_nguoi_dung;
    }

    $sv = $sinhvien->sinhvien__Get_By_Id($id_sinh_vien);
    $lop = $lophoc->lophoc__Get_By_Id($sv->id_lop_hoc);
    $nganh = $nganhhoc->nganhhoc__Get_By_Id($lop->id_nganh_hoc);
    $khoa = $khoahoc->khoahoc__Get_By_Id($lop->id_khoa_hoc);
?>
<div class="dashboard-container">
    
    <div class="row">
        <!-- Thông tin sinh viên -->
        <div class="col-md-8">
            <div class="custom-card">
                <h3 class="card-title-custom">Thông tin sinh viên</h3>
                <div class="student-info-wrapper">
                    <div class="student-avatar">
                        <img src="../../assets/img/user.png" alt="Avatar">
                    </div>
                    <div class="student-details">
                        <div class="student-name-title"><?php echo $sv->ten_sinh_vien; ?></div>
                        
                        <p><span class="lbl">MSSV:</span> <span class="val"><?php echo $sv->ma_sinh_vien; ?></span></p>
                        <p><span class="lbl">Họ tên:</span> <span class="val"><?php echo $sv->ten_sinh_vien; ?></span></p>
                        
                        <p><span class="lbl">Giới tính:</span> <span class="val"><?php echo $sv->gioi_tinh == 1 ? "Nam" : "Nữ"; ?></span></p>
                        <p><span class="lbl">Ngày sinh:</span> <span class="val"><?php echo $sv->ngay_sinh; ?></span></p>
                        
                        <p><span class="lbl">Địa chỉ:</span> <span class="val"><?php echo $sv->dia_chi_thuong_tru; ?></span></p>
                        <p><span class="lbl">Trạng thái:</span> <span class="val">Đang học</span></p>
                        
                        <p><span class="lbl">Sinh viên năm thứ:</span> <span class="val"></span></p>
                        <p><span class="lbl">Lớp học:</span> <span class="val"><?php echo $lop->ten_lop_hoc; ?></span></p>
                        
                        <p><span class="lbl">Khóa học:</span> <span class="val"><?php echo $khoa->ten_khoa_hoc; ?></span></p>
                        <p><span class="lbl">Bậc đào tạo:</span> <span class="val"></span></p>
                        
                        <p><span class="lbl">Loại hình đào tạo:</span> <span class="val"></span></p>
                        <p><span class="lbl">Ngành:</span> <span class="val"><?php echo $nganh->ten_nganh_hoc; ?></span></p>
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
                    <a href="#" style="font-size: 0.85rem; font-weight: normal; color: #1d4ed8; text-decoration: none;">Xem chi tiết <i class="ri-arrow-right-s-line"></i></a>
                </h3>
                <div class="notification-list">
                    <a href="#" class="notification-item">
                        <div class="notif-date">Th4<span>24</span></div>
                        <div class="notif-content">
                            <p class="notif-title">Thông báo cập nhật điểm rèn luyện học kỳ I</p>
                            <p class="notif-meta">Phòng CTSV &bull; 08:30</p>
                        </div>
                    </a>
                    <a href="#" class="notification-item">
                        <div class="notif-date">Th4<span>22</span></div>
                        <div class="notif-content">
                            <p class="notif-title">Hướng dẫn đăng ký hoạt động ngoại khóa</p>
                            <p class="notif-meta">Đoàn - Hội &bull; 14:10</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions-row">
        <a href="index.php?page=ket-qua" class="action-btn">
            <i class="ri-bar-chart-2-line"></i>
            <span>Kết quả rèn luyện</span>
        </a>
        <a href="#" class="action-btn">
            <i class="ri-calendar-event-line"></i>
            <span>Lịch hoạt động</span>
        </a>
        <a href="#" class="action-btn">
            <i class="ri-survey-line"></i>
            <span>Đăng ký hoạt động</span>
        </a>
        <!-- LINK TO EVALUATION FORM -->
        <a href="../index.php?page=thong-ke" class="action-btn">
            <i class="ri-file-list-3-line"></i>
            <span>Phiếu đánh giá</span>
        </a>
        <a href="#" class="action-btn">
            <i class="ri-add-box-line"></i>
            <span>Hoạt động đã đăng ký</span>
        </a>
        <a href="#" class="action-btn">
            <i class="ri-user-follow-line"></i>
            <span>Điểm danh</span>
        </a>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-md-4">
            <div class="custom-card" style="height: calc(100% - 10px);">
                <h3 class="card-title-custom">Hoạt động đã đăng ký</h3>
                <canvas id="chartRegistered" height="200"></canvas>
            </div>
        </div>
        <div class="col-md-4">
            <div class="custom-card" style="height: calc(100% - 10px);">
                <h3 class="card-title-custom">Tiến độ rèn luyện</h3>
                <div class="chart-placeholder" style="height: calc(100% - 45px);">
                    <i class="ri-pie-chart-line"></i>
                    <p>Chưa có dữ liệu thống kê</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="custom-card" style="height: calc(100% - 10px);">
                <h3 class="card-title-custom">Kết quả rèn luyện</h3>
                <div class="chart-placeholder" style="height: calc(100% - 45px);">
                    <i class="ri-bar-chart-grouped-line"></i>
                    <p>Chưa có dữ liệu thống kê</p>
                </div>
            </div>
        </div>
    </div>
</div>
