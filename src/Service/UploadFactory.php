<?php

namespace Wgy\Upload\Service;

use UU\Contract\Exception\BusinessException;

class UploadFactory
{
    private UploadInterface $upload;

    public function __invoke()
    {
        $disk = config('upload')['disk'] ?? 'local';
        switch ($disk) {
            case 'local':
                $this->upload = di(UploadLocalService::class);
                break;
            case 'oss':
                $this->upload = di(UploadOssService::class);
                break;
            default:
                throw new BusinessException('该驱动暂未实现!');
        }
        return $this->upload;
    }
}