<?php
/**
 * @package   ImpressPages
 */




namespace Plugin\PayPalSubscription\Setup;


class Worker
{
    public function activate()
    {

        $table = ipTable('paypal_subscription');
        $sql="
        CREATE TABLE IF NOT EXISTS $table (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `userId` int(11) NOT NULL,
          `item` varchar(255) NOT NULL,
          `currency` varchar(3) NOT NULL,
          `a3` int(11) NOT NULL,
          `p3` int(11) NOT NULL,
          `t3` char(1) NOT NULL,
          `isActive` tinyint(1) DEFAULT 0,
          `createdAt` datetime NOT NULL,
        PRIMARY KEY (`id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

        ";

        ipDb()->execute($sql);
    }
}
