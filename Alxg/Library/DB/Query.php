<?php

namespace Alxg\Library\DB;


use Alxg\Library\DB\Traits\Where;

class Query
{
    use Where;
    private $prefix = ''; //表前缀

    private $update = 'UPDATE';
    private $insert = 'INSERT INTO';
    private $delete = 'DELETE ';
    private $select = 'SELECT';
    private $field = '*';
    private $table = '';
    private $where = '';
    private $order = '';
    private $group = '';
    private $join = '';

    private $limit = null;
    private $offset = null;

    private $lastSql = null;

    /**
     * 语句模式
     * 1，select,
     * 2, insert,
     * 3, update,
     * 4, delete
     * @var int
     */
    private $sqlType = 0;

    public function __construct(string $prefix = null)
    {
        $this->prefix = $prefix;
    }

    /**
     * @param string $field
     * @return Query
     */
    public function select($field = null)
    {
        $this->field($field);
        $this->sqlType = 1;
        return $this;
    }

    public function insert()
    {
        $this->sqlType = 2;
        return $this;
    }

    public function update()
    {
        $this->sqlType = 3;
        return $this;
    }

    public function delete()
    {
        $this->sqlType = 4;
        return $this;
    }

    /**
     * @param null $field
     * @return $this
     */
    public function field($field = null)
    {
        if (is_string($field) && $field != '*') {
            $field = explode(',', $field);
        }
        $field = '`' . implode('`,`', $field) . '`';
        $this->field = $field;
        return $this;
    }

    /**
     * @param string $table
     * @return $this
     */
    public function from(string $table)
    {
        if ($this->prefix) $table = $this->prefix . $table;
        $this->table = '`'.$table.'`';
        return $this;
    }

    public function where(){

    }

    public function andWhere(){

    }

    public function orWhere(){

    }

    /**
     * 获得最后的sql语句
     * @return null
     */
    public function getLastSql()
    {
        $lastSql = $this->lastSql;
        if($lastSql === null){
            $lastSql = $this->buildSql();
        }
        return $lastSql;
    }

    /**
     * 构建sql语句
     * @return null
     */
    protected function buildSql()
    {
        $sql = null;
        switch ($this->sqlType){
            case 1:
                $sql = $this->buildSelectSql();
                break;
            case 2:
                $sql = $this->buildInsertSql();
                break;
            case 3:
                $sql = $this->buildUpdateSql();
                break;
            case 4:
                $sql = $this->buildDeleteSql();
                break;
        }
        $this->lastSql = $sql;
        return $sql;
    }


    /**
     * 构建查询语句
     * @return null
     */
    protected function buildSelectSql(){
        $sql = null;
        $sqlArr = [
            $this->select,
            $this->field,
            'FROM',
            $this->table,
            $this->join,
            $this->where,
            $this->limit,
            $this->offset,
            $this->group,
            $this->order
        ];
        $sql = implode(' ',$sqlArr);
        return $sql;
    }

    /**
     * 构建插入语句
     * @return null
     */
    protected function buildInsertSql(){
        $sql = null;

        return $sql;
    }

    /**
     * 构建更新语句
     * @return null
     */
    protected function buildUpdateSql(){
        $sql = null;

        return $sql;
    }

    /**
     * 构建删除语句
     * @return null
     */
    protected function buildDeleteSql(){
        $sql = null;

        return $sql;
    }
}