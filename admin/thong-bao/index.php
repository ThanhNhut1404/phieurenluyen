<?php
    $danh_sach_yeu_cau = $yeucaukichhoat->yeucaukichhoat__Get_All_Pending();
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <section class="content-header pb-0">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Thông báo</h1>
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
                <h3 class="card-title">Danh sách Thông báo</h3>
            </div>
            <div class="card-body">
                <?php if(count($danh_sach_yeu_cau) == 0): ?>
                    <p class="text-center text-muted my-4">Hiện tại chưa có yêu cầu kích hoạt nào.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table id="tablejs" class="table table-bordered table-striped display responsive" width="100%">
                            <thead>
                                <tr>
                                    <th class="text-center">STT</th>
                                    <th>Họ và tên</th>
                                    <th class="text-center">Email</th>
                                    <th class="text-center">Phân nhóm</th>
                                    <th class="text-center">Phân quyền</th>
                                    <th class="text-center" style="width: 12%;">Kích hoạt</th>
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
                                    <td class="text-center"><?= ++$num ?></td>
                                    <td><b><?= $ho_ten ?></b></td>
                                    <td class="text-center"><?= $item->email ?></td>
                                    <td class="text-center">Sinh viên</td>
                                    <td class="text-center">Sinh viên</td>
                                    <td class="text-center">
                                        <a href="#" type="button" class="btn btn-success m-1" title="Gửi Mail"
                                           onclick="return gui_mail_kich_hoat('<?= $item->id_yeu_cau ?>', '<?= $item->email ?>')">
                                            <i class="fas fa-paper-plane"></i>
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
            "dom": "<'row'<'col-sm-6'l><'col-sm-6 d-flex justify-content-end align-items-center'Bf>>rtip",
            "pagingType": "full_numbers",
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
            "language": {
                "decimal": ",",
                "thousands": ".",
                "emptyTable": "Không có dữ liệu trong bảng",
                "info": "Hiển thị _START_ - _END_ của _TOTAL_ thông báo",
                "infoEmpty": "Hiển thị 0 - 0 của 0 thông báo",
                "infoFiltered": "(lọc từ _MAX_ thông báo)",
                "infoPostFix": "",
                "lengthMenu": "Hiển thị _MENU_ thông báo",
                "loadingRecords": "Đang tải...",
                "processing": "Đang xử lý...",
                "search": "Tìm kiếm:",
                "zeroRecords": "Không tìm thấy kết quả phù hợp",
                "paginate": {
                    "first": "&laquo;",
                    "last": "&raquo;",
                    "next": "&rsaquo;",
                    "previous": "&lsaquo;"
                },
                "aria": {
                    "sortAscending": ": kích hoạt để sắp xếp cột tăng dần",
                    "sortDescending": ": kích hoạt để sắp xếp cột giảm dần"
                }
            },
            "columnDefs": [{
                "targets": -1,
                "orderable": false,
                "searchable": false
            }],
            "buttons": [{
                "extend": "collection",
                "text": "<i class='fas fa-file-export'></i> Xuất dữ liệu",
                "className": "btn btn-sm btn-primary",
                "align": "button-right",
                "buttons": [
                    {
                        "extend": "copy",
                        "text": "<i class='far fa-copy'></i> Copy",
                        "exportOptions": {
                            "columns": ":visible:not(:last-child)"
                        }
                    },
                    {
                        "extend": "csv",
                        "text": "<i class='fas fa-file-csv'></i> CSV",
                        "bom": true,
                        "exportOptions": {
                            "columns": ":visible:not(:last-child)"
                        }
                    },
                    {
                        "extend": "excel",
                        "text": "<i class='far fa-file-excel'></i> Excel",
                        "exportOptions": {
                            "columns": ":visible:not(:last-child)"
                        }
                    },
                    {
                        "extend": "pdf",
                        "text": "<i class='far fa-file-pdf'></i> PDF",
                        "exportOptions": {
                            "columns": ":visible:not(:last-child)"
                        }
                    },
                    {
                        "extend": "print",
                        "text": "<i class='fas fa-print'></i> In",
                        "exportOptions": {
                            "columns": ":visible:not(:last-child)"
                        }
                    }
                ]
            }]
        });
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
