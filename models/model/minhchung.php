<?php

$a = "./models/configs/config.php";
$b = "../models/configs/config.php";
$c = "../../models/configs/config.php";
$d = "../../../models/configs/config.php";
$e = "../../../../models/configs/config.php";

if (file_exists($a)) {
    $des = $a;
}
if (file_exists($b)) {
    $des = $b;
}
if (file_exists($c)) {
    $des = $c;
} 
if (file_exists($d)) {
    $des = $d;
} 

if (file_exists($e)) {
    $des = $e;
} 
include_once($des);

class minhchung extends Database {

    public function minhchung__Get_All() {
        $obj = $this->connect->prepare("SELECT * FROM minhchung");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute();
        return $obj->fetchAll();
    }
    
    public function minhchung__Add($id_phieu, $hinh_anh, $ghi_chu, $id_muc = null, $ai_trang_thai = 0, $ai_nhan_xet = null) {
        try {
            $obj = $this->connect->prepare("INSERT INTO minhchung(id_phieu, hinh_anh, ghi_chu, id_muc, ai_trang_thai, ai_nhan_xet) VALUES (?,?,?,?,?,?)");
            $obj->execute(array($id_phieu, $hinh_anh, $ghi_chu, $id_muc, $ai_trang_thai, $ai_nhan_xet));
            return $obj->rowCount();
        } catch (PDOException $e) {
            if ($e->getCode() == '08S01' || strpos($e->getMessage(), 'max_allowed_packet') !== false) {
                return 0;
            }
            throw $e;
        }
    }

    public function minhchung__Update($id_minh_chung, $id_phieu, $hinh_anh, $ghi_chu) {
        $obj = $this->connect->prepare("UPDATE minhchung SET id_phieu=?, hinh_anh=?, ghi_chu=? WHERE id_minh_chung=?");
        $obj->execute(array($id_phieu, $hinh_anh, $ghi_chu, $id_minh_chung));
        return $obj->rowCount();
    }
    

    public function minhchung__Delete($id_minh_chung) {
        $obj = $this->connect->prepare("DELETE FROM minhchung WHERE id_minh_chung = ?");
        $obj->execute(array($id_minh_chung));
        return $obj->rowCount();
    }

  
    public function minhchung__Get_By_Id($id_minh_chung) {
        $obj = $this->connect->prepare("SELECT * FROM minhchung WHERE id_minh_chung = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_minh_chung));
        return $obj->fetch();
    }

    public function minhchung__Get_By_Id_Phieu($id_phieu) {
        $obj = $this->connect->prepare("SELECT * FROM minhchung WHERE id_phieu = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_phieu));
        return $obj->fetchAll();
    }

    public function minhchung__Get_By_Id_Phieu_And_Muc($id_phieu, $id_muc) {
        $obj = $this->connect->prepare("SELECT * FROM minhchung WHERE id_phieu = ? AND id_muc = ?");
        $obj->setFetchMode(PDO::FETCH_OBJ);
        $obj->execute(array($id_phieu, $id_muc));
        return $obj->fetchAll();
    }

    // Hàm tiện ích: Nén ảnh và trả về chuỗi Base64
    public function minhchung__Compress_Image($source_path, $max_size = 800, $quality = 60) {
        if (filesize($source_path) > 5 * 1024 * 1024) { // 5MB limit cho tất cả file
            return false;
        }

        $mime = mime_content_type($source_path);

        // Hỗ trợ lưu trữ file PDF
        if ($mime == 'application/pdf') {
            $pdf_content = file_get_contents($source_path);
            return "data:application/pdf;base64," . base64_encode($pdf_content);
        }

        // Xử lý nén nếu là file ảnh
        $info = getimagesize($source_path);
        if ($info == false) return false;

        switch ($info['mime']) {
            case 'image/jpeg': $image = imagecreatefromjpeg($source_path); break;
            case 'image/png': $image = imagecreatefrompng($source_path); break;
            case 'image/gif': $image = imagecreatefromgif($source_path); break;
            default: return false;
        }

        $width = $info[0];
        $height = $info[1];
        
        if ($width > $max_size || $height > $max_size) {
            $ratio = $width / $height;
            if ($ratio > 1) {
                $new_width = $max_size;
                $new_height = $max_size / $ratio;
            } else {
                $new_height = $max_size;
                $new_width = $max_size * $ratio;
            }
        } else {
            $new_width = $width;
            $new_height = $height;
        }

        $image_p = imagecreatetruecolor($new_width, $new_height);
        
        // Handle transparency for PNG and GIF
        if ($mime == 'image/png' || $mime == 'image/gif') {
            imagecolortransparent($image_p, imagecolorallocatealpha($image_p, 0, 0, 0, 127));
            imagealphablending($image_p, false);
            imagesavealpha($image_p, true);
        }

        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

        ob_start();
        imagejpeg($image_p, null, $quality); // Convert to JPEG for smaller size
        $compressed_image = ob_get_clean();
        
        imagedestroy($image);
        imagedestroy($image_p);

        return "data:image/jpeg;base64," . base64_encode($compressed_image);
    }

    public function minhchung__Has_By_Id_Dot($id_dot) {
        // Nhựt sửa lỗi: Không cho xóa Đợt chấm điểm nếu Phiếu thuộc Đợt đã có Minh chứng.
        $obj = $this->connect->prepare("SELECT COUNT(*) FROM minhchung, phieuchamdiem, lopapdung WHERE minhchung.id_phieu = phieuchamdiem.id_phieu AND phieuchamdiem.id_lop_ap_dung = lopapdung.id_lop_ap_dung AND lopapdung.id_dot = ?");
        $obj->execute(array($id_dot));
        return (int)$obj->fetchColumn() > 0;
    }

    public function minhchung__Delete_By_Id_Dot($id_dot) {
        // Nhựt sửa lỗi: Khi cho phép xóa Đợt thì phải xóa Minh chứng thuộc các Phiếu của Đợt trước để tránh dữ liệu mồ côi.
        $obj = $this->connect->prepare("DELETE minhchung FROM minhchung, phieuchamdiem, lopapdung WHERE minhchung.id_phieu = phieuchamdiem.id_phieu AND phieuchamdiem.id_lop_ap_dung = lopapdung.id_lop_ap_dung AND lopapdung.id_dot = ?");
        $obj->execute(array($id_dot));
        return $obj->rowCount();
    }

    // Giao tiếp với Google Apps Script (GAS) để Upload ảnh lên Drive
    public function minhchung__Upload_Google_Drive($base64_data, $file_name, $folder_name) {
        $gas_url = "https://script.google.com/macros/s/AKfycbz68oQ-ePClCRWAzy2c9RwpXCXC_5jWQReIhNEbGuBzslQhN2igPpWN3MIO4UyXmgIB1w/exec";
        
        $comma_pos = strpos($base64_data, ",");
        if ($comma_pos !== false) {
            $mime_type = substr($base64_data, 5, $comma_pos - 12);
            if(strpos($mime_type, ";") !== false) {
                $mime_type = explode(";", $mime_type)[0];
            }
            $base64_clean = substr($base64_data, $comma_pos + 1);
        } else {
            $mime_type = "image/jpeg";
            $base64_clean = $base64_data;
        }

        $post_data = [
            "action" => "upload",
            "folderName" => $folder_name,
            "fileName" => $file_name,
            "mimeType" => $mime_type,
            "fileData" => $base64_clean
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $gas_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Nhựt sửa: Bỏ qua kiểm tra SSL trên XAMPP
        $response = curl_exec($ch);
        curl_close($ch);

        $res = json_decode($response, true);
        if ($res && isset($res["status"]) && $res["status"] == "success") {
            return $res["fileId"];
        }
        return false;
    }

    // Tải lên Google Drive CÙNG LÚC NHIỀU FILE (Nhanh gấp 10 lần)
    public function minhchung__Upload_Google_Drive_Multi($requests) {
        if (empty($requests)) return [];
        $gas_url = "https://script.google.com/macros/s/AKfycbz68oQ-ePClCRWAzy2c9RwpXCXC_5jWQReIhNEbGuBzslQhN2igPpWN3MIO4UyXmgIB1w/exec";
        
        $multi_curl = curl_multi_init();
        $curl_handles = [];
        
        foreach ($requests as $idx => $req) {
            $base64_data = $req["base64"];
            $file_name = $req["file_name"];
            $folder_name = $req["folder_name"];
            
            $comma_pos = strpos($base64_data, ",");
            if ($comma_pos !== false) {
                $mime_type = substr($base64_data, 5, $comma_pos - 12);
                if(strpos($mime_type, ";") !== false) {
                    $mime_type = explode(";", $mime_type)[0];
                }
                $base64_clean = substr($base64_data, $comma_pos + 1);
            } else {
                $mime_type = "image/jpeg";
                $base64_clean = $base64_data;
            }

            $post_data = [
                "action" => "upload",
                "folderName" => $folder_name,
                "fileName" => $file_name,
                "mimeType" => $mime_type,
                "fileData" => $base64_clean
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $gas_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Nhựt sửa: Bỏ qua kiểm tra SSL trên XAMPP
            curl_setopt($ch, CURLOPT_TIMEOUT, 60); 
            
            curl_multi_add_handle($multi_curl, $ch);
            $curl_handles[$idx] = $ch;
        }
        
        $active = null;
        do {
            $mrc = curl_multi_exec($multi_curl, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        while ($active && $mrc == CURLM_OK) {
            if (curl_multi_select($multi_curl) != -1) {
                do {
                    $mrc = curl_multi_exec($multi_curl, $active);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }
        
        $results = [];
        foreach ($curl_handles as $idx => $ch) {
            $response = curl_multi_getcontent($ch);
            $res = json_decode($response, true);
            if ($res && isset($res["status"]) && $res["status"] == "success") {
                $results[$idx] = $res["fileId"];
            } else {
                $results[$idx] = false;
            }
            curl_multi_remove_handle($multi_curl, $ch);
        }
        
        curl_multi_close($multi_curl);
        return $results;
    }

    // Xóa file trên Google Drive
    public function minhchung__Delete_Google_Drive($fileId) {
        if(empty($fileId) || strlen($fileId) < 10) return false;
        
        $gas_url = "https://script.google.com/macros/s/AKfycbz68oQ-ePClCRWAzy2c9RwpXCXC_5jWQReIhNEbGuBzslQhN2igPpWN3MIO4UyXmgIB1w/exec";
        $post_data = [
            "action" => "delete",
            "fileId" => $fileId
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $gas_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Nhựt sửa: Bỏ qua kiểm tra SSL trên XAMPP
        $response = curl_exec($ch);
        curl_close($ch);
        
        return true; 
    }
}
?>
