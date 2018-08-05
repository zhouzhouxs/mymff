<?php

namespace Alxg;

use Alxg\Library\Log\Log;
use Whoops\{
    Run, Handler\PrettyPageHandler
};

class AlxgTrace
{

    public static function Register()
    {
        if (ALXG_DEBUG) {
            $whoops = new Run();
            $whoops->pushHandler(new PrettyPageHandler());
            $whoops->register();
        } else {
            register_shutdown_function(AlxgTrace::class . '::HandleShutdown');
            set_error_handler(AlxgTrace::class . '::HandleError');
            set_exception_handler(AlxgTrace::class . '::HandleException');
        }
    }

    /**
     * 错误处理
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     * @return bool
     * @throws \Exception
     */
    public static function HandleError($errno, $errstr, $errfile, $errline)
    {
        $self = new self();
        switch ($errno) {
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
            case E_RECOVERABLE_ERROR:
                $self->ToHandleError('ERROR', $errstr, $errfile, $errline);
                break;
            case E_WARNING:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
            case E_USER_WARNING:
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                $self->ToHandleWarning('WARNING', $errstr, $errfile, $errline);
                break;
            case E_NOTICE:
            case E_USER_NOTICE:
            case E_STRICT:
                $self->ToHandleNotice("NOTICE", $errstr, $errfile, $errline);
                break;
            default:
                $self->ToHandleError('ERROR UNKNOWN', $errstr, $errfile, $errline);
                break;
        }
        return true;
    }

    /**
     * 异常处理
     * @param \Throwable $e
     * @return bool
     * @throws \Exception
     */
    public static function HandleException(\Throwable $e)
    {
        $self = new self();
        $self->ToHandleException($e);
        return true;
    }

    /**
     *  脚本终止运行后的处理方法
     * @throws \Exception
     */
    public static function HandleShutdown()
    {
        $e = error_get_last();
        if ($e) {
            //发生fatal error错误时，工作路径可能发生改变，用相对路径可能就会发生错误，比如，日志目录不正确了
            echo ' ';
            self::HandleError($e['type'], $e['message'], $e['file'], $e['line']);
        }
        return true;
    }

    /**
     * 处理错误类信息
     * @param $level
     * @param $errstr
     * @param $errfile
     * @param $errline
     * @throws \Exception
     */
    private function ToHandleError($level, $errstr, $errfile, $errline)
    {
        $trace = $this->getTrace();
        $trace = $this->createMsgArray($level, $trace, $errstr, $errfile, $errline);
        Log::Init(Runtime::getRuntimePath() . Runtime::LOG)->add($trace, Log::LEVEL_ERROR)->save();
        exit(0);
    }

    /**
     * 处理警告类信息
     * @param $level
     * @param $errstr
     * @param $errfile
     * @param $errline
     * @throws \Exception
     */
    private function ToHandleWarning($level, $errstr, $errfile, $errline)
    {
        $trace = $this->getTrace();
        $trace = $this->createMsgArray($level, $trace, $errstr, $errfile, $errline);
        Log::Init(Runtime::getRuntimePath() . Runtime::LOG)->add($trace, Log::LEVEL_WARNING)->save();
    }

    /**
     * 处理提示类信息
     * @param $level
     * @param $errstr
     * @param $errfile
     * @param $errline
     * @throws \Exception
     */
    private function ToHandleNotice($level, $errstr, $errfile, $errline)
    {
        $trace = $this->getTrace();
        $trace = $this->createMsgArray($level, $trace, $errstr, $errfile, $errline);
        Log::Init(Runtime::getRuntimePath() . Runtime::LOG)->add($trace, Log::LEVEL_NOTICE)->save();
    }

    /**
     * 异常处理
     * @param \Throwable $e
     * @throws \Exception
     */
    private function ToHandleException(\Throwable $e)
    {
        $errno = $e->getCode();
        $errstr = $e->getMessage();
        $errfile = $e->getFile();
        $errline = $e->getLine();
        $str = $e->getTraceAsString();
        $patter = ['/#\d+/', '/\n/'];
        $str = preg_replace($patter, ['##$0', ''], $str);
        $trace = explode('##', $str);
        $trace = $this->createMsgArray('EXCEPTION', $trace, $errstr, $errfile, $errline);
        Log::Init(Runtime::getRuntimePath() . Runtime::LOG)->add($trace, Log::LEVEL_EXCEPTION)->save();
        exit();
    }

    /**
     * 格式化错误信息
     * @param $level
     * @param $trace
     * @param $errstr
     * @param $errfile
     * @param $errline
     * @return array
     */
    private function createMsgArray($level, $trace, $errstr, $errfile, $errline)
    {
        $arr[0] = "[$level] : {$errstr}";
        $arr[1] = "[File At] : $errfile : At Line $errline";
        $trace = array_merge($arr, $trace);
        return $trace;
    }

    /**
     * @return array
     */
    private function getTrace()
    {
        while (ob_get_level()) {
            ob_end_flush();
        }
        ob_start();
        debug_print_backtrace();
        $str = ob_get_clean();
        $patter = ['/#\d+/', '/\n/'];
        $str = preg_replace($patter, ['##$0', ''], $str);
        $trace = explode('##', $str);
        return $trace;
    }
}
