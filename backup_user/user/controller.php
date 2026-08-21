<?php 
    if(isset($_GET["page"])){
        switch($_GET["page"]){

            case "thong-ke":
                require "trang-chu/thong-ke.php";
                break;
            case "sinh-vien":
                require "sinh-vien/index.php";
                break;
            case "lop-truong":
                require "lop-truong/index.php";
                break;
            case "lop-truong-tu-cham":
                require "lop-truong/add.php";
                break;
            case "bi-thu-chi-doan":
                require "bi-thu-chi-doan/index.php";
                break;
            case "bi-thu-chi-doan-tu-cham":
                require "bi-thu-chi-doan/add.php";
                break;
            case "bi-thu-doan-khoa":
                require "bi-thu-doan-khoa/index.php";
                break;
            case "co-van-hoc-tap":
                require "co-van-hoc-tap/index.php";
                break;
            case "quan-ly-tai-khoan":
                require "quan-ly-tai-khoan/index.php";
                break;
            case "error":
                require "layouts/error.php";
                break;
            case "chi-tiet":
                require "sinh-vien/chi-tiet.php";
                break; 
            default:
                echo "<script>location.href = 'index.php?page=error'</script>";
                break;
        }
    }else{
            echo "<script>location.href = 'index.php?page=thong-ke'</script>";
        }

?>