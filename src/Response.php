<?php

namespace iamgold\yii2\dynamodb;

/**
 * It's a manipulate class for Aws\Result named Response
 */

class Response
{
    /**
     * @var Aws\Result
     */
    public $result;

    /**
     * Construct
     */
    public function __construct(\Aws\Result $result)
    {
        $this->result = &$result;
    }

    /**
     * Return original Aws\Result
     * @return Aws\Result
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Return ConsumedCapacity Wrapper
     * @return ConumedCapcityWrapper|false
     */
    public function getConsumedCapacity()
    {
        if (!$this->result->hasKey('ConsumedCapacity'))
            return false;

        return new ConsumedCapacityWrapper($this->result->get('ConsumedCapacity'));
    }

    /**
     * Return php navtive array of result array
     * @return array|false
     */
    public function getAttributes()
    {
        $marshaler = new \Aws\Marshaler;
        $attrs = ($this->result->Attributes) ? $marshaler->unmarshalItem($this->result->Attributes) : false;

        return $attrs;
    }
}