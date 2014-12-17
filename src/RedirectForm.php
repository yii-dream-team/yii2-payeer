<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 */

namespace yiidreamteam\payeer;

use yii\base\Widget;

class RedirectForm extends Widget
{
    /** @var Api */
    public $api;
    public $invoiceId;
    public $amount;
    public $description = '';

    public function init()
    {
        parent::init();
        assert(isset($this->api));
        assert(isset($this->invoiceId));
        assert(isset($this->amount));
    }

    public function run()
    {
        $amount = number_format($this->amount, 2, '.', '');
        $description = base64_encode($this->description);

        $parts = array(
            $this->api->merchantId,
            $this->invoiceId,
            $amount,
            $this->api->merchantCurrency,
            $description,
            $this->api->merchantSecret,
        );

        $sign = strtoupper(hash('sha256', implode(':', $parts)));

        return $this->render('redirect', [
            'api' => $this->api,
            'invoiceId' => $this->invoiceId,
            'amount' => $amount,
            'description' => $description,
            'sign' => $sign,
        ]);
    }
}