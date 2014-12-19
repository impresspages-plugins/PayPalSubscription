<?php echo ipRenderWidget('Heading', array('title' => __('Subscription cancelation', 'PayPalSubscription'))) ?>
<?php echo ipRenderWidget('Text', array('text' => ipView('helper/cancellationInstructionsText.php', $this->getVariables())->render())) ?>
