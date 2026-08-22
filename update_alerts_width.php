<?php
$files = [
    'user/lop-truong/index.php',
    'user/bi-thu-chi-doan/index.php',
    'user/bi-thu-doan-khoa/index.php',
    'user/co-van-hoc-tap/index.php'
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    
    $content = file_get_contents($file);
    
    $pattern = '/<div class="card overflow-auto mx-auto mt-3" style="max-width: fit-content;">/s';
    $replacement = '<div class="card overflow-auto w-100 mt-3">';
    
    $content = preg_replace($pattern, $replacement, $content);
    file_put_contents($file, $content);
}
echo "Updated\n";
?>
