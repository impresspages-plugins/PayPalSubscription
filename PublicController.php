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

        $payment = Model::getPayment($customData['paymentId']);

        $orderUrl = ipRouteUrl('PayPalSubscription_status', array('paymentId' => $customData['paymentId'], 'securityCode' => $customData['securityCode']));
        $response = new \Ip\Response\Redirect($orderUrl);

        if (!empty($payment['successUrl'])) {
            $response = new \Ip\Response\Redirect($payment['successUrl']);
        }
        $response = ipFilter('PayPalSubscription_userBackResponse', $response);
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
