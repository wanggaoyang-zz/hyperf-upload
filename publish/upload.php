<?php

return [
    // Disk in `config/filesystem.php`.
    'disk' => 'local',
    'host' => '',
    'save_path' => '/upload',
    'uniqueName' => false,
    // Image and file upload path under the disk above.
    'directory' => [
        'image' => 'images',
        'file' => 'files',
    ],
    'image_size' => 1024 * 1024 * 5,
    'file_size' => 1024 * 1024 * 5,
    //文件上传类型
    'file_mimes' => 'txt,sql,zip,rar,ppt,word,xls,xlsx,doc,docx',
    //文件上传类型
    'image_mimes' => 'jpeg,bmp,png,gif,jpg',
];