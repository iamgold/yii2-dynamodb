<?php

namespace iamgold\yii2\dynamodb;

/**
 * This is a query wrapper for dynamodb;
 *
 * @author Eric Huang <iamgold0105@gmail.com>
 * @version 0.1.0
 * @since 0.1.0
 */

class Query extends \yii\db\Query implements \yii\db\QueryInterface
{
    /**
     * @var string $operator
     * @see http://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_Operations.html
     */
    public $operation = 'getItem';

    /**
     * @var array $allowOperators
     */
    public $allowOperators = ['getItem', 'batchGetItem', 'scan', 'query'];

    /**
     * @var bool $consistentRead
     */
    public $consistentRead = false;

    /**
     * Create Command
     * @param string $operation
     * @param Connection $connection
     * @see http://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_Operations.html
     * @return Command
     */
    public function createCommand($operation=null, $connection=null)
    {
        if ($connection===null) {
            $connection = \Yii::$app->get('dynamodb');
        }

        $operation = $this->getOperation($operation);

        $builder = $connection->getQueryBuilder()->build($this);
        return $connection->createCommand($operation, $builder->getQuery());
    }

    /**
     * Get operation
     * @param string $operation
     * @return string
     */
    public function getOperation($operation=null)
    {
        if ($operation===null)
            $operation = $this->operation;
        else
            $this->operation = $operation;

        if (!in_array($operation, $this->allowOperators))
            throw new \yii\base\InvalidParamException("This operator ($operation) is not support");

        return $this->operation;
    }

    /**
     * Set consistanRead
     * @param bool $value
     * @return $this
     */
    public function setConsistanRead($value)
    {
        if (!is_bool($value))
            throw new \yii\base\InvalidParamException("The argument type must be boolean");

        $this->consistanRead = $value;

        return $this;
    }
}