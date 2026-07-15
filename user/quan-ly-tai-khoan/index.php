<!-- Content Wrapper. Contains page content -->
<div class="ml-0 mr-3 content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content">
        <form class="row form" action="../auth/action.php?req=doi-mat-khau" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id_tai_khoan" value="<?=$_SESSION['user']->id_tai_khoan?>">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-lock"></i> Đổi mật khẩu
                        </h3>
                        <div class="card-tools">

                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="">Email</label>
                            <input type="text" readonly class="form-control" id="staticEmail2"
                                value="<?=$_SESSION['user']->email?>">
                        </div>
                        <div class="form-group">
                            <label for="">Mật khẩu</label>
                            <input type="password" class="form-control" id="inputPassword2" placeholder="Mật khẩu mới"
                                name="mat_khau" required>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <input type="submit" value="Cập nhật" class="btn btn-secondary float-right">
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </form>
    </section>
</div>