<?php 
    ob_start(); 
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chấm điểm rèn luyện</title>
    <link rel="icon" href="../../assets/img/favicon.ico" type="image/gif" sizes="16x16">
    <meta name="description" content="Trang chủ Sinh viên">
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../assets/css/source-sans-pro.css">
    <link rel="stylesheet" href="../../assets/theme/plugins/fontawesome-free/css/all.min.css">
    <!-- Remix Icon -->
    <link href="../../assets/remixicon/fonts/remixicon.css" rel="stylesheet">
    
    <!-- Bootstrap 4 for grid -->
    <link rel="stylesheet" href="../../assets/theme/dist/css/adminlte.min.css">
    
    <!-- Custom Dashboard CSS -->
    <link rel="stylesheet" href="../../assets/css/sv_style.css">
</head>
<body>
    <div class="wrapper">
        <!-- header -->
        <?php require 'header.php';?>

        <!-- Sidebar Dropdown -->
        <div class="sidebar-dropdown-container">
            <button class="sidebar-toggle-btn" id="sidebarToggleBtn">
                <i class="ri-menu-line"></i>
            </button>
            
            <div class="custom-sidebar" id="customSidebar">
                <ul class="sidebar-menu-list">
                    <li>
                        <a href="index.php" class="sidebar-link">
                            <i class="ri-grid-fill"></i> Trang chủ
                        </a>
                    </li>
                    <li>
                        <a href="#" class="sidebar-link">
                            <i class="ri-notification-3-line"></i> Thông báo
                        </a>
                    </li>
                    <li class="sidebar-dropdown">
                        <a href="javascript:void(0)" class="sidebar-link dropdown-toggle-btn">
                            <i class="ri-information-line"></i> Thông tin chung
                            <i class="ri-arrow-down-s-line caret"></i>
                        </a>
                        <ul class="sidebar-submenu">
                            <li><a href="?page=thong-tin-ca-nhan">Thông tin sinh viên</a></li>
                            <li><a href="?page=doi-mat-khau">Đổi mật khẩu</a></li>
                        </ul>
                    </li>

                    <li class="sidebar-dropdown">
                        <a href="javascript:void(0)" class="sidebar-link dropdown-toggle-btn">
                            <i class="ri-history-line"></i> Điểm rèn luyện
                            <i class="ri-arrow-down-s-line caret"></i>
                        </a>
                        <ul class="sidebar-submenu">
                            <li><a href="?page=dot-cham-diem">Phiếu đánh giá</a></li>
                            <li><a href="?page=ket-qua">Kết quả rèn luyện</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="../../auth/action.php?req=dang-xuat&role=user" class="sidebar-link">
                            <i class="ri-logout-box-r-line"></i> Đăng xuất
                        </a>
                    </li>
                </ul>


            </div>
        </div>
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- content -->
        <?php 
            $page = isset($_GET['page']) ? $_GET['page'] : 'home';
            if ($page == 'home') {
                require "home.php";
            } else if ($page == 'ket-qua') {
                require "ketqua.php";
            } else if ($page == 'dot-cham-diem' || $page == 'phieu-cham-diem') {
                require "dot-cham-diem.php";
            } else if ($page == 'doi-mat-khau') {
                require "doi-mat-khau.php";
            } else if ($page == 'thong-tin-ca-nhan') {
                require "thong-tin-ca-nhan.php";
            } else {
                require "home.php";
            }
        ?>

        <!-- footer -->
        <?php require 'footer.php';?>
    </div>

    <!-- Js Files -->
    <script src="../../assets/vendor/jquery/jquery.min.js"></script>
    <script src="../../assets/theme/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/vendor/sweetalert2@11.js"></script>
    <script src="../../assets/js/chart.min.js"></script>
    
    <script>
        // Init Toast for SweetAlert2
        window.Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // Init chart.js without dummy data
        const ctxRegistered = document.getElementById('chartRegistered');
        
        if(ctxRegistered) {
            new Chart(ctxRegistered, {
                type: 'bar',
                data: {
                    labels: [], // Dữ liệu trống
                    datasets: [{
                        label: 'Tham gia',
                        data: [], // Dữ liệu trống
                        backgroundColor: 'rgba(29, 78, 216, 0.5)'
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    </script>
    
    <style>
        /* CSS cho thẻ Toast: Giảm khoảng cách giữa tiêu đề và nội dung */
        .swal2-popup.swal2-toast {
            padding: 8px 12px !important;
        }
        .swal2-popup.swal2-toast:has(.swal2-success) {
            border: 1px solid #28a745 !important;
            border-radius: 6px !important;
        }
        .swal2-popup.swal2-toast:has(.swal2-error) {
            border: 1px solid #dc3545 !important;
            border-radius: 6px !important;
        }
        .swal2-toast .swal2-title {
            margin: 0.1em 0 0 0 !important;
            font-size: 15px !important;
        }
        .swal2-toast.swal2-icon-success .swal2-title,
        .swal2-toast .swal2-success ~ .swal2-title {
            color: #28a745 !important;
            font-weight: bold !important;
        }
        .swal2-toast.swal2-icon-error .swal2-title,
        .swal2-toast .swal2-error ~ .swal2-title {
            color: #dc3545 !important;
            font-weight: bold !important;
        }
        .swal2-toast .swal2-html-container {
            margin: 0.2em 0 0.2em 0 !important;
            font-size: 14px !important;
        }
    </style>
    
    <?php
       // Xử lý thông báo sau khi chuyển trang
       if(isset($_GET['status'])){
            if($_GET['status'] == "success"){
                $msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : 'Thao tác thành công!';
                $title = isset($_GET['title']) ? htmlspecialchars($_GET['title']) : 'Thành công!';
                echo "<script>
                try {
                    if (typeof window.Toast !== 'undefined') {
                        window.Toast.fire({ icon: 'success', title: '" . $title . "', text: '" . $msg . "' });
                    } else {
                        alert('" . $title . "! " . $msg . "');
                    }
                } catch (e) {
                    console.error('Lỗi hiển thị thông báo:', e);
                    alert('" . $title . "!');
                }
                if (window.history.replaceState) {
                    const url = new URL(window.location.href);
                    url.searchParams.delete('status');
                    url.searchParams.delete('msg');
                    url.searchParams.delete('title');
                    window.history.replaceState({ path: url.href }, '', url.href);
                }
                </script>";
            }
            if($_GET['status'] == "failed"){
               echo "<script>
               try {
                   if (typeof window.Toast !== 'undefined') {
                       window.Toast.fire({ icon: 'error', title: 'Thất bại!', text: 'Thao tác không thành công!' });
                   } else {
                       alert('Thất bại!');
                   }
               } catch (e) {
                   console.error('Lỗi hiển thị thông báo:', e);
                   alert('Thất bại!');
               }
               if (window.history.replaceState) {
                   const url = new URL(window.location.href);
                   url.searchParams.delete('status');
                   window.history.replaceState({ path: url.href }, '', url.href);
               }
               </script>";
           }
       }
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarBtn = document.getElementById('sidebarToggleBtn');
            const customSidebar = document.getElementById('customSidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            
            // Toggle sidebar dropdown
            sidebarBtn.addEventListener('click', function() {
                sidebarBtn.classList.toggle('active');
                customSidebar.classList.toggle('active');
                sidebarOverlay.classList.toggle('active');
            });
            
            // Close when clicking overlay
            sidebarOverlay.addEventListener('click', function() {
                sidebarBtn.classList.remove('active');
                customSidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
            });
            
            // Toggle submenus with animation
            const dropdownBtns = document.querySelectorAll('.dropdown-toggle-btn');
            dropdownBtns.forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const parentLi = this.parentElement;
                    const submenu = this.nextElementSibling;
                    // Close others
                    document.querySelectorAll('.sidebar-dropdown').forEach(function(item) {
                        if (item !== parentLi) {
                            item.classList.remove('active');
                            const otherSub = item.querySelector('.sidebar-submenu');
                            if (otherSub) $(otherSub).slideUp(250);
                        }
                    });
                    // Toggle current
                    parentLi.classList.toggle('active');
                    if (submenu) $(submenu).slideToggle(250);
                });
            });
            

        });
    </script>
</body>
</html>
