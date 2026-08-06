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
    <!-- CSS Files -->
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../assets/theme/plugins/fontawesome-free/css/all.min.css">

    <link rel="stylesheet" href="../assets/theme/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../assets/css/signin.css">
</head>

<body class="text-center">
    <form class="form-signin" action="action.php?req=dang-nhap" method="POST">
        <img class="mb-4" src="../assets/img/logo.png" alt="" width="100">
        <h1 class="h3 mb-3 font-weight-normal">Vui lòng đăng nhập</h1>
        <div class="form-group">
            <label for="inputEmail" class="sr-only">Email</label>
            <input type="email" id="inputEmail" name="email" class="form-control" placeholder="Nhập email" required
                autofocus>
        </div>
        <div class="form-group">
            <label for="inputMatKhau" class="sr-only">Mật khẩu</label>
            <input type="password" id="inputMatKhau" name="mat_khau" class="form-control" placeholder="Nhập mật khẩu"
                required>
        </div>
        <div class="form-group">
            <button class="btn btn-lg btn-success btn-block" type="submit">Đăng nhập</button>
        </div>

    </form>

    <!-- Js Files -->
    <script src="../assets/vendor/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../assets/theme/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- AdminLTE App -->
    <script src="../assets/theme/dist/js/adminlte.min.js"></script>

    <script src="../assets/vendor/sweetalert2@11.js"></script>

    <?php
       if(isset($_GET['status'])){
        if($_GET['status'] == "error"){
        echo "<script>
        Swal.fire(
            'Đăng nhập không thành công!',
            'Thông tin đăng nhập không đúng hoặc tài khoản bị khóa!',
            'error'
          )</script>";
    }
}
?>
</body>

</html>