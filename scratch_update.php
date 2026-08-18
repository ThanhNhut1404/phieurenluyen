<?php
\ = glob(__DIR__ . '/../../admin/quan-ly-*/index.php');
foreach (\ as \) {
    \ = file_get_contents(\);
    
    // Replace the old Swal.fire config
    \ = \"/confirmButtonColor:\\s*'\#[a-zA-Z0-9]+',\\s*cancelButtonColor:\\s*'\#[a-zA-Z0-9]+',\\s*confirmButtonText:\\s*'[^']+'/s\";
    
    \ = \"confirmButtonText: '<i class=\\\"fas fa-trash\\\"></i> Xóa',
        cancelButtonText: '<i class=\\\"fas fa-times\\\"></i> H?y',
        customClass: {
            confirmButton: 'btn btn-danger font-weight-bold mx-2',
            cancelButton: 'btn btn-cancel-custom font-weight-bold mx-2'
        },
        buttonsStyling: false,
        showClass: {
            popup: 'animate__animated animate__fadeInDown animate__faster'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp animate__faster'
        }\";
        
    \ = preg_replace(\, \, \);
    
    // Check if the file had 'text: \"Thao tác này s? xóa <xyz> dã ch?n.\"'
    // and replace with '... và không th? hoàn tác.'
    \ = \"/(text:\\s*'Thao tác này s? xóa [^']+) dã ch?n\\.'/s\";
    \ = \"\ dã ch?n và không th? hoàn tác.\";
    \ = preg_replace(\, \, \);
    
    if (\ !== \) {
        file_put_contents(\, \);
        echo \\\"Updated: \\\n\\\";
    }
}
