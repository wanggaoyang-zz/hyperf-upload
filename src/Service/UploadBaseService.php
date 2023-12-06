<?php


namespace Wgy\Upload\Service;

use Exception;
use Hyperf\Filesystem\FilesystemFactory;
use Hyperf\HttpMessage\Upload\UploadedFile;
use League\Flysystem\Filesystem;


abstract class UploadBaseService implements UploadInterface
{
    /**
     * @var FilesystemFactory
     */
    protected FilesystemFactory $filesystemFactory;

    private $file;

    protected Filesystem $filesystem;


    public function __construct(FilesystemFactory $filesystemFactory)
    {
        $this->filesystemFactory = $filesystemFactory;
    }

    public function getUploadConfig()
    {
        return \Hyperf\Config\config('upload');
    }

    public function setDriveConfig($file)
    {
        $this->file = $file;
        return $this->file;
    }

    public function configurate(UploadedFile $file, $fileName = 'image')
    {
        if (empty($file) || !$file->isValid()) {
            throw new Exception('请选择正确的文件！');
        }
        switch ($fileName) {
            case 'image':
                $fileSize = $this->getUploadConfig()['image_size'];
                if ($file->getSize() > $fileSize) {
                    throw new Exception('文件不能大于！' . ($fileSize / 1024 / 1024) . 'MB');
                }
                $imageMimes = explode(',', $this->config['image_mimes'] ?? 'jpeg,bmp,png,gif,jpg');
                if (!in_array(strtolower($file->getExtension()), $imageMimes)) {
                    throw new Exception('后缀不允许！');
                }
                #检测类型
                if (!in_array(strtolower($file->getClientMediaType()), ['image/gif', 'image/jpeg', 'image/jpg', 'image/png', 'image/pjpeg', 'image/x-png'])) {
                    throw new Exception('不允许上传此文件！');
                }
                break;
            case 'files':
                $fileSize = $this->getUploadConfig()['file_size'];
                if ($file->getSize() > $fileSize) {
                    throw new Exception('文件不能大于！' . ($fileSize / 1024 / 1024) . 'MB');
                }
                #检测类型
                $imageMimes = explode(',', $this->config['file_mimes'] ?? 'txt,sql,zip,rar,ppt,word,xls,xlsx,doc,docx');
                if (!in_array(strtolower($file->getExtension()), $imageMimes)) {
                    throw new Exception('类型不允许！');
                }
                break;
            default:
                throw new Exception('该类型不支持！');
        }
        return true;
    }
    public function getDriveConfig()
    {
        return $this->file;
    }
}