<?php
/**
 * @package   ImpressPages
 */


/**
 * Created by PhpStorm.
 * User: mangirdas
 * Date: 7/30/14
 * Time: 2:19 PM
 */

namespace Plugin\PayPalSubscription;


class AdminController {
    public function index()
    {
        $config = array(
            'table' => 'paypal_subscription',
            'orderBy' => '`id` desc',
            'allowUpdate' => false,
            'allowInsert' => false,
            'allowDelete' => false,
            'fields' => array(
                array(
                    'label' => __('Payment ID', 'PayPalSubscription', false),
                    'field' => 'id',
                    'allowUpdate' => false,
                    'allowInsert' => false
                ),
                array(
                    'label' => __('Title', 'PayPalSubscription', false),
                    'field' => 'title'
                ),
                array(
                    'label' => __('Item', 'PayPalSubscription', false),
                    'field' => 'item'
                ),
                array(
                    'label' => __('Price', 'PayPalSubscription', false),
                    'field' => 'a3',
                    'type' => 'Currency',
                    'currencyField' => 'currency'
                ),
                array(
                    'label' => __('Period', 'PayPalSubscription', false),
                    'field' => 't3',
                    'type' => 'Select',
                    'values' => array(array('D', 'Days'), array('W', 'Weeks'), array('M', 'Months'), array('Y', 'Years'), )
                ),
                array(
                    'label' => __('Number of time periods between each payment.', 'PayPalSubscription', false),
                    'field' => 'p3'
                ),
                array(
                    'label' => __('Currency', 'PayPalSubscription', false),
                    'field' => 'currency'
                ),
                array(
                    'label' => __('Active (is paid)', 'PayPalSubscription', false),
                    'field' => 'isActive',
                    'type' => 'Checkbox'
                ),
                array(
                    'label' => __('User ID', 'PayPalSubscription', false),
                    'field' => 'userId',
                    'type' => 'Integer'
                ),
                array(
                    'label' => __('First Name', 'PayPalSubscription', false),
                    'field' => 'payer_first_name'
                ),
                array(
                    'label' => __('Last Name', 'PayPalSubscription', false),
                    'field' => 'payer_last_name'
                ),
                array(
                    'label' => __('Email', 'PayPalSubscription', false),
                    'field' => 'payer_email'
                ),
                array(
                    'label' => __('Country', 'PayPalSubscription', false),
                    'field' => 'payer_country'
                ),
                array(
                    'label' => __('Created At', 'PayPalSubscription', false),
                    'field' => 'createdAt'
                ),



            )
        );
        return ipGridController($config);
    }
}
