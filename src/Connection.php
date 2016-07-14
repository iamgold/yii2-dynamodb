<?php

namespace iamgold\yii2\dynamodb;

/**
 * Dynamo client for Yii2
 *
 * @author Eric Huang <iamgold0105@gmail.com>
 * @since Yii 2.0
 * @version 0.1.0
 */

use Exception;
use Yii;
use yii\base\InvalidConfigException;

class Connection extends \yii\base\Component
{
    /**
     * @var string $key Aws API key
     */
    public $key;

    /**
     * @var string $region Aws API region
     */
    public $region;

    /**
     * @var string $secret Aws API secret
     */
    public $secret;

    /**
     * @var string $version Aws API version default: latest
     */
    public $version = 'latest';

    /**
     * @var $_dynamoDbClient
     */
    private $_dynamoDbClient;

    /**
     * Open a DynamoDbClient by specific property.
     * @return void
     */
    public function open()
    {
        if ($this->_dynamoDbClient!==null)
            return ;

        if (empty($this->key))
            throw new InvalidConfigException('Missing config of [key]');

        if (empty($this->secret))
            throw new InvalidConfigException('Missing config of [secret]');

        if (empty($this->region))
            throw new InvalidConfigException('Missing config of [region]');

        if (empty($this->version))
            $this->version = 'latest';

        $token = 'Create DynamoDbClient: ' . sprintf('key: %s, secret: %s, region: %s, version: %s', $this->key, $this->secret, $this->region, $this->version);
        try {
            Yii::info($token, __METHOD__);
            Yii::beginProfile($token, __METHOD__);
            $this->_dynamoDbClient = $this->createClient();
            Yii::endProfile($token, __METHOD__);;
        } catch (Exception $e) {
            Yii::endProfile($token, __METHOD__);
            throw $e;
        }
    }

    /**
     * Get original connection client.
     * @return Aws\DynamoDb\DynamoDbClient
     */
    public function getClient()
    {
        $this->open();
        return $this->_dynamoDbClient;
    }

    /**
     * Creating a DynamoDbClient instance
     * @return Aws\DynamoDb\DynamoDbClient
     */
    protected function createDynamoDbClient()
    {
        return new \Aws\DynamoDb\DynamoDbClient([
            'credentials' => [
                'key'    => $this->key,
                'secret' => $this->secret,
            ],
            'region' => $this->region,
            'version' => $this->version
        ]);
    }
}