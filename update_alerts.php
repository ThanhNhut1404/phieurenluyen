<?php
$file = 'user/bi-thu-doan-khoa/index.php';
$content = file_get_contents($file);

// Thay thế h3
$content = preg_replace(
    '/(<h3 class="card-title text-center )font-weight-bold( w-100 mt-3 mb-3">)/',
    '$1text-danger font-weight-bold$2',
    $content
);

// Thay thế alert
$content = preg_replace(
    '/<div class="alert alert-warning text-center">\s*<strong>(Sinh viên này chưa được Lớp trưởng\/Bí thư chấm điểm\.)<\/strong>\s*(Bạn không thể chấm điểm lúc này\.)\s*<\/div>/is',
    '<div class="alert alert-danger text-center font-weight-bold">' . "\n" . '        $1 $2' . "\n" . '    </div>',
    $content
);

file_put_contents($file, $content);
echo "Updated bi-thu-doan-khoa/index.php\n";

// Fix file user/co-van-hoc-tap/index.php in case it also has h3 not updated correctly
$file2 = 'user/co-van-hoc-tap/index.php';
$content2 = file_get_contents($file2);
$content2 = preg_replace(
    '/(<h3 class="card-title text-center )font-weight-bold( w-100 mt-3 mb-3">)/',
    '$1text-danger font-weight-bold$2',
    $content2
);
file_put_contents($file2, $content2);
echo "Updated co-van-hoc-tap/index.php\n";
?>
