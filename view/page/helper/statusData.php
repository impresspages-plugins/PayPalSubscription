<table>
    <tr>
        <td><b><?php echo __('Order ID', 'PayPalSubscription') ?></b></td>
        <td><?php echo esc($payment['orderId']) ?></td>
    </tr>
    <tr>
        <td><b><?php echo __('Paid', 'PayPalSubscription') ?></b></td>
        <td><?php echo __($payment['isActive'] ? 'Yes' : 'No', 'PayPalSubscription') ?>
            <?php if (!$payment['isActive']) { ?>
                <a href="<?php echo $paymentUrl ?>">(<?php echo __('Pay Now', 'PayPalSubscription') ?>)</a>
            <?php } ?>
        </td>
    </tr>
    <tr>
        <td><b><?php echo __('Item', 'PayPalSubscription') ?></b></td>
        <td><?php echo esc($payment['title'] ? $payment['title'] : $payment['item']) ?></td>
    </tr>
    <tr>
        <td><b><?php echo __('Amount', 'PayPalSubscription') ?></b></td>
        <td><?php echo esc(ipFormatPrice($payment['price'], $payment['currency'], 'PayPalSubscription')) ?></td>
    </tr>
    <tr>
        <td><b><?php echo __('Date', 'PayPalSubscription') ?></b></td>
        <td><?php echo esc(ipFormatDateTime(strtotime($payment['createdAt']), 'PayPalSubscription')) ?></td>
    </tr>
</table>
