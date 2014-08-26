<?php
/**
 * @package   ImpressPages
 */


namespace Plugin\PayPalSubscription;


class SubscriptionPayment extends \Ip\SubscriptionPayment
{
    public function name()
    {
        return 'PayPal';
    }

    public function icon($width = null, $height = null)
    {
        return ipFileUrl('Plugin/PayPalSubscription/assets/PayPal.svg');
    }

    /**
     * This method should generate payment URL.
     * Typical actions of this method:
     * 1 write down all passed data to database table
     * 2 return URL which starts payment method execution
     *
     * @param array $data subscription data
     */
    public function paymentUrl($data)
    {
        $orderId = Model::createOrder($data);
        $urlData = array(
            'orderId' => $orderId
        );
        return ipRouteUrl('PayPalSubscription', $urlData);
    }
}
