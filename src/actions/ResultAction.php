<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 */

namespace yiidreamteam\payeer\actions;

use yii\base\Action;
use yiidreamteam\payeer\Api;

class ResultAction extends Action
{
    /** @var Api */
    public $api;

    /**
     * @inheritdoc
     */
    public function init()
    {
        assert(isset($this->api));

        parent::init();
    }

    public function run()
    {
        $invoiceId = \Yii::$app->request->post('m_orderid');
        $result = $this->api->processResult(\Yii::$app->request->post());
        if ($result)
            echo $invoiceId . '|success';
        else
            echo $invoiceId . '|error';
    }
}