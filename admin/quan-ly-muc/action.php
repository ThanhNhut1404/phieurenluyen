<?php

    require '../../models/getModel.php';
    
    if (isset($_GET['req'])){
        switch($_GET['req']){
            case 'add':

                $ten_muc = trim($_POST['ten_muc']);
                $ghi_chu = trim($_POST['ghi_chu']);
                $id_khoan = $_POST['id_khoan'];
                $thu_tu = trim($_POST['thu_tu']);

                // quân sửa: Thứ tự giờ đã được chọn từ dropdown và đảm bảo không trùng
                $thu_tu = isset($_POST['thu_tu']) ? $_POST['thu_tu'] : 1;

                $quyen_sv = isset($_POST['quyen_sv']) ? 1 : 0;
                $quyen_lt = isset($_POST['quyen_lt']) ? 1 : 0;
                $quyen_btdk = isset($_POST['quyen_btdk']) ? 1 : 0;
                $quyen_gv = isset($_POST['quyen_gv']) ? 1 : 0;
                
                // quân sửa: Bổ sung điểm tối đa và kiểm tra quỹ điểm, bổ sung co_minh_chung
                $diem_toi_da = isset($_POST['diem_toi_da']) ? (int)$_POST['diem_toi_da'] : 0;
                $co_minh_chung = isset($_POST['co_minh_chung']) ? 1 : 0;

                $khoan_info = $khoan->khoan__Get_By_Id($id_khoan);
                $current_total_diem = $muc->muc__Get_Total_Diem_By_Khoan($id_khoan);
                
                if ($khoan_info && ($current_total_diem + $diem_toi_da > $khoan_info->can_tren)) {
                    header('location: ../index.php?page=quan-ly-muc&status=failed_over_limit');
                    exit();
                }

                $status = $muc->muc__Add($ten_muc, $ghi_chu, $thu_tu, $id_khoan, $quyen_sv, $quyen_lt, $quyen_btdk, $quyen_gv, $diem_toi_da, $co_minh_chung);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-muc&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-muc&status=failed');
                }
                
                break;
            case 'update':

                $id_muc = $_POST['id_muc'];
                $ten_muc = trim($_POST['ten_muc']);
                $ghi_chu = trim($_POST['ghi_chu']);
                $id_khoan = $_POST['id_khoan'];
                $thu_tu = trim($_POST['thu_tu']);

                // quân sửa: Thứ tự lấy trực tiếp từ Select
                $thu_tu = isset($_POST['thu_tu']) ? $_POST['thu_tu'] : 1;

                $quyen_sv = isset($_POST['quyen_sv']) ? 1 : 0;
                $quyen_lt = isset($_POST['quyen_lt']) ? 1 : 0;
                $quyen_btdk = isset($_POST['quyen_btdk']) ? 1 : 0;
                $quyen_gv = isset($_POST['quyen_gv']) ? 1 : 0;

                // quân sửa: Bổ sung điểm tối đa và kiểm tra quỹ điểm, bổ sung co_minh_chung
                $diem_toi_da = isset($_POST['diem_toi_da']) ? (int)$_POST['diem_toi_da'] : 0;
                $co_minh_chung = isset($_POST['co_minh_chung']) ? 1 : 0;

                // Quân sửa: Cập nhật điều kiện khóa Sửa (Dựa vào phát sinh phiếu chấm hoặc đợt chấm)
                if ($muc->muc__Is_Edit_Locked($id_muc)) {
                    echo "<script>
                            alert('Nội dung này thuộc mẫu đã phát sinh đợt chấm nên chưa thể chỉnh sửa.');
                            window.history.back();
                          </script>";
                    exit();
                }

                $old_muc = $muc->muc__Get_By_Id($id_muc);
                $khoan_info = $khoan->khoan__Get_By_Id($id_khoan);
                $current_total_diem = $muc->muc__Get_Total_Diem_By_Khoan($id_khoan);
                
                if ($khoan_info && $old_muc) {
                    $new_total = $current_total_diem - $old_muc->diem_toi_da + $diem_toi_da;
                    if ($new_total > $khoan_info->can_tren) {
                        header('location: ../index.php?page=quan-ly-muc&status=failed_over_limit');
                        exit();
                    }
                }

                $status = $muc->muc__Update($id_muc, $ten_muc, $ghi_chu, $thu_tu, $id_khoan, $quyen_sv, $quyen_lt, $quyen_btdk, $quyen_gv, $diem_toi_da, $co_minh_chung);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-muc&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-muc&status=failed');
                }
                
                break;

            case 'delete':

                $id_muc = $_GET['id_muc'];

                // quân sửa: Không cho xoá nếu Mục đang nằm trong Đợt chấm điểm Active
                if ($muc->muc__Is_In_Active_DotChamDiem($id_muc)) {
                    header('location: ../index.php?page=quan-ly-muc&status=locked_by_dotchamdiem');
                    exit();
                }

                $status = $muc->muc__Delete($id_muc);
                if($status !=0 ){
                    header('location: ../index.php?page=quan-ly-muc&status=success');
                }else{
                    header('location: ../index.php?page=quan-ly-muc&status=failed');
                }
                
                break;
        }
    }

?>