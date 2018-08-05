<?php
/**
 * 系统配置
 */
return [
    //应用的命名空间
    'appNamespace' => 'app',
    //命名空间与实际路径的映射
    'classMap' => [],
    //pathinfo分隔符
    'varPathInfo' => 's',
    //默认模块
    'defaultModule' => 'home',
    'bindModule' => '',//绑定模块
    //默认控制器
    'defaultController' => 'site',
    'bindController' => '',//控制器绑定
    //默认行为
    'defaultAction' => 'index',
    'bindAction' => '',//绑定行为
    //路径分隔符
    'pathSeperator' => '/',
    'viewSuffix' => '.html',
    'runtimePath' => 'runtime/',
    'log' => [
        'type' => 'File',
    ],
];