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
                $_SESSION['User_redirectAfterLogin'] = ipRequest()->getUrl();
                return new \Ip\Response\Redirect(ipRouteUrl('User_login'));
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
