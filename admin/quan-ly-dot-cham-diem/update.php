    <?php 
        session_start();
        require '../../models/getModel.php';

        // Nhựt sửa lỗi: Tạo CSRF token cho form cập nhật đợt chấm điểm.
        if (empty($_SESSION['csrf_token'])) {
            try {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            } catch (Throwable $e) {
                $_SESSION['csrf_token'] = hash('sha256', session_id() . uniqid('', true));
            }
        }
        function dotchamdiem_update_escape($value) {
            // Nhựt sửa lỗi: Escape dữ liệu hiển thị trong form cập nhật để tránh XSS.
            return htmlspecialchars($value ?? "", ENT_QUOTES, 'UTF-8');
        }
        function dotchamdiem_update_format_range_label($title, $start, $end) {
            // Nhựt sửa lỗi: Combobox chỉ hiển thị tên theo yêu cầu mới.
            return dotchamdiem_update_escape($title);
        }

        $id_dot = isset($_POST['id_dot']) ? trim($_POST['id_dot']) : "";
        // Nhựt sửa lỗi: Ajax update thiếu id_dot hoặc id sai kiểu thì quay về danh sách.
        if (!preg_match('/^[1-9][0-9]*$/', $id_dot)) {
            echo "<script>location.href = 'index.php?page=quan-ly-dot-cham-diem&status=not-found'</script>";
            exit();
        }
        $dotchamdiem__Get_By_Id = $dotchamdiem->dotchamdiem__Get_By_Id($id_dot);
        // Nhựt sửa lỗi: id_dot không tồn tại thì không render form update.
        if (!$dotchamdiem__Get_By_Id) {
            echo "<script>location.href = 'index.php?page=quan-ly-dot-cham-diem&status=not-found'</script>";
            exit();
        }
        $namhoc__Get_All = $namhoc->namhoc__Get_All();
        $mauphieu__Get_All = $mauphieu->mauphieu__Get_All();
        $lophoc__Get_All = $lophoc->lophoc__Get_All();
        
        $lopapdung__Get_By_Id_Dot = $lopapdung->lopapdung__Get_By_Id_Dot($id_dot);
        $id_mau_phieu_hien_tai = count($lopapdung__Get_By_Id_Dot) > 0 ? $lopapdung__Get_By_Id_Dot[0]->id_mau_phieu : "";
        $arr_id_lop_hoc_hien_tai = [];
        foreach ($lopapdung__Get_By_Id_Dot as $item) {
            $arr_id_lop_hoc_hien_tai[] = $item->id_lop_hoc;
        }
        
        $has_data = $phieuchamdiem->phieuchamdiem__Has_Scored_Data_By_Id_Dot($id_dot) || $minhchung->minhchung__Has_By_Id_Dot($id_dot) || $ketquaxeploai->ketquaxeploai__Has_By_Id_Dot($id_dot);
        $id_nam_hoc_hien_tai = $dotchamdiem__Get_By_Id->id_nam_hoc;
    ?>

    <form class="row form" action="quan-ly-dot-cham-diem/action.php?req=update" method="post"
        enctype="multipart/form-data">
        <input type="hidden" name="id_dot" value="<?=(int)$dotchamdiem__Get_By_Id->id_dot?>">
        <?php // Nhựt sửa lỗi: Thêm CSRF token cho form cập nhật đợt chấm điểm. ?>
        <input type="hidden" name="csrf_token" value="<?=dotchamdiem_update_escape($_SESSION['csrf_token'])?>">
        <div class="col-12">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Cập nhật</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                    <div class="form-group">
                        <label for="">Tên đợt <span class="color-crimson">(*)</span></label>
                        <?php // Nhựt sửa lỗi: Đổi id form cập nhật sang hậu tố _up và sửa placeholder sai "tên điều". ?>
                        <input type="text" id="ten_dot_up" name="ten_dot" class="form-control" required
                            placeholder="Nhập tên đợt chấm điểm" value="<?=dotchamdiem_update_escape($dotchamdiem__Get_By_Id->ten_dot)?>">
                    </div>
                    <div class="form-group">
                        <label for="">Thời gian bắt đầu<span class="color-crimson">(*)</span></label>
                        <input type="date" id="thoi_gian_bat_dau_up" name="thoi_gian_bat_dau" class="form-control"
                            required placeholder="Nhập thời gian bắt đầu"
                            value="<?=dotchamdiem_update_escape($dotchamdiem__Get_By_Id->thoi_gian_bat_dau)?>">
                    </div>
                    <div class="form-group">
                        <label for="">Thời gian kết thúc<span class="color-crimson">(*)</span></label>
                        <input type="date" id="thoi_gian_ket_thuc_up" name="thoi_gian_ket_thuc" class="form-control"
                            required placeholder="Nhập thời gian kết thúc"
                            value="<?=dotchamdiem_update_escape($dotchamdiem__Get_By_Id->thoi_gian_ket_thuc)?>">
                    </div>
                    <div class="form-group">
                        <label for="">Năm học <span class="color-crimson">(*)</span></label>
                        <?php // Nhựt sửa lỗi: Thêm Năm học trong form cập nhật để chọn Học kỳ theo đúng Năm học. ?>
                        <select class="form-control" id="id_nam_hoc_up" name="id_nam_hoc" required>
                            <option value="">Chọn năm học</option>
                            <?php foreach ($namhoc__Get_All as $item):?>
                            <option value="<?=$item->id_nam_hoc?>" <?=$item->id_nam_hoc == $id_nam_hoc_hien_tai ? "selected" : ""?>><?=dotchamdiem_update_format_range_label($item->ten_nam_hoc, $item->ngay_bat_dau, $item->ngay_ket_thuc)?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Học kỳ <span class="color-crimson">(*)</span></label>
                        <?php // Nhựt sửa lỗi: Học kỳ form cập nhật chỉ hiển thị theo Năm học đang chọn. ?>
                        <select class="form-control" id="id_hoc_ky_up" name="id_hoc_ky" required disabled>
                            <option value="">--- Chọn học kỳ ---</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Ghi chú</label>
                        <?php // Nhựt sửa lỗi: Đổi id ghi chú form cập nhật sang hậu tố _up để tránh trùng DOM. ?>
                        <textarea id="ghi_chu_up" name="ghi_chu" class="form-control"
                            placeholder="Nhập Ghi chú"><?=dotchamdiem_update_escape($dotchamdiem__Get_By_Id->ghi_chu)?></textarea>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="">Mẫu phiếu <span class="color-crimson">(*)</span></label>
                        <?php if ($has_data): ?>
                            <input type="hidden" name="id_mau_phieu" value="<?=$id_mau_phieu_hien_tai?>">
                        <?php endif; ?>
                        <select class="form-control" name="id_mau_phieu" required <?= $has_data ? 'disabled' : '' ?>>
                            <option value="">Chọn Mẫu phiếu</option>
                            <?php foreach ($mauphieu__Get_All as $item):?>
                            <option value="<?=$item->id_mau_phieu?>" <?= $item->id_mau_phieu == $id_mau_phieu_hien_tai ? 'selected' : '' ?>><?=$item->ten_mau_phieu?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($has_data): ?>
                            <small class="text-danger">Đã phát sinh dữ liệu, không thể thay đổi mẫu phiếu.</small>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="">Lớp áp dụng <span class="color-crimson">(*)</span></label>
                        <select class="duallistbox" multiple="multiple" name="id_lop_hoc[]" required>
                            <?php foreach ($lophoc__Get_All as $item):?>
                            <option value="<?=$item->id_lop_hoc?>" <?= in_array($item->id_lop_hoc, $arr_id_lop_hoc_hien_tai) ? 'selected' : '' ?>><?=$item->ten_lop_hoc?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($has_data): ?>
                            <small class="text-danger">Lớp đã phát sinh dữ liệu sẽ được giữ lại khi cập nhật.</small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
                    <!-- /.card-body -->
                     <div class="card-footer">
                         <input type="submit" value="Cập nhật" class="btn btn-danger float-right">
                         <button type="button" class="btn btn-default float-right mr-2" onclick="cancel_update()">Hủy</button>
                     </div>
                </div>
                <!-- /.card -->
            </div>
    </form>

    <script>
thoi_gian_bat_dau_up = document.getElementById('thoi_gian_bat_dau_up');
// Nhựt sửa lỗi: Khi form cập nhật vừa load đã có ngày bắt đầu thì cập nhật min cho ngày kết thúc ngay.
$('#thoi_gian_ket_thuc_up').attr({
    "min": thoi_gian_bat_dau_up.value
});
$("#thoi_gian_bat_dau_up").change(function() {
    $('#thoi_gian_ket_thuc_up').attr({
        "min": thoi_gian_bat_dau_up.value
    });
});

// Nhựt sửa lỗi: Khi mở form cập nhật thì load Học kỳ theo Năm học hiện tại và chọn đúng Học kỳ của đợt.
load_hoc_ky_by_nam_hoc(<?=json_encode($id_nam_hoc_hien_tai)?>, '#id_hoc_ky_up', <?=json_encode($dotchamdiem__Get_By_Id->id_hoc_ky)?>);

// Nhựt sửa lỗi: Đổi Năm học trong form cập nhật thì tải lại danh sách Học kỳ tương ứng và bỏ chọn Học kỳ cũ.
$("#id_nam_hoc_up").change(function() {
    load_hoc_ky_by_nam_hoc($(this).val(), '#id_hoc_ky_up', '');
});

// Init duallistbox for update form
$('.duallistbox').bootstrapDualListbox();
    </script>
