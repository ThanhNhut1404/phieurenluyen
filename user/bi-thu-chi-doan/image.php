<?php if(isset($_GET['id_minh_chung'])):?>
<?php 
    require "../../models/getModel.php";
    $id_minh_chung = $_GET['id_minh_chung'];
    $minhchung__Get_By_Id = $minhchung->minhchung__Get_By_Id($id_minh_chung);
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
    <?php $img_src = (strlen($minhchung__Get_By_Id->hinh_anh) < 100) ? "https://drive.google.com/uc?id=" . $minhchung__Get_By_Id->hinh_anh : $minhchung__Get_By_Id->hinh_anh; ?>
    <img style="display: block;
    -webkit-user-select: none;
    margin: auto;
    cursor: zoom-in;
    background-color: hsl(0, 0%, 90%);
    transition: background-color 300ms;" src="<?=$img_src?>" width="100%">
    <!-- <a target="_blank" href="<?=basename(__DIR__)?>/image.php?id_minh_chung=<?=$id_minh_chung?>&download=minh_chung"
        class="btn btn-secondary mt-2 text-center w-100"> <i class='bx bx-cloud-download'></i>Tải về</button>
    </a> -->
</body>


<?php endif; ?>