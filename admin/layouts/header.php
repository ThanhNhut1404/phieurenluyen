<!-- header -->

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <style>
        /* Nhựt sửa: Thêm hiệu ứng hover cho nút menu 3 gạch */
        .hamburger-btn:hover {
            background-color: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
        }
        .hamburger-btn:hover i {
            color: #0f172a !important;
        }
    </style>
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link hamburger-btn" data-widget="pushmenu" href="#" role="button"
               style="border: 1px solid #ced4da; border-radius: 6px; padding: 0; display: inline-flex; align-items: center; justify-content: center; height: 34px; width: 34px; background-color: #ffffff; transition: all 0.2s ease;">
                <i class="ri-menu-line" style="font-size: 1.1rem; color: #495057; transition: all 0.2s ease;"></i>
            </a>
        </li>
    </ul>

    <!-- Middle Search Input (Nhựt sửa) -->
    <ul class="navbar-nav mx-auto w-50" id="header-search-container" style="display: none;">
        <li class="nav-item w-100">
            <div class="position-relative w-100 d-flex align-items-center" style="height: 38px;">
                <input type="text" id="global-header-search" class="form-control" placeholder="Tìm kiếm..." 
                       style="border-radius: 10px; padding: 8px 45px 8px 18px; border: 1px solid #e2e8f0; background-color: #f1f5f9; color: #334155; font-size: 0.95rem; width: 100%; height: 38px; outline: none; box-shadow: none; transition: all 0.3s ease;">
                <i class="ri-search-2-line position-absolute" 
                   style="right: 18px; font-size: 1.2rem; color: #0d3b66 !important; pointer-events: none; font-weight: bold;"></i>
            </div>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="ri-user-line"></i>
                <i class="ri-arrow-down-s-line"></i>
            </a>

            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                <a href="../auth/action.php?req=dang-xuat" class="dropdown-item">
                    <i class="ri-logout-box-line"></i>
                    Đăng xuất
                </a>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="ri-fullscreen-line"></i>
            </a>
        </li>

    </ul>
</nav>
<!-- /.navbar -->