<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('location: ../auth/');
    exit();
}
require "../models/getModel.php";

?>

<!-- sidebar -->
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="?page=thong-ke" class="brand-link">
        <img src="../assets/img/logo.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light"><?= $phanquyen->phanquyen__Get_By_Id($_SESSION['admin']->id_phan_quyen)->ten_phan_quyen ?></span>
    </a>
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item">
                <a href="?page=thong-ke" class="nav-link">
                    <i class="nav-icon fas fa-home"></i>
                    <p>
                        Thống kê
                    </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-copy"></i>
                    <p>
                        Quản lý phiếu
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="?page=quan-ly-ket-qua" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Kết quả xếp loại</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="?page=quan-ly-phieu-cham-diem" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Phiếu chấm điểm</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="?page=quan-ly-xep-loai" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Xếp loại</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="?page=quan-ly-dot-cham-diem" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Đợt chấm điểm</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=quan-ly-mau-phieu" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Mẫu phiếu</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=quan-ly-dieu" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Điều</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=quan-ly-khoan" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Khoản</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=quan-ly-muc" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Mục</p>
                        </a>
                    </li>
                </ul>
            </li>



            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-user"></i>
                    <p>
                        Quản lý tài khoản
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="?page=quan-ly-sinh-vien" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Sinh viên</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=quan-ly-bi-thu-doan-khoa" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Bí thư đoàn khoa</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=quan-ly-giang-vien" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Giảng viên</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=quan-ly-phan-cong" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Phân công cố vấn</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=quan-ly-phan-nhom" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Phân nhóm</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=quan-ly-phan-quyen" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Phân quyền</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=quan-ly-tai-khoan" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Tài khoản</p>
                        </a>
                    </li>
                </ul>
            </li>


            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-table"></i>
                    <p>
                        Quản lý chung
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="?page=quan-ly-khoa" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Khoa</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=quan-ly-nganh-hoc" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Ngành học</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=quan-ly-lop-hoc" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Lớp học</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=quan-ly-khoa-hoc" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Khóa học</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=quan-ly-trinh-do" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Trình độ</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=quan-ly-nam-hoc" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Năm học</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=quan-ly-hoc-ky" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Học kỳ</p>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
    <!-- /.sidebar -->
</aside>