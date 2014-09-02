<?php

$routes['paypalSubscription/{paymentId}/{securityCode}'] = array(
    'name' => 'PayPalSubscription_pay',
    'plugin' => 'PayPalSubscription',
    'controller' => 'SiteController',
    'action' => 'subscribe'
);


$routes['paypalSubscriptionIPN'] = array(
    'name' => 'PayPalSubscription_ipn',
    'plugin' => 'PayPalSubscription',
    'controller' => 'PublicController',
    'action' => 'ipn'
);



$routes['paypalSubscriptionReturn'] = array(
    'name' => 'PayPalSubscription_userBack',
    'plugin' => 'PayPalSubscription',
    'controller' => 'PublicController',
    'action' => 'userBack'
);


$routes['paypalSubscriptionStatus'] = array(
    'name' => 'PayPalSubscription_status',
    'plugin' => 'PayPalSubscription',
    'controller' => 'SiteController',
    'action' => 'status'
);
