<?php ob_start(); ?>
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
    <link rel="stylesheet" href="../assets/css/main.css?v=6">

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

    // Quân sửa: Tăng cường trải nghiệm người dùng (UX) khi nhập điểm rèn luyện
    $(document).on('focus', 'input.kq_sv, input.kq_lt_bt, input.kq_btdk, input.kq_gv', function() {
        if ($(this).prop('readonly') || $(this).prop('disabled')) {
            return;
        }
        if ($(this).val() === '0') {
            $(this).val('');
        }
    });

    $(document).on('blur', 'input.kq_sv, input.kq_lt_bt, input.kq_btdk, input.kq_gv', function() {
        if ($(this).prop('readonly') || $(this).prop('disabled')) {
            return;
        }
        if ($(this).val() === '') {
            $(this).val('0');
            $(this).trigger('change');
        }
    });

    $(document).on('keydown keypress', 'input.kq_sv, input.kq_lt_bt, input.kq_btdk, input.kq_gv', function(e) {
        if ($(this).prop('readonly') || $(this).prop('disabled')) {
            return;
        }
        // Chặn các phím không phải là số (e, E, -, +, dấu chấm, dấu phẩy)
        if (e.key === 'e' || e.key === 'E' || e.key === '-' || e.key === '+' || e.key === '.' || e.key === ',') {
            e.preventDefault();
        }
    });

    $(document).on('input', 'input.kq_sv, input.kq_lt_bt, input.kq_btdk, input.kq_gv', function() {
        if ($(this).prop('readonly') || $(this).prop('disabled')) {
            return;
        }
        var val = $(this).val();
        // Tự động bỏ số 0 ở đầu khi nhập số khác (ví dụ: nhập 05 -> đổi thành 5)
        if (val.length > 1 && val.startsWith('0')) {
            $(this).val(parseInt(val, 10));
        }
    });

    // Nhựt sửa: Tự động điền 0 vào các ô điểm rỗng trước khi submit form và gửi bằng AJAX để giữ lại dữ liệu khi thất bại
    $(document).on('submit', 'form', function(e) {
        var form = $(this);
        form.find('input.kq_sv, input.kq_lt_bt, input.kq_btdk, input.kq_gv').each(function() {
            if ($(this).val() === '') {
                $(this).val('0');
            }
        });

        var actionUrl = form.attr('action') || '';
        if (actionUrl.indexOf('action.php?req=add') !== -1) {
            e.preventDefault();
            var submitBtn = form.find('input[type="submit"], button[type="submit"]');
            var originalBtnValue = submitBtn.val();
            submitBtn.prop('disabled', true).val('Đang xử lý...');

            $.ajax({
                url: actionUrl,
                type: form.attr('method') || 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: function(response, status, xhr) {
                    var finalUrl = xhr.responseURL || '';
                    if (finalUrl.indexOf('status=success') !== -1) {
                        Swal.fire({
                            title: 'Thành công!',
                            text: 'Thao tác thành công!',
                            icon: 'success'
                        }).then(function() {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Thất bại!',
                            text: 'Thao tác không thành công! Điểm số đã nhập vẫn được giữ lại để bạn chỉnh sửa.',
                            icon: 'error'
                        });
                        submitBtn.prop('disabled', false).val(originalBtnValue);
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Thất bại!',
                        text: 'Có lỗi kết nối mạng xảy ra. Điểm số đã nhập vẫn được giữ lại.',
                        icon: 'error'
                    });
                    submitBtn.prop('disabled', false).val(originalBtnValue);
                }
            });
        }
    });
    </script>
    <?php
       // Nhựt sửa lỗi: Sử dụng replaceState để xóa tham số status khỏi URL nhằm tránh lặp lại thông báo SweetAlert khi F5/reload trang.
       if(isset($_GET['status'])){
           if($_GET['status'] == "success"){
               echo "<script>
               Swal.fire(
                   'Thành công!',
                   'Thao tác thành công!',
                   'success'
                 );
                 if (window.history.replaceState) {
                     const url = new URL(window.location.href);
                     url.searchParams.delete('status');
                     window.history.replaceState({ path: url.href }, '', url.href);
                 }
                 </script>";
           }
           if($_GET['status'] == "failed"){
               echo "<script>
               Swal.fire(
                   'Thất bại!',
                   'Thao tác không thành công!',
                   'error'
                 );
                 if (window.history.replaceState) {
                     const url = new URL(window.location.href);
                     url.searchParams.delete('status');
                     window.history.replaceState({ path: url.href }, '', url.href);
                 }
                 </script>";
           }
         
       }
?>
</body>

</html>