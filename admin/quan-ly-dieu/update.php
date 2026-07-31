    <?php 
        require '../../models/getModel.php';
        $id_dieu = isset($_POST['id_dieu']) ? trim($_POST['id_dieu']) : "";
        // Nhựt sửa lỗi: Ajax update thiếu id_dieu hoặc id_dieu sai kiểu thì quay về danh sách.
        if (!preg_match('/^[1-9][0-9]*$/', $id_dieu)) {
            echo "<script>location.href = 'index.php?page=quan-ly-dieu&status=not-found'</script>";
            exit();
        }
        $dieu__Get_By_Id = $dieu->dieu__Get_By_Id($id_dieu);
        // Nhựt sửa lỗi: id_dieu không tồn tại thì không render form update để tránh lỗi object rỗng.
        if (!$dieu__Get_By_Id) {
            echo "<script>location.href = 'index.php?page=quan-ly-dieu&status=not-found'</script>";
            exit();
        }
        // Nhựt sửa lỗi: Giới hạn thứ tự trên form cập nhật từ 1 đến max hiện tại để chặn nhập số âm, 0 hoặc vượt giới hạn ở client.
        $dieu__Max_Thu_Tu = $dieu->dieu__Get_Max_Thu_Tu();
    ?>

    <form class="row form" action="quan-ly-dieu/action.php?req=update" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_dieu" value="<?=$dieu__Get_By_Id->id_dieu?>">
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
                        <label for="">Tên điều <span class="color-crimson">(*)</span></label>
                        <input type="text" id="ten_dieu" name="ten_dieu" class="form-control" required
                            placeholder="Nhập tên điều" value="<?=$dieu__Get_By_Id->ten_dieu?>">
                    </div>
                    <div class="form-group">
                        <label for="">Nội dung chi tiết</label>
                        <textarea id="ghi_chu" name="ghi_chu" class="form-control" placeholder="Nhập nội dung chi tiết"
                            required><?=$dieu__Get_By_Id->ghi_chu?></textarea>
                    </div>
                    <div class="form-group">
                        <!-- quân sửa: Cập nhật lời nhắc Thứ tự tự động -->
                        <label for="">Thứ tự</label>
                        <input type="number" id="thu_tu" name="thu_tu" class="form-control" min="1"
                            max="<?=$dieu__Max_Thu_Tu?>" step="1"
                            placeholder="Nhập thứ tự (Có thể để trống)" value="<?=$dieu__Get_By_Id->thu_tu?>">
                        <small class="form-text text-muted">Mẹo: Để trống để giữ nguyên. Nếu nhập trùng, hệ thống sẽ tự động hoán đổi 2 Điều.</small>
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
