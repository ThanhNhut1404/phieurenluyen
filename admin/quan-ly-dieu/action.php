<?php

    require '../../models/getModel.php';

    // Nhựt sửa lỗi: Tập trung redirect để sau khi xử lý POST/GET luôn quay về danh sách, tránh submit lại khi refresh.
    function dieu__Redirect($status) {
        header('location: ../index.php?page=quan-ly-dieu&status=' . $status);
        exit();
    }

    // Nhựt sửa lỗi: Chặn id_dieu/thu_tu sai kiểu, bằng 0 hoặc số âm.
    function dieu__Is_Positive_Integer($value) {
        return preg_match('/^[1-9][0-9]*$/', $value);
    }

    // Nhựt sửa lỗi: Khóa danh sách Điều trong transaction để tránh swap/add/delete cập nhật một phần khi submit trùng.
    function dieu__Lock_All($dieu) {
        $obj = $dieu->connect->query("SELECT id_dieu FROM dieu ORDER BY id_dieu FOR UPDATE");
        $obj->fetchAll();
    }
    
    if (isset($_GET['req'])){
        switch($_GET['req']){
            case 'add':

                // Nhựt sửa lỗi: Request thiếu field vẫn phải xử lý an toàn, không đọc trực tiếp $_POST gây warning.
                $ten_dieu = isset($_POST['ten_dieu']) ? trim($_POST['ten_dieu']) : "";
                $ghi_chu = isset($_POST['ghi_chu']) ? trim($_POST['ghi_chu']) : "";
                $thu_tu = isset($_POST['thu_tu']) ? trim($_POST['thu_tu']) : "";

                // Nhựt sửa lỗi: Tên Điều rỗng hoặc chỉ có khoảng trắng vẫn lọt validate HTML required.
                if ($ten_dieu == "") {
                    dieu__Redirect('invalid');
                }

                try {
                    // Nhựt sửa lỗi: Thêm Điều có swap gồm nhiều câu SQL nên phải dùng transaction.
                    $dieu->connect->beginTransaction();
                    dieu__Lock_All($dieu);

                    // Nhựt sửa lỗi: Tên Điều là dữ liệu danh mục, không được trùng sau khi trim.
                    if ($dieu->dieu__Get_By_Ten($ten_dieu)) {
                        $error_status = 'duplicate-name';
                        throw new Exception("Dieu da ton tai");
                    }

                    // Nhựt sửa lỗi: Nếu dữ liệu thứ tự hiện tại đã trùng/hở/NULL thì không xử lý tiếp làm sai thêm.
                    if (!$dieu->dieu__Check_Thu_Tu_Hop_Le()) {
                        throw new Exception("Thu tu dieu khong hop le");
                    }

                    $max_thu_tu = $dieu->dieu__Get_Max_Thu_Tu();
                    // Nhựt sửa lỗi: Thứ tự thêm phải là số nguyên dương và không vượt quá max + 1.
                    if ($thu_tu == "") {
                        $thu_tu = $max_thu_tu + 1;
                    } else if (!dieu__Is_Positive_Integer($thu_tu) || (int)$thu_tu > $max_thu_tu + 1) {
                        throw new Exception("Thu tu them khong hop le");
                    } else {
                        $thu_tu = (int)$thu_tu;
                    }

                    // Nhựt sửa lỗi: Khi thêm vào thứ tự đã tồn tại, chỉ hoán đổi với Điều bị trùng, không shift danh sách.
                    $conflict = $dieu->dieu__Get_By_Thu_Tu($thu_tu);
                    if ($conflict) {
                        $dieu->dieu__Update_Thu_Tu($conflict->id_dieu, $max_thu_tu + 1);
                    }

                    $status = $dieu->dieu__Add($ten_dieu, $ghi_chu, $thu_tu);
                    // Nhựt sửa lỗi: Sau khi thêm phải kiểm tra lại không trùng, không âm/0/NULL và không hở thứ tự.
                    if ($status == 0 || !$dieu->dieu__Check_Thu_Tu_Hop_Le()) {
                        throw new Exception("Them dieu that bai");
                    }

                    $dieu->connect->commit();
                    dieu__Redirect('success');
                } catch (Exception $e) {
                    // Nhựt sửa lỗi: Có lỗi trong quá trình swap/thêm thì rollback toàn bộ.
                    if ($dieu->connect->inTransaction()) {
                        $dieu->connect->rollBack();
                    }
                    dieu__Redirect('invalid');
                }
                
                break;
            case 'update':

                // Nhựt sửa lỗi: Request update thiếu field vẫn phải xử lý an toàn, không đọc trực tiếp $_POST gây warning.
                $id_dieu = isset($_POST['id_dieu']) ? trim($_POST['id_dieu']) : "";
                $ten_dieu = isset($_POST['ten_dieu']) ? trim($_POST['ten_dieu']) : "";
                $ghi_chu = isset($_POST['ghi_chu']) ? trim($_POST['ghi_chu']) : "";
                $thu_tu = isset($_POST['thu_tu']) ? trim($_POST['thu_tu']) : "";

                // Nhựt sửa lỗi: id_dieu update sai kiểu hoặc không hợp lệ thì quay về danh sách.
                if (!dieu__Is_Positive_Integer($id_dieu)) {
                    dieu__Redirect('not-found');
                }

                // Nhựt sửa lỗi: Không cho update tên Điều rỗng hoặc chỉ chứa khoảng trắng.
                if ($ten_dieu == "") {
                    dieu__Redirect('invalid');
                }

                $error_status = 'invalid';
                try {
                    // Nhựt sửa lỗi: Cập nhật Điều có thể swap nhiều câu SQL nên phải dùng transaction.
                    $dieu->connect->beginTransaction();
                    dieu__Lock_All($dieu);

                    // Nhựt sửa lỗi: id_dieu không tồn tại thì không update và quay về danh sách.
                    $old_dieu = $dieu->dieu__Get_By_Id($id_dieu);
                    if (!$old_dieu) {
                        $error_status = 'not-found';
                        throw new Exception("Khong tim thay dieu");
                    }

                    // Nhựt sửa lỗi: Không cho đổi tên sang tên Điều đã tồn tại ở bản ghi khác.
                    if ($dieu->dieu__Get_By_Ten($ten_dieu, $id_dieu)) {
                        $error_status = 'duplicate-name';
                        throw new Exception("Dieu da ton tai");
                    }

                    // Nhựt sửa lỗi: Dữ liệu thứ tự đang lỗi thì không swap/update tiếp.
                    if (!$dieu->dieu__Check_Thu_Tu_Hop_Le()) {
                        throw new Exception("Thu tu dieu khong hop le");
                    }

                    $max_thu_tu = $dieu->dieu__Get_Max_Thu_Tu();
                    // Nhựt sửa lỗi: Update để trống thứ tự thì giữ nguyên, nhập thì phải là số nguyên trong khoảng hiện có.
                    if ($thu_tu == "") {
                        $thu_tu = (int)$old_dieu->thu_tu;
                    } else if (!dieu__Is_Positive_Integer($thu_tu) || (int)$thu_tu > $max_thu_tu) {
                        throw new Exception("Thu tu cap nhat khong hop le");
                    } else {
                        $thu_tu = (int)$thu_tu;
                    }

                    // Nhựt sửa lỗi: Nhập đúng thứ tự hiện tại thì không swap; nhập thứ tự khác thì chỉ swap đúng 2 Điều.
                    if ($thu_tu != $old_dieu->thu_tu) {
                        $conflict = $dieu->dieu__Get_By_Thu_Tu($thu_tu);
                        if (!$conflict || $conflict->id_dieu == $id_dieu) {
                            throw new Exception("Khong tim thay dieu can swap");
                        }
                        $dieu->dieu__Update_Thu_Tu($conflict->id_dieu, $old_dieu->thu_tu);
                    }

                    $dieu->dieu__Update($id_dieu, $ten_dieu, $ghi_chu, $thu_tu);
                    // Nhựt sửa lỗi: Sau update/swap phải kiểm tra lại toàn vẹn thứ tự.
                    if (!$dieu->dieu__Check_Thu_Tu_Hop_Le()) {
                        throw new Exception("Cap nhat dieu that bai");
                    }

                    $dieu->connect->commit();
                    dieu__Redirect('success');
                } catch (Exception $e) {
                    // Nhựt sửa lỗi: Có lỗi trong quá trình swap/update thì rollback toàn bộ.
                    if ($dieu->connect->inTransaction()) {
                        $dieu->connect->rollBack();
                    }
                    dieu__Redirect($error_status);
                }
                
                break;

            case 'delete':

                // Nhựt sửa lỗi: Request delete thiếu id_dieu vẫn phải xử lý an toàn.
                $id_dieu = isset($_GET['id_dieu']) ? trim($_GET['id_dieu']) : "";

                // Nhựt sửa lỗi: id_dieu delete sai kiểu hoặc không hợp lệ thì quay về danh sách.
                if (!dieu__Is_Positive_Integer($id_dieu)) {
                    dieu__Redirect('not-found');
                }

                $error_status = 'invalid';
                try {
                    // Nhựt sửa lỗi: Xóa Điều có thể cần đổi thứ tự Điều cuối nên phải dùng transaction.
                    $dieu->connect->beginTransaction();
                    dieu__Lock_All($dieu);

                    // Nhựt sửa lỗi: id_dieu không tồn tại thì không xóa và quay về danh sách.
                    $old_dieu = $dieu->dieu__Get_By_Id($id_dieu);
                    if (!$old_dieu) {
                        $error_status = 'not-found';
                        throw new Exception("Khong tim thay dieu");
                    }

                    // Nhựt sửa lỗi: Điều đang được dùng trong bocauhoi/Mẫu phiếu thì không được xóa.
                    if ($dieu->dieu__Is_Used_In_Bocauhoi($id_dieu)) {
                        $error_status = 'related-dieu';
                        throw new Exception("Dieu dang duoc su dung trong mau phieu");
                    }

                    // Nhựt sửa lỗi: Điều đang được Khoản tham chiếu thì không xóa để tránh dữ liệu mồ côi.
                    if ($dieu->dieu__Is_Used_In_Khoan($id_dieu)) {
                        $error_status = 'related-dieu-khoan';
                        throw new Exception("Dieu nay dang co Khoan nen khong the xoa");
                    }

                    // Nhựt sửa lỗi: Dữ liệu thứ tự đang lỗi thì không xóa tiếp làm sai thêm.
                    if (!$dieu->dieu__Check_Thu_Tu_Hop_Le()) {
                        throw new Exception("Thu tu dieu khong hop le");
                    }

                    $thu_tu_xoa = $old_dieu->thu_tu;
                    $status = $dieu->dieu__Delete($id_dieu);
                    // Nhựt sửa lỗi: Chỉ dồn thứ tự sau khi đã xóa thành công để tránh trùng thứ tự tạm thời.
                    if ($status == 0 || !$dieu->dieu__Giam_Thu_Tu_Sau_Khi_Xoa($thu_tu_xoa) || !$dieu->dieu__Check_Thu_Tu_Hop_Le()) {
                        throw new Exception("Xoa dieu that bai");
                    }

                    $dieu->connect->commit();
                    dieu__Redirect('success');
                } catch (Exception $e) {
                    // Nhựt sửa lỗi: Có lỗi trong quá trình đổi thứ tự/xóa thì rollback toàn bộ.
                    if ($dieu->connect->inTransaction()) {
                        $dieu->connect->rollBack();
                    }
                    dieu__Redirect($error_status);
                }
                
                break;
            default:
                dieu__Redirect('invalid');
        }
    } else {
        dieu__Redirect('invalid');
    }

?>
