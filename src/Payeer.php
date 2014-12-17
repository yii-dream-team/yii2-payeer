<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 */
namespace yiidreamteam\payeer;

use yii\base\Component;
use yii\base\InvalidConfigException;

class Payeer extends Component
{
    /** @var string */
    public $accountNumber;
    /** @var string */
    public $apiId;
    /** @var string  */
    public $apiSecret;

    /** @var \CPayeer */
    public $api;

    /**
     * @inheritdoc
     */
    public function init()
    {
        assert(isset($this->accountNumber));
        assert(isset($this->apiId));
        assert(isset($this->apiSecret));

        $this->api = new \CPayeer($this->accountNumber, $this->apiId, $this->apiSecret);
        if (!$this->api->isAuth())
            throw new InvalidConfigException('Invalid payeer credentials');
    }
}