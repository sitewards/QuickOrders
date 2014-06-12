<?php

/**
 * Test for class Sitewards_MultipleOrder_Helper_Data
 *
 * @category    Sitewards
 * @package     Sitewards_MultipleOrder
 * @copyright   Copyright (c) 2014 Sitewards GmbH (http://www.sitewards.com/)
 */
class Sitewards_MultipleOrder_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Tests is extension active
     *
     * @test
     * @loadFixture
     */
    public function testIsExtensionActive()
    {
        $this->assertTrue(
            Mage::helper('sitewards_multipleorder')->isExtensionActive(),
            'Extension is not active please check config'
        );
    }
}