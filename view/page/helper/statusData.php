<table>
    <tr>
        <td><b><?php echo __('Subscription ID', 'PayPalSubscription') ?></b></td>
        <td><?php echo esc($payment['id']) ?></td>
    </tr>
    <tr>
        <td><b><?php echo __('Subscribed', 'PayPalSubscription') ?></b></td>
        <td><?php echo __($payment['isActive'] ? 'Yes' : 'No', 'PayPalSubscription') ?>
            <?php if (!$payment['isActive']) { ?>
                <a href="<?php echo $paymentUrl ?>">(<?php echo __('Subscribe Now', 'PayPalSubscription') ?>)</a>
            <?php } ?>
        </td>
    </tr>
    <tr>
        <td><b><?php echo __('Item', 'PayPalSubscription') ?></b></td>
        <td><?php echo esc($payment['title'] ? $payment['title'] : $payment['item']) ?></td>
    </tr>
    <tr>
        <td><b><?php echo __('Price', 'PayPalSubscription') ?></b></td>
        <td><?php echo esc(ipFormatPrice($payment['a3'], $payment['currency'], 'PayPalSubscription')) ?>
            /
            <?php if ($payment['p3'] != 1) { echo (int)$payment['p3']; } ?>
            <?php
            switch (mb_strtoupper($payment['t3'])) {
                case 'Y':
                    echo __('Year', 'PayPalSubscription');
                break;
                case 'M':
                    echo __('Month', 'PayPalSubscription');
                    break;
                case 'D':
                    echo __('Day', 'PayPalSubscription');
                    break;
            }

            ?></td>
    </tr>
    <tr>
        <td><b><?php echo __('Date', 'PayPalSubscription') ?></b></td>
        <td><?php echo esc(ipFormatDateTime(strtotime($payment['createdAt']), 'PayPalSubscription')) ?></td>
    </tr>
</table>
