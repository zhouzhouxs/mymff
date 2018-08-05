<?php

namespace Alxg\Library\Log\driver;


use Alxg\Library\FileSys\Directory;

class File implements LogInterface
{
    private $path = null;
    private $content = '';

    /**
     * File constructor.
     * @param string $path
     * @throws \Exception
     */
    public function __construct(string $path)
    {
        $this->checkPath($path);
    }

    /**
     * @param $path
     * @throws \Exception
     */
    private function checkPath($path)
    {
        $path .= '/' . date('Ym') . '/';
        if (!is_dir($path)) {
            $r = Directory::createDir($path);
            if (!$r) {
                throw new \Exception(Directory::getError());
            }
        }
        $filepath = $path . '/' . date('Ymd') . '.log';
        $this->path = $filepath;
    }

    /**
     * @param $message
     * @param $level
     */
    public function add($message, $level)
    {
        $content = "[$level]" . PHP_EOL;
        $line = str_repeat('-', 20);
        $content .= $line . date('Y-m-d H:i:s') . $line . PHP_EOL;
        if (is_array($message)) {
            foreach ($message as $msg) {
                $content .= $msg . PHP_EOL;
            }
        } else {
            $content .= $message . PHP_EOL;
        }
        $this->content .= $content . PHP_EOL;
    }

    /**
     * @param string|null $path
     * @return bool|int
     */
    public function save(string $path = null)
    {
        if (!$path) $path = $this->path;
        return file_put_contents($path, $this->content, FILE_APPEND);
    }
}