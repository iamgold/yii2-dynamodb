<?php

namespace iamgold\yii2\dynamodb;

/**
 * Create a request query for dynamodb
 */

use Aws\DynamoDb\Marshaler;
use yii\base\InvalidParamException;

class QueryBuilder extends \yii\base\Component
{
    /**
     * @var array $marshalerOptions
     * @see http://docs.aws.amazon.com/aws-sdk-php/v3/api/class-Aws.DynamoDb.Marshaler.html#___construct
     */
    public $marshalerOptions = [];

    /**
     * @var string $operation
     */
    private $operation = null;

    /**
     * @var array $query
     */
    private $query = [];

    /**
     * @var Marshaler $marshaler
     */
    private $marshaler = null;

    /**
     * inheridoc
     */
    public function behaviors()
    {
        return [
            ReturnSpecBehavior::className()
        ];
    }

    /**
     * Add select to query
     * @param array
     * @return $this
     */
    public function addSelect(array $columns)
    {
        $this->query['ProjectionExpression'] = (is_array($this->query['ProjectionExpression'])) ?
                                                    array_merge($this->query['ProjectionExpression'], $columns) :
                                                        $columns;

        return $this;
    }

    /**
     * Bind a query by specific query
     * @param Query $query
     * @return $this
     */
    public function build(Query $query)
    {
        $this->setOperation($query->operation)
             ->setTableName($query->from);

        $select = &$query->select;
        if (is_array($select))
            $this->addSelect($query->select);

        return $this;
    }

    /**
     * Build an delete query for dynamodb
     * @param string $tableName
     * @param array $condition the format must be key-value pair [$key=>$value].
     * @return $this
     */
    public function delete(string $tableName, $condition=[])
    {
        if (empty($tableName))
            throw new InvalidParamException("The parameter tableName must be passed.");

        if (count($condition)==0)
            throw new InvalidParamException("The parameter columns must be passed.");

        return $this->setKey($condition)
                    ->setOperation('deleteItem')
                    ->setTableName($tableName);
    }

    /**
     * Build an insert query for dynamodb
     * @param string $tableName
     * @param array $columns the format must be key-value paire [$key=>$value].
     * @return $this
     */
    public function insert(string $tableName, $columns=[])
    {
        if (empty($tableName))
            throw new InvalidParamException("The parameter tableName must be passed.");

        if (count($columns)==0)
            throw new InvalidParamException("The parameter columns must be passed.");

        return $this->setItem($columns)
                    ->setOperation('putItem')
                    ->setTableName($tableName);
    }

    /**
     * Return operation string
     * @return string
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * Return query array
     * @return array
     */
    public function getQuery()
    {
        $query = $this->query;

        if (isset($query['Key']))
            $query['Key'] = $this->getMarshaler()->marshalItem($query['Key']);

        if (isset($query['Item']))
            $query['Item'] = $this->getMarshaler()->marshalItem($query['Item']);

        if (isset($query['ProjectionExpression']))
            $query['ProjectionExpression'] = implode(', ', $query['ProjectionExpression']);

        return $query;
    }

    /**
     * Get marshaler
     * @return Marshaler
     */
    protected function getMarshaler()
    {
        if ($this->marshaler!==null)
            return $this->marshaler;

        return $this->marshaler = new Marshaler($this->marshalerOptions);
    }

    /**
     * Set Item
     * @param array $columns
     */
    public function setItem(array $columns)
    {
        $this->query['Item'] = &$columns; //$this->getMarshaler()->marshalItem($columns);
        return $this;
    }

    /**
     * Set key
     * @param array $condition
     * @return $this
     */
    public function setKey(array $condition)
    {
        $this->query['Key'] = &$condition;
        return $this;
    }

    /**
     * Set operation
     * @param string $operation
     * @return $this
     */
    public function setOperation($operation)
    {
        $this->operation = $operation;
        return $this;
    }

    /**
     * Set table name to query array
     * @param string $tableName
     * @return $this
     */
    protected function setTableName($tableName)
    {
        $this->query['TableName'] = $tableName;
        return $this;
    }
}