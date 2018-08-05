<?php
namespace Alxg\Library\DB\Driver;


interface Driver
{
    //创建连接实体
    public function connect($config);
    //执行语句
    public function query($sql,$bindParams);
}