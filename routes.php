<?php

$routes['paypalSubscription{/orderId}'] = array(
    'name' => 'PayPalSubscription',
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
