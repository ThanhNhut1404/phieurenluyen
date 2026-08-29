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

$grouped_ketqua = [];
foreach ($list_ketqua as $kq) {
    $grouped_ketqua[$kq->ten_nam_hoc][] = $kq;
}
?>

<div class="dashboard-container">
    <div class="custom-card ketqua-card">
        <div class="ketqua-header">
            <h3 class="ketqua-title">Kết quả rèn luyện</h3>
        </div>
        
        <div class="ketqua-content">
            <?php if (empty($grouped_ketqua)): ?>
                <div class="text-center mt-4 mb-4 text-danger font-weight-bold">
                    Bạn chưa có dữ liệu kết quả rèn luyện nào.
                </div>
            <?php else: ?>
                <?php foreach ($grouped_ketqua as $nam_hoc => $hoc_kys): ?>
                    <?php
                        $sum_diem = 0;
                        $count_hk = 0;
                    ?>
                    <div class="table-responsive">
                        <table class="ketqua-table">
                            <thead>
                                <tr class="year-header-row">
                                    <th colspan="4"><?php echo htmlspecialchars($nam_hoc); ?></th>
                                </tr>
                                <tr class="col-header-row">
                                    <th>HỌC KỲ</th>
                                    <th class="text-center">ĐIỂM</th>
                                    <th class="text-center">XẾP LOẠI</th>
                                    <th>GHI CHÚ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($hoc_kys as $hk): ?>
                                    <?php
                                        $sum_diem += $hk->ket_qua;
                                        $count_hk++;
                                        $ghi_chu = !empty($hk->ghi_chu) ? htmlspecialchars($hk->ghi_chu) : 'Không có ghi chú';
                                        $is_empty_note = empty($hk->ghi_chu) ? 'text-muted italic' : '';
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($hk->ten_hoc_ky); ?></td>
                                        <td class="text-center font-weight-bold"><?php echo number_format($hk->ket_qua, 2, ',', '.'); ?></td>
                                        <td class="text-center">
                                            <span class="<?php echo getBadgeClass($hk->xep_loai); ?>">
                                                <?php echo htmlspecialchars($hk->xep_loai); ?>
                                            </span>
                                        </td>
                                        <td class="<?php echo $is_empty_note; ?>"><?php echo $ghi_chu; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                
                                <?php
                                    // Calculate average
                                    $diem_tb = 0;
                                    $xep_loai_tb = '';
                                    if ($count_hk > 0) {
                                        $diem_tb = $sum_diem / $count_hk;
                                        // Lookup classification
                                        $xl_obj = $xeploai->xeploai__Get_By_Kq($diem_tb);
                                        if ($xl_obj) {
                                            $xep_loai_tb = $xl_obj->ten_xep_loai;
                                        } else {
                                            $xep_loai_tb = 'Không phân loại';
                                        }
                                    }
                                ?>
                                <tr class="table-footer-row">
                                    <td class="font-weight-bold text-uppercase">ĐIỂM TRUNG BÌNH:</td>
                                    <td class="text-center font-weight-bold"><?php echo number_format($diem_tb, 2, ',', '.'); ?></td>
                                    <td class="text-center">
                                        <?php if ($xep_loai_tb != 'Không phân loại'): ?>
                                            <span class="<?php echo getBadgeClass($xep_loai_tb); ?>">
                                                <?php echo htmlspecialchars($xep_loai_tb); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted"><?php echo htmlspecialchars($xep_loai_tb); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
