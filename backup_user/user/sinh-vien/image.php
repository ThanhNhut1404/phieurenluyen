<?php
// Nhựt sửa lỗi: Khởi động session và kiểm tra đăng nhập tránh bypass auth.
session_start();
if (!isset($_SESSION['admin']) && !isset($_SESSION['user'])) {
    header('location: ../../auth/');
    exit();
}

if(isset($_GET['id_minh_chung'])):
    require "../../models/getModel.php";
    $id_minh_chung = $_GET['id_minh_chung'];
    $minhchung__Get_By_Id = $minhchung->minhchung__Get_By_Id($id_minh_chung);
    if (!$minhchung__Get_By_Id) {
        echo "Minh chứng không tồn tại.";
        exit();
    }

    // Nhựt sửa lỗi: Phân quyền truy cập hình ảnh minh chứng (IDOR).
    // Kiểm tra quyền truy cập minh chứng
    $id_sinh_vien = 0;
    $id_lop_hoc = 0;

    $phieu = $phieuchamdiem->phieuchamdiem__Get_By_Id($minhchung__Get_By_Id->id_phieu);
    if ($phieu) {
        $id_sinh_vien = $phieu->id_sinh_vien;
        $sv = $sinhvien->sinhvien__Get_By_Id($id_sinh_vien);
        if ($sv) {
            $id_lop_hoc = $sv->id_lop_hoc;
        }
    }

    // 1. Sinh viên thường chỉ được xem minh chứng của chính mình
    if (isset($_SESSION['sv']) && (int)$_SESSION['sv']->id_nguoi_dung !== (int)$id_sinh_vien) {
        header('location: ../../auth/');
        exit();
    }

    // 2. Lớp trưởng / Bí thư chi đoàn chỉ được xem minh chứng thuộc lớp của mình
    if (isset($_SESSION['lt']) || isset($_SESSION['bt'])) {
        $session_user = isset($_SESSION['lt']) ? $_SESSION['lt'] : $_SESSION['bt'];
        $sinhvien_sender = $sinhvien->sinhvien__Get_By_Id($session_user->id_nguoi_dung);
        if ($sinhvien_sender && (int)$sinhvien_sender->id_lop_hoc !== (int)$id_lop_hoc) {
            header('location: ../../auth/');
            exit();
        }
    }

    // 3. Giảng viên chỉ được xem minh chứng của sinh viên lớp mình phụ trách cố vấn
    if (isset($_SESSION['gv'])) {
        $is_assigned = false;
        $gv_id = $_SESSION['gv']->id_nguoi_dung;
        $db = $phancong->connect;
        $stmt_pc = $db->prepare("SELECT COUNT(*) FROM phancong WHERE id_giang_vien = ? AND id_lop_hoc = ?");
        $stmt_pc->execute(array($gv_id, $id_lop_hoc));
        if ((int)$stmt_pc->fetchColumn() > 0) {
            $is_assigned = true;
        }
        if (!$is_assigned) {
            header('location: ../../auth/');
            exit();
        }
    }

    // 4. Bí thư đoàn khoa chỉ được xem minh chứng thuộc khoa của mình
    if (isset($_SESSION['btdk'])) {
        $is_same_khoa = false;
        $btdk_id = $_SESSION['btdk']->id_nguoi_dung;
        $btdk_info = $bithudoankhoa->bithudoankhoa__Get_By_Id($btdk_id);
        if ($btdk_info) {
            $lop_hoc = $lophoc->lophoc__Get_By_Id($id_lop_hoc);
            if ($lop_hoc && isset($lop_hoc->id_nganh_hoc)) {
                $nganh = $nganhhoc->nganhhoc__Get_By_Id($lop_hoc->id_nganh_hoc);
                if ($nganh && (int)$nganh->id_khoa === (int)$btdk_info->id_khoa) {
                    $is_same_khoa = true;
                }
            }
        }
        if (!$is_same_khoa) {
            header('location: ../../auth/');
            exit();
        }
    }
    function download($fileName) {
        $file = $fileName.".jpeg";
        $name = $fileName."_".date('d-m-Y').".jpeg";

        if (file_exists($file)) {

            header("Content-type: image/jpeg");  
            header("Cache-Control: no-store, no-cache");  
            header('Content-Disposition: attachment; filename='.basename($name));
            ob_clean();
            flush();
            readfile($file);
            unlink($file);
            exit;

        }
        echo "<script>window.close();</script>";

    }
    if(isset($_GET['download'])){

        $base64DataString = $minhchung__Get_By_Id->hinh_anh;
        $pattern = '/data:image\/(.+);base64,(.*)/';
        preg_match($pattern, $base64DataString, $matches);
        $imageExtension = $matches[1];
        $encodedImageData = $matches[2];
        $decodedImageData = base64_decode($encodedImageData);
        $fileName = $minhchung__Get_By_Id->id_minh_chung;
        file_put_contents("{$fileName}.{$imageExtension}", $decodedImageData);
        
        download($fileName);
       
    }
?>

<body style="margin: 0px; background: #0e0e0e; height: 100%">
    <img style="display: block;
    -webkit-user-select: none;
    margin: auto;
    cursor: zoom-in;
    background-color: hsl(0, 0%, 90%);
    transition: background-color 300ms;" src="<?=$minhchung__Get_By_Id->hinh_anh?>" width="100%">
    <!-- <a target="_blank" href="<?=basename(__DIR__)?>/image.php?id_minh_chung=<?=$id_minh_chung?>&download=minh_chung"
        class="btn btn-secondary mt-2 text-center w-100"> <i class='bx bx-cloud-download'></i>Tải về</button>
    </a> -->
</body>


<?php endif; ?>