<?php
/**
 * @package   ImpressPages
 */


namespace Plugin\PayPalSubscription;


class PublicController extends \Ip\Controller
{

    public function ipn()
    {
        $this->processPayPalNotification();
    }

    public function userBack()
    {
        $this->processPayPalNotification();

        $customData = json_decode(ipRequest()->getPost('custom'), true);
        if (empty($customData['paymentId'])) {
            throw new \Ip\Exception("Unknown order ID");
        }
        if (empty($customData['securityCode'])) {
            throw new \Ip\Exception("Unknown order security code");
        }

        $response = PayPalModel::instance()->successStatusPage($customData['paymentId'], $customData['securityCode']);
        return $response;

    }

    protected function processPayPalNotification()
    {
        $paypalModel = PayPalModel::instance();
        $postData = ipRequest()->getPost();
        ipLog()->info('PayPalSubscription.ipn: PayPal notification', $postData);
        $paypalModel->processPayPalCallback($postData);
    }

}
