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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
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
                        <h2>ĐĂNG NHẬP HỆ THỐNG</h2>
                        <div class="login-title-line"></div>

                        <form id="loginForm" action="action.php?req=dang-nhap" method="POST" onsubmit="handleFormSubmit(event)">
                            <!-- Email Input -->
                            <div class="form-group-custom">
                                <label for="inputEmail">Email</label>
                                <div class="input-wrapper-custom">
                                    <input type="email" id="inputEmail" name="email" class="form-control-custom" placeholder="Nhập email của bạn" required autofocus autocomplete="email">
                                    <span class="input-icon-right">
                                        <i class="far fa-user"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- Password Input -->
                            <div class="form-group-custom">
                                <label for="inputMatKhau">Mật khẩu</label>
                                <div class="input-wrapper-custom">
                                    <input type="password" id="inputMatKhau" name="mat_khau" class="form-control-custom" placeholder="Nhập mật khẩu" required>
                                    <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility()">
                                        <i id="passwordEyeIcon" class="far fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Remember Me and Forgot Password Link -->
                            <div class="login-options-row">
                                <label class="custom-checkbox-wrap">
                                    <input type="checkbox" id="rememberMeCheckbox">
                                    <span>Ghi nhớ đăng nhập</span>
                                </label>
                                <a href="#" class="forgot-password-link">Quên mật khẩu?</a>
                            </div>

                            <!-- Captcha Input and Canvas -->
                            <div class="form-group-custom captcha-group-custom">
                                <label>Nhập mã xác thực</label>
                                <div class="captcha-container-custom">
                                    <div class="captcha-input-col">
                                        <input type="text" id="captchaInput" class="form-control-custom" placeholder="Nhập mã" required autocomplete="off">
                                    </div>
                                    <button type="button" class="captcha-reload-btn" onclick="generateCaptcha()">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                    <div class="captcha-canvas-col">
                                        <canvas id="captchaCanvas" width="120" height="40"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!-- Message Alert Area -->
                            <div id="loginMessage" class="login-message-container mb-1 d-none"></div>

                            <!-- Submit Button -->
                            <button class="btn-submit-custom mt-1" type="submit" id="submitBtn">Đăng nhập</button>

                            <!-- Footer Link -->
                            <div class="login-card-footer">
                                Chưa có tài khoản? <a href="#">Liên hệ</a>
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
        let captchaCode = '';

        function generateCaptcha() {
            const canvas = document.getElementById('captchaCanvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            const chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789';
            
            captchaCode = '';
            for (let i = 0; i < 5; i++) {
                captchaCode += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            ctx.fillStyle = '#eff6ff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            for (let i = 0; i < 30; i++) {
                ctx.fillStyle = 'rgba(30, 64, 175, 0.08)';
                ctx.beginPath();
                ctx.arc(Math.random() * canvas.width, Math.random() * canvas.height, Math.random() * 2 + 1, 0, Math.PI * 2);
                ctx.fill();
            }
            
            ctx.textBaseline = 'middle';
            
            for (let i = 0; i < captchaCode.length; i++) {
                ctx.save();
                const x = 12 + i * 20 + Math.random() * 4;
                const y = canvas.height / 2 + (Math.random() * 8 - 4);
                const angle = (Math.random() * 0.4 - 0.2);
                
                ctx.translate(x, y);
                ctx.rotate(angle);
                
                const size = Math.floor(Math.random() * 4) + 18;
                ctx.font = `bold ${size}px "Plus Jakarta Sans", sans-serif`;
                
                ctx.fillStyle = '#1e40af';
                ctx.fillText(captchaCode.charAt(i), 0, 0);
                ctx.restore();
            }
            
            for (let i = 0; i < 4; i++) {
                ctx.strokeStyle = 'rgba(30, 64, 175, 0.25)';
                ctx.lineWidth = 1.6;
                ctx.beginPath();
                ctx.moveTo(0, Math.random() * canvas.height);
                ctx.lineTo(canvas.width, Math.random() * canvas.height);
                ctx.stroke();
            }

            for (let i = 0; i < 25; i++) {
                ctx.fillStyle = 'rgba(30, 64, 175, 0.3)';
                ctx.beginPath();
                ctx.arc(Math.random() * canvas.width, Math.random() * canvas.height, Math.random() * 1.5 + 0.8, 0, Math.PI * 2);
                ctx.fill();
            }

            for (let i = 0; i < 8; i++) {
                ctx.strokeStyle = 'rgba(30, 64, 175, 0.2)';
                ctx.lineWidth = 1.2;
                const x = Math.random() * canvas.width;
                const y = Math.random() * canvas.height;
                const offset = 4;
                ctx.beginPath();
                ctx.moveTo(x - offset, y - offset);
                ctx.lineTo(x + offset, y + offset);
                ctx.moveTo(x + offset, y - offset);
                ctx.lineTo(x - offset, y + offset);
                ctx.stroke();
            }
        }

        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('inputMatKhau');
            const eyeIcon = document.getElementById('passwordEyeIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.className = 'far fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                eyeIcon.className = 'far fa-eye';
            }
        }

        function handleFormSubmit(event) {
            event.preventDefault();

            const emailInput = document.getElementById('inputEmail').value.trim();
            const passwordInput = document.getElementById('inputMatKhau').value.trim();
            const captchaInput = document.getElementById('captchaInput').value.trim();
            const messageBox = document.getElementById('loginMessage');
            const submitBtn = document.getElementById('submitBtn');

            messageBox.className = 'login-message-container mb-1 d-none';
            messageBox.innerText = '';

            if (captchaInput !== captchaCode) {
                messageBox.innerText = 'Mã xác thực không chính xác!';
                messageBox.className = 'login-message-container mb-1 error';
                generateCaptcha();
                document.getElementById('captchaInput').value = '';
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerText = 'Đang xử lý...';

            const formData = new FormData(document.getElementById('loginForm'));
            
            fetch('action.php?req=dang-nhap', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.url.includes('status=error')) {
                    messageBox.innerText = 'Email hoặc mật khẩu không chính xác!';
                    messageBox.className = 'login-message-container mb-1 error';
                    generateCaptcha();
                    document.getElementById('captchaInput').value = '';
                    document.getElementById('inputMatKhau').value = '';
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Đăng nhập';
                } else {
                    messageBox.innerText = 'Đăng nhập thành công!';
                    messageBox.className = 'login-message-container mb-1 success';
                    
                    setTimeout(() => {
                        window.location.href = response.url;
                    }, 1500);
                }
            })
            .catch(error => {
                console.error(error);
                messageBox.innerText = 'Đã xảy ra lỗi kết nối. Vui lòng thử lại!';
                messageBox.className = 'login-message-container mb-3 error';
                submitBtn.disabled = false;
                submitBtn.innerText = 'Đăng nhập';
            });
        }

        // Initialize Captcha
        window.onload = function() {
            generateCaptcha();
        };
    </script>

    <?php
       if(isset($_GET['status'])){
            if($_GET['status'] == "error"){
                echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Đăng nhập không thành công!',
                    text: 'Thông tin đăng nhập không đúng hoặc tài khoản bị khóa!',
                    confirmButtonColor: '#3182ce'
                })</script>";
            }
       }
    ?>
</body>

</html>