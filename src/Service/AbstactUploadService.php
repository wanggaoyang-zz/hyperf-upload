<?php


namespace Wgy\Upload\Service;


use Hyperf\HttpMessage\Upload\UploadedFile as UploadedFileAlias;
use UU\Contract\Exception\BusinessException;
use Wgy\Upload\Service\Storage\StorageFactory;


abstract class AbstactUploadService implements UploadInterface
{
    private $config;

    /**
     * @var StorageFactory
     */
    protected $storageFactory;

    public function __construct()
    {
        $this->config = config('upload');
        $this->storageFactory = di(StorageFactory::class);
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function configurate(UploadedFileAlias $file, $fileName = 'image')
    {
        if (empty($file) || !$file->isValid()) {
            throw new BusinessException('请选择正确的文件！');
        }
        switch ($fileName) {
            case 'image':
                $fileSize = $this->getConfig()['image_size'];
                if ($file->getSize() > $fileSize) {
                    throw new BusinessException('文件不能大于！' . ($fileSize / 1024 / 1024) . 'MB');
                }
                $imageMimes = explode(',', $this->config['image_mimes'] ?? 'jpeg,bmp,png,gif,jpg');
                if (!in_array(strtolower($file->getExtension()), $imageMimes)) {
                    throw new BusinessException('后缀不允许！');
                }
                #检测类型
                if (!in_array(strtolower($file->getClientMediaType()), ['image/gif', 'image/jpeg', 'image/jpg', 'image/png', 'image/pjpeg', 'image/x-png'])) {
                    throw new BusinessException('不允许上传此文件！');
                }
                break;
            case 'files':
                $fileSize = $this->getConfig()['file_size'];
                if ($file->getSize() > $fileSize) {
                    throw new BusinessException('文件不能大于！' . ($fileSize / 1024 / 1024) . 'MB');
                }
                #检测类型
                $imageMimes = explode(',', $this->config['file_mimes'] ?? 'txt,sql,zip,rar,ppt,word,xls,xlsx,doc,docx');
                if (!in_array(strtolower($file->getExtension()), $imageMimes)) {
                    throw new BusinessException('类型不允许！');
                }
                break;
            default:
                throw new BusinessException('该驱动暂未实现！');
                break;
        }
        return true;
    }

    abstract function saveFiles(UploadedFileAlias $file, $fileName = 'image'): array;
}