<?php echo ipRenderWidget('Heading', array('title' => __('Subscription status', 'PayPalSubscription'))) ?>
<?php echo ipRenderWidget('Text', array('text' => ipView('helper/statusData.php', $this->getVariables()))) ?>
