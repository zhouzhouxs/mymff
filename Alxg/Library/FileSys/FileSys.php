<?php

namespace Alxg\Library\FileSys;


class FileSys
{
    protected static $last_err_msg = 'ok';

    public static function getError()
    {
        $msg = self::$last_err_msg;
        self::$last_err_msg = 'ok';
        return $msg;
    }

    protected static function setError(string $msg, int $type = E_USER_NOTICE)
    {
        self::$last_err_msg = $msg;
    }

    /**
     * 检查文件名是否合法
     * @param $filename
     * @return bool
     */
    public static function checkFilename($filename)
    {
        $pattern = "/[\/:\\*?\"<>|]/";
        if (preg_match($pattern, $filename)) {
            return false;
        }
        return true;
    }

    public static function getRelativePath($dirname)
    {
        $web_root = rtrim($_SERVER['DOCUMENT_ROOT'], '/');
        $root = dirname($_SERVER['SCRIPT_NAME']);
        $path = str_replace('\\', '/', $web_root . $root);
        $path = rtrim($path, '/');
        $realpath = str_replace('\\', '/', realpath($dirname));
        $relative = str_replace($path, '', $realpath);
        return '.' . $relative;
    }
}