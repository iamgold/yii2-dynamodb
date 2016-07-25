<?php

namespace iamgold\yii2\dynamodb;

/**
 * It's a wrapper class for Aws\Result:ConsumedCapacity
 */

class ConsumedCapacityWrapper extends \yii\base\Object
{
    /**
     * @var string $tableName
     */
    public $TableName;

    /**
     * @var float $CapacityUnits
     */
    public $CapacityUnits;

    /**
     * @var array $Table
     */
    public $Table = [];

    /**
     * Return capacity units of table
     * @return float|false
     */
    public function getTableUnit()
    {
        return \yii\helpers\ArrayHelper::getValue($this->Table, 'CapacityUnits', false);
    }
}