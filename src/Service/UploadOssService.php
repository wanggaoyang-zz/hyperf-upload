<?php

namespace Wgy\Upload\Service;

use Hyperf\HttpMessage\Upload\UploadedFile as UploadedFileAlias;
use League\Flysystem\Filesystem;

class UploadOssService extends AbstactUploadService
{
    public function saveFiles(UploadedFileAlias $file, $fileName = 'image'): array
    {
        $this->configurate($file, $fileName);
        $stream = fopen($file->getRealPath(), 'r+');
        /**
         * @var Filesystem $filesystem
         */
        $filesystem = $this->storageFactory->disk($this->getConfig()['disk'])->getDriver();
        $file_name = $this->getConfig()['save_path'];
        $file_name = $file_name . '/' . $fileName . '/' . date('Ym') . '/' . date('d') . '/' . uuid(16) . '.' . strtolower($file->getExtension());
        $filesystem->writeStream(
            $file_name,
            $stream
        );
        return [
            'path' => 'https://' . env('OSS_BUCKET', 'chengming-ds') . '.' . env('OSS_ENDPOINT', 'oss-cn-beijing.aliyuncs.com') . $file_name,
            'name' => $file->getClientFilename(),
        ];
    }
}