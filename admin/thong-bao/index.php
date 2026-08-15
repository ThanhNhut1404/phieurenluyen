<?php
    $danh_sach_yeu_cau = $yeucaukichhoat->yeucaukichhoat__Get_All_Pending();
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <section class="content-header pb-0">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Thông báo kích hoạt tài khoản</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Thông báo</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="card card-primary mt-3">
            <div class="card-header">
                <h3 class="card-title">Danh sách yêu cầu kích hoạt</h3>
            </div>
            <div class="card-body">
                <?php if(count($danh_sach_yeu_cau) == 0): ?>
                    <p class="text-center text-muted my-4">Hiện tại chưa có yêu cầu kích hoạt nào.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table id="tablejs" class="table table-bordered table-striped display responsive" width="100%">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Họ và tên</th>
                                    <th>Email</th>
                                    <th>Phân nhóm</th>
                                    <th>Phân quyền</th>
                                    <th>Gửi Mail</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $num = 0; 
                                foreach($danh_sach_yeu_cau as $item): 
                                    $sv = $sinhvien->sinhvien__Get_By_Email($item->email);
                                    $ho_ten = $sv ? $sv->ten_sinh_vien : '<span class="text-secondary">N/A</span>';
                                ?>
                                <tr>
                                    <td><?= ++$num ?></td>
                                    <td><b><?= $ho_ten ?></b></td>
                                    <td><?= $item->email ?></td>
                                    <td>Sinh viên</td>
                                    <td>Sinh viên</td>
                                    <td>
                                        <a href="#" type="button" class="btn btn-success"
                                           onclick="return gui_mail_kich_hoat('<?= $item->id_yeu_cau ?>', '<?= $item->email ?>')">
                                            <i class="fas fa-paper-plane"></i> Gửi Mail
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<script>
window.addEventListener("load", function() {
    if ($("#tablejs").length) {
        $("#tablejs").DataTable({
            "responsive": true,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print"]
        }).buttons().container().appendTo('#tablejs_wrapper .col-md-6:eq(0)');
    }
});

function gui_mail_kich_hoat(id_yeu_cau, email) {
    Swal.fire({
        title: 'Đang xử lý...',
        html: 'Hệ thống đang tạo tài khoản và chuẩn bị gửi email',
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading()
        }
    });

    // Gọi API để tạo tài khoản
    $.post('thong-bao/action.php?req=tao_tai_khoan', {
        'id_yeu_cau': id_yeu_cau,
        'email': email
    }, function(data) {
        try {
            var res = JSON.parse(data);
            if(res.status == 'success') {
                // Sau khi tạo thành công, gọi send_mail để gửi mật khẩu
                $.post('quan-ly-tai-khoan/mail.php', {
                    'email': email,
                    'password': res.password 
                }, function(mail_data) {
                    Swal.fire(
                        'Thành công!',
                        'Đã tạo tài khoản và gửi email cho sinh viên.',
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                });
            } else {
                Swal.fire('Lỗi!', res.message, 'error');
            }
        } catch(e) {
            Swal.fire('Lỗi!', 'Đã xảy ra lỗi từ server.', 'error');
        }
    });
    return false;
}
</script>
