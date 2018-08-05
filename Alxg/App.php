<?php

namespace Alxg;


class App
{
    //程序配置
    private static $config = [];

    //记录核心加载过的类
    private static $registerClass = [];

    /**
     * @throws \Exception
     */
    public static function run()
    {
        self::Init();
        //路由调度
        Router::Run();

    }

    private static function Init()
    {
        //获得系统路径
        self::getCorePath();
        //加载系统配置
        self::LoadCoreConfig();
        //注册自动加载
        self::AutoLoadRegister();
        //注册错误与异常处理类
        AlxgTrace::Register();
        //检测运行时目录
        Runtime::Check();
    }

    private static function AutoLoadRegister()
    {
        //注册核心加载器
        spl_autoload_register(__CLASS__ . '::AutoLoadCore', true);
        //注册用户加载器
        spl_autoload_register(__CLASS__ . '::AutoLoadCustom', true);
    }

    private static function AutoLoadCore(string $class, $extension = 'php'): bool
    {
        //如果已经加载，则不必重复加载
        if (isset(self::$registerClass[$class])) {
            return true;
        }
        $path = CORE_PATH . DIRECTORY_SEPARATOR . $class . '.' . $extension;
        $path = str_replace("\\", "/", $path);
        if (file_exists($path)) {
            //var_dump($path);
            require $path;
            self::$registerClass[$class] = true;
            return true;
        }
        return false;
    }

    private static function AutoLoadCustom(string $class, $extension = 'php'): bool
    {
        //如果已经加载，则不必重复加载
        if (isset(self::$registerClass[$class])) {
            return true;
        }
        //分析命名空间与实际路径的映射
        $classMap = App::config('classMap');
        if ($classMap) {
            list($namespace) = explode('\\', $class);
            if (isset($classMap[$namespace])) {
                $path = $classMap[$namespace];
                $class = str_replace($namespace, $path, $class);
            }
        }

        $path = APP_PATH . DIRECTORY_SEPARATOR . $class . '.' . $extension;
        $path = str_replace("\\", "/", $path);
        if (file_exists($path)) {
            //var_dump($path);
            require $path;
            self::$registerClass[$class] = true;
            return true;
        }
        return false;
    }


    /**
     * 获得系统核心路径
     * @return string
     */
    private static function getCorePath(): string
    {
        if (!defined('CORE_PATH')) {
            $path = dirname(dirname(__FILE__));
            define('CORE_PATH', $path);
        }
        return CORE_PATH;
    }

    /**
     * 加载用户配置
     * 注意，后面加载的配置会把
     * 前面加载的相同配置覆盖掉
     * @param $file
     * @return bool
     */
    public static function LoadConfig(string $file)
    {
        if (!file_exists($file)) return false;
        $config = include $file;
        if (is_array($config)) {
            self::$config = array_merge(self::$config, $config);
        }
        return true;
    }

    /**
     * 加载系统配置，用户配置覆盖系统配置
     * @return bool
     */
    private static function LoadCoreConfig()
    {
        $path = CORE_PATH . '/Alxg/conf/convention.php';
        $path = str_replace("\\", "/", $path);
        if (!file_exists($path)) return false;
        $config = include $path;
        if (is_array($config)) {
            //用户端配置覆盖系统配置
            self::$config = array_merge($config, self::$config);
        }
        return true;
    }

    /**
     * 设置或返回配置信息
     *
     * @param string $key
     * @param mixed $val
     * @return mixed
     */
    public static function Config(string $key, $val = null)
    {
        if ($val !== null) {
            if (isset(self::$config[$key])) {
                if (is_array(self::$config[$key])) {
                    self::$config[$key] = array_merge(self::$config[$key], $val);
                } else {
                    self::$config[$key] = $val;
                }
            } else {
                self::$config[$key] = $val;
            }
            return true;
        }
        return self::$config[$key] ?? null;
    }
}