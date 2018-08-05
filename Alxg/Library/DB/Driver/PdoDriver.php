<?php

namespace Alxg\Library\DB\Driver;

use PDO;

class PdoDriver
{
    /**
     * 所有配置的实例
     * @var array
     */
    private static $Instances = [];
    /**
     * 当前配置的连接实例
     * @var
     */
    private $conn;

    /**
     * 当前Pdostatement实例
     * @var
     */
    private $stmt;

    /**
     * PdoDriver constructor.
     * @param $dsn
     * @param $user
     * @param $passwd
     * @param array $options
     */
    private function __construct($dsn, $user, $passwd, $options = [])
    {
        $this->conn = new PDO($dsn, $user, $passwd, $options);
    }

    /**
     * 根据配置获得实例
     * @param $dsn
     * @param $user
     * @param $passwd
     * @param array $options
     * @return PdoDriver|bool|mixed
     */
    public static function connect($dsn, $user, $passwd, $options = [])
    {
        $config = [$dsn, $user, $passwd, $options];
        $instance = self::isRegister($config);
        if (!$instance) {
            $instance = new self($dsn, $user, $passwd, $options);
            self::Register($config, $instance);
        }
        return $instance;
    }

    /**
     * 判断当前配置是否已获得实例
     * @param $array
     * @return bool|mixed
     */
    private static function isRegister($array)
    {
        $key = md5(serialize($array));
        if (isset(self::$Instances[$key])) {
            return self::$Instances[$key];
        }
        return false;
    }

    /**
     * 注册当前配置的实例
     * @param $array
     * @param $instance
     * @return bool
     */
    private static function Register($array, $instance)
    {
        $key = md5(serialize($array));
        self::$Instances[$key] = $instance;
        return true;
    }

    /**
     * @param string $statement
     * @return $this
     */
    public function query(string $statement)
    {
        $stmt = $this->conn->prepare($statement);
        $this->stmt = $stmt;
        return $this;

    }

    /**
     * @param $parameter
     * @param $variable
     * @param mixed ...$opt
     * @return $this
     */
    public function bindParam($parameter, &$variable, ...$opt)
    {
        $this->stmt->bindParam($parameter, $variable, ...$opt);
        return $this;
    }

    /**
     * @param $parameter
     * @param $value
     * @param int $data_type
     * @return $this
     */
    public function bindValue($parameter, $value, $data_type = PDO::PARAM_STR)
    {
        $this->stmt->bindValue($parameter, $value, $data_type);
        return $this;
    }

    public function execute($input_parameters = [])
    {
        $this->stmt->execute($input_parameters);
        return $this;
    }

    /**
     * @param int $fetch_style
     * @param int $cursor_orientation
     * @param int $cursor_offset
     * @return mixed
     */
    public function fetch(int $fetch_style = PDO::FETCH_ASSOC, int $cursor_orientation = PDO::FETCH_ORI_NEXT, int $cursor_offset = 0)
    {
        $row = $this->stmt->fetch($fetch_style, $cursor_orientation, $cursor_offset);
        return $row;
    }
}