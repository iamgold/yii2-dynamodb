<?php

namespace iamgold\yii2\dynamodb;

/**
 * This is a active record for dynamodb
 *
 * @author Eric Huang <iamgole0105@gmail.com>
 * @since Yii 2.0.9
 * @version 0.1.0
 */

abstract class ActiveRecord extends \yii\db\BaseActiveRecord
{
    /**
     * Get database connection
     *
     * @return Connection $db
     */
    public static function getDb()
    {
        return \Yii::$app->get('dynamodb');
    }

    /**
     * Return a primary key
     *
     * @return array
     */
    public static function primaryKey()
    {
        return ['id'];
    }

    /**
     * inheritdoc
     */
    public static function find()
    {

    }

    /**
     * Returns the list of all attribute names of the model.
     * This method must be overridden by child classes to define available attributes.
     * Note: primary key attribute "_id" should be always present in returned array.
     * For example:
     *
     * ```php
     * public function attributes()
     * {
     *     return ['_id', 'name', 'address', 'status'];
     * }
     * ```
     *
     * @throws \yii\base\InvalidConfigException if not implemented
     * @return array list of attribute names.
     */
    public function attributes()
    {
        throw new InvalidConfigException('The attributes() method of dynamodb ActiveRecord has to be implemented by child classes.');
    }

    /**
     * Insert a item to db
     *
     * @param bool $runValidation
     * @param array $attributes
     * @return bool
     */
    public function insert($runValidation=true, $attributes=null)
    {
        if ($runValidation && !$this->validate($attributes)) {
            return false;
        }
        $result = $this->insertInternal($attributes);
        return $result;
    }

    /**
     * @see ActiveRecord::insert()
     */
    protected function insertInternal($attributes=null)
    {
        if (!$this->beforeSave(true)) {
            return false;
        }

        $values = $this->getDirtyAttributes($attributes);
        if (empty($values)) {
            $currentAttributes = $this->getAttributes();
            foreach ($this->primaryKey() as $key) {
                if (isset($currentAttributes[$key])) {
                    $values[$key] = $currentAttributes[$key];
                }
            }
        }

        var_dump(static::getDb()->createCommand()->insert(static::tableName(), $values)
                                                 ->execute());

        $this->setAttribute('_id', $newId);
        $values['_id'] = $newId;
        $changedAttributes = array_fill_keys(array_keys($values), null);
        $this->setOldAttributes($values);
        $this->afterSave(true, $changedAttributes);
        return true;
    }
    /**
     * Save item to db
     *
     * @param bool $runValidation
     * @param array $attributes
     * @return bool
     */
    public function save($runValidation=true, $attributes=null)
    {
        if ($this->getIsNewRecord())
            return $this->insert($runValidation, $attributes);
        else
            return $this->update($runValidation, $attributes);
    }

    /**
     * Update item
     *
     * @param bool $runValidation
     * @param array $attributes
     * @return bool
     */
    public function update($runValidation=true, $attributes=null)
    {

    }


}

