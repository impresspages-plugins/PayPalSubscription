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


        //add title column if not exist
        $sql = "
        SELECT
          *
        FROM
          information_schema.COLUMNS
        WHERE
            TABLE_SCHEMA = :database
            AND TABLE_NAME = :table
            AND COLUMN_NAME = :column
        ";
        $result = ipDb()->fetchAll($sql, array('database' => ipConfig()->database(), 'table' => ipConfig()->tablePrefix() . 'paypal_subscription', 'column' => 'title'));
        if (!$result) {
            $sql = "ALTER TABLE `ip_paypal_subscription` ADD `title` VARCHAR(255) NOT NULL AFTER `userId`;";
            ipDb()->execute($sql);
        }


        $result = ipDb()->fetchAll($sql, array('database' => ipConfig()->database(), 'table' => ipConfig()->tablePrefix() . 'paypal_subscription', 'column' => 'securityCode'));
        if (!$result) {
            $sql = "ALTER TABLE `ip_paypal_subscription` ADD `securityCode` VARCHAR(32) NOT NULL AFTER `isActive`;";
            ipDb()->execute($sql);
        }


        $result = ipDb()->fetchAll($sql, array('database' => ipConfig()->database(), 'table' => ipConfig()->tablePrefix() . 'paypal_subscription', 'column' => 'successUrl'));
        if (!$result) {
            $sql = "ALTER TABLE `ip_paypal_subscription` ADD `successUrl` VARCHAR(255) NOT NULL AFTER `isActive`;";
            ipDb()->execute($sql);
        }

        $result = ipDb()->fetchAll($sql, array('database' => ipConfig()->database(), 'table' => ipConfig()->tablePrefix() . 'paypal_subscription', 'column' => 'cancelUrl'));
        if (!$result) {
            $sql = "ALTER TABLE `ip_paypal_subscription` ADD `cancelUrl` VARCHAR(255) NOT NULL AFTER `isActive`;";
            ipDb()->execute($sql);
        }

    }

    public function deactivate()
    {

    }
}
