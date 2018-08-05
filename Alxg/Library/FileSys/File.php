<?php

namespace Alxg\Library\FileSys;


class File extends FileSys
{
    /**
     * 获得文件信息
     * @param $filepath
     * @return array
     */
    public static function fileInfo($filepath)
    {
        $pathinfo = pathinfo($filepath);
        $info = [
            'filename' => $pathinfo['filename'],
            'extension' => $pathinfo['extension'] ?? 'unkonw',
            'filesize' => round(filesize($filepath) / 1024, 2) . 'kb',
            //'owner' => fileowner($filepath),
            //'perms' => fileperms($filepath),
            'type' => filetype($filepath),
            'is_readable' => is_readable($filepath) ? 'yes' : 'no',
            'is_writeable' => is_writable($filepath) ? 'yes' : 'no',
            'last_read_time' => date('Y-m-d H:i:s', fileatime($filepath)),
            'last_modify_time' => date('Y-m-d H:i:s', filemtime($filepath)),
        ];
        return $info;
    }

    /**
     * 删除文件
     * @param mixed $files 如果是一个字符串，则只删除对应文件
     * 如果是一个数组，则删除数组条目对应的所有文件
     * @return array|bool 删除失败，则返回失败列表
     */
    public static function deleteFile($files)
    {
        $error = false;
        $errlist = [];
        if (is_array($files)) {
            foreach ($files as $file) {
                $error = unlink($file);
                if ($error) {
                    $errlist[] = $file;
                }
            }
        } else {
            $error = unlink($files);
        }
        //如果删除失败则反回删除失败列表
        if ($errlist) {
            return $errlist;
        }
        return $error;
    }

    /**
     * @param string $source 原文件名（包含路径）
     * @param string $dest 为目录时复制后的文件名与原文件一致，为文件时复制到对应的文件名
     * @return bool
     */
    public static function copyFile(string $source, string $dest)
    {
        //检查源文档是否存在且是一个文件
        if (!is_file($source)) {
            $msg = "$source is not a file";
            self::setError($msg);
            return false;
        }
        if (!file_exists($source)) {
            $msg = "$source is not exists";
            self::setError($msg);
            return false;
        }
        //检查目标
        $destfilepath = null;
        if (is_dir($dest)) {
            $dest = rtrim(str_replace('\\', '/', $dest), '/');
            /**
             * 如果目标路径是一个目录的话
             * 检查目录内是否有相同文件名的文件
             */
            $filename = pathinfo($source, PATHINFO_BASENAME);
            $destfilepath = $dest . '/' . $filename;
        } else {
            $destfilepath = $dest;
        }
        if (is_file($destfilepath) && file_exists($destfilepath)) {
            self::setError("$destfilepath is exists");
            return false;
        }
        if (copy($source, $destfilepath)) {
            return true;
        }
        return false;
    }

    /**
     * 文件重命名，不支持移动文件
     * @param string $oldname 原文件路径
     * @param string $newname 新文件名，不包含路径（如a.log）
     * @return bool
     */
    public static function renameFile(string $oldname, string $newname)
    {
        if (!self::checkFilename($newname)) {
            self::setError("$newname is not a valid filename");
            return false;
        }
        if (!is_file($oldname) || !file_exists($oldname)) {
            self::setError("$oldname is not a file or not exists");
            return false;
        }
        $path = pathinfo($oldname, PATHINFO_DIRNAME) . '/' . $newname;
        if(file_exists($path)){
            self::setError("$path is exists");
            return false;
        }
        if (rename($oldname, $path)) {
            return true;
        }
        return false;
    }

    /**
     * 移动文件
     * @param string $source
     * @param string $dest
     * @return bool
     */
    public static function moveFile(string $source, string $dest)
    {
        if (!is_file($source)) {
            self::setError("$source is not a file");
            return false;
        }
        if (!file_exists($source)) {
            self::setError("$source is not exists");
            return false;
        }
        $destfilename = null;
        if (is_dir($dest)) {
            $dest = rtrim(str_replace('\\', '/', $dest), '/');
            $filename = pathinfo($source, PATHINFO_BASENAME);
            $destfilename = $dest . '/' . $filename;
        } else {
            $destfilename = $dest;
            $filename = pathinfo($destfilename, PATHINFO_BASENAME);
            if (!self::checkFilename($filename)) {
                self::setError("$destfilename is not a valid filename");
                return false;
            }
        }
        if (is_file($destfilename) && file_exists($destfilename)) {
            self::setError("$destfilename is exists");
            return false;
        }
        if (rename($source, $destfilename)) {
            return true;
        }
        return false;
    }
}