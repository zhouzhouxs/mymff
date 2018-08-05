<?php

namespace Alxg;


class Router
{
    /**
     * 当前访问模型
     * @var null
     */
    public static $module = null;
    public static $controller = null;
    public static $action = null;

    /**
     * @throws \Exception
     */
    public static function Run()
    {
        self::Analyze();
        self::DisPatch();
    }

    /**
     * @throws \Exception
     */
    private static function DisPatch()
    {
        if (empty(self::$module)) throw new \Exception('未知的模块');
        if (empty(self::$controller)) throw new \Exception('未知的控制器');
        if (empty(self::$action)) throw new \Exception('未知的方法');

        $namespace = App::config('appNamespace');
        $module = self::$module;
        $controller = ucfirst(self::$controller);
        $action = self::$action;

        //调用控制器
        $class = '\\' . $namespace . '\\' . $module . '\\controller\\' . $controller;

        if (!preg_match('/^[A-Za-z](\w)*$/', $action)) {
            // 非法操作
            throw new \Exception('不合法的方法名');
        }

        $reflectClass = new \ReflectionClass($class);
        $instance = $reflectClass->newInstance();
        //判断方法是否存在
        if (!$reflectClass->hasMethod($action)) {
            throw new \Exception($action . '方法不存在');
        }
        //调用初始化方法
        $initMethod = $reflectClass->getMethod('__init');
        if (!$initMethod->isPublic()) {
            $initMethod->setAccessible(true);
        }

        //调用当前方法
        $runMethod = $reflectClass->getMethod($action);
        //参数绑定
        $args = [];
        $vars = self::GetVars();
        if ($runMethod->getNumberOfParameters()) {
            $params = $runMethod->getParameters();
            foreach ($params as $param) {
                $name = $param->getName();
                $pClass = $param->getClass();
                if ($pClass) {
                    //依赖注入
                    $args[] = $pClass->newInstance();
                } elseif (isset($vars[$name])) {
                    $args[] = $vars[$name];
                } elseif ($param->isDefaultValueAvailable()) {
                    $args[] = $param->getDefaultValue();
                }
            }
        }
        //执行方法
        $initMethod->invoke($instance, $action);
        $runMethod->invokeArgs($instance, $args);
    }

    //分析url，获得模型控制器等
    private static function Analyze()
    {
        $pathinfo = null;
        if (isset($_SERVER['PATH_INFO'])) {
            $pathinfo = $_SERVER['PATH_INFO'];
        } else {
            $var = App::config('varPathInfo');
            $var = $var ?? 's';
            if (isset($_GET[$var])) {
                $pathinfo = $_GET[$var];
                unset($_GET[$var]);
            }
        }

        if ($pathinfo) {
            $pathinfo = ltrim($pathinfo, '/');
            $suffix = App::config('viewSuffix');
            $pathinfo = str_replace($suffix, '', $pathinfo);
            $params = explode('/', $pathinfo);
            //模块绑定
            $bindModule = App::config('bindModule');
            //绑定控制器
            $bindController = App::config('bindController');
            //绑定行为
            $bindAction = App::config('bindAction');
            $index = 0;
            if ($bindModule) {
                self::$module = $bindModule;
                $index++;
            }
            if ($bindController) {
                self::$controller = $bindController;
                $index++;
            }
            if ($bindAction) {
                self::$action = $bindAction;
                $index++;
            }
            $analyze = [];
            $get = [];
            foreach ($params as $p) {
                if ($index < 3) {
                    $analyze[$index] = $p;
                    $index++;
                } else {
                    $get[] = $p;
                }
            }
            if (isset($analyze[0])) self::$module = $analyze[0];
            if (isset($analyze[1])) self::$controller = $analyze[1];
            if (isset($analyze[2])) self::$action = $analyze[2];
            $index = 0;
            $k = null;
            foreach ($get as $v) {
                if ($index) {
                    $_GET[$k] = $v;
                    $index = 0;
                } else {
                    $k = $v;
                    $index = 1;
                }
            }
        } else {
            $module = App::config('bindModule');
            $module = $module ? $module : App::config('defaultModule');
            self::$module = $module;
            $controller = App::config('bindController');
            $controller = $controller ? $controller : App::config('defaultController');
            self::$controller = $controller;
            $action = App::config('bindAction');
            $action = $action ? $action : App::config('defaultAction');
            self::$action = $action;
        }
    }

    private static function GetVars()
    {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $vars = array_merge($_GET, $_POST);
                break;
            case 'PUT':
                parse_str(file_get_contents('php://input'), $vars);
                break;
            default:
                $vars = $_GET;
        }
        return $vars;
    }
}