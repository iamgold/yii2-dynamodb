<?php

namespace iamgold\yii2\dynamodb;

/**
 * This is a command for dynamodb;
 *
 * @author Eric Huang <iamgold0105@gmail.com>
 * @version 0.1.0
 * @since 0.1.0
 */

class Command extends \yii\base\Component
{
    /**
     * @var Connection the DB connection that this command is associated with
     */
    public $db = null;

    /**
     * @var string $operation a DynamoDb API method
     * @see http://docs.aws.amazon.com/aws-sdk-php/v3/api/class-Aws.DynamoDb.DynamoDbClient.html
     */
    public $operation = null;

    /**
     * @var array $query a DynamoDb query array
     * @see http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-dynamodb-2012-08-10.html
     */
    public $query = [];

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
     * Insert a item
     * @param string $tableName
     * @param array $columns [key => value]
     * @return $this
     */
    public function insert(string $tableName, array $columns)
    {
        $builder = $this->db->getQueryBuilder()->insert($tableName, $columns);
        $this->query = $builder->getQuery();
        $this->operation = $builder->getOperation();
        return $this;
    }

    /**
     * Execute a query using DynamoDb API, and return response accoding its condition, ex: ReturnValues etc.
     * @param Connection $db
     * @return array
     */
    public function execute($db=null)
    {
        try {
            if ($db!==null) {
                if (!($db instanceOf Connection))
                    throw new \yii\base\UnknownClassException("This db instance is not a iamgold\yii2\dynamodb\Connection");
            } else
                $db = $this->getDb();

            if (empty($this->operation))
                throw new \Exception("This Opertion property is undefined");

            return $db->getClient()->{$this->operation}($this->query);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get default database connection
     * @retrun Connection
     */
    public function getDb()
    {
        if ($this->db!==null)
            return $this->db;

        return $this->db = \Yii::$app->get('dynamodb');
    }
}