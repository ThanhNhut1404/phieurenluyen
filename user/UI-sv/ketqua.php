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

// Ensure the models are loaded. They are loaded in header.php which included getModel.php, 
// so $ketquaxeploai and $xeploai instances are available.
$id_sv = $id_sinh_vien; // from header.php
$list_ketqua = $ketquaxeploai->ketquaxeploai__Get_By_Id_Sinh_Vien_With_HocKy_NamHoc($id_sv);

$years = [];
$semesters = [];
foreach ($list_ketqua as $kq) {
    $years[$kq->ten_nam_hoc] = 1;
    $semesters[$kq->ten_hoc_ky] = 1;
}
$years = array_keys($years);
$semesters = array_keys($semesters);
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
    position: relative;
    z-index: 10;
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
    border-radius: 8px;
    border: 1px solid #d1d5db;
    background-color: #fff;
    color: #374151;
    font-size: 15px;
    outline: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 15px center;
    background-size: 16px;
    cursor: pointer;
}
.filter-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
.result-card {
    background: #f4f6eb;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    border: 1px solid #e2e8f0;
}
.result-card-title {
    color: #1d4ed8;
    font-weight: 700;
    font-size: 1.1rem;
    margin-bottom: 15px;
    border-bottom: 1px solid #d1d5db;
    padding-bottom: 10px;
}
.result-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    color: #4b5563;
    font-size: 15px;
}
.result-row.total {
    font-weight: 700;
    color: #111827;
}
.classification-box {
    background: #e0f2fe;
    border-radius: 8px;
    padding: 12px 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 15px;
}
.classification-box span:first-child {
    color: #1d4ed8;
    font-weight: 500;
}
.classification-box span:last-child {
    color: #1d4ed8;
    font-weight: 700;
    font-size: 1.15rem;
}
.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #6b7280;
    background: #fff;
    border-radius: 12px;
    border: 1px dashed #d1d5db;
}
</style>

<div class="dashboard-container">
    <div class="d-flex align-items-center mb-4">
        <h3 class="mb-0 font-weight-bold" style="color: #1d4ed8; font-size: 24px;">Kết quả rèn luyện</h3>
    </div>

    <!-- Filter Section -->
    <div class="filter-card">
        <div class="row">
            <div class="col-md-6 mb-3 mb-md-0">
                <label class="filter-label">Năm học</label>
                <select id="filterYear" class="filter-select" onchange="filterResults()">
                    <option value="all">Tất cả</option>
                    <?php foreach ($years as $y): ?>
                        <option value="<?= htmlspecialchars($y) ?>"><?= htmlspecialchars($y) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="filter-label">Học kỳ</label>
                <select id="filterSemester" class="filter-select" onchange="filterResults()">
                    <option value="all">Tất cả</option>
                    <?php foreach ($semesters as $s): ?>
                        <option value="<?= htmlspecialchars($s) ?>"><?= htmlspecialchars($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <!-- Results Section -->
    <div id="resultsContainer">
        <?php if (empty($list_ketqua)): ?>
            <div class="empty-state">
                <i class="ri-hourglass-2-line" style="font-size: 3rem; color: #9ca3af; margin-bottom: 10px;"></i>
                <h5>Đang đợi kết quả cuối...</h5>
                <p class="mb-0">Bạn chưa có kết quả rèn luyện nào được tổng kết.</p>
            </div>
        <?php else: ?>
            <?php foreach ($list_ketqua as $kq): ?>
                <?php
                    // Lấy điểm sinh viên tự chấm từ bảng phieuchamdiem
                    $diem_sv = "-";
                    if (isset($kq->id_phieu)) {
                        $phieu = $phieuchamdiem->phieuchamdiem__Get_By_Id($kq->id_phieu);
                        if ($phieu && !empty($phieu->kq_sv)) {
                            $arr = $phieuchamdiem->phieuchamdiem__Get_Ket_Qua($phieu->kq_sv);
                            $diem_sv = array_sum($arr);
                        }
                    }
                    
                    $title = "Chấm điểm " . strtolower($kq->ten_hoc_ky) . " (" . $kq->ten_hoc_ky . " - " . $kq->ten_nam_hoc . ")";
                ?>
                <div class="result-card" data-year="<?= htmlspecialchars($kq->ten_nam_hoc) ?>" data-semester="<?= htmlspecialchars($kq->ten_hoc_ky) ?>">
                    <div class="result-card-title"><?= htmlspecialchars($title) ?></div>
                    
                    <div class="result-row">
                        <span>Điểm SV tự chấm:</span>
                        <span class="font-weight-bold" style="color: #111827;"><?= $diem_sv ?></span>
                    </div>
                    <div class="result-row total">
                        <span>Điểm tổng cuối cùng:</span>
                        <span><?= floatval($kq->ket_qua) ?></span>
                    </div>
                    
                    <div class="classification-box">
                        <span>Xếp loại:</span>
                        <span><?= htmlspecialchars($kq->xep_loai) ?></span>
                    </div>
                    <?php if (!empty($kq->ghi_chu)): ?>
                        <div class="mt-3 text-muted" style="font-size: 13px;">
                            <i class="ri-information-line"></i> <?= htmlspecialchars($kq->ghi_chu) ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            
            <div id="noResultMsg" class="empty-state" style="display: none;">
                <i class="ri-search-line" style="font-size: 3rem; color: #9ca3af; margin-bottom: 10px;"></i>
                <h5>Không tìm thấy kết quả</h5>
                <p class="mb-0">Không có kết quả rèn luyện nào phù hợp với bộ lọc của bạn.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function filterResults() {
    const year = document.getElementById('filterYear').value;
    const semester = document.getElementById('filterSemester').value;
    const cards = document.querySelectorAll('.result-card');
    let visibleCount = 0;
    
    cards.forEach(card => {
        const cardYear = card.getAttribute('data-year');
        const cardSemester = card.getAttribute('data-semester');
        
        let matchYear = (year === 'all' || cardYear === year);
        let matchSemester = (semester === 'all' || cardSemester === semester);
        
        if (matchYear && matchSemester) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    const noResultMsg = document.getElementById('noResultMsg');
    if (noResultMsg) {
        if (visibleCount === 0 && cards.length > 0) {
            noResultMsg.style.display = 'block';
        } else {
            noResultMsg.style.display = 'none';
        }
    }
}
</script>
