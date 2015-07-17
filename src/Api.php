<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 */
namespace yiidreamteam\payeer;

use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yiidreamteam\payeer\events\GatewayEvent;

class Api extends Component
{
    /** @var string */
    public $accountNumber;
    /** @var string */
    public $apiId;
    /** @var string */
    public $apiSecret;
    /** @var string Shop id */
    public $merchantId;
    /** @var string Secret sequence from shop settings */
    public $merchantSecret;
    /** @var string Shop currency */
    public $merchantCurrency = 'USD';

    /** @var \CPayeer */
    public $payeer;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        assert(isset($this->accountNumber));
        assert(isset($this->apiId));
        assert(isset($this->apiSecret));

        $this->payeer = new \CPayeer($this->accountNumber, $this->apiId, $this->apiSecret);
        if (!$this->payeer->isAuth())
            throw new InvalidConfigException('Invalid payeer credentials');
    }

    /**
     * Validates incoming request security sign
     *
     * @param array $data
     * @return boolean
     */
    protected function checkSign($data)
    {
        $parts = [
            ArrayHelper::getValue($data, 'm_operation_id'),
            ArrayHelper::getValue($data, 'm_operation_ps'),
            ArrayHelper::getValue($data, 'm_operation_date'),
            ArrayHelper::getValue($data, 'm_operation_pay_date'),
            ArrayHelper::getValue($data, 'm_shop'),
            ArrayHelper::getValue($data, 'm_orderid'),
            ArrayHelper::getValue($data, 'm_amount'),
            ArrayHelper::getValue($data, 'm_curr'),
            ArrayHelper::getValue($data, 'm_desc'),
            ArrayHelper::getValue($data, 'm_status'),
            $this->merchantSecret,
        ];

        $sign = strtoupper(hash('sha256', implode(':', $parts)));
        return ArrayHelper::getValue($data, 'm_sign') == $sign;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function processResult($data)
    {
        // required parameters
        if (!array_key_exists('m_operation_id', $data) || !array_key_exists('m_sign', $data))
            return false;

        // we process only succeeded payments
        if (ArrayHelper::getValue($data, 'm_status') != 'success')
            return false;

        if (!$this->checkSign($data))
            return false;

        $event = new GatewayEvent(['gatewayData' => $data]);
        $transaction = \Yii::$app->getDb()->beginTransaction();
        try {
            $this->trigger(GatewayEvent::EVENT_PAYMENT_REQUEST, $event);
            if (!$event->handled)
                throw new \Exception();
            $this->trigger(GatewayEvent::EVENT_PAYMENT_SUCCESS, $event);
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            \Yii::error('Payment processing error: ' . $e->getMessage(), 'Payeer');
            return false;
        }

        return true;
    }

}
