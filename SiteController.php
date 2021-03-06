<?php
/**
 * @package   ImpressPages
 */



namespace Plugin\PayPalSubscription;


class SiteController extends \Ip\Controller
{
    public function subscribe($paymentId, $securityCode)
    {
        if (PayPalModel::instance()->isInSkipMode()) {
            PayPalModel::instance()->markAsPaid($paymentId);
            $response = PayPalModel::instance()->successStatusPage($paymentId, $securityCode);
            return $response;
        }


        $payment = Model::getPayment($paymentId);
        if (!$payment) {
            throw new \Ip\Exception('Order ' . $paymentId . ' doesn\'t exist');
        }


        if ($payment['securityCode'] != $securityCode) {
            throw new \Ip\Exception('Payment security code is incorrect. Order Id '. $paymentId . '. SecurityCode ' . $securityCode);
        }


        if (!$payment['userId']) {
            if (!ipUser()->loggedIn()) {
                $_SESSION['User_redirectAfterLogin'] = ipRequest()->getUrl();
                return new \Ip\Response\Redirect(ipRouteUrl('User_login'));
            }
            Model::update($paymentId, array('userId' => ipUser()->userId()));
        }


        $paypalModel = PayPalModel::instance();


        $data = array(
            'form' => $paypalModel->getPaypalForm($paymentId)
        );

        $answer = ipView('view/page/paymentRedirect.php', $data)->render();


        return $answer;

    }

    public function cancel($subscriptionId, $securityCode)
    {
        $subscription = Model::getPayment($subscriptionId);
        if (!$subscription) {
            throw new \Ip\Exception("Unknown subscription");
        }

        $data = array (
            'subscriptionTitle' => $subscription['title']
        );
        return ipView('view/page/cancelInstructions.php', $data);
    }

    public function status($paymentId, $securityCode)
    {
        $payment = Model::getPayment($paymentId);
        if (!$payment) {
            throw new \Ip\Exception('Unknown order. Id: ' . $paymentId);
        }
        if ($payment['securityCode'] != $securityCode) {
            throw new \Ip\Exception('Incorrect order security code');
        }

        $data = array(
            'payment' => $payment,
            'paymentUrl' => ipRouteUrl('PayPalSubscription_pay', array('paymentId' => $payment['id'], 'securityCode' => $payment['securityCode']))
        );
        $view = ipView('view/page/status.php', $data);
        return $view;
    }
}
