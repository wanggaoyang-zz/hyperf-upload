<?php

namespace Wgy\Upload\Service;

use Exception;
use Hyperf\HttpMessage\Upload\UploadedFile;


class OssDriveService extends UploadBaseService implements UploadInterface
{
    public function __invoke(): UploadInterface
    {
        $this->filesystem = $this->filesystemFactory->get('oss');
        $this->setDriveConfig(\Hyperf\Config\config('file')['storage']['oss']);
        return $this;
    }

    public function saveFiles(UploadedFile $file, $fileName = 'image'): array
    {
        $this->configurate($file, $fileName);
        $stream = fopen($file->getRealPath(), 'r+');
        $file_name = $this->getUploadConfig()['save_path'];
        $file_name = $file_name . '/' . $fileName . '/' . date('Ym') . '/' . date('d') . '/' . uuid(16) . '.' . strtolower($file->getExtension());
        $this->filesystem->writeStream(
            $file_name,
            $stream
        );
        return [
            'path' => 'https://' . $this->getDriveConfig()['bucket'] . '.' . $this->getDriveConfig()['endpoint'] . $file_name,
            'name' => $file->getClientFilename(),
        ];
    }

    public function token(string $typeName)
    {
        // $host的格式为 bucketname.endpoint，请替换为您的真实信息。
        $host = !empty($this->getUploadConfig()['host']) ? $this->getUploadConfig()['host'] : 'https://' . $this->getDriveConfig()['bucket'] . '.' . $this->getDriveConfig()['endpoint'];
        // $callbackUrl为上传回调服务器的URL，请将下面的IP和Port配置为您自己的真实URL信息。
        $callbackUrl = $this->getUploadConfig()['callback_url'];
        $dir = $this->getUploadConfig()['directory'][$typeName];// 用户上传文件时指定的前缀。
        $callback_param = array(
            'callbackUrl' => $callbackUrl,
            'callbackBody' => 'filename=${object}&size=${size}&mimeType=${mimeType}&height=${imageInfo.height}&width=${imageInfo.width}',
            'callbackBodyType' => "application/x-www-form-urlencoded"
        );
        $callback_string = json_encode($callback_param);
        $base64_callback_body = base64_encode($callback_string);
        $now = time();
        $expire = $this->getUploadConfig()['token_expire'];;  //设置该policy超时时间是10s. 即这个policy过了这个有效时间，将不能访问。
        $end = $now + $expire;
        $expiration = gmt_iso8601($end);
        //最大文件大小.用户可以自己设置
        $condition = array(0 => 'content-length-range', 1 => 0, 2 => $this->getUploadConfig()[$typeName . '_size']);
        $conditions[] = $condition;
        // 表示用户上传的数据，必须是以$dir开始，不然上传会失败，这一步不是必须项，只是为了安全起见，防止用户通过policy上传到别人的目录。
        $start = array(0 => 'starts-with', 1 => '$key', 2 => $dir);
        $conditions[] = $start;
        $arr = array('expiration' => $expiration, 'conditions' => $conditions);
        $policy = json_encode($arr);
        $base64_policy = base64_encode($policy);
        $string_to_sign = $base64_policy;
        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $this->getDriveConfig()['accessSecret'], true));
        $response = array();
        $response['accessid'] = $this->getDriveConfig()['accessId'];
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

    public function callback(array $data)
    {
        $config =  $this->getUploadConfig();
        $fileConfig = $this->getDriveConfig();
        $host = !empty($config['host']) ? $config['host'] : 'https://' . $fileConfig['bucket'] . '.' . $fileConfig['endpoint'];
        $path = $host . '/' . $data['filename'];
        return [
            'path' => $path,
            'name' => substr($data['filename'], strrpos($data['filename'], '/') + 1),
            'size' => $data['size'] ?? -1,
        ];
    }
}