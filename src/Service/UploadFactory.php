<?php

namespace Wgy\Upload\Service;


class UploadFactory
{
    /**
     * @throws \Exception
     */
    public static function create()
    {
        return match (\Hyperf\Config\config('upload')['disk'] ?? 'oss') {
            'oss' => di(OssDriveService::class)(),
            default => throw new \Exception('该驱动暂未实现!'),
        };


    }
}