<?php
/**
 * @package   ImpressPages
 */



namespace Plugin\PayPalSubscription;


class Filter
{
    public static function ipSubscriptionPaymentMethods($paymentMethods, $data)
    {
        $paymentMethod = new SubscriptionPayment();
        $paymentMethods[] = $paymentMethod;
        return $paymentMethods;
    }
}
