<?php

namespace Wgy\Upload\Controller;

use HPlus\Route\Annotation\AdminController;
use HPlus\Route\Annotation\FormData;
use HPlus\Route\Annotation\PostApi;
use Psr\Container\ContainerInterface;
use UU\Contract\Exception\BusinessException;
use Wgy\Upload\AbstractController;
use Wgy\Upload\Service\UploadFactory;
use Wgy\Upload\Service\UploadInterface;

/**
 * @AdminController(tag="上传管理", description="上传文件或图片")
 * Class IndexController
 */
class Upload extends AbstractController
{
    /**
     * @var UploadInterface
     */
    protected $uploadService;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->uploadService = make(UploadFactory::class)();
    }

    /**
     * @PostApi(path="image")
     * @FormData(key="file", type="file", rule="file")
     * @FormData(key="path", default="avatar")
     */
    public function image()
    {
        $file = $this->request->file('file');
        if (empty($file)) {
            throw new BusinessException('参数不能为空');
        }
        return $this->uploadService->saveFiles($file, 'image');
    }

    /**
     * @PostApi(path="file")
     * @FormData(key="file", type="file", rule="file")
     * @FormData(key="path", default="file")
     */
    public function file()
    {
        $file = $this->request->file('file');
        if (empty($file)) {
            throw new BusinessException('参数不能为空');
        }
        return $this->uploadService->saveFiles($file, 'files');
    }
}