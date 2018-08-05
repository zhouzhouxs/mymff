<?php

namespace Alxg\Library\FileSys;


class Directory extends FileSys
{

    /**
     * 扫描文件夹中的内容
     * @param string $dirname 目录路径
     * @param bool $recursive 是否递归扫描
     * @return array|bool
     */
    public static function scanDirectory($dirname, $recursive = true)
    {
        if (!is_dir($dirname)) {
            self::setError("$dirname is not a valid directory");
            return false;
        }
        $root = rtrim($dirname, '/\\');
        $dir = dir($dirname);
        $res = self::scan($dir, $root, $recursive);
        return $res;
    }

    /**
     * 执行目录扫描
     * @param \Directory $dir
     * @param string $root
     * @param bool $recursive
     * @return array
     */
    private static function scan(\Directory $dir, $root = '', $recursive = true)
    {
        $directory = [];
        $tmp = $dir->read();
        while ($tmp !== false) {
            if ($tmp == '.' || $tmp == '..') {
                $tmp = $dir->read();
                continue;
            }
            $path = $root . '/' . $tmp;
            $temp = ['name' => $tmp, 'path' => $path, 'type' => filetype($path)];
            if (is_file($path)) $temp = array_merge($temp, File::fileInfo($path));
            if (is_dir($path) && $recursive) {
                $temp['childrens'] = self::scan(dir($path), $path, $recursive);
            }
            $directory[] = $temp;
            $tmp = $dir->read();
        }
        //一定要记得关闭文件夹
        $dir->close();
        return $directory;
    }

    /**
     * 创建文件夹
     * @param string $pathname 路径
     * @param int $mode 权限
     * @param bool $recursive 是否递归创建
     * @return bool
     */
    public static function createDir(string $pathname, int $mode = 0755, $recursive = true): bool
    {
        $pathname = str_replace('\\', '/', $pathname);
        if (is_dir($pathname)) return true;
        if (mkdir($pathname, $mode, $recursive)) {
            return true;
        }
        return false;
    }

    /**
     * 删除文件夹
     * @param string $pathname 路径
     * @param bool $force 是否强制执行
     * @return bool
     */
    public static function removeDir(string $pathname, bool $force = false)
    {
        $success = true;
        $pathname = rtrim($pathname, '/\\');
        if (!is_dir($pathname)) {
            self::setError("$pathname is not a vald directory");
            return false;
        }

        if (!$force) {
            $success = rmdir($pathname);
            return $success;
        }
        //强制删除
        $success = self::rmdirForce($pathname);
        return $success;
    }

    private static function rmdirForce(string $path)
    {
        $success = true;
        $directory = self::scan(dir($path), $path, false);
        foreach ($directory as $d) {
            if ($d['type'] == 'dir') {
                $success = self::rmdirForce($d['path']);
                if (!$success) break;
            } else {
                $success = File::deleteFile($d['path']);
                if (!$success) break;
            }
        }
        if ($success) $success = rmdir($path);
        return $success;
    }

    public static function copyDir()
    {

    }

    public static function renameDir(string $oldname, string $newname)
    {
        if (!self::checkFilename($newname)) {
            self::setError("$newname is not a valid filename");
            return false;
        }
        if (!is_dir($oldname) || !file_exists($oldname)) {
            self::setError("$oldname is not a file or not exists");
            return false;
        }
        $path = pathinfo($oldname, PATHINFO_DIRNAME) . '/' . $newname;
        if (file_exists($path)) {
            self::setError("$path is exists");
            return false;
        }
        if (rename($oldname, $path)) {
            return true;
        }
        return false;
    }

    public static function moveDir()
    {

    }
}