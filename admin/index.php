<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHẤM ĐIỂM RÈN LUYỆN</title>
    <link rel="icon" href="../assets/img/favicon.ico" type="image/gif" sizes="16x16">
    <meta name="description" content="CHẤM ĐIỂM RÈN LUYỆN">
    <!-- CSS Files -->
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../assets/theme/plugins/fontawesome-free/css/all.min.css">

    <!-- Bootstrap4 Duallistbox -->
    <link rel="stylesheet" href="../assets/theme/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">

    <link rel="stylesheet" href="../assets/theme/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../assets/theme/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="../assets/theme/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../assets/theme/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../assets/css/main.css">

</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- header -->
        <?php require 'layouts/header.php'; ?>

        <!-- sidebar -->
        <?php require 'layouts/sidebar.php'; ?>

        <!-- controller -->
        <?php require 'controller.php'; ?>

        <!-- footer -->
        <?php require 'layouts/footer.php'; ?>

    </div>

    <!-- Js Files -->
    <script src="../assets/vendor/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../assets/theme/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap4 Duallistbox -->
    <script src="../assets/theme/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>


    <script src="../assets/theme/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../assets/theme/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../assets/theme/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../assets/theme/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="../assets/theme/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="../assets/theme/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="../assets/theme/plugins/jszip/jszip.min.js"></script>
    <script src="../assets/theme/plugins/pdfmake/pdfmake.min.js"></script>
    <script src="../assets/theme/plugins/pdfmake/vfs_fonts.js"></script>
    <script src="../assets/theme/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="../assets/theme/plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="../assets/theme/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>



    <!-- AdminLTE App -->
    <script src="../assets/theme/dist/js/adminlte.min.js"></script>

    <script src="../assets/vendor/sweetalert2@11.js"></script>


    <script>
    window.addEventListener('load', function() {
        var po = localStorage.getItem("position");
        window.scrollBy(0, po);
        //alert(po);
        window.onscroll = function() {
            //alert("1234");
            var position = window.pageYOffset;
            //alert(position);
            if (typeof Storage !== "undefined") {
                localStorage.setItem("position", position);
            } else {
                alert("not");
            }
        };
    });
    //Bootstrap Duallistbox
    $('.duallistbox').bootstrapDualListbox()

    function confirm_sweet_chi_tiet(url) {
        Swal.fire({
            title: 'Xem chi tiết phiếu?',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                window.open(url, '_blank');
            }
        })
    }

    function confirm_sweet(url) {
        Swal.fire({
            title: 'Xác nhận thao tác?',
            text: "Bạn chắc chắn thực hiện thao tác này",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                location.href = url;
            }
        })
    }

    function confirm_sweet_ha_bac(url) {
        Swal.fire({
            title: 'Xác nhận hạ bậc?',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                location.href = url;
            }
        })
    }
    </script>
    <?php
    if (isset($_GET['status'])) {
        if ($_GET['status'] == "success") {
            echo "<script>
               Swal.fire(
                   'Thành công!',
                   'Thao tác thành công!',
                   'success'
                 )</script>";
        }
        if ($_GET['status'] == "failed") {
            echo "<script>
               Swal.fire(
                   'Thất bại!',
                   'Thao tác không thành công!',
                   'error'
                 )</script>";
        }
    }
    ?>
</body>

</html>