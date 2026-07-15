    <?php 
        require '../../models/getModel.php';
        $id_xep_loai = $_POST['id_xep_loai'];
        $xeploai__Get_By_Id = $xeploai->xeploai__Get_By_Id($id_xep_loai);
    ?>

    <form class="row form" action="quan-ly-xep-loai/action.php?req=update" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_xep_loai" value="<?=$xeploai__Get_By_Id->id_xep_loai?>">
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
                        <input type="text" id="ten_xep_loai" name="ten_xep_loai" class="form-control" required
                            placeholder="Nhập tên điều" value="<?=$xeploai__Get_By_Id->ten_xep_loai?>">
                    </div>
                    <div class="form-group">
                        <label for="">Nội dung chi tiết</label>
                        <textarea id="ghi_chu" name="ghi_chu" class="form-control"
                            placeholder="Nhập nội dung chi tiết"><?=$xeploai__Get_By_Id->ghi_chu?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="">Điểm tối thiểu <span class="color-crimson">(*)</span></label>
                        <input type="number" id="can_duoi_up" name="can_duoi" pattern="[0-9]{1-2}"
                            value="<?=$xeploai__Get_By_Id->can_duoi?>" class=" form-control" required
                            title="Thấp nhất là 1, lớn nhất là 100" placeholder="Nhập điểm tối thiểu" minlength="0"
                            max="100">
                    </div>
                    <div class="form-group">
                        <label for="">Điểm tối đa <span class="color-crimson">(*)</span></label>
                        <input type="number" id="can_tren_up" name="can_tren" pattern="[0-9]{1-3}"
                            value="<?=$xeploai__Get_By_Id->can_tren?>" class=" form-control" required
                            title="Thấp nhất là 1, lớn nhất là 100" placeholder="Nhập điểm tối đa" minlength="0"
                            max="100">
                    </div>
                    <div class="form-group">
                        <label for="">Trừ trễ hạn <span class="color-crimson">(*)</span></label>
                        <input type="number" id="ha_bac" name="ha_bac" pattern="[0-9]{1-2}" class=" form-control"
                            required title="Thấp nhất là 10, lớn nhất là 15" placeholder="Nhập điểm trừ trễ hạn"
                            minlength="10" max="15" value="<?=$xeploai__Get_By_Id->ha_bac?>">
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
        "min": can_duoi.value
    });
});
    </script>