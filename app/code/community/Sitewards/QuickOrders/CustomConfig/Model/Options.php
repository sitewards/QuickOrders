<?php

/**
 * Sitewards_QuickOrders_CustomConfig_Options
 *  Custom Configuration to let users choose to search by Product SKU or Name.
 *
 * @category    Sitewards
 * @package     Sitewards_QuickOrders
 * @copyright   Copyright (c) 2013 Sitewards GmbH (http://www.sitewards.com/)
 */
class Sitewards_QuickOrders_CustomConfig_Options
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>'SKU'),
            array('value'=>2, 'label'=>'Name')
        );
    }
}