<?php
require_once 'core.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response_json("error", "Invalid request method");
}

// Lấy token từ header
$token = get_bearer_token();
if (!$token) {
    response_json("error", "Vui lòng đăng nhập (Missing Token)", null, 401);
}

// Kiểm tra token có hợp lệ không
$tai_khoan = $taikhoan->taikhoan__Get_By_Token($token);
if ($tai_khoan == "0") {
    response_json("error", "Token không hợp lệ hoặc đã hết hạn", null, 401);
}

// Lấy dữ liệu chấm điểm từ App
$data = json_decode(file_get_contents("php://input"), true);
$id_phieu = $data['id_phieu'] ?? $_POST['id_phieu'] ?? '';
$kq_sv = $data['kq_sv'] ?? $_POST['kq_sv'] ?? []; // Mảng chứa điểm của từng mục
$minh_chung = $data['minh_chung'] ?? $_POST['minh_chung'] ?? []; // Mảng chứa minh chứng base64

if (empty($id_phieu) || empty($kq_sv) || !is_array($kq_sv)) {
    response_json("error", "Dữ liệu chấm điểm không hợp lệ");
}

// Kiểm tra quyền sở hữu phiếu
$phieu = $phieuchamdiem->phieuchamdiem__Get_By_Id($id_phieu);
if (!$phieu || $phieu->id_sinh_vien != $tai_khoan->id_nguoi_dung) {
    response_json("error", "Bạn không có quyền chấm phiếu này");
}

// Chuyển mảng từ app (id_muc-diem) thành mảng kết hợp
$diem_sv = [];
foreach ($kq_sv as $item) {
    $parts = explode('-', $item);
    if (count($parts) == 2) {
        $diem_sv[$parts[0]] = $parts[1];
    }
}

// Lấy danh sách id_muc theo đúng thực tế của mẫu phiếu
$kq_string_arr = [];
if ($phieu) {
    $lop_ap_dung = $lopapdung->lopapdung__Get_By_Id($phieu->id_lop_ap_dung);
    if ($lop_ap_dung) {
        $bocauhoi_list = $bocauhoi->bocauhoi__Get_By_Id_Mau_Phieu($lop_ap_dung->id_mau_phieu);
        foreach ($bocauhoi_list as $item_1) {
            $khoan_list = $khoan->khoan__Get_All_By_Id_Dieu($item_1->id_dieu);
            foreach ($khoan_list as $item_2) {
                $muc_list = $muc->muc__Get_All_By_Id_Khoan($item_2->id_khoan);
                foreach ($muc_list as $item_3) {
                    $id_muc = $item_3->id_muc;
                    // Lấy điểm nếu có, không thì bằng 0 (hoặc chuỗi rỗng)
                    $diem = isset($diem_sv[$id_muc]) ? $diem_sv[$id_muc] : 0;
                    $kq_string_arr[] = $diem;
                }
            }
        }
    }
}

// Chuyển mảng thành chuỗi lưu database: "5|10|0|5" (đúng định dạng của Web)
$kq_string = implode("|", $kq_string_arr);

// Lưu vào cơ sở dữ liệu
$status = $phieuchamdiem->phieuchamdiem__Update_Kq_Sv($id_phieu, $kq_string);
$phieuchamdiem->phieuchamdiem__Update_Trang_Thai($id_phieu, 2);

// Xử lý lưu minh chứng
if (is_array($minh_chung) && count($minh_chung) > 0) {
    // Lấy danh sách minh chứng cũ để xóa khỏi DB và Google Drive nếu không dùng nữa
    $danh_sach_mc_cu = $minhchung->minhchung__Get_By_Id_Phieu($id_phieu);
    $new_file_ids = [];

    // Xác định tên thư mục con theo đợt chấm
    $folder_name = "ChuaXacDinh";
    if ($phieu) {
        $lop_ap_dung = $lopapdung->lopapdung__Get_By_Id($phieu->id_lop_ap_dung);
        if ($lop_ap_dung) {
            $dot_cham = $dotchamdiem->dotchamdiem__Get_By_Id($lop_ap_dung->id_dot);
            if ($dot_cham) {
                // Lấy thông tin sinh viên
                $sv = $sinhvien->sinhvien__Get_By_Id($phieu->id_sinh_vien);
                $ten_sv = $sv ? $sv->ten_sinh_vien : "";
                
                $base_folder = $dot_cham->ten_hoc_ky . "_" . $dot_cham->ten_nam_hoc;
                // Làm sạch tên thư mục đợt chấm
                $base_folder = preg_replace('/[^A-Za-z0-9\-\_]/', '', str_replace(' ', '_', $base_folder));
                
                // Làm sạch tên sinh viên và nối vào tên thư mục
                $ten_sv_clean = preg_replace('/[^A-Za-z0-9\-\_\s]/', '', $ten_sv);
                $folder_name = $base_folder . "/" . $tai_khoan->ten_dang_nhap . "_" . str_replace(' ', '', $ten_sv_clean);
            }
        }
    }

    $order_counter = [];
    $mssv = $tai_khoan->ten_dang_nhap; 

    // Chuẩn bị danh sách upload
    $upload_requests = [];
    $upload_mappings = []; // map index to id_muc

    foreach ($minh_chung as $mc_item) {
        if (!empty($mc_item['id_muc']) && !empty($mc_item['base64'])) {
            $id_muc = $mc_item['id_muc'];
            $base64 = $mc_item['base64'];
            
            if (!isset($order_counter[$id_muc])) $order_counter[$id_muc] = 0;
            $order_counter[$id_muc]++;
            $order = $order_counter[$id_muc];
            
            if (strlen($base64) < 100 || strpos($base64, 'https://') === 0) {
                // Đã là fileId cũ hoặc link Drive cũ, lưu luôn
                $new_file_ids[] = $base64;
                $minhchung->minhchung__Add($id_phieu, $base64, date("Y-m-d H:i:s"), $id_muc, 0, null);
            } else {
                // Đưa vào hàng đợi upload đa luồng
                $muc_info = $muc->muc__Get_By_Id($id_muc);
                $ten_muc_clean = "Muc" . $id_muc;
                if ($muc_info) {
                    $cleaned = preg_replace('/[^A-Za-z0-9\-\_\s]/', '', $muc_info->ten_muc);
                    if (!empty(trim($cleaned))) {
                        $ten_muc_clean = trim($cleaned);
                    }
                }
                
                $file_name = $mssv . "_" . $ten_muc_clean . "_" . $order . ".jpg";
                
                $upload_requests[] = [
                    'base64' => $base64,
                    'file_name' => $file_name,
                    'folder_name' => $folder_name
                ];
                $upload_mappings[] = $id_muc;
            }
        }
    }
    
    // Thực thi upload đa luồng siêu tốc
    if (!empty($upload_requests)) {
        $uploaded_fileIds = $minhchung->minhchung__Upload_Google_Drive_Multi($upload_requests);
        foreach ($uploaded_fileIds as $idx => $fileId) {
            if ($fileId) {
                $id_muc = $upload_mappings[$idx];
                $full_url = "https://drive.google.com/uc?id=" . $fileId;
                $new_file_ids[] = $full_url;
                $minhchung->minhchung__Add($id_phieu, $full_url, date("Y-m-d H:i:s"), $id_muc, 0, null);
            }
        }
    }
    
    // Dọn dẹp minh chứng cũ
    if (is_array($danh_sach_mc_cu)) {
        foreach ($danh_sach_mc_cu as $mc) {
            $old_fileId = $mc->hinh_anh;
            
            // Xóa file trên Google Drive TRƯỚC
            // Nếu file cũ không nằm trong danh sách mới (tức là sinh viên đã xóa/thay thế), thì xóa trên Drive
            if ((strlen($old_fileId) < 100 || strpos($old_fileId, 'https://') === 0) && !in_array($old_fileId, $new_file_ids)) {
                // Extract fileId from URL if it's a URL
                $drive_fileId = $old_fileId;
                if (strpos($old_fileId, 'id=') !== false) {
                    $drive_fileId = explode('id=', $old_fileId)[1];
                }
                $minhchung->minhchung__Delete_Google_Drive($drive_fileId);
            }
            
            // Xóa trên Database SAU
            $minhchung->minhchung__Delete($mc->id_minh_chung);
        }
    }
}

if ($status !== false) {
    response_json("success", "Lưu điểm rèn luyện thành công!");
} else {
    response_json("error", "Lỗi khi lưu điểm rèn luyện");
}
?>
