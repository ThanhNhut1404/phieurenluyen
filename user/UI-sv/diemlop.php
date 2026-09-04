<?php
// Function to get CSS badge class
function getBadgeClass($xeploai) {
    switch (strtolower(trim($xeploai))) {
        case 'xuất sắc': return 'badge-xuatsac';
        case 'tốt': return 'badge-tot';
        case 'khá': return 'badge-kha';
        case 'trung bình': return 'badge-trungbinh';
        case 'yếu': return 'badge-yeu';
        case 'kém': return 'badge-kem';
        default: return 'badge-kha';
    }
}

$sv = $sinhvien->sinhvien__Get_By_Id($id_sinh_vien);
$id_lop = $sv->id_lop_hoc;
$list_ketqua = $ketquaxeploai->ketquaxeploai__Get_By_Id_Lop_Hoc_With_HocKy_NamHoc($id_lop);

$filter_options = [];
foreach ($list_ketqua as $kq) {
    $option_name = $kq->ten_hoc_ky . " - " . $kq->ten_nam_hoc;
    if (!in_array($option_name, $filter_options)) {
        $filter_options[] = $option_name;
    }
}

$selected_filter = isset($_GET['filter']) ? $_GET['filter'] : (count($filter_options) > 0 ? $filter_options[0] : "");

$filtered_data = [];
foreach ($list_ketqua as $kq) {
    $option_name = $kq->ten_hoc_ky . " - " . $kq->ten_nam_hoc;
    if ($option_name === $selected_filter) {
        $filtered_data[] = $kq;
    }
}
?>

<style>
.dashboard-container {
    padding: 20px 30px;
}
.filter-card {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 25px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.02);
}
.filter-label {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 8px;
    display: block;
}
.filter-select {
    width: 100%;
    padding: 10px 15px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-family: inherit;
    font-size: 14px;
    color: #374151;
    background-color: #f9fafb;
}
.table-card {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    overflow-x: auto;
}
.result-table {
    width: 100%;
    border-collapse: collapse;
}
.result-table th, .result-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #f3f4f6;
}
.result-table th {
    background-color: #f8fafc;
    color: #475569;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
}
.result-table tr:hover {
    background-color: #f8fafc;
}
.result-table td {
    color: #334155;
    font-size: 14px;
}
.student-name {
    font-weight: 600;
}
.badge-xeploai {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}
.badge-xuatsac { background-color: #dcfce7; color: #166534; }
.badge-tot { background-color: #dbeafe; color: #1e40af; }
.badge-kha { background-color: #fef9c3; color: #854d0e; }
.badge-trungbinh { background-color: #ffedd5; color: #9a3412; }
.badge-yeu { background-color: #fee2e2; color: #991b1b; }
.badge-kem { background-color: #f3f4f6; color: #374151; }
.point-value {
    font-weight: 700;
    color: #0f172a;
}
.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #64748b;
}
</style>

<div class="dashboard-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="m-0 text-dark" style="font-weight: 700;">Điểm rèn luyện của lớp</h3>
    </div>

    <?php if (count($filter_options) > 0): ?>
    <div class="filter-card">
        <div class="row">
            <div class="col-md-6">
                <label class="filter-label">Chọn Học kỳ - Năm học</label>
                <form id="filterForm" method="GET" action="index.php">
                    <input type="hidden" name="page" value="diemlop">
                    <select name="filter" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                        <?php foreach ($filter_options as $opt): ?>
                            <option value="<?= htmlspecialchars($opt) ?>" <?= $opt === $selected_filter ? "selected" : "" ?>>
                                <?= htmlspecialchars($opt) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="table-card">
        <?php if (count($filtered_data) > 0): ?>
            <table class="result-table">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Mã Sinh Viên</th>
                        <th>Họ và Tên</th>
                        <th>Điểm Tổng Kết</th>
                        <th>Xếp Loại</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $stt = 1; foreach ($filtered_data as $kq): ?>
                        <tr>
                            <td><?= $stt++ ?></td>
                            <td><?= htmlspecialchars($kq->ma_sinh_vien) ?></td>
                            <td class="student-name"><?= htmlspecialchars($kq->ten_sinh_vien) ?></td>
                            <td class="point-value"><?= floatval($kq->ket_qua) ?></td>
                            <td>
                                <span class="badge-xeploai <?= getBadgeClass($kq->xep_loai) ?>">
                                    <?= htmlspecialchars($kq->xep_loai) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <i class="bx bx-file-blank" style="font-size: 48px; margin-bottom: 15px; color: #cbd5e1;"></i>
                <h5>Chưa có dữ liệu</h5>
                <p>Lớp của bạn chưa có kết quả rèn luyện nào được xử lý cho học kỳ này.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
