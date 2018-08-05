<?php
/**
 * 公共配置
 */

return [
    'classMap' => [
        'app' => 'App',
    ],
    'bindModule' => 'admin',
    //数据库配置
    'db.mysql' => [
        'dsn' => 'mysql:host=127.0.0.1;dbname=cells;port=3306',
        'user' => 'root',
        'password' => '',
        'prefix' => 'cell_',
        'options' => [
            \PDO::ATTR_PERSISTENT => true,
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        ]
    ]
];