<?php
/**
 * @package   ImpressPages
 */


namespace Plugin\PayPalSubscription;


class Event
{
    public static function ipBeforeController()
    {
        ipAddJs('assets/paypalSubscription.js');
    }
}
