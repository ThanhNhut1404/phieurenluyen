<?php 
    if(isset($_GET["page"])){
        switch($_GET["page"]){

            case "thong-ke":
                require "quan-ly-thong-ke/thong-ke.php";
                break;

            case "import-from-excel":
                require "import-from-excel/index.php";
                break;

            case "quan-ly-ket-qua":
                require "quan-ly-ket-qua/index.php";
                break; 
            case "quan-ly-xep-loai":
                require "quan-ly-xep-loai/index.php";
                break; 
       
            case "quan-ly-phieu":
                require "quan-ly-phieu/index.php";
                break; 
            case "quan-ly-dot-cham-diem":
                require "quan-ly-dot-cham-diem/index.php";
                break; 
            case "quan-ly-phieu-cham-diem":
                require "quan-ly-phieu-cham-diem/index.php";
                break; 
            
            case "quan-ly-mau-phieu":
                require "quan-ly-mau-phieu/index.php";
                break; 

            case "quan-ly-dieu":
                require "quan-ly-dieu/index.php";
                break; 
            case "quan-ly-khoan":
                require "quan-ly-khoan/index.php";
                break; 
            case "quan-ly-muc":
                require "quan-ly-muc/index.php";
                break; 
                
            case "quan-ly-khoa":
                require "quan-ly-khoa/index.php";
                break; 
            case "quan-ly-nganh-hoc":
                require "quan-ly-nganh-hoc/index.php";
                break; 
            case "quan-ly-lop-hoc":
                require "quan-ly-lop-hoc/index.php";
                break; 
            case "quan-ly-khoa-hoc":
                require "quan-ly-khoa-hoc/index.php";
                break; 
            case "quan-ly-trinh-do":
                require "quan-ly-trinh-do/index.php";
                break; 
            case "quan-ly-nam-hoc":
                require "quan-ly-nam-hoc/index.php";
                break; 
            case "quan-ly-hoc-ky":
                require "quan-ly-hoc-ky/index.php";
                break; 


            case "quan-ly-sinh-vien":
                require "quan-ly-sinh-vien/index.php";
                break; 
            case "quan-ly-bi-thu-doan-khoa":
                require "quan-ly-bi-thu-doan-khoa/index.php";
                break; 
            case "quan-ly-giang-vien":
                require "quan-ly-giang-vien/index.php";
                break; 
            case "quan-ly-phan-cong":
                require "quan-ly-phan-cong/index.php";
                break; 
            case "quan-ly-phan-nhom":
                require "quan-ly-phan-nhom/index.php";
                break; 
            case "quan-ly-phan-quyen":
                require "quan-ly-phan-quyen/index.php";
                break; 
            case "quan-ly-tai-khoan":
                require "quan-ly-tai-khoan/index.php";
                break; 

            case "error":
                require "layouts/error.php";
                break;
            
            default:
                echo "<script>location.href = 'index.php?page=error'</script>";
                break;
        }
    }else{
            echo "<script>location.href = 'index.php?page=dashboard'</script>";
        }

?>