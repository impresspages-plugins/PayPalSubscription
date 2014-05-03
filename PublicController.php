<?php
/**
 * @package   ImpressPages
 */


namespace Plugin\PayPalSubscription;


class PublicController extends \Ip\Controller
{

    public function ipn()
    {
        $paypalModel = PayPalModel::instance();
        $postData = ipRequest()->getPost();
        ipLog()->info('PayPalSubscription.ipn: PayPal notification', $postData);
        $paypalModel->processPayPalCallback($postData);
    }

}
