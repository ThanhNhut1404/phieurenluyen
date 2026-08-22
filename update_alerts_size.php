<?php
$files = [
    'user/lop-truong/index.php',
    'user/bi-thu-chi-doan/index.php',
    'user/bi-thu-doan-khoa/index.php'
];

foreach ($files as $file) {
    $content = file_get_contents($file);
    
    $pattern = '/<div class="card overflow-auto w-100">\s*<div class="card-header">\s*<div class="row">\s*<div class="col">\s*<h3 class="card-title text-center text-danger font-weight-bold w-100 mt-3 mb-3">\s*(.*?)\s*<\/h3>\s*<\/div>\s*<\/div>\s*<\/div>\s*<\/div>/s';
    
    $replacement = '<div class="card overflow-auto mx-auto mt-3" style="max-width: fit-content;">
            <div class="card-header p-3">
                <h3 class="card-title text-center text-danger font-weight-bold m-0" style="float: none;">
                    $1
                </h3>
            </div>
        </div>';
        
    $content = preg_replace($pattern, $replacement, $content);
    file_put_contents($file, $content);
}

// And for the alert in bi-thu-doan-khoa and co-van-hoc-tap that were changed to h3
$files2 = [
    'user/bi-thu-doan-khoa/index.php',
    'user/co-van-hoc-tap/index.php'
];

foreach ($files2 as $file) {
    $content = file_get_contents($file);
    
    $pattern = '/<h3 class="card-title text-center text-danger font-weight-bold w-100 mt-3 mb-3">\s*(Sinh viên này chưa được.*?)\s*<\/h3>/s';
    
    $replacement = '<div class="card overflow-auto mx-auto mt-3" style="max-width: fit-content;">
    <div class="card-header p-3">
        <h3 class="card-title text-center text-danger font-weight-bold m-0" style="float: none;">
            $1
        </h3>
    </div>
</div>';
        
    $content = preg_replace($pattern, $replacement, $content);
    file_put_contents($file, $content);
}

echo "Updated\n";
?>
