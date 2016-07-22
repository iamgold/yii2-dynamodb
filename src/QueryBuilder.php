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
     * Build an insert query for dynamodb
     *
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

        $this->query['Item'] = $this->getMarshaler()->marshalItem($columns);
        $this->operation = 'putItem';

        return $this->setTableName($tableName);
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
        return $this->query;
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