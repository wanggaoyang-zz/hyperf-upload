<?php

namespace Wgy\Upload\Service;

use Hyperf\HttpMessage\Upload\UploadedFile;

interface UploadInterface
{

    function getUploadConfig();

    function getDriveConfig();

    function setDriveConfig($file);

    public function configurate(UploadedFile $file, $fileName = 'image');

    function saveFiles(UploadedFile $file, $fileName): array;

    function token(string $typeName);

}