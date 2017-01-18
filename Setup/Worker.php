<?php
/**
 * @package   ImpressPages
 */




namespace Plugin\PayPalSubscription\Setup;


class Worker
{
    public function activate()
    {

        $version = \Ip\Application::getVersion();
        if ($version < 4.2) {
            throw new \Ip\Exception('ImpressPages 4.2.0 or later required');

        }

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
        $ceckSql = "
        SELECT
          *
        FROM
          information_schema.COLUMNS
        WHERE
            TABLE_SCHEMA = :database
            AND TABLE_NAME = :table
            AND COLUMN_NAME = :column
        ";

        $table = ipTable('paypal_subscription');

        $result = ipDb()->fetchAll($ceckSql, array('database' => ipConfig()->database(), 'table' => ipConfig()->tablePrefix() . 'paypal_subscription', 'column' => 'title'));
        if (!$result) {
            $sql = "ALTER TABLE $table ADD `title` VARCHAR(255) NOT NULL AFTER `userId`;";
            ipDb()->execute($sql);
        }


        $result = ipDb()->fetchAll($ceckSql, array('database' => ipConfig()->database(), 'table' => ipConfig()->tablePrefix() . 'paypal_subscription', 'column' => 'securityCode'));
        if (!$result) {
            $sql = "ALTER TABLE $table ADD `securityCode` VARCHAR(32) NOT NULL AFTER `isActive`;";
            ipDb()->execute($sql);
        }


        $result = ipDb()->fetchAll($ceckSql, array('database' => ipConfig()->database(), 'table' => ipConfig()->tablePrefix() . 'paypal_subscription', 'column' => 'successUrl'));
        if (!$result) {
            $sql = "ALTER TABLE $table ADD `successUrl` VARCHAR(255) NOT NULL AFTER `isActive`;";
            ipDb()->execute($sql);
        }

        $result = ipDb()->fetchAll($ceckSql, array('database' => ipConfig()->database(), 'table' => ipConfig()->tablePrefix() . 'paypal_subscription', 'column' => 'cancelUrl'));
        if (!$result) {
            $sql = "ALTER TABLE $table ADD `cancelUrl` VARCHAR(255) NOT NULL AFTER `isActive`;";
            ipDb()->execute($sql);
        }


       if (ipGetOption('PayPalSubscription.testMode')) {
           ipSetOption('PayPalSubscription.mode', 'Test');
       }

    }

    public function deactivate()
    {

    }
}
