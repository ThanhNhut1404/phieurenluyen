<?php
require_once 'core.php';

$email = 'nvquan-cntt17@tdu.edu.vn';
$tk = $taikhoan->taikhoan__Get_By_Email($email);
if ($tk) {
    echo "Taikhoan ID: " . $tk->id_tai_khoan . "\n";
    echo "Phan quyen: " . $tk->id_phan_quyen . "\n";
    echo "Phan nhom: " . $tk->id_phan_nhom . "\n";
    echo "Nguoi dung ID: " . $tk->id_nguoi_dung . "\n";
    
    $pn = $phannhom->phannhom__Get_By_Id($tk->id_phan_nhom);
    echo "Cap bac phan nhom: " . $pn->cap_bac . "\n";
    
    if ($pn->cap_bac == 2 || $pn->cap_bac == 3) {
        $sv = $sinhvien->sinhvien__Get_By_Id($tk->id_nguoi_dung);
        if ($sv) {
            echo "Ten sinh vien: " . $sv->ten_sinh_vien . "\n";
        } else {
            echo "Khong tim thay sinh vien\n";
        }
    } else {
        echo "Cap bac khong phai la 2 hoac 3, thu tim trong giang vien...\n";
        $gv = $giangvien->giangvien__Get_By_Id($tk->id_nguoi_dung);
        if ($gv) {
            echo "Ten giang vien: " . $gv->ten_giang_vien . "\n";
        } else {
            echo "Khong tim thay giang vien\n";
        }
    }
} else {
    echo "Khong tim thay tai khoan\n";
}
?>
