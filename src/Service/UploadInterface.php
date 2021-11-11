<?php

namespace Wgy\Upload\Service;

use Hyperf\HttpMessage\Upload\UploadedFile as UploadedFileAlias;

interface UploadInterface
{
    /**
     * 获取uplaod配置文件
     * @return mixed
     * @author wgy
     */
    function getConfig();

    /**
     * 获取file配置文件
     * @return mixed
     * @author wgy
     */
    function getFileConfig();

    /**
     * 设置file配置文件
     * @return mixed
     * @author wgy
     */
    function setFileConfig($file);

    /**
     * 文件校验
     * @param \Hyperf\HttpMessage\Upload\UploadedFile $file
     * @param string $fileName
     * @return mixed
     * @author wgy
     */
    function configurate(UploadedFileAlias $file, $fileName = 'image');

    /**
     * 保存文件
     * @param \Hyperf\HttpMessage\Upload\UploadedFile $file
     * @param $fileName
     * @return array
     * @author wgy
     */
    function saveFiles(UploadedFileAlias $file, $fileName): array;

    /**
     * 获取直传token
     * @param int $type
     * @return mixed
     * @author wgy
     */
    function token(int $type);

}