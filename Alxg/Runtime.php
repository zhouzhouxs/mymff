<?php

namespace Alxg;


use Alxg\Library\FileSys\Directory;
use Alxg\Library\Log\Log;

class Runtime
{
    const LOG = '/log/runtime';
    private static $runtimePath = '';
    private $folders = ['temp', 'log', 'data'];

    public static function Check()
    {
        $self = new self();
        $self->checkPath();
    }

    /**
     * 获得runtime路径
     * @return mixed
     */
    public static function getRuntimePath()
    {
        if(self::$runtimePath) return self::$runtimePath;

        $path = App::Config('runtimePath');
        if ($path) {
            $path = realpath($path);
            $path = str_replace('\\', '/', $path);
            $path = rtrim($path, '/');
            self::$runtimePath = $path;
        }
        return $path;
    }

    private function __construct()
    {
        self::getRuntimePath();
    }

    /**
     * 检查是否runtime是否已存在
     * 如果不存在则生成
     * @return bool
     */
    private function checkPath()
    {
        $runtime = self::$runtimePath;
        $folders = $this->folders;
        foreach ($folders as $folder) {
            $path = $runtime . '/' . $folder;
            if (is_dir($path)) continue;
            $r = Directory::createDir($path);
            if (!$r) {
                trigger_error(Directory::getError(), E_USER_ERROR);
                return false;
            }
        }
        return true;
    }

}