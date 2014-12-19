<?php
/**
 * @package   ImpressPages
 */


/**
 * Created by PhpStorm.
 * User: mangirdas
 * Date: 14.12.19
 * Time: 16.26
 */

namespace Plugin\PayPalSubscription;


class Job
{
    public static function ipSubscriptionCancelUrl($info)
    {
        $item = $info['item'];
        if (!empty($info['userId'])) {
            $userId = $info['userId'];
        } else {
            $userId = ipUser()->userId();
        }
        $subscription = Model::getActiveSubscription($item, $userId);

        if ($subscription) {
            $cancelUrl = ipRouteUrl('PayPalSubscription_cancel', array('subscriptionId' => $subscription['id'], 'securityCode' => $subscription['securityCode']));
            return $cancelUrl;
        }

    }
}
