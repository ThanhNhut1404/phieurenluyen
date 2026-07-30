<?php
require '../../models/configs/config.php';
require '../../models/getModel.php';

// quân sửa: API trả về các Thứ tự còn trống dựa theo số lượng mục của Khoản
if (isset($_POST['id_khoan'])) {
    $id_khoan = $_POST['id_khoan'];
    $current_thu_tu = isset($_POST['current_thu_tu']) ? $_POST['current_thu_tu'] : 0;
    
    $khoan_info = $khoan->khoan__Get_By_Id($id_khoan);
    $so_luong = (isset($khoan_info->so_luong_muc) && $khoan_info->so_luong_muc > 0) ? $khoan_info->so_luong_muc : 10;
    
    $all_muc = $muc->muc__Get_By_Id_Khoan($id_khoan);
    $used = array();
    foreach ($all_muc as $item) {
        // Loại bỏ current_thu_tu khỏi danh sách đã sử dụng để nó hiện ra cho form cập nhật
        if ($item->thu_tu != $current_thu_tu) {
            $used[] = $item->thu_tu;
        }
    }
    
    $has_option = false;
    echo '<option value="">-- Chọn thứ tự --</option>';
    for ($i = 1; $i <= $so_luong; $i++) {
        if (!in_array($i, $used)) {
            $selected = ($i == $current_thu_tu) ? 'selected' : '';
            echo '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
            $has_option = true;
        }
    }
    
    if (!$has_option) {
        echo '<option value="" disabled>Đã hết số lượng mục cho phép!</option>';
    }
}
?>
