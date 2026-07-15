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
    <!-- Ekko Lightbox -->
    <link rel="stylesheet" href="../assets/theme/plugins/ekko-lightbox/ekko-lightbox.css">
    <link rel="stylesheet" href="../assets/theme/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../assets/theme/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="../assets/theme/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../assets/theme/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../assets/vendor/dropzone/dropzone.min.css">
    <link rel="stylesheet" href="../assets/css/main.css">

</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- header -->
        <?php require 'layouts/header.php';?>

        <!-- controller -->
        <?php require 'controller.php';?>

        <!-- footer -->
        <?php require 'layouts/footer.php';?>

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
    <!-- Ekko Lightbox -->
    <script src="../assets/theme/plugins/ekko-lightbox/ekko-lightbox.min.js"></script>
    <!-- Filterizr-->
    <script src="../assets/theme/plugins/filterizr/jquery.filterizr.min.js"></script>
    <!-- sweetalert2  -->
    <script src="../assets/vendor/sweetalert2@11.js"></script>
    <!-- dropzonejs -->
    <script src="../assets/vendor/dropzone/dropzone.min.js"></script>

    <!-- AdminLTE App -->
    <script src="../assets/theme/dist/js/adminlte.min.js"></script>


    <script>
    //Bootstrap Duallistbox
    $('.duallistbox').bootstrapDualListbox()
    $('.duallistbox_sv').bootstrapDualListbox()


    Dropzone.options.uploadForm = { // The camelized version of the ID of the form element

        dictDefaultMessage: 'Kéo thả hình ảnh vào đây',
        paramName: "hinh_anh",
        acceptedFiles: "image/jpeg,image/png,image/jpg",
        autoProcessQueue: false,
        thumbnailWidth: 400,
        thumbnailHeight: 400,
        maxFilesize: 10,
        addRemoveLinks: true,

        init: function() {
            var myDropzone = this;
            document.getElementById("btn_upload").removeAttribute("disabled");
            this.element.querySelector("button[type=submit]").addEventListener("click", function(e) {
                e.preventDefault();
                e.stopPropagation();
                myDropzone.processQueue();
            });
        },
        queuecomplete: function() {
            this.removeAllFiles();
            Swal.fire({
                title: 'Đã thêm minh chứng thành công',
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
            })
        },

    }
    $(document).on('click', '[data-toggle="lightbox"]', function(event) {
        event.preventDefault();
        $(this).ekkoLightbox({
            alwaysShowClose: true
        });
    });

    $('.filter-container').filterizr({
        gutterPixels: 3
    });
    $('.btn[data-filter]').on('click', function() {
        $('.btn[data-filter]').removeClass('active');
        $(this).addClass('active');
    });


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
    </script>
    <?php
       if(isset($_GET['status'])){
           if($_GET['status'] == "success"){
               echo "<script>
               Swal.fire(
                   'Thành công!',
                   'Thao tác thành công!',
                   'success'
                 )</script>";
           }
           if($_GET['status'] == "failed"){
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