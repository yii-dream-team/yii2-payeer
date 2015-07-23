<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 */

namespace yiidreamteam\payeer\actions;

use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
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
        $post =  \Yii::$app->request->post();
        $orderId = ArrayHelper::getValue($post, 'm_orderid', false);

        if (false === $orderId)
            throw new BadRequestHttpException('Missing order id');

        $result = $this->api->processResult($post);
        if ($result)
            echo $orderId . '|success';
        else
            echo $orderId . '|error';
    }
}