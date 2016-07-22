<?php

namespace iamgold\yii2\dynamodb;

/**
 * This behavior prepare several method to owner instance for setting return specs
 *
 * @author Eric Huang <iamgold0105@gmail.com>
 * @since 0.2.0
 */

use yii\base\NotSupportedException;

class ReturnSpecBehavior extends \yii\base\Behavior
{
    /**
     * @var string ConsumedCapacityPattern
     */
    const ConsumedCapacityPattern = '/^(INDEXES|TOTAL|NONE)$/';

    /**
     * @var string ConsumedCapacityKey
     */
    const ConsumedCapacityKey = 'ReturnConsumedCapacity';

    /**
     * @var string ConsumedCapacityPattern
     */
    const ItemCollectionMetricsPattern = '/^(SIZE|NONE)$/';

    /**
     * @var string ItemCollectionMetricsKey
     */
    const ItemCollectionMetricsKey = 'ReturnItemCollectionMetrics';

    /**
     * @var string ValuesPattern
     */
    const ValuesPattern = '/^(NONE|ALL_OLD|UPDATED_OLD|ALL_NEW|UPDATED_NEW)$/';

    /**
     * @var string ValuesKey
     */
    const ValuesKey = 'ReturnValues';

    /**
     * Set Consumed Capacity option
     * @param string $option
     * @return $owner
     */
    public function setReturnConsumedCapacity($option='NONE')
    {
        try {
            if (preg_match(static::ConsumedCapacityPattern, $option)==false)
                throw new NotSupportedException("Ths argument option is fatal value.");

            return $this->setReturnOption(static::ConsumedCapacityKey, $option);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Set Item Collection Metrics option
     * @param string $option
     * @return $owner
     */
    public function setReturnItemCollectionMetrics($option='NONE')
    {
        try {
            if (preg_match(static::ItemCollectionMetricsPattern, $option)==false)
                throw new NotSupportedException("Ths argument option is fatal value.");

            return $this->setReturnOption(static::ItemCollectionMetricsKey, $option);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Set Return Values option
     * @param string $option
     * @return $owner
     */
    public function setReturnValues($option='NONE')
    {
        try {
            if (preg_match(static::ValuesPattern, $option)==false)
                throw new NotSupportedException("Ths argument option is fatal value.");

            return $this->setReturnOption(static::ValuesKey, $option);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Reset return options by specific key
     * @param string $key
     * @return $owner
     */
    public function resetReturnOption($key=null)
    {
        if (empty($key))
            return $this->owner;

        $methodName = 'set' . ucfirst($key);
        if (!method_exists($this, $methodName))
            throw new NotSupportedException("Ths argument key is not support.");

        if (isset($this->owner->query[$key]))
            unset($this->owner->query[$key]);

        return $this->owner;
    }

    /**
     * Reset all return options
     * @return $owner
     */
    public function resetAllReturnOptions()
    {
        return $this->resetReturnOption('ReturnConsumedCapacity')
                    ->resetReturnOption('ReturnItemCollectionMetrics')
                    ->resetReturnOption('ReturnValues');
    }

    /**
     * Set owner's property of query
     * @param string $key
     * @param string $value
     * @return vold
     */
    private function setReturnOption($key, $value)
    {
        $query = &$this->owner->query;
        $query[$key] = $value;
        return $this->owner;
    }
}

