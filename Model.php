<?php
/**
 * @package   ImpressPages
 */


namespace Plugin\PayPalSubscription;


class Model
{
    public static function createPayment($paymentData, $userId = null)
    {
        if ($userId == null) {
            $userId = ipUser()->userId();
        }





        $data = array(
            'item' => $paymentData['item'],
            'cancelUrl' => empty($paymentData['cancelUrl']) ? '' : $paymentData['cancelUrl'],
            'successUrl' => empty($paymentData['successUrl']) ? '' : $paymentData['successUrl'],
            'currency' => $paymentData['currency'],
            'userId' => $userId,
            'securityCode' => self::randomString(32),
            'createdAt' => date('Y-m-d H:i:s')
        );



        switch($paymentData['periodType']) {
            case 'day':
                $data['t3'] = 'D';
                break;
            case 'week':
                $data['t3'] = 'W';
                break;
            case 'month':
                $data['t3'] = 'M';
                break;
            case 'year':
                $data['t3'] = 'Y';
                break;
        }

        $data['p3'] = $paymentData['period'];
        $data['a3'] = $paymentData['amount'];

        if (!empty($paymentData['title'])) {
            $data['title'] = $paymentData['title'];
        } else {
            $data['title'] = $data['item'];
        }

        $orderId = ipDb()->insert('paypal_subscription', $data);
        return $orderId;
    }

    public static function getPayment($orderId)
    {
        $order = ipDb()->selectRow('paypal_subscription', '*', array('id' => $orderId));
        return $order;
    }
    public static function update($orderId, $data)
    {
        ipDb()->update('paypal_subscription', $data, array('id' => $orderId));
    }

    protected static function randomString($length)
    {
        return substr(sha1(rand()), 0, $length);
    }


    public static function getActiveSubscription($item, $userId)
    {
        $subscription = ipDb()->selectRow('paypal_subscription', '*', array('item' => $item, 'userId' => $userId, 'isActive' => 1));
        return $subscription;
    }
}
