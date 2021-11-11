<?php

namespace Wgy\Upload\Service;

use Hyperf\HttpMessage\Upload\UploadedFile as UploadedFileAlias;
use League\Flysystem\Filesystem;
use UU\Contract\Exception\BusinessException;

class UploadOssService extends AbstactUploadService
{
    public function __construct()
    {
        parent::__construct();
        $this->setFileConfig(config('file')['storage']['oss']);
    }

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
            'path' => 'https://' . $this->getFileConfig()['bucket'] . '.' . $this->getFileConfig()['endpoint'] . $file_name,
            'name' => $file->getClientFilename(),
        ];
    }

    public function token($type)
    {

        if (!in_array($type, [0, 1])) {
            throw new BusinessException('类型值不正确！');
        }
        $typeName = ['image', 'file'];
        // $host的格式为 bucketname.endpoint，请替换为您的真实信息。
        $host = !empty($this->getConfig()['host']) ? $this->getConfig()['host'] : 'https://' . $this->getFileConfig()['bucket'] . '.' . $this->getFileConfig()['endpoint'];
        // $callbackUrl为上传回调服务器的URL，请将下面的IP和Port配置为您自己的真实URL信息。
        $callbackUrl = $this->getConfig()['callback_url'];
        $dir = $this->getConfig()['directory'][$typeName[$type]];// 用户上传文件时指定的前缀。
        $callback_param = array(
            'callbackUrl' => $callbackUrl,
            'callbackBody' => 'filename=${object}&size=${size}&type=' . $type . '&mimeType=${mimeType}&height=${imageInfo.height}&width=${imageInfo.width}',
            'callbackBodyType' => "application/x-www-form-urlencoded"
        );
        $callback_string = json_encode($callback_param);
        $base64_callback_body = base64_encode($callback_string);
        $now = time();
        $expire = $this->getConfig()['token_expire'];;  //设置该policy超时时间是10s. 即这个policy过了这个有效时间，将不能访问。
        $end = $now + $expire;
        $expiration = gmt_iso8601($end);
        //最大文件大小.用户可以自己设置
        $condition = array(0 => 'content-length-range', 1 => 0, 2 => $this->getConfig()[$typeName[$type] . '_size']);
        $conditions[] = $condition;
        // 表示用户上传的数据，必须是以$dir开始，不然上传会失败，这一步不是必须项，只是为了安全起见，防止用户通过policy上传到别人的目录。
        $start = array(0 => 'starts-with', 1 => '$key', 2 => $dir);
        $conditions[] = $start;
        $arr = array('expiration' => $expiration, 'conditions' => $conditions);
        $policy = json_encode($arr);
        $base64_policy = base64_encode($policy);
        $string_to_sign = $base64_policy;
        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $this->getFileConfig()['accessSecret'], true));
        $response = array();
        $response['accessid'] = $this->getFileConfig()['accessId'];
        $response['host'] = $host;
        $response['policy'] = $base64_policy;
        $response['signature'] = $signature;
        $response['expire'] = $end;
        $response['callback'] = $base64_callback_body;
        $response['dir'] = $dir;  // 这个参数是设置用户上传文件时指定的前缀。
        $response['key'] = '';
        $response['filename'] = time() . round(100, 999);
        return $response;
    }
}