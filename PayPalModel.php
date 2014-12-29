<?php
/**
 * @package   ImpressPages
 */


namespace Plugin\PayPalSubscription;


class PayPalModel
{
    const PAYPAL_POST_URL_TEST = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
    const PAYPAL_POST_URL = 'https://www.paypal.com/cgi-bin/webscr';


    protected static $instance;

    protected function __construct()
    {
    }

    protected function __clone()
    {
    }

    /**
     * Get singleton instance
     * @return PayPalModel
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new PayPalModel();
        }

        return self::$instance;
    }

    public function processPayPalCallback($postData)
    {
        if (empty($postData['txn_type'])) {
            return;
        }

        $postUrl = $this->getPayPalUrl();

        $response = $this->httpPost($postUrl, $postData);

        if (!$response["status"]) {
            ipLog()->error(
                'PayPalSubscription.ipn: notification check error',
                $response
            );
            return;
        }

        $customData = json_decode($postData['custom'], true);

        $paymentId = isset($customData['paymentId']) ? $customData['paymentId'] : null;
        $currency = isset($postData['mc_currency']) ? $postData['mc_currency'] : null;
        $receiver = isset($postData['receiver_email']) ? $postData['receiver_email'] : null;
        $period = isset($postData['period3']) ? $postData['period3'] : null;
        $a3 = isset($postData['mc_amount3']) ? $postData['mc_amount3'] : null;


        switch ($postData['txn_type']) {
            case 'subscr_signup':


                $order = Model::getPayment($paymentId);

                if (!$order) {
                    ipLog()->error('PayPalSubscription.ipn: Order not found.', array('orderId' => $paymentId));
                    return;
                }

                if ($period != $order['p3'] . ' ' . $order['t3']) {
                    ipLog()->error('PayPalSubscription.ipn: IPN rejected. Period and type doesn\'t match', array('paypal period' => $period, 'expected period' => $order['p3'] . ' ' . $order['t3']));
                    return;
                }

                if ($order['currency'] != $currency) {
                    ipLog()->error('PayPalSubscription.ipn: IPN rejected. Currency doesn\'t match', array('paypal currency' => $currency, 'expected currency' => $order['currency']));
                    return;
                }

                $orderPrice = substr_replace($order['a3'], '.', -2, 0);
                if ($a3 != $orderPrice) {
                    ipLog()->error('PayPalSubscription.ipn: IPN rejected. Price doesn\'t match', array('paypal price' => $a3, 'expected price' => '' . $orderPrice));
                    return;
                }

                if ($receiver != $this->getEmail()) {
                    ipLog()->error('PayPalSubscription.ipn: IPN rejected. Recipient doesn\'t match', array('paypal recipient' => $receiver, 'expected recipient' => $this->getEmail()));
                    return;
                }

                if ($response["httpResponse"] != 'VERIFIED') {
                    ipLog()->error('PayPalSubscription.ipn: Paypal doesn\'t recognize the payment', $response);
                    return;
                }

                if ($order['isActive']) {
                    ipLog()->error('PayPalSubscription.ipn: Subscription is already active', $response);
                    return;
                }
                $this->markAsPaid($paymentId);
                break;
            case 'subscr_eot':
                $order = Model::getPayment($paymentId);
                if (!$order) {
                    ipLog()->error('PayPalSubscription.ipn: Order not found.', array('orderId' => $paymentId));
                    return;
                }

                if ($response["httpResponse"] != 'VERIFIED') {
                    ipLog()->error('PayPalSubscription.ipn: Paypal doesn\'t recognize the IPN', $response);
                    return;
                }


                ipLog()->info('PayPalSubscription.ipn: Subscription expired', $response);
                $info = array(
                    'item' => $order['item'],
                    'userId' => $order['userId']
                );
                ipEvent('ipSubscriptionExpired', $info);

                if (!$order['isActive']) {
                    ipLog()->error('PayPalSubscription.ipn: Subscription is already inactive', $response);
                }
                Model::update($paymentId, array('isActive' => 0));

                break;
        }


    }


    /**
     *
     * Enter description here ...
     * @param string $url
     * @param array $values
     * @return array
     */
    private function httpPost($url, $values)
    {
        $tmpAr = array_merge($values, array("cmd" => "_notify-validate"));
        $postFieldsAr = array();
        foreach ($tmpAr as $name => $value) {
            $postFieldsAr[] = "$name=" . urlencode($value);
        }
        $postFields_ = implode("&", $postFieldsAr);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        //setting the nvpreq as POST FIELD to curl
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields_);

        //getting response from server
        $httpResponse = curl_exec($ch);

        if (!$httpResponse) {
            return array("status" => false, "error_msg" => curl_error($ch), "error_no" => curl_errno($ch));
        }


        return array("status" => true, "httpResponse" => $httpResponse);

    }

    public function getPaypalForm($orderId)
    {
        if (!$this->getEmail()) {
            throw new \Ip\Exception('Please enter configuration values for PayPal Subscription plugin');
        }


        $order = Model::getPayment($orderId);
        if (!$order) {
            throw new \Ip\Exception("Can't find order id. " . $orderId);
        }


        $currency = $order['currency'];
        $privateData = array(
            'paymentId' => $orderId,
            'userId' => $order['userId'],
            'securityCode' => $order['securityCode']
        );



        $values = array(
            'business' => $this->getEmail(),
            'cmd' => '_xclick-subscriptions',
            'currency_code' => $currency,
            't3' => $order['t3'],
            'p3' => $order['p3'],
            'a3' => $order['a3'] / 100,
            'src' => 1,
            'rm' => 2,
            'sra' => 1,
            'no_shipping' => 1,
            'custom' => json_encode($privateData),
            'return' => ipRouteUrl('PayPalSubscription_userBack'),
            'notify_url' => ipRouteUrl('PayPalSubscription_ipn'),
            'item_name' => $order['title']
        );

        if (!empty($payment['cancelUrl'])) {
            $values['cancel_return'] = $payment['cancelUrl'];
        }

        $form = new \Ip\Form();
        $form->addClass('ipsPayPalSubscriptionAutosubmit');
        $form->setAction($this->getPayPalUrl());
        $form->setAjaxSubmit(0);

        foreach ($values as $valueKey => $value) {
            $field = new \Ip\Form\Field\Hidden(
                array(
                    'name' => $valueKey,
                    'value' => $value
                ));
            $form->addField($field);
        }

        $form->setMethod(\Ip\Form::METHOD_POST);
        return $form;
    }

    /**
     *
     *  Returns $data encoded in UTF8. Very useful before json_encode as it fails if some strings are not utf8 encoded
     * @param mixed $dat array or string
     * @return array
     */
    private function checkEncoding($dat)
    {
        if (is_string($dat)) {
            if (mb_check_encoding($dat, 'UTF-8')) {
                return $dat;
            } else {
                return utf8_encode($dat);
            }
        }
        if (is_array($dat)) {
            $answer = array();
            foreach ($dat as $i => $d) {
                $answer[$i] = $this->checkEncoding($d);
            }
            return $answer;
        }
        return $dat;
    }


    public function getEmail()
    {
        if ($this->isInSandboxMode()) {
            return ipGetOption('PayPalSubscription.paypalEmailTest');
        } else {
            return ipGetOption('PayPalSubscription.paypalEmail');
        }
    }

    public function getPayPalUrl()
    {
        if ($this->isInSandboxMode()) {
            return self::PAYPAL_POST_URL_TEST;
        } else {
            return self::PAYPAL_POST_URL;
        }
    }

    public function isInSandboxMode()
    {
        return (ipGetOption('PayPalSubscription.mode') == 'Test');
    }


    public function isInSkipMode()
    {
        return (ipGetOption('PayPalSubscription.mode') == 'Skip');
    }

    public function correctConfiguration()
    {
        if ($this->getActive() && $this->getEmail()) {
            return true;
        } else {
            return false;
        }
    }

    public function successStatusPage($paymentId, $securityCode)
    {
        $payment = Model::getPayment($paymentId);
        $orderUrl = ipRouteUrl('PayPalSubscription_status', array('paymentId' => $paymentId, 'securityCode' => $securityCode));
        $response = new \Ip\Response\Redirect($orderUrl);

        if (!empty($payment['successUrl'])) {
            $response = new \Ip\Response\Redirect($payment['successUrl']);
        }
        $response = ipFilter('PayPalSubscription_userBackResponse', $response);
        return $response;
    }

    public function markAsPaid($paymentId)
    {
        $payment = Model::getPayment($paymentId);
        $info = array(
            'item' => $payment['item'],
            'userId' => $payment['userId']
        );
        ipLog()->info('PayPalSubscription.ipn: Successful sign-up', $info);
        ipEvent('ipSubscriptionSignup', $info);
        Model::update($paymentId, array('isActive' => 1));

    }

}
