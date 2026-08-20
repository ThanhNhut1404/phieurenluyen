<?php
    session_start();

    require '../models/getModel.php';
    
    if (isset($_GET['req'])){
        switch($_GET['req']){
            case 'dang-nhap':

                
                
                $email = $_POST['email'];
                $mat_khau = $_POST['mat_khau'];
                
                // Nhựt sửa: Xóa toàn bộ session cũ trước khi đăng nhập tài khoản mới để tránh bị lưu dính quyền (ví dụ: đang là bí thư đăng nhập lại thành sinh viên nhưng vẫn còn session bí thư)
                session_unset();

                $status = $taikhoan->taikhoan__Check_Login($email, $hashpassword->Encryption($mat_khau));
                // $status = $taikhoan->taikhoan__Check_Login($email, $mat_khau);
                
                if($status != "0" ){

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
                                header('location: ../user/index.php?page=thong-ke');
                                break;
                            }
                            if($sinhvien__Get_By_Id->chuc_vu == 1){
                                $_SESSION['lt'] = $status;
                                header('location: ../user/index.php?page=thong-ke');
                                break;
                            }
                            if($sinhvien__Get_By_Id->chuc_vu == 2){
                                $_SESSION['bt'] = $status;
                                header('location: ../user/index.php?page=thong-ke');
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
                $mat_khau = $_POST['mat_khau'];
                $status = $taikhoan->taikhoan__Reset($id_tai_khoan, $hashpassword->Encryption($mat_khau));
                if($status != "0" ){
                    header('location: ../user/index.php?page=quan-ly-tai-khoan&status=success');
                }else{
                    header('location: ../user/index.php?page=quan-ly-tai-khoan&status=failed');
                }
                break; 

            case 'dang-xuat':

                session_destroy();
                header('location: ../auth/index.php');
                break;
            }
        }
