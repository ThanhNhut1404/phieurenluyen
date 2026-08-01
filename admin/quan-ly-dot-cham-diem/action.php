<?php

    session_start();
    require '../../models/getModel.php';

    function dotchamdiem__Redirect($status) {
        // Nhựt sửa lỗi: Dùng chung redirect và dừng xử lý ngay sau header().
        header('location: ../index.php?page=quan-ly-dot-cham-diem&status=' . $status);
        exit();
    }

    function dotchamdiem__Is_Positive_Integer($value) {
        // Nhựt sửa lỗi: Validate id phải là số nguyên dương trước khi xử lý.
        return preg_match('/^[1-9][0-9]*$/', $value);
    }

    function dotchamdiem__Valid_Csrf() {
        // Nhựt sửa lỗi: Kiểm tra CSRF token bằng hash_equals() cho Add/Update.
        return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }

    function dotchamdiem__Valid_Csrf_Get() {
        // Nhựt sửa lỗi: Kiểm tra CSRF token cho thao tác Delete đang gọi qua URL.
        return isset($_GET['csrf_token'], $_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $_GET['csrf_token']);
    }

    function dotchamdiem__Rotate_Csrf_Token() {
        // Nhựt sửa lỗi: Đổi CSRF token sau thao tác thành công để giảm rủi ro submit lại.
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Throwable $e) {
            $_SESSION['csrf_token'] = hash('sha256', session_id() . uniqid('', true));
        }
    }

    function dotchamdiem__Is_Valid_Date($date) {
        // Nhựt sửa lỗi: Kiểm tra ngày đúng định dạng Y-m-d phía server.
        $date_check = DateTime::createFromFormat('Y-m-d', $date);
        return $date_check && $date_check->format('Y-m-d') == $date;
    }

    function dotchamdiem__Validate_Date($thoi_gian_bat_dau, $thoi_gian_ket_thuc) {
        // Nhựt sửa lỗi: Không cho ngày bắt đầu lớn hơn ngày kết thúc.
        if (!dotchamdiem__Is_Valid_Date($thoi_gian_bat_dau) || !dotchamdiem__Is_Valid_Date($thoi_gian_ket_thuc)) {
            return false;
        }
        return strtotime($thoi_gian_bat_dau) <= strtotime($thoi_gian_ket_thuc);
    }

    function dotchamdiem__Validate_Date_In_Hoc_Ky($id_hoc_ky, $thoi_gian_bat_dau, $thoi_gian_ket_thuc, $hocky) {
        // Nhựt sửa lỗi: Thời gian Đợt chấm điểm phải nằm trong khoảng ngày của Học kỳ đã chọn.
        $hoc_ky = $hocky->hocky__Get_By_Id($id_hoc_ky);
        if (!$hoc_ky) {
            return false;
        }
        if (!dotchamdiem__Is_Valid_Date($hoc_ky->ngay_bat_dau) || !dotchamdiem__Is_Valid_Date($hoc_ky->ngay_ket_thuc)) {
            return false;
        }
        return strtotime($thoi_gian_bat_dau) >= strtotime($hoc_ky->ngay_bat_dau)
            && strtotime($thoi_gian_ket_thuc) <= strtotime($hoc_ky->ngay_ket_thuc);
    }

    function dotchamdiem__Validate_Hoc_Ky_Nam_Hoc($id_nam_hoc, $id_hoc_ky, $namhoc, $hocky) {
        // Nhựt sửa lỗi: Validate quan hệ Năm học - Học kỳ ở server, không chỉ dựa vào client.
        if (!dotchamdiem__Is_Positive_Integer($id_nam_hoc) || !dotchamdiem__Is_Positive_Integer($id_hoc_ky)) {
            return 'invalid';
        }
        if (!$namhoc->namhoc__Get_By_Id($id_nam_hoc) || !$hocky->hocky__Get_By_Id($id_hoc_ky)) {
            return 'invalid';
        }
        $hoc_ky = $hocky->hocky__Get_By_Id($id_hoc_ky);
        if (!$hoc_ky || $hoc_ky->id_nam_hoc != $id_nam_hoc) {
            return 'invalid-semester';
        }
        return 'valid';
    }

    function dotchamdiem__Validate_Lop_Hoc($id_lop_hoc, $lophoc) {
        // Nhựt sửa lỗi: Validate danh sách lớp áp dụng và loại bỏ id lớp bị trùng trước khi thêm.
        if (!is_array($id_lop_hoc) || count($id_lop_hoc) == 0) {
            return false;
        }
        $list = array_unique($id_lop_hoc);
        foreach ($list as $id) {
            if (!dotchamdiem__Is_Positive_Integer($id) || !$lophoc->lophoc__Get_By_Id($id)) {
                return false;
            }
        }
        return array_values($list);
    }
    
    if (isset($_GET['req'])){
        switch($_GET['req']){
            case 'get-hoc-ky':
                // Nhựt sửa lỗi: Xử lý AJAX lấy Học kỳ theo Năm học ngay trong action.php, không dùng file API riêng.
                header('Content-Type: application/json; charset=UTF-8');
                $id_nam_hoc = isset($_GET['id_nam_hoc']) ? trim($_GET['id_nam_hoc']) : "";
                if (!dotchamdiem__Is_Positive_Integer($id_nam_hoc) || !$namhoc->namhoc__Get_By_Id($id_nam_hoc)) {
                    echo json_encode(array());
                    exit();
                }
                $hocky__Get_By_Id_Nam_Hoc = $hocky->hocky__Get_By_id_Nam_Hoc($id_nam_hoc);
                $data = array();
                foreach ($hocky__Get_By_Id_Nam_Hoc as $item) {
                    // Nhựt sửa lỗi: Trả thêm khoảng ngày để combobox Học kỳ hiển thị rõ thời gian áp dụng.
                    $data[] = array(
                        "id_hoc_ky" => (int)$item->id_hoc_ky,
                        "ten_hoc_ky" => $item->ten_hoc_ky,
                        "label" => $item->ten_hoc_ky . " (" . date('d/m/Y', strtotime($item->ngay_bat_dau)) . " - " . date('d/m/Y', strtotime($item->ngay_ket_thuc)) . ")"
                    );
                }
                echo json_encode($data);
                exit();

                break;
            case 'add':
                // Nhựt sửa lỗi: Chặn Add khi CSRF token không hợp lệ.
                if (!dotchamdiem__Valid_Csrf()) {
                    dotchamdiem__Redirect('invalid');
                }

                $ten_dot = isset($_POST['ten_dot']) ? trim($_POST['ten_dot']) : "";
                $ghi_chu = isset($_POST['ghi_chu']) ? $_POST['ghi_chu'] : "";
                $thoi_gian_bat_dau = isset($_POST['thoi_gian_bat_dau']) ? trim($_POST['thoi_gian_bat_dau']) : "";
                $thoi_gian_ket_thuc = isset($_POST['thoi_gian_ket_thuc']) ? trim($_POST['thoi_gian_ket_thuc']) : "";
                $id_nam_hoc = isset($_POST['id_nam_hoc']) ? trim($_POST['id_nam_hoc']) : "";
                $id_hoc_ky = isset($_POST['id_hoc_ky']) ? trim($_POST['id_hoc_ky']) : "";
                $id_mau_phieu = isset($_POST['id_mau_phieu']) ? trim($_POST['id_mau_phieu']) : "";
                $id_lop_hoc = isset($_POST['id_lop_hoc']) ? $_POST['id_lop_hoc'] : array();

                // Nhựt sửa lỗi: Validate server các dữ liệu bắt buộc trước khi thêm đợt chấm điểm.
                if ($ten_dot == "" || !dotchamdiem__Is_Positive_Integer($id_mau_phieu) || !$mauphieu->mauphieu__Get_By_Id($id_mau_phieu)) {
                    dotchamdiem__Redirect('invalid');
                }
                // Nhựt sửa lỗi: Kiểm tra Học kỳ phải thuộc Năm học đã chọn.
                $validate_hoc_ky = dotchamdiem__Validate_Hoc_Ky_Nam_Hoc($id_nam_hoc, $id_hoc_ky, $namhoc, $hocky);
                if ($validate_hoc_ky != 'valid') {
                    dotchamdiem__Redirect($validate_hoc_ky);
                }
                // Nhựt sửa lỗi: Mỗi Học kỳ chỉ được phép có một Đợt chấm điểm.
                if ($dotchamdiem->dotchamdiem__Exists_By_Hoc_Ky($id_hoc_ky)) {
                    dotchamdiem__Redirect('duplicate-semester');
                }
                // Nhựt sửa lỗi: Kiểm tra tất cả lớp áp dụng phải tồn tại và bỏ lớp trùng.
                $id_lop_hoc = dotchamdiem__Validate_Lop_Hoc($id_lop_hoc, $lophoc);
                if ($id_lop_hoc === false) {
                    dotchamdiem__Redirect('invalid');
                }
                // Nhựt sửa lỗi: Validate thời gian Add ở server.
                if (!dotchamdiem__Validate_Date($thoi_gian_bat_dau, $thoi_gian_ket_thuc)) {
                    dotchamdiem__Redirect('invalid-date');
                }
                // Nhựt sửa lỗi: Không cho thời gian Đợt chấm điểm vượt ngoài khoảng Học kỳ đã chọn.
                if (!dotchamdiem__Validate_Date_In_Hoc_Ky($id_hoc_ky, $thoi_gian_bat_dau, $thoi_gian_ket_thuc, $hocky)) {
                    dotchamdiem__Redirect('invalid-dot-date');
                }

                // Nhựt sửa lỗi: Dùng chung PDO connection để transaction bao phủ dotchamdiem, lopapdung và phieuchamdiem.
                $lopapdung->connect = $dotchamdiem->connect;
                $phieuchamdiem->connect = $dotchamdiem->connect;
                $sinhvien->connect = $dotchamdiem->connect;

                try {
                    // Nhựt sửa lỗi: Bọc toàn bộ quá trình Add trong một transaction duy nhất.
                    $dotchamdiem->connect->beginTransaction();

                    $id_dot = $dotchamdiem->dotchamdiem__Add($ten_dot, $ghi_chu, $thoi_gian_bat_dau, $thoi_gian_ket_thuc, $id_hoc_ky);
                    // Nhựt sửa lỗi: Nếu thêm đợt không trả về id hợp lệ thì rollback.
                    if (!dotchamdiem__Is_Positive_Integer($id_dot)) {
                        throw new Exception('invalid-dot-id');
                    }

                    foreach($id_lop_hoc as $item_1){
                        $id_lop_ap_dung = $lopapdung->lopapdung__Add($id_dot, $id_mau_phieu, $item_1);
                        // Nhựt sửa lỗi: Nếu thêm lớp áp dụng không trả về id hợp lệ thì rollback.
                        if (!dotchamdiem__Is_Positive_Integer($id_lop_ap_dung)) {
                            throw new Exception('invalid-lop-ap-dung-id');
                        }
                    
                        $sinhvien__Get_By_Id_Lop_Hoc = $sinhvien->sinhvien__Get_By_Id_Lop_Hoc($item_1);

                        foreach($sinhvien__Get_By_Id_Lop_Hoc as $item){
                            $status_phieu = $phieuchamdiem->phieuchamdiem__Add($id_lop_ap_dung, $item->id_sinh_vien, "", "", "", date("Y-m-d"));
                            // Nhựt sửa lỗi: Nếu sinh phiếu cho bất kỳ sinh viên nào thất bại thì rollback toàn bộ.
                            if ($status_phieu == 0) {
                                throw new Exception('insert-phieu-failed');
                            }
                        }
                    }

                    // Nhựt sửa lỗi: Chỉ commit khi thêm đủ đợt, lớp áp dụng và phiếu chấm điểm.
                    $dotchamdiem->connect->commit();
                    dotchamdiem__Rotate_Csrf_Token();
                    dotchamdiem__Redirect('success');
                } catch (Throwable $e) {
                    if ($dotchamdiem->connect->inTransaction()) {
                        // Nhựt sửa lỗi: Rollback toàn bộ khi Add lỗi giữa chừng.
                        $dotchamdiem->connect->rollBack();
                    }
                    dotchamdiem__Redirect('failed');
                }
                
                break;
            case 'update':
                // Nhựt sửa lỗi: Chặn Update khi CSRF token không hợp lệ.
                if (!dotchamdiem__Valid_Csrf()) {
                    dotchamdiem__Redirect('invalid');
                }

                $id_dot = isset($_POST['id_dot']) ? trim($_POST['id_dot']) : "";
                $ten_dot = isset($_POST['ten_dot']) ? trim($_POST['ten_dot']) : "";
                $ghi_chu = isset($_POST['ghi_chu']) ? $_POST['ghi_chu'] : "";
                $thoi_gian_bat_dau = isset($_POST['thoi_gian_bat_dau']) ? trim($_POST['thoi_gian_bat_dau']) : "";
                $thoi_gian_ket_thuc = isset($_POST['thoi_gian_ket_thuc']) ? trim($_POST['thoi_gian_ket_thuc']) : "";
                $id_nam_hoc = isset($_POST['id_nam_hoc']) ? trim($_POST['id_nam_hoc']) : "";
                $id_hoc_ky = isset($_POST['id_hoc_ky']) ? trim($_POST['id_hoc_ky']) : "";

                // Nhựt sửa lỗi: Không update nếu id_dot sai kiểu hoặc không tồn tại.
                if (!dotchamdiem__Is_Positive_Integer($id_dot) || !$dotchamdiem->dotchamdiem__Get_By_Id($id_dot)) {
                    dotchamdiem__Redirect('not-found');
                }
                // Nhựt sửa lỗi: Không cho cập nhật Đợt đã phát sinh dữ liệu chấm, Minh chứng hoặc Kết quả xếp loại.
                if ($phieuchamdiem->phieuchamdiem__Has_Scored_Data_By_Id_Dot($id_dot) || $minhchung->minhchung__Has_By_Id_Dot($id_dot) || $ketquaxeploai->ketquaxeploai__Has_By_Id_Dot($id_dot)) {
                    dotchamdiem__Redirect('locked-dot');
                }
                if ($ten_dot == "") {
                    dotchamdiem__Redirect('invalid');
                }
                // Nhựt sửa lỗi: Kiểm tra Học kỳ cập nhật phải thuộc Năm học đã chọn.
                $validate_hoc_ky = dotchamdiem__Validate_Hoc_Ky_Nam_Hoc($id_nam_hoc, $id_hoc_ky, $namhoc, $hocky);
                if ($validate_hoc_ky != 'valid') {
                    dotchamdiem__Redirect($validate_hoc_ky);
                }
                // Nhựt sửa lỗi: Khi đổi Học kỳ thì không được trùng với Đợt chấm điểm khác.
                if ($dotchamdiem->dotchamdiem__Exists_By_Hoc_Ky($id_hoc_ky, $id_dot)) {
                    dotchamdiem__Redirect('duplicate-semester');
                }
                // Nhựt sửa lỗi: Validate thời gian Update ở server.
                if (!dotchamdiem__Validate_Date($thoi_gian_bat_dau, $thoi_gian_ket_thuc)) {
                    dotchamdiem__Redirect('invalid-date');
                }
                // Nhựt sửa lỗi: Không cho thời gian Đợt chấm điểm vượt ngoài khoảng Học kỳ đã chọn.
                if (!dotchamdiem__Validate_Date_In_Hoc_Ky($id_hoc_ky, $thoi_gian_bat_dau, $thoi_gian_ket_thuc, $hocky)) {
                    dotchamdiem__Redirect('invalid-dot-date');
                }

                $status = $dotchamdiem->dotchamdiem__Update($id_dot, $ten_dot, $ghi_chu, $thoi_gian_bat_dau, $thoi_gian_ket_thuc, $id_hoc_ky);
                dotchamdiem__Rotate_Csrf_Token();
                if($status !=0 ){
                    dotchamdiem__Redirect('success');
                }else{
                    dotchamdiem__Redirect('failed');
                }
                
                break;

            case 'delete':

                $id_dot = isset($_GET['id_dot']) ? trim($_GET['id_dot']) : "";
                // Nhựt sửa lỗi: Chặn Delete khi CSRF token không hợp lệ.
                if (!dotchamdiem__Valid_Csrf_Get()) {
                    dotchamdiem__Redirect('csrf');
                }

                // Nhựt sửa lỗi: Không delete nếu id_dot sai kiểu hoặc không tồn tại.
                if (!dotchamdiem__Is_Positive_Integer($id_dot) || !$dotchamdiem->dotchamdiem__Get_By_Id($id_dot)) {
                    dotchamdiem__Redirect('not-found');
                }

                // Nhựt sửa lỗi: Chỉ không cho xóa khi phiếu đã có dữ liệu chấm, không khóa vì phiếu khởi tạo.
                if ($phieuchamdiem->phieuchamdiem__Has_Scored_Data_By_Id_Dot($id_dot)) {
                    dotchamdiem__Redirect('related-phieu');
                }

                // Nhựt sửa lỗi: Minh chứng là dữ liệu phát sinh từ Phiếu nên phải khóa xóa Đợt để tránh mất dữ liệu.
                if ($minhchung->minhchung__Has_By_Id_Dot($id_dot)) {
                    dotchamdiem__Redirect('related-phieu');
                }

                // Nhựt sửa lỗi: Không chỉ dựa vào cột điểm, phải khóa xóa nếu Đợt đã có Kết quả xếp loại.
                if ($ketquaxeploai->ketquaxeploai__Has_By_Id_Dot($id_dot)) {
                    dotchamdiem__Redirect('related-phieu');
                }

                // Nhựt sửa lỗi: Dùng chung connection và bọc toàn bộ quá trình Delete trong transaction.
                $lopapdung->connect = $dotchamdiem->connect;
                $phieuchamdiem->connect = $dotchamdiem->connect;
                $minhchung->connect = $dotchamdiem->connect;
                $ketquaxeploai->connect = $dotchamdiem->connect;
                try {
                    $dotchamdiem->connect->beginTransaction();

                    // Nhựt sửa lỗi: Khóa và kiểm tra lại trong transaction để tránh xóa khi vừa phát sinh dữ liệu liên quan.
                    if (!$dotchamdiem->dotchamdiem__Lock_By_Id($id_dot)) {
                        $dotchamdiem->connect->rollBack();
                        dotchamdiem__Redirect('not-found');
                    }
                    if ($phieuchamdiem->phieuchamdiem__Has_Scored_Data_By_Id_Dot($id_dot) || $minhchung->minhchung__Has_By_Id_Dot($id_dot) || $ketquaxeploai->ketquaxeploai__Has_By_Id_Dot($id_dot)) {
                        $dotchamdiem->connect->rollBack();
                        dotchamdiem__Redirect('related-phieu');
                    }

                    $status_phieu = $phieuchamdiem->phieuchamdiem__Delete_By_Id_Dot($id_dot);
                    $status_lop = $lopapdung->lopapdung__Delete_By_Id_Dot($id_dot);
                    $status_dot = $dotchamdiem->dotchamdiem__Delete($id_dot);

                    if ($status_dot != 0) {
                        $dotchamdiem->connect->commit();
                        dotchamdiem__Redirect('success');
                    }

                    if ($dotchamdiem->connect->inTransaction()) {
                        $dotchamdiem->connect->rollBack();
                    }
                    dotchamdiem__Redirect('failed');
                } catch (Throwable $e) {
                    if ($dotchamdiem->connect->inTransaction()) {
                        // Nhựt sửa lỗi: Rollback toàn bộ nếu Delete lỗi giữa chừng.
                        $dotchamdiem->connect->rollBack();
                    }
                    dotchamdiem__Redirect('failed');
                }
                
                break;
        }
    }

?>
