<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Auth check is handled globally in admin/index.php

require_once __DIR__ . "/../../models/getModel.php";

// Nhựt sửa lỗi: tránh warning khi session admin thiếu phân quyền hoặc phân quyền không còn tồn tại.
$ten_phan_quyen = 'Admin';
if (isset($_SESSION['admin']->id_phan_quyen)) {
    $phan_quyen_hien_tai = $phanquyen->phanquyen__Get_By_Id($_SESSION['admin']->id_phan_quyen);
    if ($phan_quyen_hien_tai && isset($phan_quyen_hien_tai->ten_phan_quyen)) {
        $ten_phan_quyen = $phan_quyen_hien_tai->ten_phan_quyen;
    }
}
?>

<!-- sidebar -->
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="?page=thong-ke" class="brand-link">
        <img src="../assets/img/logo.png" alt="Logo" class="brand-image elevation-3">
        <span class="brand-text font-weight-light"><?= htmlspecialchars($ten_phan_quyen, ENT_QUOTES, 'UTF-8') ?></span>
    </a>
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="true">
            <li class="nav-item">
                <a href="?page=thong-ke" class="nav-link">
                    <i class="nav-icon ri-home-6-line"></i>
                    <p>
                        Thống kê
                    </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="nav-icon ri-file-list-3-line"></i>
                    <p>
                        Quản lý phiếu
                        <i class="right ri-arrow-left-s-line"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="?page=quan-ly-xep-loai" class="nav-link">
                            <i class="ri-award-line nav-icon"></i>
                            <p>Xếp loại</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=quan-ly-mau-phieu" class="nav-link">
                            <i class="ri-file-copy-2-line nav-icon"></i>
                            <p>Mẫu phiếu</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=quan-ly-dieu" class="nav-link">
                            <i class="ri-article-line nav-icon"></i>
                            <p>Điều</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=quan-ly-khoan" class="nav-link">
                            <i class="ri-sticky-note-line nav-icon"></i>
                            <p>Khoản</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=quan-ly-muc" class="nav-link">
                            <i class="ri-task-line nav-icon"></i>
                            <p>Mục</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=quan-ly-dot-cham-diem" class="nav-link">
                            <i class="ri-calendar-todo-line nav-icon"></i>
                            <p>Đợt chấm điểm</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=quan-ly-phieu-cham-diem" class="nav-link">
                            <i class="ri-file-edit-line nav-icon"></i>
                            <p>Phiếu chấm điểm</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=quan-ly-ket-qua" class="nav-link">
                            <i class="ri-file-chart-line nav-icon"></i>
                            <p>Kết quả xếp loại</p>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="nav-icon ri-team-line"></i>
                    <p>
                        Quản lý người dùng
                        <i class="right ri-arrow-left-s-line"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="?page=quan-ly-sinh-vien" class="nav-link">
                            <i class="ri-graduation-cap-line nav-icon"></i>
                            <p>Sinh viên</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=quan-ly-bi-thu-doan-khoa" class="nav-link">
                            <i class="ri-user-star-line nav-icon"></i>
                            <p>Bí thư đoàn khoa</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=quan-ly-giang-vien" class="nav-link">
                            <i class="ri-user-settings-line nav-icon"></i>
                            <p>Giảng viên</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=quan-ly-phan-cong" class="nav-link">
                            <i class="ri-contacts-line nav-icon"></i>
                            <p>Phân công cố vấn</p>
                        </a>
                    </li>
                    <!-- quân sửa: Ẩn menu Phân nhóm và Phân quyền để tránh xoá nhầm làm sập hệ thống phân quyền -->
                    <!-- 
                    <li class="nav-item">
                        <a href="?page=quan-ly-phan-nhom" class="nav-link">
                            <i class="ri-checkbox-blank-circle-line nav-icon" style="font-size: 0.6rem;"></i>
                            <p>Phân nhóm</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=quan-ly-phan-quyen" class="nav-link">
                            <i class="ri-checkbox-blank-circle-line nav-icon" style="font-size: 0.6rem;"></i>
                            <p>Phân quyền</p>
                        </a>
                    </li>
                    -->
                    <li class="nav-item">
                        <a href="?page=quan-ly-tai-khoan" class="nav-link">
                            <i class="ri-key-line nav-icon"></i>
                            <p>Tài khoản</p>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="nav-icon ri-database-2-line"></i>
                    <p>
                        Quản lý chung
                        <i class="right ri-arrow-left-s-line"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="?page=quan-ly-khoa" class="nav-link">
                            <i class="ri-building-line nav-icon"></i>
                            <p>Khoa</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=quan-ly-nganh-hoc" class="nav-link">
                            <i class="ri-git-branch-line nav-icon"></i>
                            <p>Ngành học</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=quan-ly-khoa-hoc" class="nav-link">
                            <i class="ri-book-open-line nav-icon"></i>
                            <p>Khóa học</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=quan-ly-trinh-do" class="nav-link">
                            <i class="ri-medal-line nav-icon"></i>
                            <p>Trình độ</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=quan-ly-lop-hoc" class="nav-link">
                            <i class="ri-group-line nav-icon"></i>
                            <p>Lớp học</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=quan-ly-nam-hoc" class="nav-link">
                            <i class="ri-calendar-line nav-icon"></i>
                            <p>Năm học</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=quan-ly-hoc-ky" class="nav-link">
                            <i class="ri-time-line nav-icon"></i>
                            <p>Học kỳ</p>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
    <!-- /.sidebar -->

    <div class="sidebar-toggle-btn-container">
        <button class="btn-sidebar-toggle" data-widget="pushmenu" role="button">
            <i class="ri-arrow-left-s-line"></i>
        </button>
    </div>
</aside>
