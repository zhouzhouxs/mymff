<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors',0);
ini_set('date.timezone','PRC');

//开发环境下请设置为true
define('ALXG_DEBUG',false);

//必须定义，当前访问的根目录
define("APP_PATH", dirname(__FILE__));

require './vendor/autoload.php';
require './Alxg/App.php';

\Alxg\App::LoadConfig('./App/common/conf/config.php');
\Alxg\App::LoadConfig('./App/admin/conf/config.php');

\Alxg\App::run();
