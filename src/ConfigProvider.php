<?php


namespace Wgy\Upload;


use Hyperf\Filesystem\FilesystemInvoker;
use League\Flysystem\Filesystem;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                Filesystem::class => FilesystemInvoker::class,
            ],
            'commands' => [
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'publish' => [
                [
                    'id' => 'uplaod',
                    'description' => 'wgy-upload-config',
                    'source' => __DIR__ . '/../publish/upload.php',
                    'destination' => BASE_PATH . '/config/autoload/upload.php',
                ],
                [
                    'id' => 'file',
                    'description' => 'wgy-file-config',
                    'source' => __DIR__ . '/../publish/file.php',
                    'destination' => BASE_PATH . '/config/autoload/file.php',
                ]
            ],
        ];
    }
}