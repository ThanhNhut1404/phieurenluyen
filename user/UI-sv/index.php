<?php 
    ob_start(); 
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chấm điểm rèn luyện</title>
    <link rel="icon" href="../../assets/img/favicon.ico" type="image/gif" sizes="16x16">
    <meta name="description" content="Trang chủ Sinh viên">
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../assets/css/source-sans-pro.css">
    <link rel="stylesheet" href="../../assets/theme/plugins/fontawesome-free/css/all.min.css">
    <!-- Remix Icon -->
    <link href="../../assets/remixicon/fonts/remixicon.css" rel="stylesheet">
    
    <!-- Bootstrap 4 for grid -->
    <link rel="stylesheet" href="../../assets/theme/dist/css/adminlte.min.css">
    
    <!-- Custom Dashboard CSS -->
    <link rel="stylesheet" href="../../assets/css/sv_style.css">
</head>
<body>
    <div class="wrapper">
        <!-- header -->
        <?php require 'header.php';?>

        <!-- content -->
        <?php 
            $page = isset($_GET['page']) ? $_GET['page'] : 'home';
            if ($page == 'home') {
                require "home.php";
            } else if ($page == 'ket-qua') {
                require "ketqua.php";
            } else {
                require "home.php";
            }
        ?>

        <!-- footer -->
        <?php require 'footer.php';?>
    </div>

    <!-- Js Files -->
    <script src="../../assets/vendor/jquery/jquery.min.js"></script>
    <script src="../../assets/theme/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/chart.min.js"></script>
    
    <script>
        // Init chart.js without dummy data
        const ctxRegistered = document.getElementById('chartRegistered');
        
        if(ctxRegistered) {
            new Chart(ctxRegistered, {
                type: 'bar',
                data: {
                    labels: [], // Dữ liệu trống
                    datasets: [{
                        label: 'Tham gia',
                        data: [], // Dữ liệu trống
                        backgroundColor: 'rgba(29, 78, 216, 0.5)'
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>
