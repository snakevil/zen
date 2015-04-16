<?php
/**
 * 定义基于数据库的抽象数据访问对象组件。
 *
 * @author    Snakevil Zen <zsnakevil@gmail.com>
 * @copyright © 2014 SZen.in
 * @license   LGPL-3.0+
 */

namespace snakevil\zen\Model;

use Zen\Model as ZenModel;
use Zen\Data\Pdo as ZenPdo;

/**
 * 基于数据库的抽象数据访问对象组件。
 *
 * @package snakevil\zen
 * @version 0.1.0
 * @since   0.1.0
 */
abstract class Dao extends ZenModel\Dao\Dao
{
    /**
     * 对应数据库表名称。
     *
     * @var string
     */
    const TABLE = '';

    /**
     * 对应数据库表主键字段名。
     *
     * @var string
     */
    const PK = 'Id';

    /**
     * 数据库组件实例。
     *
     * @internal
     *
     * @var ZenPdo\IPdo
     */
    protected static $ds;

    /**
     * 绑定数据库组件实例。
     *
     * @param  ZenPdo\IPdo $pdo 数据库组件实例
     * @return void
     */
    final public static function bind(ZenPdo\IPdo $pdo)
    {
        self::$ds = $pdo;
    }

    /**
     * 获取数据源对象。
     *
     * @return ZenPdo\IPdo
     *
     * @throws ExDataSourceMissing 当数据源未绑定时
     */
    final protected function getDs()
    {
        if (!self::$ds instanceof ZenPdo\IPdo) {
            throw new ExDataSourceMissing;
        }

        return self::$ds;
    }

    /**
     * {@inheritdoc}
     *
     * @param  mixed[] $fields 实体属性值集合
     * @return scalar
     */
    final public function create($fields)
    {
        $a_terms = array(
            array(),
            array()
        );
        $a_values = array();
        foreach ($this->reverseMap($fields) as $ii => $jj) {
            $a_terms[0][] = '`' . $ii . '`';
            $a_terms[1][] = ':' . $ii;
            $a_values[':' . $ii] = $jj;
        }
        $s_sql = 'INSERT INTO `' . static::TABLE . '` (' . implode(', ', $a_terms[0]) . ') VALUES (' .
            implode(', ', $a_terms[1]) . ');';
        $this->getDs()->prepare($s_sql)->execute($a_values);
        if (isset($fields['id'])) {
            return $fields['id'];
        }
        $o_stmt = $this->getDs()->prepare('SELECT last_insert_id() AS ida, @last_insert_id AS idb;')->execute();
        $a_ret = $o_stmt->fetch();
        $o_stmt->closeCursor();

        return $a_ret['ida'] ?: $a_ret['idb'];
    }

    /**
     * {@inheritdoc}
     *
     * @param  scalar  $id 编号
     * @return mixed[]
     *
     * @throws ExRecordNotFound 当数据记录不存在时
     */
    final public function read($id)
    {
        $s_sql = 'SELECT * FROM `' . static::TABLE . '` WHERE `' . static::PK . '` = ?';
        $o_stmt = $this->getDs()->prepare($s_sql)->execute(array($id));
        $a_ret = $o_stmt->fetch();
        $o_stmt->closeCursor();

        if (!$a_ret) {
            throw new ExRecordNotFound(static::TABLE, $id);
        }

        return $this->cast($this->map($a_ret));
    }

    /**
     * {@inheritdoc}
     *
     * @param  scalar  $id     编号
     * @param  mixed[] $fields 新的属性值集合
     * @return bool
     */
    final public function update($id, $fields)
    {
        $a_terms = $a_values = array();
        foreach ($this->reverseMap($fields) as $ii => $jj) {
            $a_terms[] = '`' . $ii . '` = :' . $ii;
            $a_values[':' . $ii] = $jj;
        }
        $s_sql = 'UPDATE `' . static::TABLE . '` SET ' . implode(', ', $a_terms)
            . ' WHERE `' . static::PK . '` = :old_id';
        $a_values[':old_id'] = $id;
        $this->getDs()->prepare($s_sql)->execute($a_values);

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @param  scalar $id 编号
     * @return bool
     */
    public function delete($id)
    {
        $s_sql = 'DELETE FROM `' . static::TABLE . '` WHERE `' . static::PK . '` = ?';
        $this->getDs()->prepare($s_sql)->execute(array($id));

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @param  array[] $conditions 条件
     * @param  int     $limit      可选。集合大小限制
     * @param  int     $offset     可选。集合起始偏移量
     * @return int
     */
    public function count($conditions, $limit = 0, $offset = 0)
    {
        $a_values = $this->parseConditions($conditions);
        $s_sql = 'SELECT COUNT(m.`' . static::PK . '`) quantity FROM `' . static::TABLE . '` m'
            . array_shift($a_values);
        $o_stmt = $this->getDs()->prepare($s_sql)->execute($a_values);
        $i_quantity = $o_stmt->fetchColumn();
        $o_stmt->closeCursor();
        if (0 < $offset) {
            $i_quantity -= $offset;
        }
        if (0 < $limit) {
            $i_quantity = min($limit, $i_quantity);
        }

        return $i_quantity;
    }

    /**
     * 将条件转化为可用地 SQL 片段。
     *
     * @param  array[] $conditions 过滤条件集合
     * @param  bool[]  $orders     可选。排序条件集合
     * @return array
     *
     * @throws ExTooManyForeignTables 当外联表超过一张时
     */
    final protected function parseConditions($conditions, $orders = array())
    {
        if (empty($conditions) && empty($orders)) {
            return array('');
        }
        $s_clause = '';
        $a_wterms = $a_values = array();
        foreach ($this->reverseMap($conditions) as $ii => $jj) {
            list($s_term, $s_join) = $this->parseTerm($ii);
            if ($s_join) {
                if ($s_clause && $s_clause != $s_join) {
                    throw new ExTooManyForeignTables(static::TABLE, $conditions);
                }
                $s_clause = $s_join;
            }
            foreach ($jj as $kk) {
                switch ($kk[0]) {
                    case ZenModel\ISet::OP_IN:
                    case ZenModel\ISet::OP_NI:
                        $a_wterms[] = $s_term . $kk[0] . ' (' . implode(', ', array_fill(0, count($kk[1]), '?')) . ')';
                        array_splice($a_values, count($a_values), 0, $kk[1]);
                        break;
                    case ZenModel\ISet::OP_BT:
                        $a_wterms[] = $s_term . ' > ? AND ' . $s_term . ' < ?';
                        $a_values[] = $kk[1][0];
                        $a_values[] = $kk[1][1];
                        break;
                    case ZenModel\ISet::OP_NB:
                        $a_wterms[] = '(' . $s_term . ' <= ? OR ' . $s_term . ' >= ?)';
                        $a_values[] = $kk[1][0];
                        $a_values[] = $kk[1][1];
                        break;
                    case ZenModel\ISet::OP_LK:
                    case ZenModel\ISet::OP_NL:
                        $a_wterms[] = $s_term . $kk[0] . ' ?';
                        $a_values[] = str_replace(array('\\*', '*'), array('*', '%'), $kk[1]);
                        break;
                    default:
                        $a_wterms[] = $s_term . $kk[0] . ' ?';
                        $a_values[] = $kk[1];
                }
            }
        }
        $a_oterms = array();
        foreach ($this->reverseMap($orders) as $ii => $jj) {
            list($s_term, $s_join) = $this->parseTerm($ii);
            if ($s_join) {
                if ($s_clause) {
                    throw new ExTooManyForeignTables(static::TABLE, $orders);
                }
                $s_clause = $s_join;
            }
            $a_oterms[] = $s_term . ($jj ? 'ASC' : 'DESC');
        }
        if (!empty($a_wterms)) {
            $s_clause .= ' WHERE ' . implode(' AND ', $a_wterms);
        }
        if (!empty($a_oterms)) {
            $s_clause .= ' ORDER BY ' . implode(', ', $a_oterms);
        }
        array_unshift($a_values, $s_clause);

        return $a_values;
    }

    /**
     * 处理字段名。
     *
     * @param  string $field 字段名
     * @return array
     */
    final protected function parseTerm($field)
    {
        if (preg_match('#^(?P<table>[~\w]+)\.(?P<field>\w+)/(?P<using>\w+)(?:|=(?P<on>\w+))$#', $field, $a_matches)) {
            $s_clause = ' LEFT JOIN `' . $a_matches['table'] . '` f';
            if (isset($a_matches['on'])) {
                $s_clause .= ' ON m.`' . $a_matches['on'] . '` = f.`' . $a_matches['using'] . '`';
            } else {
                $s_clause .= ' USING(`' . $a_matches['using'] . '`)';
            }
            $s_term = 'f.`' . $a_matches['field'] . '` ';
        } else {
            $s_clause = false;
            $s_term = 'm.`' . $field . '` ';
        }

        return array($s_term, $s_clause);
    }

    /**
     * {@inheritdoc}
     *
     * @param  array[] $conditions 条件
     * @param  array[] $orders     可选。排序方案
     * @param  int     $limit      可选。集合大小限制
     * @param  int     $offset     可选。集合起始偏移量
     * @return array[]
     */
    public function query($conditions, $orders = array(), $limit = 0, $offset = 0)
    {
        $a_values = $this->parseConditions($conditions, $orders);
        $s_sql = 'SELECT m.* FROM `' . static::TABLE . '` m' . array_shift($a_values);
        if (0 < $offset) {
            if (1 > $limit) {
                $limit = 999;
            }
        }
        if (0 < $offset || 0 < $limit) {
            $s_sql .= ' LIMIT ' . $offset . ', ' . $limit;
        }
        $o_stmt = $this->getDs()->prepare($s_sql)->execute($a_values);
        $a_ret = $o_stmt->fetchAll();
        $o_stmt->closeCursor();

        return $this->cast($this->map($a_ret));
    }
}
