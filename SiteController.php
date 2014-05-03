<?php
/**
 * @package   ImpressPages
 */



namespace Plugin\PayPalSubscription;


class SiteController extends \Ip\Controller
{
    public function subscribe($orderId)
    {
        $order = Model::getOrder($orderId);
        if (!$order) {
            throw new \Ip\Exception('Order ' . $orderId . ' doesn\'t exist');
        }


        if (!$order['userId']) {
            if (!ipUser()->loggedIn()) {
                throw new \Ip\Exception('User is not logged in');
            }
            Model::update($orderId, array('userId' => ipUser()->userId()));
            $order = Model::getOrder($orderId);
        }


        $paypalModel = PayPalModel::instance();


        $data = array(
            'form' => $paypalModel->getPaypalForm($orderId)
        );

        $answer = ipView('view/page/paymentRedirect.php', $data)->render();


        return $answer;

    }
}
