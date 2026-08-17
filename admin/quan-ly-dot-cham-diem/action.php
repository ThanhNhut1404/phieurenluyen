<?php

    session_start();
    require '../../models/getModel.php';

    function dotchamdiem_store_old_input($context, $ten_dot, $ghi_chu, $thoi_gian_bat_dau, $thoi_gian_ket_thuc, $id_nam_hoc, $id_hoc_ky, $id_mau_phieu, $id_lop_hoc, $id_dot = null) {
        $_SESSION['dotchamdiem_old_input'] = array(
            'context' => $context,
            'ten_dot' => $ten_dot,
            'ghi_chu' => $ghi_chu,
            'thoi_gian_bat_dau' => $thoi_gian_bat_dau,
            'thoi_gian_ket_thuc' => $thoi_gian_ket_thuc,
            'id_nam_hoc' => $id_nam_hoc,
            'id_hoc_ky' => $id_hoc_ky,
            'id_mau_phieu' => $id_mau_phieu,
            'id_lop_hoc' => is_array($id_lop_hoc) ? $id_lop_hoc : array(),
            'id_dot' => $id_dot,
        );
    }

    function dotchamdiem_clear_old_input() {
        unset($_SESSION['dotchamdiem_old_input']);
    }

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

    function dotchamdiem__Validate_Hoc_Ky_Nam_Hoc($id_nam_hoc, $id_hoc_ky, $namhoc, $hocky) {
        // Nhựt sửa lỗi: Validate quan hệ Năm học - Học kỳ ở server, không chỉ dựa vào client.
        if (!dotchamdiem__Is_Positive_Integer($id_nam_hoc) || !dotchamdiem__Is_Positive_Integer($id_hoc_ky)) {
            return 'invalid-namhoc';
        }
        if (!$namhoc->namhoc__Get_By_Id($id_nam_hoc) || !$hocky->hocky__Get_By_Id($id_hoc_ky)) {
            return 'invalid-namhoc';
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
                        "ten_hoc_ky" => $item->ten_hoc_ky
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

                if ($ten_dot == "") {
                    dotchamdiem_store_old_input('add', $ten_dot, $ghi_chu, $thoi_gian_bat_dau, $thoi_gian_ket_thuc, $id_nam_hoc, $id_hoc_ky, $id_mau_phieu, $id_lop_hoc);
                    dotchamdiem__Redirect('invalid-ten');
                }
                if (!dotchamdiem__Is_Positive_Integer($id_mau_phieu) || !$mauphieu->mauphieu__Get_By_Id($id_mau_phieu)) {
                    dotchamdiem_store_old_input('add', $ten_dot, $ghi_chu, $thoi_gian_bat_dau, $thoi_gian_ket_thuc, $id_nam_hoc, $id_hoc_ky, $id_mau_phieu, $id_lop_hoc);
                    dotchamdiem__Redirect('invalid-mauphieu');
                }
                // Nhựt sửa lỗi: Kiểm tra Học kỳ phải thuộc Năm học đã chọn.
                $validate_hoc_ky = dotchamdiem__Validate_Hoc_Ky_Nam_Hoc($id_nam_hoc, $id_hoc_ky, $namhoc, $hocky);
                if ($validate_hoc_ky != 'valid') {
                    dotchamdiem_store_old_input('add', $ten_dot, $ghi_chu, $thoi_gian_bat_dau, $thoi_gian_ket_thuc, $id_nam_hoc, $id_hoc_ky, $id_mau_phieu, $id_lop_hoc);
                    dotchamdiem__Redirect($validate_hoc_ky);
                }
                // Nhựt sửa lỗi: Kiểm tra tất cả lớp áp dụng phải tồn tại và bỏ lớp trùng.
                $id_lop_hoc_valid = dotchamdiem__Validate_Lop_Hoc($id_lop_hoc, $lophoc);
                if ($id_lop_hoc_valid === false) {
                    dotchamdiem_store_old_input('add', $ten_dot, $ghi_chu, $thoi_gian_bat_dau, $thoi_gian_ket_thuc, $id_nam_hoc, $id_hoc_ky, $id_mau_phieu, $id_lop_hoc);
                    dotchamdiem__Redirect('invalid-lop');
                }
                // Nhựt sửa lỗi: Validate thời gian Add ở server.
                if (!dotchamdiem__Validate_Date($thoi_gian_bat_dau, $thoi_gian_ket_thuc)) {
                    dotchamdiem_store_old_input('add', $ten_dot, $ghi_chu, $thoi_gian_bat_dau, $thoi_gian_ket_thuc, $id_nam_hoc, $id_hoc_ky, $id_mau_phieu, $id_lop_hoc);
                    dotchamdiem__Redirect('invalid-date');
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

                    foreach($id_lop_hoc_valid as $item_1){
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
                    dotchamdiem_clear_old_input();
                    dotchamdiem__Rotate_Csrf_Token();
                    dotchamdiem__Redirect('success');
                } catch (Throwable $e) {
                    if ($dotchamdiem->connect->inTransaction()) {
                        // Nhựt sửa lỗi: Rollback toàn bộ khi Add lỗi giữa chừng.
                        $dotchamdiem->connect->rollBack();
                    }
                    dotchamdiem_store_old_input('add', $ten_dot, $ghi_chu, $thoi_gian_bat_dau, $thoi_gian_ket_thuc, $id_nam_hoc, $id_hoc_ky, $id_mau_phieu, $id_lop_hoc);
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
                $id_mau_phieu = isset($_POST['id_mau_phieu']) ? trim($_POST['id_mau_phieu']) : "";
                $id_lop_hoc = isset($_POST['id_lop_hoc']) ? $_POST['id_lop_hoc'] : [];

                // Nhựt sửa lỗi: Không update nếu id_dot sai kiểu hoặc không tồn tại.
                if (!dotchamdiem__Is_Positive_Integer($id_dot) || !$dotchamdiem->dotchamdiem__Get_By_Id($id_dot)) {
                    dotchamdiem__Redirect('not-found');
                }
                
                $has_data = $phieuchamdiem->phieuchamdiem__Has_Scored_Data_By_Id_Dot($id_dot) || $minhchung->minhchung__Has_By_Id_Dot($id_dot) || $ketquaxeploai->ketquaxeploai__Has_By_Id_Dot($id_dot);

                if ($ten_dot == "") {
                    dotchamdiem_store_old_input('update', $ten_dot, $ghi_chu, $thoi_gian_bat_dau, $thoi_gian_ket_thuc, $id_nam_hoc, $id_hoc_ky, $id_mau_phieu, $id_lop_hoc, $id_dot);
                    dotchamdiem__Redirect('invalid-ten');
                }
                // Nhựt sửa lỗi: Kiểm tra Học kỳ cập nhật phải thuộc Năm học đã chọn.
                $validate_hoc_ky = dotchamdiem__Validate_Hoc_Ky_Nam_Hoc($id_nam_hoc, $id_hoc_ky, $namhoc, $hocky);
                if ($validate_hoc_ky != 'valid') {
                    dotchamdiem_store_old_input('update', $ten_dot, $ghi_chu, $thoi_gian_bat_dau, $thoi_gian_ket_thuc, $id_nam_hoc, $id_hoc_ky, $id_mau_phieu, $id_lop_hoc, $id_dot);
                    dotchamdiem__Redirect($validate_hoc_ky);
                }
                // Nhựt sửa lỗi: Validate thời gian Update ở server.
                if (!dotchamdiem__Validate_Date($thoi_gian_bat_dau, $thoi_gian_ket_thuc)) {
                    dotchamdiem_store_old_input('update', $ten_dot, $ghi_chu, $thoi_gian_bat_dau, $thoi_gian_ket_thuc, $id_nam_hoc, $id_hoc_ky, $id_mau_phieu, $id_lop_hoc, $id_dot);
                    dotchamdiem__Redirect('invalid-date');
                }
                
                $id_lop_hoc_valid = dotchamdiem__Validate_Lop_Hoc($id_lop_hoc, $lophoc);
                if ($id_lop_hoc_valid === false) {
                    dotchamdiem_store_old_input('update', $ten_dot, $ghi_chu, $thoi_gian_bat_dau, $thoi_gian_ket_thuc, $id_nam_hoc, $id_hoc_ky, $id_mau_phieu, $id_lop_hoc, $id_dot);
                    dotchamdiem__Redirect('invalid-lop');
                }

                $lopapdung->connect = $dotchamdiem->connect;
                $phieuchamdiem->connect = $dotchamdiem->connect;
                
                try {
                    $dotchamdiem->connect->beginTransaction();
                    
                    $lopapdung__Get_By_Id_Dot = $lopapdung->lopapdung__Get_By_Id_Dot($id_dot);
                    
                    if ($has_data) {
                        $id_mau_phieu = count($lopapdung__Get_By_Id_Dot) > 0 ? $lopapdung__Get_By_Id_Dot[0]->id_mau_phieu : $id_mau_phieu;
                    } else {
                        if (!dotchamdiem__Is_Positive_Integer($id_mau_phieu) || !$mauphieu->mauphieu__Get_By_Id($id_mau_phieu)) {
                            $dotchamdiem->connect->rollBack();
                            dotchamdiem_store_old_input('update', $ten_dot, $ghi_chu, $thoi_gian_bat_dau, $thoi_gian_ket_thuc, $id_nam_hoc, $id_hoc_ky, $id_mau_phieu, $id_lop_hoc, $id_dot);
                            dotchamdiem__Redirect('invalid-mauphieu');
                        }
                    }

                    $arr_id_lop_hoc_hien_tai = [];
                    foreach ($lopapdung__Get_By_Id_Dot as $lad) {
                        $arr_id_lop_hoc_hien_tai[] = $lad->id_lop_hoc;
                    }

                    foreach ($lopapdung__Get_By_Id_Dot as $lad) {
                        if (!in_array($lad->id_lop_hoc, $id_lop_hoc_valid)) {
                            $count_phieu = $phieuchamdiem->phieuchamdiem__Count_By_Id_Dot_And_Id_Lop_Hoc($id_dot, $lad->id_lop_hoc);
                            if ($count_phieu > 0) {
                                // Bỏ qua việc xóa
                            } else {
                                $lopapdung->lopapdung__Delete($lad->id_lop_ap_dung);
                            }
                        } else {
                            if (!$has_data && $lad->id_mau_phieu != $id_mau_phieu) {
                                $lopapdung->lopapdung__Update($lad->id_lop_ap_dung, $id_dot, $id_mau_phieu, $lad->id_lop_hoc);
                            }
                        }
                    }

                    foreach ($id_lop_hoc_valid as $id_lop_moi) {
                        if (!in_array($id_lop_moi, $arr_id_lop_hoc_hien_tai)) {
                            $id_lop_ap_dung = $lopapdung->lopapdung__Add($id_dot, $id_mau_phieu, $id_lop_moi);
                            if (!dotchamdiem__Is_Positive_Integer($id_lop_ap_dung)) {
                                throw new Exception('invalid-lop-ap-dung-id');
                            }
                            $sinhvien__Get_By_Id_Lop_Hoc = $sinhvien->sinhvien__Get_By_Id_Lop_Hoc($id_lop_moi);
                            foreach($sinhvien__Get_By_Id_Lop_Hoc as $item){
                                $status_phieu = $phieuchamdiem->phieuchamdiem__Add($id_lop_ap_dung, $item->id_sinh_vien, "", "", "", date("Y-m-d"));
                                if ($status_phieu == 0) {
                                    throw new Exception('insert-phieu-failed');
                                }
                            }
                        }
                    }

                    $dotchamdiem->dotchamdiem__Update($id_dot, $ten_dot, $ghi_chu, $thoi_gian_bat_dau, $thoi_gian_ket_thuc, $id_hoc_ky);
                    
                    // Quân sửa: Tự động cập nhật lại trạng thái đợt dựa vào thời gian kết thúc mới
                    $new_trang_thai = (strtotime($thoi_gian_ket_thuc) >= strtotime(date('Y-m-d'))) ? 1 : 0;
                    $dotchamdiem->dotchamdiem__Update_Trang_Thai($id_dot, $new_trang_thai);
                    
                    $dotchamdiem->connect->commit();
                    dotchamdiem_clear_old_input();
                    dotchamdiem__Rotate_Csrf_Token();
                    
                    dotchamdiem__Redirect('success');

                } catch (Throwable $e) {
                    if ($dotchamdiem->connect->inTransaction()) {
                        $dotchamdiem->connect->rollBack();
                    }
                    dotchamdiem_store_old_input('update', $ten_dot, $ghi_chu, $thoi_gian_bat_dau, $thoi_gian_ket_thuc, $id_nam_hoc, $id_hoc_ky, $id_mau_phieu, $id_lop_hoc, $id_dot);
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

                // Quân sửa: Đưa logic xóa phân tầng và transaction vào model để dọn dẹp dữ liệu an toàn.
                $status = $dotchamdiem->dotchamdiem__DeleteWithDependencies($id_dot);
                
                if ($status) {
                    dotchamdiem__Rotate_Csrf_Token();
                    dotchamdiem__Redirect('success');
                } else {
                    dotchamdiem__Redirect('failed');
                }
                
                break;
        }
    }

?>
