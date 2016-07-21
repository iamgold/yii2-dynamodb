<?php

namespace iamgold\yii2\dynamodb;

/**
 * This is a query wrapper for dynamodb;
 *
 * @author Eric Huang <iamgold0105@gmail.com>
 * @version 0.1.0
 * @since 0.1.0
 */

class Query extends \yii\base\Component implements \yii\db\QueryInterface
{
    use \yii\db\QueryTrait;


    /**
     * @var array the columns being selected. For example, `['id', 'name']`.
     * This is used to construct the SELECT clause in a SQL statement. If not set, it means selecting all columns.
     * @see select()
     */
    public $select;

    /**
     * @var array the table(s) to be selected from. For example, `['user', 'post']`.
     * This is used to construct the FROM clause in a SQL statement.
     * @see from()
     */
    public $from;
    /**
     * @var array how to group the query results. For example, `['company', 'department']`.
     * This is used to construct the GROUP BY clause in a SQL statement.
     */

    /**
     * @var array list of query parameter values indexed by parameter placeholders.
     * For example, `[':name' => 'Dan', ':age' => 31]`.
     */
    public $params = [];



    /**
     * Create Command
     * @param Connection $connection
     * @return Command
     */
    public function createCommand($connection=null)
    {}

    /**
     * Prepare
     * @param QueryBuilder $builder
     * @return $this
     */
    public function prepare($builder)
    {
        return $this;
    }

    /**
     * inheritdoc
     */
    public function all($db=null)
    {
        $rows = $this->createCommand($db)->queryAll();
        return $this->populate($rows);
    }

    /**
     * Populate
     * @param array $rows
     * @return array
     */
    public function populate($rows)
    {
        return $rows;
    }

    /**
     * inheritdoc
     */
    public function one($db=null)
    {
        return $this->createCommand($db)->queryOne();
    }

    /**
     * inheritdoc
     */
    public function count($q='*', $db=null)
    {
        throw new \yii\base\NotSupportedException("The method (count) is not support for this implement.", 500);
    }

    /**
     * inheritdoc
     */
    public function exists($db=null)
    {
        throw new \yii\base\NotSupportedException("The method (exists) is not support for this implement.", 500);
    }

    /**
     * Select
     * @param string $option
     * @return $this
     */
    public function select($columns, $option=null)
    {
        return $this;
    }

    /**
     * Add select
     * @param string|array $columns
     * @return $this
     */
    public function addSelect($columns)
    {
        return $this;
    }

    /**
     * From a table
     * @param string $table
     * @return $this
     */
    public function from($table)
    {
        $this->from = $table;
        return $this;
    }

    /**
     * Where
     * @param string|array $condition
     * @param array $params
     * @return $this
     */
    public function where($condition, $params=[])
    {
        return $this;
    }

    /**
     * Adds an additional WHERE condition to the existing one.
     * The new condition and the existing one will be joined using the 'AND' operator.
     * @param string|array|Expression $condition the new WHERE condition. Please refer to [[where()]]
     * on how to specify this parameter.
     * @param array $params the parameters (name => value) to be bound to the query.
     * @return $this the query object itself
     * @see where()
     * @see orWhere()
     */
    public function andWhere($condition, $params=[])
    {
        if ($this->where===null) {
            $this->where = $condition;
        } else {
            $this->where = ['and', $this->where, $condition];
        }

        $this->addParams($params);
        return $this;
    }

    /**
     * Adds an additional WHERE condition to the existing one.
     * The new condition and the existing one will be joined using the 'OR' operator.
     * @param string|array|Expression $condition the new WHERE condition. Please refer to [[where()]]
     * on how to specify this parameter.
     * @param array $params the parameters (name => value) to be bound to the query.
     * @return $this the query object itself
     * @see where()
     * @see andWhere()
     */
    public function orWhere($condition, $params = [])
    {
        if ($this->where === null) {
            $this->where = $condition;
        } else {
            $this->where = ['or', $this->where, $condition];
        }
        $this->addParams($params);
        return $this;
    }

    /**
     * And filter compare
     *
     * @param string $name the column name.
     * @param string $value the column value optionally prepended with the comparison operator.
     * @param string $defaultOperator The operator to use, when no operator is given in `$value`.
     * Defaults to `=`, performing an exact match.
     * @return $this The query object itself
     */
    public function andFilterCompare($name, $value, $defaultOperator = '=')
    {
        if (preg_match("/^(<>|>=|>|<=|<|=)/", $value, $matches)) {
            $operator = $matches[1];
            $value = substr($value, strlen($operator));
        } else {
            $operator = $defaultOperator;
        }
        return $this->andFilterWhere([$operator, $name, $value]);
    }

    /**
     * Sets the parameters to be bound to the query.
     * @param array $params list of query parameter values indexed by parameter placeholders.
     * For example, `[':name' => 'Dan', ':age' => 31]`.
     * @return $this the query object itself
     * @see addParams()
     */
    public function params($params)
    {
        $this->params = $params;
        return $this;
    }


    /**
     * Adds additional parameters to be bound to the query.
     * @param array $params list of query parameter values indexed by parameter placeholders.
     * For example, `[':name' => 'Dan', ':age' => 31]`.
     * @return $this the query object itself
     * @see params()
     */
    public function addParams($params)
    {
        if (!empty($params)) {
            if (empty($this->params)) {
                $this->params = $params;
            } else {
                foreach ($params as $name => $value) {
                    if (is_int($name)) {
                        $this->params[] = $value;
                    } else {
                        $this->params[$name] = $value;
                    }
                }
            }
        }
        return $this;
    }


    /**
     * Creates a new Query object and copies its property values from an existing one.
     * The properties being copies are the ones to be used by query builders.
     * @param Query $from the source query object
     * @return Query the new Query object
     */
    public static function create($from)
    {
        return new self([
            'where' => $from->where,
            'limit' => $from->limit,
            'offset' => $from->offset,
            'orderBy' => $from->orderBy,
            'indexBy' => $from->indexBy,
            'select' => $from->select,
            'selectOption' => $from->selectOption,
            'distinct' => $from->distinct,
            'from' => $from->from,
            'groupBy' => $from->groupBy,
            'join' => $from->join,
            'having' => $from->having,
            'union' => $from->union,
            'params' => $from->params,
        ]);
    }
}