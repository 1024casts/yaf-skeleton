<?php
namespace Core\Mvc\Model;

use Core\Databases\MedooWrapper;

/**
 * Class DbTrait
 *
 * @property MedooWrapper $db
 */
trait DbTrait
{
    protected $dbname;
    protected $table;

    protected $shardFormat = '%s%02x';
    protected $shardPrefix;
    protected $shardFactor = 256;

    public function setDbName($dbname)
    {
        $this->dbname = $dbname;
    }

    public function getDbName()
    {
        return $this->dbname;
    }

    public function setTable($table)
    {
        $this->table = $table;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function getShardTable()
    {
        if (!$this->shardPrefix) {
            $this->shardPrefix = $this->table . '_';
        }

        return sprintf($this->shardFormat, $this->shardPrefix, crc32(implode('_', func_get_args())) % $this->shardFactor);
    }

    protected function adjustWhere(&$join, &$columns = null, &$where = null)
    {
        $joinKey = is_array($join) ? array_keys($join) : [];

        if (isset($joinKey[0]) && strpos($joinKey[0], '[') === 0) {
            // using join, params is in normal sequence
            $this->doAdjustWhere($where);
        } elseif ($columns !== null) {
            $this->doAdjustWhere($columns);
        }
    }

    protected function doAdjustWhere(&$where)
    {
        if (empty($where) || !is_array($where) || count($where) <= 0) {
            return;
        }

        $filter = ['AND', 'OR', 'GROUP', 'HAVING', 'ORDER', 'LIMIT', 'MATCH'];
        $whereAnd = [];
        $tmpWhereAnd = [];
        foreach ($where as $k => $v) {
            if (!in_array($k, $filter)) {
                $whereAnd[$k] = $v;
                $tmpWhereAnd[$k] = $v;

                unset($where[$k]);
            }
        }
        if (count($whereAnd) > 1) {
            if (isset($where['AND'])) {
                $where['AND'] = array_merge($where['AND'], $whereAnd);
            } else {
                $where['AND'] = $whereAnd;
            }
        } else {
            $where = array_merge($where, $tmpWhereAnd);
        }

        if (isset($where['LIMIT']) && is_array($where['LIMIT'])) {
            foreach ($where['LIMIT'] as &$v) {
                $v = (int)$v;
            }
        }
    }

    public function select($join, $columns = null, $where = null)
    {
        $this->adjustWhere($join, $columns, $where);

        return $this->db->select($this->table, $join, $columns, $where);
    }

    public function get($join = null, $column = null, $where = null)
    {
        $this->adjustWhere($join, $column, $where);

        return $this->db->get($this->table, $join, $column, $where);
    }

    public function has($join = null, $where = null)
    {
        $this->adjustWhere($join, $where);

        return $this->db->has($this->table, $join, $where);
    }

    public function count($join = null, $column = null, $where = null)
    {
        $this->adjustWhere($join, $column, $where);

        return $this->db->count($this->table, $join, $column, $where);
    }

    public function sum($join = null, $column = null, $where = null)
    {
        $this->adjustWhere($join, $column, $where);

        return $this->db->sum($this->table, $join, $column, $where);
    }

    public function query($statement)
    {
        return $this->db->query($statement);
    }

    public function exec($statement)
    {
        return $this->db->exec($statement);
    }

    public function update($row, $where)
    {
        $this->doAdjustWhere($where);

        return $this->db->update($this->table, $row, $where);
    }

    public function insert($rows)
    {
        return $this->db->insert($this->table, $rows);
    }

    public function delete($where = null)
    {
        $this->doAdjustWhere($where);

        return $this->db->delete($this->table, $where);
    }

    public function replace($rows, $updates, $where = null)
    {
        $this->doAdjustWhere($where);

        return $this->db->replace($this->table, $rows, $updates, $where);
    }

    /**
     * 主键或唯一键不重复则插入, 存在则在原有数据基础上做更新操作
     *
     * @param array $rows
     * @param array $updates
     *
     * @return bool|int
     */
    public function upsert($rows, $updates = [])
    {
        return $this->db->upsert($this->table, $rows, $updates);
    }

    /**
     * 开启事务
     *
     * @return bool
     */
    public function beginTransaction()
    {
        return $this->db->beginTransaction();
    }

    /**
     * 回滚事务
     *
     * @return bool
     */
    public function rollback()
    {
        return $this->db->rollBack();
    }

    /**
     * 执行事务
     *
     * @return bool
     */
    public function commit()
    {
        return $this->db->commit();
    }

    /**
     * 通过$action执行事务
     *
     * @param callable $action
     * @param array $params
     * @param mixed $result
     *
     * @return bool
     */
    public function action(callable $action, $params = [], &$result = null)
    {
        return $this->db->action($action, $params, $result);
    }
}
