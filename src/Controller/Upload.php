<?php

namespace Wgy\Upload\Controller;

use Exception;
use HPlus\Route\Annotation\ApiController;
use HPlus\Route\Annotation\FormData;
use HPlus\Route\Annotation\GetApi;
use HPlus\Route\Annotation\PostApi;
use HPlus\Route\Annotation\Query;
use HPlus\Validate\Annotations\RequestValidation;
use Psr\Container\ContainerInterface;
use Wgy\Upload\AbstractController;
use Wgy\Upload\Service\UploadFactory;
use Wgy\Upload\Service\UploadInterface;

#[ApiController(tag: "上传管理",description: "上传文件或图片")]
class Upload extends AbstractController
{
    /**
     * @var UploadInterface
     */
    protected UploadInterface $uploadService;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->uploadService = UploadFactory::create();
    }

    #[PostApi(summary: "oss回调")]
    public function callback()
    {
        $data = $this->request->all();
        return $this->uploadService->callback($data);
    }

    #[PostApi(path: "file")]
    #[RequestValidation(rules: [
        "file|文件"=> "require|file",
        "type|类型"=> "require|string",
    ])]
    public function file()
    {
        $file = $this->request->file('file');
        if (empty($file)) {
            throw new \Exception('参数不能为空');
        }
        return $this->uploadService->saveFiles($file, 'files');
    }


    #[GetApi(path: "token")]
    #[Query(key: "type")]
    public function token()
    {
        $file = $this->request->getQueryParams()['type'] ?? 'file';
        if (!in_array($file,['image', 'file'])) {
            throw new Exception('类型不支持！');
        }
        return $this->uploadService->token($file);
    }
}