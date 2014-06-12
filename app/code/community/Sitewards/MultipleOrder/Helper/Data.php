<?php

/**
 * Sitewards_MultipleOrder_Helper_Data
 *  - Helper containing the checks for
 *      - extension is active
 *
 * @category    Sitewards
 * @package     Sitewards_MultipleOrder
 * @copyright   Copyright (c) 2014 Sitewards GmbH (http://www.sitewards.com/)
 */
class Sitewards_MultipleOrder_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Path for the config for extension active status
     */
    const CONFIG_EXTENSION_ACTIVE = 'multipleorder/generalsettings/active';

    /**
     * Variable for if the extension is active
     *
     * @var bool
     */
    protected $isExtensionActive;

    /**
     * Check to see if the extension is active
     *
     * @return bool
     */
    public function isExtensionActive()
    {
        if ($this->isExtensionActive === null) {
            $this->isExtensionActive = Mage::getStoreConfigFlag(self::CONFIG_EXTENSION_ACTIVE);
        }
        return $this->isExtensionActive;
    }

    /**
     * Check to see if the controller can be dispatched
     *
     * @param Mage_Core_Controller_Front_Action $oController
     */
    public function isDispatchAllowed($oController)
    {
        $sLoginUrl = Mage::helper('customer')->getLoginUrl();

        if (
            !Mage::getSingleton('customer/session')->authenticate($oController, $sLoginUrl)
            || !Mage::helper('sitewards_multipleorder')->isExtensionActive()
        ) {
            $oController->setFlag('', $oController::FLAG_NO_DISPATCH, true);
        }
    }
}