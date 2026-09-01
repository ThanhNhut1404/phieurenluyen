<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin'])) {
    header('location: ../index.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <script>
        window.onerror = function(message, source, lineno, colno, error) {
            alert("JS Error: " + message + " at " + source + ":" + lineno);
            return true;
        };
    </script>
    <title>Chấm Điểm Rèn Luyện</title>
    <link rel="icon" href="../assets/img/favicon.ico" type="image/gif" sizes="16x16">
    <meta name="description" content="Chấm Điểm Rèn Luyện">
    <!-- CSS Files -->
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="../assets/css/source-sans-pro.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../assets/theme/plugins/fontawesome-free/css/all.min.css">

    <!-- Bootstrap4 Duallistbox -->
    <link rel="stylesheet" href="../assets/theme/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">
    
    <!-- Select2 -->
    <link rel="stylesheet" href="../assets/theme/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="../assets/theme/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

    <link rel="stylesheet" href="../assets/theme/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../assets/theme/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="../assets/theme/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../assets/theme/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../assets/css/main.css?v=10">

    <link rel="stylesheet" href="../assets/theme/plugins/summernote/summernote-bs4.min.css">
    <link href="../assets/remixicon/fonts/remixicon.css" rel="stylesheet" />

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
    
    <!-- Select2 -->
    <script src="../assets/theme/plugins/select2/js/select2.full.min.js"></script>


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

    <script src="../assets/theme/plugins/summernote/summernote-bs4.min.js"></script>

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
    $('.duallistbox').bootstrapDualListbox({
        filterTextClear: 'Hiện tất cả',
        filterPlaceHolder: 'Tìm kiếm',
        infoText: 'Hiển thị tất cả ({0})',
        infoTextFiltered: '<span class="badge badge-warning">Tìm kiếm</span> {0} từ {1}',
        infoTextEmpty: 'Danh sách trống'
    });

    function confirm_sweet_chi_tiet(url) {
        Swal.fire({
            title: 'Xem chi tiết phiếu?',
            text: 'Hệ thống sẽ mở chi tiết phiếu trong một tab mới.',
            icon: 'warning',
            width: '36em',
            showCancelButton: true,
            confirmButtonText: 'Có',
            cancelButtonText: 'Hủy',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-success font-weight-bold mx-2 px-4 py-2',
                cancelButton: 'btn btn-cancel-custom font-weight-bold mx-2 px-4 py-2'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.open(url, '_blank');
            }
        });
        return false;
    }

    function confirm_sweet(url, message = "Bạn chắc chắn thực hiện thao tác này?", actionType = "default") {
        let titleColor = "#0f2a5a";
        if (actionType === "status") {
            titleColor = "#28a745"; // green
        } else if (actionType === "reset") {
            titleColor = "#ffc107"; // yellow
        } else if (actionType === "delete") {
            titleColor = "#dc3545"; // red
        }

        Swal.fire({
            title: `<span style="color: ${titleColor} !important">Xác nhận thao tác?</span>`,
            text: message,
            icon: 'warning',
            width: '36em',
            showCancelButton: true,
            confirmButtonText: 'Có',
            cancelButtonText: 'Hủy',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-success font-weight-bold mx-2 px-4 py-2',
                cancelButton: 'btn btn-cancel-custom font-weight-bold mx-2 px-4 py-2'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                location.href = url;
            }
        });
        return false;
    }

    function confirm_delete_sweet(url, entity_name) {
        Swal.fire({
            title: 'Xác nhận xóa?',
            html: 'Thao tác này sẽ xóa <b>' + entity_name + '</b><br>đã chọn và không thể hoàn tác.',
            icon: 'warning',
            width: '36em',
            showCancelButton: true,
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-success font-weight-bold mx-2 px-4 py-2',
                cancelButton: 'btn btn-cancel-custom font-weight-bold mx-2 px-4 py-2'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                location.href = url;
            }
        });
        return false;
    }

    function confirm_toggle_sweet(url, message) {
        Swal.fire({
            title: '<span style="color: #ffc107 !important">Xác nhận thao tác?</span>',
            html: message,
            icon: 'warning',
            width: '36em',
            showCancelButton: true,
            confirmButtonText: 'Xác nhận',
            cancelButtonText: 'Hủy',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-success font-weight-bold mx-2 px-4 py-2',
                cancelButton: 'btn btn-cancel-custom font-weight-bold mx-2 px-4 py-2'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                location.href = url;
            }
        });
        return false;
    }

    function confirm_sweet_ha_bac(url) {
        Swal.fire({
            title: 'Xác nhận hạ bậc?',
            text: 'Bạn có chắc chắn muốn hạ bậc rèn luyện của sinh viên này?',
            icon: 'warning',
            width: '36em',
            showCancelButton: true,
            confirmButtonText: 'Có',
            cancelButtonText: 'Hủy',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-success font-weight-bold mx-2 px-4 py-2',
                cancelButton: 'btn btn-cancel-custom font-weight-bold mx-2 px-4 py-2'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                location.href = url;
            }
        });
        return false;
    }
    const Toast = Swal.mixin({
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
    </script>
    <?php
    if (isset($_GET['status'])) {
        // Nhựt sửa lỗi: bổ sung thông báo riêng cho lỗi validate, không tìm thấy, trùng dữ liệu, ràng buộc, CSRF và lỗi hệ thống.
        $invalid_alerts = array(
            "quan-ly-hoc-ky" => array("Dữ liệu học kỳ không hợp lệ!", "Vui lòng kiểm tra tên học kỳ, năm học, ngày bắt đầu, ngày kết thúc và ghi chú.", "error"),
            "quan-ly-dot-cham-diem" => array("Dữ liệu đợt chấm điểm không hợp lệ!", "Vui lòng kiểm tra tên đợt, năm học, học kỳ, thời gian bắt đầu, thời gian kết thúc và lớp áp dụng.", "error"),
            "quan-ly-xep-loai" => array("Dữ liệu xếp loại không hợp lệ!", "Vui lòng kiểm tra tên xếp loại, cận dưới, cận trên, hạ bậc và ghi chú.", "error"),
            "quan-ly-dieu" => array("Dữ liệu Điều không hợp lệ!", "Vui lòng kiểm tra tên Điều, nội dung chi tiết và thứ tự.", "error"),
            "quan-ly-nam-hoc" => array("Dữ liệu năm học không hợp lệ!", "Vui lòng kiểm tra tên năm học và khoảng thời gian.", "error"),
            "quan-ly-khoa" => array("Dữ liệu khoa không hợp lệ!", "Vui lòng kiểm tra tên khoa và ghi chú.", "error"),
            "quan-ly-khoa-hoc" => array("Dữ liệu khóa học không hợp lệ!", "Vui lòng kiểm tra tên khóa học và ghi chú.", "error"),
            "quan-ly-lop-hoc" => array("Dữ liệu lớp học không hợp lệ!", "Vui lòng kiểm tra tên lớp học và các trường liên quan.", "error"),
            "quan-ly-nganh-hoc" => array("Dữ liệu ngành học không hợp lệ!", "Vui lòng kiểm tra tên ngành học và khoa đã chọn.", "error"),
            "quan-ly-trinh-do" => array("Dữ liệu trình độ không hợp lệ!", "Vui lòng kiểm tra tên trình độ và mô tả.", "error"),
        );
        $alerts = array(
            // Nhựt sửa lỗi: thông báo riêng cho lỗi trùng/xóa trình độ.
            "duplicate-trinh-do" => array("Dữ liệu bị trùng!", "Tên trình độ đã tồn tại.", "error"),
            "related-trinh-do" => array("Không thể xóa!", "Trình độ này đang được sử dụng bởi giảng viên.", "error"),
            // Nhựt sửa lỗi: thông báo riêng cho lỗi trùng/xóa khóa học.
            "duplicate-khoa-hoc" => array("Dữ liệu bị trùng!", "Tên khóa học đã tồn tại.", "error"),
            "related-khoa-hoc" => array("Không thể xóa!", "Khóa học này đang được sử dụng bởi lớp học.", "error"),
            // Nhựt sửa lỗi: thông báo riêng cho lỗi trùng/xóa năm học.
            "duplicate-nam-hoc" => array("Dữ liệu bị trùng!", "Tên năm học đã tồn tại.", "error"),
            "related-nam-hoc" => array("Không thể xóa!", "Năm học này đang được sử dụng bởi học kỳ.", "error"),
            "overlap-nam-hoc" => array("Khoảng thời gian bị trùng!", "Ngày bắt đầu và ngày kết thúc đang chồng lên năm học khác.", "error"),
            // Nhựt sửa lỗi: thông báo riêng cho lỗi trùng/xóa học kỳ.
            "duplicate-hoc-ky" => array("Dữ liệu bị trùng!", "Tên học kỳ đã tồn tại trong năm học đã chọn.", "error"),
            "related-hoc-ky" => array("Không thể xóa!", "Học kỳ này đang được sử dụng bởi đợt chấm điểm.", "error"),
            "limit-hoc-ky" => array("Không thể thêm!", "Mỗi năm học chỉ được tối đa 3 học kỳ.", "error"),
            "overlap-hoc-ky" => array("Khoảng thời gian bị trùng!", "Học kỳ này đang chồng lên học kỳ khác trong cùng năm học.", "error"),
            "out-of-range-hoc-ky" => array("Ngày không hợp lệ!", "Ngày của học kỳ phải nằm trong khoảng năm học đã chọn.", "error"),
            "invalid" => array("Dữ liệu không hợp lệ!", "Vui lòng kiểm tra lại thông tin nhập.", "error"),
            "not-found" => array("Không tìm thấy dữ liệu!", "Bản ghi cần thao tác không tồn tại.", "error"),
            // Nhựt sửa lỗi: Bổ sung thông báo thời gian đợt chấm điểm không hợp lệ.
            "invalid-date" => array("Thời gian không hợp lệ", "Thời gian bắt đầu phải nhỏ hơn hoặc bằng thời gian kết thúc.", "error"),
            // Nhựt sửa lỗi: Thông báo khi thời gian Đợt chấm điểm không nằm trong khoảng Học kỳ đã chọn.
            // Nhựt sửa lỗi: báo riêng khi ngày kết thúc không lớn hơn ngày bắt đầu để người dùng biết lỗi nằm ở khoảng ngày.
            "invalid-date-range" => array("Khoảng thời gian không hợp lệ", "Ngày kết thúc phải lớn hơn ngày bắt đầu.", "error"),
            // Chi tiết hóa lỗi của các chức năng quản lý chung
            "invalid-nam-hoc" => array("Năm học không hợp lệ", "Năm học không hợp lệ hoặc không tồn tại trong hệ thống.", "error"),
            "invalid-ten-hocky" => array("Tên học kỳ không hợp lệ", "Tên học kỳ không được rỗng và không quá 50 ký tự.", "error"),
            "invalid-ten-nam-hoc" => array("Tên năm học không hợp lệ", "Tên năm học không được rỗng và không quá 50 ký tự.", "error"),
            "invalid-ten-khoa" => array("Tên khoa không hợp lệ", "Tên khoa không được rỗng và không quá 50 ký tự.", "error"),
            "invalid-ten-nganh-hoc" => array("Tên ngành học không hợp lệ", "Tên ngành học không được rỗng và không quá 50 ký tự.", "error"),
            "invalid-ten-lop-hoc" => array("Tên lớp học không hợp lệ", "Tên lớp học không được rỗng và không quá 50 ký tự.", "error"),
            "invalid-ten-khoahoc" => array("Tên khóa học không hợp lệ", "Tên khóa học không được rỗng và không quá 50 ký tự.", "error"),
            "invalid-ten-trinh-do" => array("Tên trình độ không hợp lệ", "Tên trình độ không được rỗng và không quá 50 ký tự.", "error"),
            "invalid-ghichu" => array("Ghi chú không hợp lệ", "Ghi chú không được vượt quá 2000 ký tự.", "error"),
            "invalid-ngay-bat-dau" => array("Ngày bắt đầu không hợp lệ", "Ngày bắt đầu phải là ngày hợp lệ theo định dạng DD/MM/YYYY.", "error"),
            "invalid-ngay-ket-thuc" => array("Ngày kết thúc không hợp lệ", "Ngày kết thúc phải là ngày hợp lệ theo định dạng DD/MM/YYYY.", "error"),
            "invalid-khoa" => array("Khoa không hợp lệ", "Khoa đã chọn không hợp lệ hoặc không tồn tại.", "error"),
            "invalid-khoa-hoc" => array("Khóa học không hợp lệ", "Khóa học đã chọn không hợp lệ hoặc không tồn tại.", "error"),
            "invalid-nganh-hoc" => array("Ngành học không hợp lệ", "Ngành học đã chọn không hợp lệ hoặc không tồn tại.", "error"),
            "invalid-nam-nhap-hoc" => array("Năm nhập học không hợp lệ", "Năm nhập học phải là số nguyên nằm trong khoảng từ 2006 đến 2099.", "error"),
            "invalid-he-dao-tao" => array("Hệ đào tạo không hợp lệ", "Hệ đào tạo (số năm học) phải là số từ 2 đến 8 năm.", "error"),
            "duplicate-khoa" => array("Dữ liệu bị trùng!", "Tên khoa đã tồn tại.", "error"),
            "invalid-dot-date" => array("Thời gian đợt chấm điểm không hợp lệ", "Thời gian đợt chấm điểm phải nằm trong khoảng Học kỳ đã chọn.", "error"),
            // Nhựt sửa lỗi: Nội dung thông báo phải đúng nghiệp vụ, chỉ khóa xóa khi đã phát sinh dữ liệu chấm, Minh chứng hoặc Kết quả xếp loại.
            "related-phieu" => array("Không thể xóa", "Đợt chấm điểm đã phát sinh dữ liệu chấm, Minh chứng hoặc Kết quả xếp loại. Không thể xóa để đảm bảo toàn vẹn dữ liệu.", "error"),
            // Nhựt sửa lỗi: Bổ sung thông báo khi Học kỳ không thuộc Năm học đã chọn.
            "invalid-semester" => array("Học kỳ không hợp lệ", "Học kỳ không thuộc Năm học đã chọn. Vui lòng kiểm tra lại.", "error"),
            // Nhựt sửa lỗi: Chặn mỗi Học kỳ chỉ có một Đợt chấm điểm.
            "duplicate-semester" => array("Không thể tạo Đợt chấm điểm", "Học kỳ này đã có Đợt chấm điểm. Mỗi Học kỳ chỉ được phép có một Đợt chấm điểm.", "error"),
            "overlap-xep-loai" => array("Khoảng điểm bị trùng!", "Khoảng điểm xếp loại đang chồng lên xếp loại khác.", "error"),
            "incomplete-xep-loai" => array("Chưa cấu hình đủ xếp loại!", "Hệ thống chưa cấu hình đầy đủ các khoảng điểm xếp loại từ 0 đến 100. Vui lòng kiểm tra lại.", "error"),
            // Nhựt sửa lỗi: Khóa thay đổi Xếp loại khi đang có Đợt chấm điểm diễn ra để giữ nguyên cấu hình đang áp dụng.
            "active-dot-xep-loai" => array("Không thể thay đổi Xếp loại!", "Đang có Đợt chấm điểm diễn ra nên không thể thay đổi cấu hình Xếp loại.", "error"),
            "related-xep-loai" => array("Không thể xóa!", "Xếp loại này đang được sử dụng trong kết quả xếp loại.", "error"),
            "locked-xep-loai" => array("Không thể cập nhật!", "Xếp loại này đã được sử dụng trong kết quả xếp loại nên không thể cập nhật.", "error"),
            "duplicate-name" => array("Không thể thực hiện!", "Điều này đã tồn tại nên không thể lưu.", "error"),
            // Nhựt sửa lỗi: Thông báo riêng khi đã tồn tại kết quả xếp loại cho cùng một đợt/lớp.
            "duplicate-ket-qua" => array("Không thể thực hiện!", "Kết quả xếp loại đã tồn tại cho đợt/lớp này.", "error"),
            "duplicate" => array("Dữ liệu bị trùng!", "Tên khoa đã tồn tại.", "error"),
            "duplicate-bithu" => array("Dữ liệu bị trùng!", "Tên hoặc Email bí thư đoàn khoa đã tồn tại.", "error"),
            "duplicate-giangvien" => array("Dữ liệu bị trùng!", "Mã số hoặc Email giảng viên đã tồn tại.", "error"),
            "duplicate-phancong" => array("Dữ liệu bị trùng!", "Giảng viên đã được phân công lớp này rồi.", "error"),
            "related" => array("Không thể xóa!", "Khoa này đang được sử dụng bởi ngành học hoặc bí thư đoàn khoa.", "error"),
            "duplicate-lop-hoc" => array("Dữ liệu bị trùng!", "Tên lớp học đã tồn tại trong khóa/ngành đã chọn.", "error"),
            "related-lop-hoc" => array("Không thể xóa!", "Lớp học này đang được sử dụng bởi sinh viên hoặc các dữ liệu liên quan.", "error"),
            // Nhựt sửa lỗi: Thông báo đúng nghiệp vụ khi Điều đang được dùng trong Mẫu phiếu hoặc Khoản.
            "related-dieu" => array("Không thể xóa!", "Điều này đang được sử dụng trong Mẫu phiếu nên không thể xóa.", "error"),
            "related-dieu-khoan" => array("Không thể xóa!", "Điều này đang có Khoản nên không thể xóa.", "error"),
            "duplicate-ma-sinh-vien" => array("Dữ liệu bị trùng!", "Mã sinh viên đã tồn tại trong hệ thống.", "error"),
            "duplicate-email-sinh-vien" => array("Dữ liệu bị trùng!", "Email đã tồn tại trong hệ thống.", "error"),
            "invalid-import-file" => array("Định dạng file không hợp lệ!", "Vui lòng tải lên file Excel đúng mẫu.", "error"),
            "csrf" => array("Phiên thao tác không hợp lệ!", "Vui lòng tải lại trang rồi thực hiện lại.", "error"),
            "system" => array("Lỗi hệ thống!", "Có lỗi phát sinh khi xử lý dữ liệu.", "error"),
            
            // Các trạng thái CRUD chi tiết
            "add-success" => array("Thêm mới thành công!", "Bản ghi đã được lưu vào hệ thống.", "success"),
            "add-failed" => array("Thêm mới thất bại", "Có lỗi xảy ra, vui lòng thử lại.", "error"),
            "update-success" => array("Cập nhật thành công!", "Dữ liệu đã được cập nhật.", "success"),
            "update-failed" => array("Cập nhật thất bại", "Có lỗi xảy ra, vui lòng thử lại.", "error"),
            "delete-success" => array("Xóa thành công!", "Bản ghi đã bị xóa khỏi hệ thống.", "success"),
            "delete-failed" => array("Xóa thất bại", "Không thể xóa bản ghi này.", "error"),
            "reset-success" => array("Khôi phục thành công", "Mật khẩu đã được reset về mặc định.", "success"),
            "reset-failed" => array("Khôi phục thất bại", "Có lỗi xảy ra khi khôi phục.", "error"),
            "active-success" => array("Đổi trạng thái thành công", "Trạng thái tài khoản đã được cập nhật.", "success"),
            "active-failed" => array("Đổi trạng thái thất bại", "Có lỗi xảy ra khi cập nhật trạng thái.", "error"),
            "import-success" => array("Nhập dữ liệu thành công!", "Dữ liệu từ Excel đã được lưu.", "success"),
            "import-failed" => array("Nhập dữ liệu thất bại", "Có lỗi xảy ra khi đọc file Excel.", "error"),
            "export-success" => array("Xuất dữ liệu thành công!", "File Excel đã được tải xuống.", "success"),
            "export-failed" => array("Xuất dữ liệu thất bại", "Có lỗi xảy ra khi tạo file Excel.", "error"),
            "copy-success" => array("Nhân bản thành công!", "Bản sao đã được tạo ra.", "success"),
            "copy-failed" => array("Nhân bản thất bại", "Có lỗi xảy ra khi nhân bản dữ liệu.", "error")
        );

        if ($_GET['status'] == "locked-dot") {
            // Nhựt sửa lỗi: Không cho cập nhật Đợt đã phát sinh dữ liệu chấm hoặc kết quả xếp loại.
            echo "<script>Toast.fire(" . json_encode("Không thể cập nhật") . ", " . json_encode("Đợt chấm điểm đã phát sinh dữ liệu chấm hoặc kết quả xếp loại nên không thể cập nhật.") . ", " . json_encode("error") . ")</script>";
        }
        if ($_GET['status'] == "unscored-phieu") {
            // Nhựt sửa lỗi: Không cho tổng kết khi còn Phiếu chưa có kết quả cố vấn.
            echo "<script>Toast.fire(" . json_encode("Chưa thể tổng kết") . ", " . json_encode("Vẫn còn Phiếu chấm điểm chưa có kết quả cố vấn. Vui lòng kiểm tra lại.") . ", " . json_encode("error") . ")</script>";
        }

        if (isset($_GET['page']) && $_GET['page'] == "quan-ly-dot-cham-diem" && $_GET['status'] == "not-found") {
            // Nhựt sửa lỗi: Thông báo riêng khi đợt chấm điểm không tồn tại hoặc request sai.
            echo "<script>Toast.fire(" . json_encode("Không tìm thấy dữ liệu") . ", " . json_encode("Đợt chấm điểm không tồn tại hoặc dữ liệu yêu cầu không hợp lệ.") . ", " . json_encode("error") . ")</script>";
        }
        
        $inline_error_pages = ['quan-ly-xep-loai', 'quan-ly-khoa', 'quan-ly-hoc-ky', 'quan-ly-nam-hoc', 'quan-ly-khoa-hoc', 'quan-ly-nganh-hoc', 'quan-ly-trinh-do', 'quan-ly-lop-hoc', 'quan-ly-sinh-vien', 'quan-ly-bi-thu-doan-khoa', 'quan-ly-giang-vien', 'quan-ly-phan-cong', 'quan-ly-tai-khoan', 'quan-ly-dieu', 'quan-ly-muc', 'quan-ly-khoan', 'quan-ly-mau-phieu', 'quan-ly-dot-cham-diem', 'quan-ly-phieu-cham-diem', 'quan-ly-ket-qua'];
        $inline_error_statuses = ['duplicate-name', 'overlap-xep-loai', 'duplicate', 'duplicate-nganh-hoc', 'invalid', 'invalid-ten-khoa', 'invalid-ghichu', 'invalid-ten-hoc-ky', 'invalid-ngay', 'invalid-ten-nam-hoc', 'invalid-ten-khoa-hoc', 'invalid-ten-nganh-hoc', 'invalid-ten-trinh-do', 'invalid-ten-lop-hoc', 'invalid-khoa', 'invalid-nganh-hoc', 'invalid-khoa-hoc', 'invalid-trinh-do', 'invalid-sdt', 'duplicate-bithu', 'duplicate-giangvien', 'duplicate-phancong', 'duplicate-ma-sinh-vien', 'duplicate-email-sinh-vien', 'incomplete-xep-loai', 'duplicate-ket-qua'];
        $is_inline_error = (isset($_GET['page']) && in_array($_GET['page'], $inline_error_pages) && (in_array($_GET['status'], $inline_error_statuses) || (strpos($_GET['status'], 'invalid-') === 0 && $_GET['status'] != 'invalid-import-file')));

        if (!$is_inline_error) {
            if (isset($_GET['page']) && $_GET['status'] == "invalid" && isset($invalid_alerts[$_GET['page']])) {
                $alert = $invalid_alerts[$_GET['page']];
                echo "<script>Toast.fire(" . json_encode($alert[0]) . ", " . json_encode($alert[1]) . ", " . json_encode($alert[2]) . ")</script>";
            } else if (isset($alerts[$_GET['status']])) {
                $alert = $alerts[$_GET['status']];
                echo "<script>Toast.fire(" . json_encode($alert[0]) . ", " . json_encode($alert[1]) . ", " . json_encode($alert[2]) . ")</script>";
            }

            // Nhựt sửa lỗi: bổ sung thông báo riêng cho quản lý ngành học để không dùng nhầm nội dung của khoa.
            if ($_GET['status'] == "duplicate-nganh-hoc") {
                echo "<script>Toast.fire(" . json_encode("Dữ liệu bị trùng!") . ", " . json_encode("Tên ngành học đã tồn tại trong khoa đã chọn.") . ", " . json_encode("error") . ")</script>";
            }
        }
        if ($_GET['status'] == "related-nganh-hoc") {
            echo "<script>Toast.fire(" . json_encode("Không thể xóa!") . ", " . json_encode("Ngành học này đang được sử dụng bởi lớp học.") . ", " . json_encode("error") . ")</script>";
        }

        $page_to_name = [
            'quan-ly-khoa' => 'Khoa',
            'quan-ly-hoc-ky' => 'Học kỳ',
            'quan-ly-nam-hoc' => 'Năm học',
            'quan-ly-khoa-hoc' => 'Khóa học',
            'quan-ly-nganh-hoc' => 'Ngành học',
            'quan-ly-trinh-do' => 'Trình độ',
            'quan-ly-lop-hoc' => 'Lớp học',
            'quan-ly-sinh-vien' => 'Sinh viên',
            'quan-ly-bi-thu-doan-khoa' => 'Bí thư đoàn khoa',
            'quan-ly-giang-vien' => 'Giảng viên',
            'quan-ly-phan-cong' => 'Phân công',
            'quan-ly-tai-khoan' => 'Tài khoản',
            'quan-ly-dot-cham-diem' => 'Đợt chấm điểm',
            'quan-ly-xep-loai' => 'Xếp loại',
            'quan-ly-dieu' => 'Điều',
            'quan-ly-khoan' => 'Khoản',
            'quan-ly-mau-phieu' => 'Mẫu phiếu',
            'quan-ly-phan-nhom' => 'Phân nhóm',
            'quan-ly-phan-quyen' => 'Phân quyền',
            'quan-ly-muc' => 'Mục',
            'quan-ly-thong-ke' => 'Thống kê',
            'quan-ly-ket-qua' => 'Kết quả',
            'quan-ly-phieu-cham-diem' => 'Phiếu chấm điểm',
            'thong-bao' => 'Thông báo',
            'import-from-excel' => 'Dữ liệu Excel'
        ];
        $entity_name = isset($_GET['page']) && isset($page_to_name[$_GET['page']]) ? $page_to_name[$_GET['page']] : 'Dữ liệu';

        if ($_GET['status'] == "success") {
            echo "<script>Toast.fire('Thành công!', 'Thao tác thành công!', 'success')</script>";
        }
        if ($_GET['status'] == "failed") {
            echo "<script>Toast.fire('Thất bại!', 'Thao tác không thành công!', 'error')</script>";
        }
        if ($_GET['status'] == "add-success") {
            echo "<script>Toast.fire('Thành công!', 'Thêm mới " . $entity_name . " thành công!', 'success')</script>";
        }
        if ($_GET['status'] == "add-failed") {
            echo "<script>Toast.fire('Thất bại!', 'Thêm mới " . $entity_name . " thất bại!', 'error')</script>";
        }
        if ($_GET['status'] == "update-success") {
            echo "<script>Toast.fire('Thành công!', 'Cập nhật " . $entity_name . " thành công!', 'success')</script>";
        }
        if ($_GET['status'] == "update-failed") {
            echo "<script>Toast.fire('Thất bại!', 'Cập nhật " . $entity_name . " thất bại!', 'error')</script>";
        }
        if ($_GET['status'] == "delete-success") {
            echo "<script>Toast.fire('Thành công!', 'Xóa " . $entity_name . " thành công!', 'success')</script>";
        }
        if ($_GET['status'] == "delete-failed") {
            echo "<script>Toast.fire('Thất bại!', 'Xóa " . $entity_name . " thất bại!', 'error')</script>";
        }
        if ($_GET['status'] == "export-success") {
            echo "<script>Toast.fire('Thành công!', 'Xuất dữ liệu " . $entity_name . " thành công!', 'success')</script>";
        }
        if ($_GET['status'] == "export-failed") {
            echo "<script>Toast.fire('Thất bại!', 'Xuất dữ liệu " . $entity_name . " thất bại!', 'error')</script>";
        }
        if ($_GET['status'] == "reset-success") {
            echo "<script>Toast.fire('Thành công!', 'Khôi phục mật khẩu " . $entity_name . " thành công!', 'success')</script>";
        }
        if ($_GET['status'] == "reset-failed") {
            echo "<script>Toast.fire('Thất bại!', 'Khôi phục mật khẩu " . $entity_name . " thất bại!', 'error')</script>";
        }
        if ($_GET['status'] == "active-success") {
            echo "<script>Toast.fire('Thành công!', 'Đổi trạng thái " . $entity_name . " thành công!', 'success')</script>";
        }
        if ($_GET['status'] == "active-failed") {
            echo "<script>Toast.fire('Thất bại!', 'Đổi trạng thái " . $entity_name . " thất bại!', 'error')</script>";
        }
        // Nhựt sửa lỗi: Sau khi hiển thị thông báo thì xóa riêng tham số status khỏi URL để refresh không hiện lại alert.
        echo "<script>
            const url = new URL(window.location.href);
            url.searchParams.delete('status');
            history.replaceState(null, '', url.pathname + url.search + url.hash);
        </script>";
    }
    ?>
    
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
        /* Nhựt sửa: Ẩn ô tìm kiếm mặc định của DataTable để sử dụng ô tìm kiếm chung trên Header */
        .dataTables_filter {
            display: none !important;
        }
        /* Nhựt sửa: Dịch chuyển nút xuất dữ liệu sát với viền bên phải của cột Thao tác */
        .dt-buttons {
            margin-right: 0px !important;
        }
        /* Nhựt sửa: Đồng thời căn lề lại mục chọn số dòng hiển thị bên trái */
        .dataTables_length {
            margin-left: 0px !important;
        }
    </style>
    <script>
        // Cấu hình ngôn ngữ mặc định cho DataTable (đặc biệt là thông báo Copy)
        $.extend(true, $.fn.dataTable.defaults, {
            language: {
                buttons: {
                    copyTitle: 'Sao chép thành công',
                    copySuccess: {
                        _: 'Đã sao chép %d dòng vào bộ nhớ tạm',
                        1: 'Đã sao chép 1 dòng vào bộ nhớ tạm'
                    }
                }
            }
        });

        // Nhựt sửa: Tự động kết nối ô tìm kiếm ở Header với DataTable hiện tại trên trang
        window.addEventListener('load', function() {
            var tableEl = $('#tablejs');
            var searchContainer = $('#header-search-container');
            var searchInput = $('#global-header-search');

            if (tableEl.length > 0) {
                searchContainer.show();
                searchInput.val('');
                
                searchInput.off('input keyup').on('input keyup', function() {
                    var dataTable = tableEl.DataTable();
                    dataTable.search(this.value).draw();
                });
            } else {
                searchContainer.hide();
            }
        });
    </script>
</body>

</html>


