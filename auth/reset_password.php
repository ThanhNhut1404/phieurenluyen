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
                        <h2>ĐỔI MẬT KHẨU</h2>
                        <div class="login-title-line"></div>

                        <?php $email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>
                        <form id="resetForm" onsubmit="handleResetSubmit(event)">
                            <!-- Email Input -->
                            <div class="form-group-custom">
                                <label for="inputEmail">Email</label>
                                <div class="input-wrapper-custom">
                                    <input type="email" id="inputEmail" name="email" class="form-control-custom" value="<?= $email ?>" readonly style="background-color: #f1f5f9;">
                                    <span class="input-icon-right">
                                        <i class="far fa-envelope"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- OTP Input -->
                            <div class="form-group-custom">
                                <label for="inputOTP" class="d-flex justify-content-between align-items-center mb-1">
                                    <span>Mã OTP (6 số)</span>
                                    <span id="otpTimer" class="text-danger" style="font-size: 0.75rem; font-weight: 600;"></span>
                                </label>
                                <div class="input-wrapper-custom">
                                    <input type="text" id="inputOTP" name="otp" class="form-control-custom" placeholder="Nhập mã OTP từ email" required autocomplete="off" maxlength="6">
                                    <span class="input-icon-right">
                                        <i class="fas fa-key"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- New Password Input -->
                            <div class="form-group-custom">
                                <label for="inputMatKhau">Mật khẩu mới</label>
                                <div class="input-wrapper-custom">
                                    <input type="password" id="inputMatKhau" name="mat_khau" class="form-control-custom" placeholder="Nhập mật khẩu mới" required minlength="9">
                                    <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('inputMatKhau', 'passwordEyeIcon')">
                                        <i id="passwordEyeIcon" class="far fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Confirm Password Input -->
                            <div class="form-group-custom">
                                <label for="inputMatKhauConfirm">Xác nhận mật khẩu</label>
                                <div class="input-wrapper-custom">
                                    <input type="password" id="inputMatKhauConfirm" name="mat_khau_confirm" class="form-control-custom" placeholder="Nhập lại mật khẩu mới" required minlength="9">
                                    <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('inputMatKhauConfirm', 'passwordEyeIconConfirm')">
                                        <i id="passwordEyeIconConfirm" class="far fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Message Alert Area -->
                            <div id="errorMessage" class="login-message-container mb-0 d-none" style="margin-top: -15px; margin-bottom: -12px; padding: 5px 0;"></div>

                            <!-- Submit Button -->
                            <button class="btn-submit-custom mt-2" type="submit" id="submitBtn">Đổi mật khẩu</button>

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

    <!-- Captcha and Interactivity Logic -->
    <script>
        let countdownTimer;

        function startOtpCountdown(duration) {
            if(countdownTimer) clearInterval(countdownTimer);
            let timer = duration, minutes, seconds;
            const display = document.getElementById('otpTimer');
            
            countdownTimer = setInterval(function () {
                minutes = parseInt(timer / 60, 10);
                seconds = parseInt(timer % 60, 10);

                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                display.innerHTML = `Hết hạn sau: ${minutes}:${seconds}`;

                if (--timer < 0) {
                    clearInterval(countdownTimer);
                    display.innerHTML = 'Đã hết hạn. <a href="#" onclick="resendOTP(event)" style="text-decoration: underline; cursor: pointer; color: #1e40af;">Gửi lại mã</a>';
                }
            }, 1000);
        }

        window.onload = function() {
            startOtpCountdown(3 * 60); // 3 minutes
        };

        async function resendOTP(e) {
            e.preventDefault();
            const email = document.getElementById('inputEmail').value.trim();
            const errorMsg = document.getElementById('errorMessage');
            const display = document.getElementById('otpTimer');
            
            if(!email) return;

            display.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';

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
                    startOtpCountdown(3 * 60); // Khởi động lại đếm ngược 3 phút
                } else {
                    errorMsg.className = "login-message-container mb-0 error";
                    errorMsg.innerText = result.message || 'Lỗi gửi mã OTP. Vui lòng thử lại!';
                    display.innerHTML = 'Đã hết hạn. <a href="#" onclick="resendOTP(event)" style="text-decoration: underline; cursor: pointer; color: #1e40af;">Gửi lại mã</a>';
                }
            } catch (error) {
                errorMsg.className = "login-message-container mb-0 error";
                errorMsg.innerText = 'Lỗi kết nối máy chủ khi gửi lại mã OTP.';
                display.innerHTML = 'Đã hết hạn. <a href="#" onclick="resendOTP(event)" style="text-decoration: underline; cursor: pointer; color: #1e40af;">Gửi lại mã</a>';
            }
        }

        function togglePasswordVisibility(inputId, iconId) {
            const pwdInput = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                pwdInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        async function handleResetSubmit(e) {
            e.preventDefault();
            const email = document.getElementById('inputEmail').value.trim();
            const otp = document.getElementById('inputOTP').value.trim();
            const matKhau = document.getElementById('inputMatKhau').value;
            const matKhauConfirm = document.getElementById('inputMatKhauConfirm').value;
            const btn = document.getElementById('submitBtn');
            const errorMsg = document.getElementById('errorMessage');

            if(!email || !otp || !matKhau || !matKhauConfirm) return;

            errorMsg.className = 'login-message-container mb-0 d-none';
            errorMsg.innerText = '';

            if(matKhau.length < 9 || !/[A-Za-z]/.test(matKhau) || !/\d/.test(matKhau) || !/[^A-Za-z0-9]/.test(matKhau)) {
                errorMsg.className = "login-message-container mb-0 error";
                errorMsg.innerText = 'Mật khẩu phải từ 9 ký tự, gồm chữ, số và ký tự đặc biệt!';
                return;
            }

            if(matKhau !== matKhauConfirm) {
                errorMsg.className = "login-message-container mb-0 error";
                errorMsg.innerText = 'Mật khẩu xác nhận không khớp!';
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

            try {
                const response = await fetch('../api/reset_password.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        email: email,
                        otp_code: otp,
                        new_password: matKhau
                    })
                });

                const result = await response.json();

                if (result.status === 'success') {
                    errorMsg.className = "login-message-container mb-0 success";
                    errorMsg.innerText = result.message;
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 2000);
                } else {
                    errorMsg.className = "login-message-container mb-0 error";
                    errorMsg.innerText = result.message || 'Mã OTP không hợp lệ hoặc đã hết hạn!';
                }
            } catch (error) {
                errorMsg.className = "login-message-container mb-0 error";
                errorMsg.innerText = 'Lỗi kết nối máy chủ.';
            } finally {
                btn.disabled = false;
                btn.innerHTML = 'Đổi mật khẩu';
            }
        }
    </script>
</body>
</html>