<?php
    session_start();
    require '../models/getModel.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chấm Điểm Rèn Luyện</title>
    <link rel="icon" href="../assets/img/favicon.ico" type="image/gif" sizes="16x16">
    <meta name="description" content="Chấm Điểm Rèn Luyện">
    
    <!-- Google Font: Plus Jakarta Sans & Source Sans Pro -->
    <link rel="stylesheet" href="../assets/css/source-sans-pro.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../assets/theme/plugins/fontawesome-free/css/all.min.css">
    <!-- AdminLTE & Bootstrap -->
    <link rel="stylesheet" href="../assets/theme/dist/css/adminlte.min.css?v=1">
    <!-- Custom Signin Style -->
    <link rel="stylesheet" href="../assets/css/signin.css?v=<?= time(); ?> ">
</head>

<body>
    <!-- Header -->
    <header class="login-header">
        <div class="login-header-logo">
            <a href="index.php">
                <img src="../assets/img/logo.png" alt="Logo">
            </a>
        </div>
        <nav class="login-header-nav">
            <a href="#"><i class="far fa-file-alt"></i> Hướng dẫn</a>
            <a href="#"><i class="far fa-question-circle"></i> Hỗ trợ</a>
        </nav>
    </header>

    <!-- Main Content Grid -->
    <div class="login-main-container">
        <div class="container-fluid login-row-wrapper p-0">
            <div class="row align-items-stretch m-0 h-100">
                <!-- Left column: Welcome Text and Features -->
                <div class="col-lg-8 col-12 welcome-box mb-lg-0 mb-5">
                    <h1>CHÀO MỪNG <span class="blue-text">TRỞ LẠI!</span></h1>
                    <p>Chào mừng bạn đến với Hệ thống Đánh giá Kết quả Rèn luyện Sinh viên. Vui lòng đăng nhập để tiến hành tự đánh giá, xem kết quả xếp loại và quản lý quá trình rèn luyện.</p>
                    <ul class="features-list">
                        <li>Thực hiện đánh giá điểm rèn luyện định kỳ</li>
                        <li>Tra cứu và theo dõi kết quả các học kỳ</li>
                        <li>Cập nhật thông tin nhanh chóng từ Khoa và Cố vấn học tập</li>
                    </ul>
                </div>

                <!-- Right column: Login Card -->
                <div class="col-lg-4 col-12 login-card-wrapper">
                    <div class="login-card">
                        <div class="login-card-logo-wrap">
                            <img src="../assets/img/logo.png" alt="Logo">
                        </div>
                        <h2>QUÊN MẬT KHẨU</h2>
                        <div class="login-title-line"></div>

                        <form id="forgotForm" onsubmit="handleForgotSubmit(event)">
                            <!-- Email Input -->
                            <div class="form-group-custom">
                                <label for="inputEmail">Email</label>
                                <div class="input-wrapper-custom">
                                    <input type="email" id="inputEmail" name="email" class="form-control-custom" placeholder="Nhập email của bạn" required autofocus autocomplete="email">
                                    <span class="input-icon-right">
                                        <i class="far fa-envelope"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- Message Alert Area -->
                            <div id="errorMessage" class="login-message-container mb-0 d-none" style="margin-top: -15px; margin-bottom: -12px; padding: 5px 0;"></div>

                            <!-- Submit Button -->
                            <button class="btn-submit-custom mt-2" type="submit" id="submitBtn">Nhận mã OTP</button>

                            <!-- Footer Link -->
                            <div class="login-card-footer mt-2">
                                Trở về trang <a href="index.php">Đăng nhập</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JS Files -->
    <script src="../assets/vendor/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../assets/theme/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../assets/theme/dist/js/adminlte.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="../assets/vendor/sweetalert2@11.js"></script>

    <script>
        async function handleForgotSubmit(e) {
            e.preventDefault();
            const email = document.getElementById('inputEmail').value.trim();
            const btn = document.getElementById('submitBtn');
            const errorMsg = document.getElementById('errorMessage');

            if(!email) return;

            errorMsg.className = 'login-message-container mb-0 d-none';
            errorMsg.innerText = '';

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi mã...';

            try {
                const response = await fetch('../api/forgot_password.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email: email })
                });

                const result = await response.json();

                if (result.status === 'success') {
                    errorMsg.className = "login-message-container mb-0 success";
                    errorMsg.innerText = result.message;
                    
                    setTimeout(() => {
                        window.location.href = `reset_password.php?email=${encodeURIComponent(email)}`;
                    }, 2000);
                } else {
                    errorMsg.className = "login-message-container mb-0 error";
                    errorMsg.innerText = result.message || 'Có lỗi xảy ra, vui lòng thử lại!';
                }
            } catch (error) {
                errorMsg.className = "login-message-container mb-0 error";
                errorMsg.innerText = 'Lỗi kết nối máy chủ.';
            } finally {
                btn.disabled = false;
                btn.innerHTML = 'Nhận mã OTP';
            }
        }
    </script>
</body>
</html>