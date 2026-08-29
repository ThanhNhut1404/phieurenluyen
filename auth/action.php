<?php
    session_start();

    require '../models/getModel.php';
    
    if (isset($_GET['req'])){
        switch($_GET['req']){
            case 'dang-nhap':

                
                
                $email = $_POST['email'];
                $mat_khau = $_POST['mat_khau'];
                
                $status = $taikhoan->taikhoan__Check_Login($email, $mat_khau);
                // $status = $taikhoan->taikhoan__Check_Login($email, $mat_khau);
                
                if($status != "0" ){
                    // Tách biệt session Admin và User
                    $cap_bac_check = $phanquyen->phanquyen__Get_By_Id($status->id_phan_quyen)->cap_bac;
                    if ($cap_bac_check == 0 || $cap_bac_check == 1) {
                        unset($_SESSION['user'], $_SESSION['sv'], $_SESSION['lt'], $_SESSION['bt'], $_SESSION['btdk'], $_SESSION['gv']);
                    } else {
                        unset($_SESSION['admin'], $_SESSION['super'], $_SESSION['manager']);
                    }

                    $s = $dotchamdiem->dotchamdiem__Get_Time(date('Y-m-d'));
                    if(count($s) > 0){
                        foreach($s as $item){
                        $k = $dotchamdiem->dotchamdiem__Update_Trang_Thai($item->id_dot, 0);
                        }
                    }

                    
                    if($phanquyen->phanquyen__Get_By_Id($status->id_phan_quyen)->cap_bac == 0){
                        $_SESSION['admin'] = $status;
                        $_SESSION['super'] = $status;
                        header('location: ../admin/index.php?page=thong-ke');
                        break;
                    }
                    if($phanquyen->phanquyen__Get_By_Id($status->id_phan_quyen)->cap_bac == 1){
                        $_SESSION['admin'] = $status;
                        $_SESSION['manager'] = $status;
                        header('location: ../admin/index.php?page=thong-ke');
                        break;
                    }
                    if($phanquyen->phanquyen__Get_By_Id($status->id_phan_quyen)->cap_bac == 2){
                        $_SESSION['user'] = $status;

                        if($phannhom->phannhom__Get_By_Id($status->id_phan_nhom)->cap_bac == 2){
                            $sinhvien__Get_By_Id = $sinhvien->sinhvien__Get_By_Id($status->id_nguoi_dung);
                            if($sinhvien__Get_By_Id->chuc_vu == 0){
                                $_SESSION['sv'] = $status;
                                header('location: ../user/UI-sv/index.php');
                                break;
                            }
                            if($sinhvien__Get_By_Id->chuc_vu == 1){
                                $_SESSION['lt'] = $status;
                                header('location: ../user/UI-sv/index.php');
                                break;
                            }
                            if($sinhvien__Get_By_Id->chuc_vu == 2){
                                $_SESSION['bt'] = $status;
                                header('location: ../user/UI-sv/index.php');
                                break;
                            }
                        }
                       
                        if($phannhom->phannhom__Get_By_Id($status->id_phan_nhom)->cap_bac == 3){
                            $_SESSION['btdk'] = $status;
                            header('location: ../user/index.php?page=thong-ke');
                            break;
                        }
                        if($phannhom->phannhom__Get_By_Id($status->id_phan_nhom)->cap_bac == 4){
                            $_SESSION['gv'] = $status;
                            header('location: ../user/index.php?page=thong-ke');
                            break;
                        }
                }

                }else{
                    header('location: ../auth/index.php?status=error');
                }
                
                break;

            case 'doi-mat-khau':
                $id_tai_khoan = $_POST['id_tai_khoan'];
                $mat_khau_cu = $_POST['mat_khau_cu'];
                $mat_khau_moi = $_POST['mat_khau_moi'];
                $xac_nhan_mat_khau = $_POST['xac_nhan_mat_khau'];

                $redirect = isset($_POST['redirect_to']) && $_POST['redirect_to'] == 'UI-sv' ? '../user/UI-sv/index.php?page=doi-mat-khau' : '../user/index.php?page=quan-ly-tai-khoan';

                if ($mat_khau_moi !== $xac_nhan_mat_khau) {
                    header('location: ' . $redirect . '&status=password_mismatch');
                    exit();
                }

                $user = $taikhoan->taikhoan__Get_By_Id($id_tai_khoan);
                
                // Kiểm tra mật khẩu cũ
                $is_valid = false;
                if (password_verify($mat_khau_cu, $user->mat_khau)) {
                    $is_valid = true;
                } else {
                    if ($hashpassword->Encryption($mat_khau_cu) === $user->mat_khau) {
                        $is_valid = true;
                    }
                }

                if (!$is_valid) {
                    header('location: ' . $redirect . '&status=wrong_old_password');
                    exit();
                }

                $status = $taikhoan->taikhoan__Reset($id_tai_khoan, password_hash($mat_khau_moi, PASSWORD_BCRYPT));
                if($status != "0" ){
                    header('location: ' . $redirect . '&status=success');
                }else{
                    header('location: ' . $redirect . '&status=failed');
                }
                break; 

            case 'dang-xuat':
                if (isset($_GET['role'])) {
                    if ($_GET['role'] === 'admin') {
                        unset($_SESSION['admin'], $_SESSION['super'], $_SESSION['manager']);
                    } else if ($_GET['role'] === 'user') {
                        unset($_SESSION['user'], $_SESSION['sv'], $_SESSION['lt'], $_SESSION['bt'], $_SESSION['btdk'], $_SESSION['gv']);
                    }
                } else {
                    session_destroy();
                }
                header('location: ../auth/index.php');
                break;
            }
        }
