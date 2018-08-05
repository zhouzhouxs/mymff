<?php

namespace Alxg\Library\Log;

use Alxg\App;

class Log
{
    //日志级别（或信息分类）
    const LEVEL_ERROR = "ERROR";
    const LEVEL_WARNING = "WARNING";
    const LEVEL_NOTICE = "NOTICE";
    const LEVEL_EXCEPTION = "EXCEPTION";
    const LEVEL_USER = "USER";
    const LEVER_UNKNOWN = "UNKNOWN";

    private static $drivers = null;
    private $driver = null;

    /**
     * Log constructor.
     * @param string $path
     */
    private function __construct(string $path)
    {
        $config = App::config('log');
        $type = $config['type'];
        $class = "\\Alxg\\Library\\Log\\driver\\$type";
        $driver = new $class($path);
        $this->driver = $driver;
    }

    /**
     * @param string $path
     * @return Log
     */
    public static function Init(string $path)
    {
        $driver = null;
        if (isset(self::$drivers[$path])) {
            $driver = self::$drivers[$path];
        } else {
            $driver = new self($path);
            self::$drivers[$path] = $driver;
        }
        return $driver;
    }

    public function add($message, $level = Log::LEVEL_ERROR)
    {
        $this->driver->add($message, $level);
        return $this;
    }

    /**
     * @param null $path
     * @return mixed
     */
    public function save($path = null)
    {
        return $this->driver->save($path);
    }
}