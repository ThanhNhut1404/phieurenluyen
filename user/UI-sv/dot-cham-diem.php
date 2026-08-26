<?php
// Lấy id sinh viên từ session
$id_sinh_vien = "";
if(isset($_SESSION['sv'])){
    $id_sinh_vien = $_SESSION['sv']->id_nguoi_dung;
} else if(isset($_SESSION['lt'])){
    $id_sinh_vien = $_SESSION['lt']->id_nguoi_dung;
} else if(isset($_SESSION['bt'])){
    $id_sinh_vien = $_SESSION['bt']->id_nguoi_dung;
}

$sv = $sinhvien->sinhvien__Get_By_Id($id_sinh_vien);

if (!isset($_GET['id_dot'])) {
    // Hiển thị danh sách các đợt chấm điểm để chọn
    $dots = $dotchamdiem->dotchamdiem__Get_By_Id_Lop_Hoc($sv->id_lop_hoc);
?>
<div class="dashboard-container">
    <div class="ketqua-card custom-card">
                <div class="ketqua-header">
                    <h3 class="ketqua-title">Đợt chấm điểm</h3>
                </div>
                <div class="p-4">
                    <?php if(count($dots) > 0): ?>
                        <div class="row">
                        <?php foreach($dots as $dot): ?>
                            <div class="col-md-6 col-lg-3 mb-4">
                                <div class="card h-100 shadow-sm" style="border-radius: 12px; border: 1px solid #e8ecf3;">
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="text-primary font-weight-bold mb-3"><?= $dot->ten_dot ?></h5>
                                        <p class="card-text text-muted mb-2"><i class="fas fa-calendar-alt mr-2" style="width: 16px;"></i> Học kỳ <?= $dot->ten_hoc_ky ?> - <?= $dot->ten_nam_hoc ?></p>
                                        <p class="card-text text-muted mb-4"><i class="fas fa-clock mr-2" style="width: 16px;"></i> <?= date('d/m/Y', strtotime($dot->thoi_gian_bat_dau)) ?> đến <?= date('d/m/Y', strtotime($dot->thoi_gian_ket_thuc)) ?></p>
                                        
                                        <div class="mt-auto">
                                            <a href="?page=dot-cham-diem&id_dot=<?= $dot->id_dot ?>" class="btn btn-primary btn-block" style="border-radius: 8px; font-weight: 600;">
                                                <i class="fas fa-edit mr-1"></i> Vào phiếu chấm
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info text-center py-4" style="background-color: #f8f9fb; border-color: #e8ecf3; color: #6b7280;">
                            <i class="fas fa-info-circle fa-2x mb-3 d-block" style="color: #1d4ed8;"></i>
                            <h5 class="font-weight-bold" style="color: #333;">Lớp của bạn hiện không có đợt chấm điểm nào</h5>
                            <p class="mb-0">Vui lòng quay lại sau khi nhà trường mở đợt chấm điểm mới.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
</div>
<?php
} else {
    // Đã chọn đợt, hiển thị form chấm điểm (kế thừa từ file cũ)
    if (isset($_SESSION['lt'])) {
        require "../lop-truong/index.php";
    } else if (isset($_SESSION['bt'])) {
        require "../bi-thu-chi-doan/index.php";
    } else {
        require "../sinh-vien/index.php";
    }
}
?>
