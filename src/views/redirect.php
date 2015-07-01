<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 *
 * @var \yii\web\View $this
 * @var \yiidreamteam\payeer\Api $api
 * @var $invoiceId
 * @var $amount
 * @var $description
 * @var $sign
 */
?>
<div class="payeer-checkout">
    <p><?= \Yii::t('Payeer', 'Now you will be redirected to the payment system.') ?></p>
    <form id="payeer-checkout-form" method="GET" action="//payeer.com/merchant/">
        <input type="hidden" name="m_shop" value="<?= $api->merchantId ?>">
        <input type="hidden" name="m_orderid" value="<?= $invoiceId ?>">
        <input type="hidden" name="m_amount" value="<?= $amount ?>">
        <input type="hidden" name="m_curr" value="<?= $api->merchantCurrency ?>">
        <input type="hidden" name="m_desc" value="<?= $description ?>">
        <input type="hidden" name="m_sign" value="<?= $sign ?>">
        <input type="submit" name="m_process" value="send" />
    </form>
</div>

<?php
$js = <<<JS
    $('#payeer-checkout-form').submit();
JS;
$this->registerJs($js, \yii\web\View::POS_READY);
?>