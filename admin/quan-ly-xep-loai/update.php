    <?php 
        require '../../models/getModel.php';
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            // Nhựt sửa lỗi: Tạo CSRF token cho form cập nhật Xếp loại.
            try {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            } catch (Throwable $e) {
                $_SESSION['csrf_token'] = hash('sha256', session_id() . uniqid('', true));
            }
        }
        $id_xep_loai = isset($_POST['id_xep_loai']) ? trim($_POST['id_xep_loai']) : "";
        // Nhựt sửa lỗi: Ajax update thiếu id_xep_loai hoặc id sai kiểu thì quay về danh sách.
        if (!preg_match('/^[1-9][0-9]*$/', $id_xep_loai)) {
            echo "<script>location.href = 'index.php?page=quan-ly-xep-loai&status=not-found'</script>";
            exit();
        }
        $xeploai__Get_By_Id = $xeploai->xeploai__Get_By_Id($id_xep_loai);
        // Nhựt sửa lỗi: id_xep_loai không tồn tại thì không render form update để tránh lỗi object rỗng.
        if (!$xeploai__Get_By_Id) {
            echo "<script>location.href = 'index.php?page=quan-ly-xep-loai&status=not-found'</script>";
            exit();
        }
    ?>

    <form class="row form" action="quan-ly-xep-loai/action.php?req=update" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_xep_loai" value="<?=$xeploai__Get_By_Id->id_xep_loai?>">
        <input type="hidden" name="csrf_token" value="<?=htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8')?>">
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
                    <div class="form-group">
                        <label for="">Tên xếp loại <span class="color-crimson">(*)</span></label>
                        <!-- Nhựt sửa lỗi: Đổi id form cập nhật để tránh trùng id HTML với form thêm. -->
                        <input type="text" id="ten_xep_loai_up" name="ten_xep_loai" class="form-control" required
                            placeholder="Nhập tên xếp loại" value="<?=htmlspecialchars($xeploai__Get_By_Id->ten_xep_loai ?? "", ENT_QUOTES, 'UTF-8')?>">
                    </div>
                    <div class="form-group">
                        <label for="">Ghi chú</label>
                        <!-- Nhựt sửa lỗi: Đổi id form cập nhật để tránh trùng id HTML với form thêm. -->
                        <textarea id="ghi_chu_up" name="ghi_chu" class="form-control"
                            placeholder="Nhập ghi chú"><?=htmlspecialchars($xeploai__Get_By_Id->ghi_chu ?? "", ENT_QUOTES, 'UTF-8')?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="">Điểm tối thiểu <span class="color-crimson">(*)</span></label>
                        <input type="number" id="can_duoi_up" name="can_duoi"
                            value="<?=$xeploai__Get_By_Id->can_duoi?>" class=" form-control" required
                            title="Thấp nhất là 0, lớn nhất là 100" placeholder="Nhập điểm tối thiểu" min="0" max="100"
                            step="1">
                    </div>
                    <div class="form-group">
                        <label for="">Điểm tối đa <span class="color-crimson">(*)</span></label>
                        <input type="number" id="can_tren_up" name="can_tren"
                            value="<?=$xeploai__Get_By_Id->can_tren?>" class=" form-control" required
                            title="Thấp nhất là 0, lớn nhất là 100" placeholder="Nhập điểm tối đa" min="0" max="100"
                            step="1">
                    </div>
                    <div class="form-group">
                        <label for="">Hạ bậc <span class="color-crimson">(*)</span></label>
                        <!-- Nhựt sửa lỗi: Đổi id form cập nhật để tránh trùng id HTML với form thêm. -->
                        <input type="number" id="ha_bac_up" name="ha_bac" min="10" max="15" step="1" class=" form-control"
                            required title="Thấp nhất là 10, lớn nhất là 15" placeholder="Nhập điểm hạ bậc"
                            value="<?=$xeploai__Get_By_Id->ha_bac?>">
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <input type="submit" value="Cập nhật" class="btn btn-danger float-right">
                </div>
            </div>
            <!-- /.card -->
        </div>
    </form>

    <script>
can_duoi_up = document.getElementById('can_duoi_up');
$("#can_duoi_up").change(function() {
    $('#can_tren_up').attr({
        "min": can_duoi_up.value
    });
});
    </script>
