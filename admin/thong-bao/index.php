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
                <h3 class="card-title">Danh sách yêu cầu Kích hoạt tài khoản</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?php if(count($danh_sach_yeu_cau) == 0): ?>
                    <p class="text-center text-muted my-4">Hiện tại chưa có yêu cầu kích hoạt nào.</p>
                <?php else: ?>

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
                                // Fetch all roles to map dynamically
                                $phanquyen_user = $phanquyen->phanquyen__Get_By_Cap_Bac(2);
                                $ten_pq_user = $phanquyen_user ? $phanquyen_user->ten_phan_quyen : 'Không xác định';

                                $arr_pn = [];
                                foreach ($phannhom->phannhom__Get_All() as $pn) {
                                    $arr_pn[$pn->id_phan_nhom] = $pn->ten_phan_nhom;
                                }
                                // Find phan nhom for Sinh vien (cap_bac = 2)
                                $id_pn_sv = 3;
                                $id_pn_bt = 4;
                                $id_pn_cv = 5;
                                foreach ($phannhom->phannhom__Get_All() as $pn) {
                                    if ($pn->cap_bac == 2) { $id_pn_sv = $pn->id_phan_nhom; }
                                    else if ($pn->cap_bac == 3) { $id_pn_bt = $pn->id_phan_nhom; }
                                    else if ($pn->cap_bac == 4) { $id_pn_cv = $pn->id_phan_nhom; }
                                }

                                $num = 0; 
                                foreach($danh_sach_yeu_cau as $item): 
                                    $sv = $sinhvien->sinhvien__Get_By_Email($item->email);
                                    $gv = $giangvien->giangvien__Get_By_Email($item->email);
                                    $bt = $bithudoankhoa->bithudoankhoa__Get_By_Email($item->email);
                                    
                                    $ho_ten = '<span class="text-secondary">N/A</span>';
                                    $phan_nhom = 'Không xác định';
                                    $phan_quyen = $ten_pq_user; // Luôn là quyền User

                                    if ($sv) {
                                        $ho_ten = $sv->ten_sinh_vien;
                                        $phan_nhom = isset($arr_pn[$id_pn_sv]) ? $arr_pn[$id_pn_sv] : 'Sinh viên';
                                    } else if ($gv) {
                                        $ho_ten = $gv->ten_giang_vien;
                                        $phan_nhom = isset($arr_pn[$id_pn_cv]) ? $arr_pn[$id_pn_cv] : 'Cố vấn học tập';
                                    } else if ($bt) {
                                        $ho_ten = $bt->ten_bi_thu;
                                        $phan_nhom = isset($arr_pn[$id_pn_bt]) ? $arr_pn[$id_pn_bt] : 'Bí thư đoàn khoa';
                                    }
                                ?>
                                <tr>
                                    <td class="text-center"><?= ++$num ?></td>
                                    <td><b><?= $ho_ten ?></b></td>
                                    <td class="text-center"><?= $item->email ?></td>
                                    <td class="text-center"><?= $phan_nhom ?></td>
                                    <td class="text-center"><?= $phan_quyen ?></td>
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
            "dom": "<'row'<'col-sm-6'l><'col-sm-6 d-flex justify-content-end align-items-center'B>>rt<'row mt-3 mb-n2'<'col-sm-6'i><'col-sm-6 d-flex justify-content-end'p>>",
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
            "buttons": [
                {
                    "text": "<i class='fas fa-paper-plane'></i> Kích hoạt hàng loạt",
                    "className": "btn btn-custom-kich-hoat mr-2",
                    "action": function ( e, dt, node, config ) {
                        kich_hoat_hang_loat();
                    }
                },
                {
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
                },
                {
                    "text": "<i class='fas fa-filter'></i>",
                    "titleAttr": "Bộ lọc",
                    "className": "btn btn-sm btn-custom-filter ml-1",
                    "attr": {
                        "id": "btn-filter-dropdown"
                    }
                }
            ],
            "initComplete": function() {
                var filterHtml = `
                <style>
                .dataTables_wrapper .dt-buttons {
                    display: flex !important;
                    flex-wrap: nowrap !important;
                    align-items: center !important;
                }
                .dataTables_wrapper .dt-buttons .btn-custom-filter {
                    background-color: #0f2a5a !important;
                    border: 1px solid #0f2a5a !important;
                    color: #fff !important;
                    border-radius: 4px !important;
                    padding: 6px 12px !important;
                    font-size: 14px !important;
                    font-weight: 500 !important;
                    box-shadow: none !important;
                    transition: all 0.15s ease-in-out !important;
                    display: inline-flex !important;
                    align-items: center !important;
                    white-space: nowrap !important;
                }
                .dataTables_wrapper .dt-buttons .btn-custom-filter:hover {
                    background-color: transparent !important;
                    border-color: #0f2a5a !important;
                    color: #0f2a5a !important;
                }
                #custom-filter-menu {
                    display: none;
                    position: absolute;
                    right: 0;
                    top: 100%;
                    margin-top: 5px;
                    width: 300px;
                    background: #fff;
                    border: 1px solid rgba(0,0,0,.15);
                    border-radius: .25rem;
                    box-shadow: 0 .5rem 1rem rgba(0,0,0,.175);
                    z-index: 1050;
                }
                </style>
                <div id="custom-filter-menu" class="p-3">
                    <div class="form-group mb-2 text-left">
                        <label class="label-sidebar">Phân nhóm:</label>
                        <select id="filter_phan_nhom" class="form-control form-control-sm">
                            <option value="">-- Tất cả phân nhóm --</option>
                        </select>
                    </div>
                    <div class="form-group mb-2 text-left">
                        <label class="label-sidebar">Phân quyền:</label>
                        <select id="filter_phan_quyen" class="form-control form-control-sm">
                            <option value="">-- Tất cả phân quyền --</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn btn-cancel-custom mr-2 font-weight-bold" id="btn-cancel-filter">Hủy</button>
                        <button type="button" class="btn btn-success font-weight-bold" id="btn-apply-filter">Áp dụng</button>
                    </div>
                </div>`;
                
                var $btn = $('#btn-filter-dropdown');
                $btn.wrap('<div style="position: relative; display: inline-block;"></div>');
                $btn.parent().append(filterHtml);

                var table = $('#tablejs').DataTable();

                function updateCascadeDropdowns() {
                    var selectedNhom = $('#filter_phan_nhom').val() || "";
                    var selectedQuyen = $('#filter_phan_quyen').val() || "";
                    
                    var nhomOptions = [];
                    var quyenOptions = [];
                    
                    table.rows().every(function() {
                        var data = this.data();
                        var nhom = $('<div>').html(data[3]).text().trim();
                        var quyen = $('<div>').html(data[4]).text().trim();
                        
                        if (nhom !== '' && nhom !== 'Chưa xác định' && nhomOptions.indexOf(nhom) === -1) nhomOptions.push(nhom);
                        
                        if (selectedNhom === "" || nhom === selectedNhom) {
                            if (quyen !== '' && quyen !== 'Chưa xác định' && quyenOptions.indexOf(quyen) === -1) quyenOptions.push(quyen);
                        }
                    });
                    
                    var selectNhom = $('#filter_phan_nhom');
                    selectNhom.empty().append('<option value="">-- Tất cả phân nhóm --</option>');
                    nhomOptions.sort().forEach(function(opt) {
                        selectNhom.append('<option value="'+opt+'" '+(opt === selectedNhom ? 'selected' : '')+'>'+opt+'</option>');
                    });
                    
                    var selectQuyen = $('#filter_phan_quyen');
                    selectQuyen.empty().append('<option value="">-- Tất cả phân quyền --</option>');
                    quyenOptions.sort().forEach(function(opt) {
                        selectQuyen.append('<option value="'+opt+'" '+(opt === selectedQuyen ? 'selected' : '')+'>'+opt+'</option>');
                    });
                }

                $('#filter_phan_nhom').on('change', function() {
                    $('#filter_phan_quyen').val('');
                    updateCascadeDropdowns();
                });

                updateCascadeDropdowns();

                $btn.on('click', function(e) {
                    e.stopPropagation();
                    $('#custom-filter-menu').fadeToggle(200);
                });

                $('#custom-filter-menu').on('click', function(e) {
                    e.stopPropagation();
                });

                $(document).on('click', function() {
                    $('#custom-filter-menu').fadeOut(200);
                });

                $('#btn-apply-filter').on('click', function() {
                    var nhomVal = $('#filter_phan_nhom').val() || "";
                    var quyenVal = $('#filter_phan_quyen').val() || "";
                    
                    table.column(3).search(nhomVal ? '^' + $.fn.dataTable.util.escapeRegex(nhomVal) + '$' : '', true, false)
                         .column(4).search(quyenVal ? '^' + $.fn.dataTable.util.escapeRegex(quyenVal) + '$' : '', true, false)
                         .draw();
                    $('#custom-filter-menu').fadeOut(200);
                });

                $('#btn-cancel-filter').on('click', function() {
                    $('#filter_phan_nhom').val('');
                    $('#filter_phan_quyen').val('');
                    updateCascadeDropdowns();
                    
                    table.column(3).search('')
                         .column(4).search('')
                         .draw();
                         
                    $('#custom-filter-menu').fadeOut(200);
                });
            }
        });
    }
});

function gui_mail_kich_hoat(id_yeu_cau, email) {
    Swal.fire({
        title: 'Đang xử lý...',
        html: 'Hệ thống đang tạo tài khoản và chuẩn bị gửi email',
        timerProgressBar: true,
        allowOutsideClick: false,
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
                    if (mail_data.includes('Message has been sent')) {
                        Toast.fire({
                            icon: 'success',
                            title: 'Thành công!',
                            text: 'Đã tạo tài khoản và gửi email cho sinh viên.'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: 'Tạo tài khoản thành công nhưng gửi email thất bại: ' + mail_data
                        });
                    }
                }).fail(function() {
                    Toast.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: 'Lỗi kết nối khi gửi email.'
                    });
                });
            } else {
                Toast.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: res.message
                });
            }
        } catch(e) {
            Toast.fire({
                icon: 'error',
                title: 'Lỗi!',
                text: 'Đã xảy ra lỗi từ server.'
            });
        }
    });
    return false;
}

var danh_sach_cho = [
    <?php foreach($danh_sach_yeu_cau as $item): ?>
    { id: '<?= $item->id_yeu_cau ?>', email: '<?= $item->email ?>' },
    <?php endforeach; ?>
];

function kich_hoat_hang_loat() {
    if (danh_sach_cho.length === 0) {
        Swal.fire('Thông báo', 'Không có yêu cầu nào cần kích hoạt', 'info');
        return;
    }
    
    Swal.fire({
        title: 'Xác nhận kích hoạt?',
        html: "Bạn có chắc muốn kích hoạt hàng loạt <b>" + danh_sach_cho.length + "</b> tài khoản này?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Đồng ý',
        cancelButtonText: 'Hủy',
        customClass: {
            confirmButton: 'btn btn-success font-weight-bold mx-2 px-4 py-2',
            cancelButton: 'btn btn-cancel-custom font-weight-bold mx-2 px-4 py-2'
        },
        buttonsStyling: false,
        showClass: {
            popup: 'animate__animated animate__fadeInDown animate__faster'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp animate__faster'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            tien_hanh_kich_hoat(0, 0, 0);
        }
    });
}

function tien_hanh_kich_hoat(index, success_count, fail_count) {
    if (index >= danh_sach_cho.length) {
        Toast.fire('Hoàn tất!', 'Kích hoạt thành công: ' + success_count + '<br>Thất bại: ' + fail_count, 'success').then(() => {
            location.reload();
        });
        return;
    }
    
    var current = danh_sach_cho[index];
    
    Swal.fire({
        title: 'Đang xử lý...',
        html: 'Đang kích hoạt tài khoản ' + (index + 1) + '/' + danh_sach_cho.length + '<br><small class="text-primary">' + current.email + '</small>',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        }
    });
    
    $.post('thong-bao/action.php?req=tao_tai_khoan', {
        'id_yeu_cau': current.id,
        'email': current.email
    }, function(data) {
        try {
            var res = JSON.parse(data);
            if(res.status == 'success') {
                $.post('quan-ly-tai-khoan/mail.php', {
                    'email': current.email,
                    'password': res.password 
                }, function(mail_data) {
                    if (mail_data.includes('Message has been sent')) {
                        tien_hanh_kich_hoat(index + 1, success_count + 1, fail_count);
                    } else {
                        tien_hanh_kich_hoat(index + 1, success_count, fail_count + 1);
                    }
                }).fail(function() {
                    tien_hanh_kich_hoat(index + 1, success_count, fail_count + 1);
                });
            } else {
                tien_hanh_kich_hoat(index + 1, success_count, fail_count + 1);
            }
        } catch(e) {
            tien_hanh_kich_hoat(index + 1, success_count, fail_count + 1);
        }
    }).fail(function() {
        tien_hanh_kich_hoat(index + 1, success_count, fail_count + 1);
    });
}
</script>



