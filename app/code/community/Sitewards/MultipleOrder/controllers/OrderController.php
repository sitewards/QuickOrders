<?php

/**
 * Sitewards_MultipleOrder_OrederController
 *  - implements actions for new order form and cancel an order
 *
 * @category    Sitewards
 * @package     Sitewards_MultipleOrder
 * @copyright   Copyright (c) 2014 Sitewards GmbH (http://www.sitewards.com/)
 */
class Sitewards_MultipleOrder_OrderController extends Mage_Core_Controller_Front_Action
{
    /**
     * Check customer authentication and extension flag
     */
    public function preDispatch()
    {
        parent::preDispatch();
        $sLoginUrl = Mage::helper('customer')->getLoginUrl();

        if (
            !Mage::getSingleton('customer/session')->authenticate($this, $sLoginUrl)
            || !Mage::helper('sitewards_multipleorder')->isExtensionActive()
        ) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }

    /**
     * Inital form display action
     */
    public function formAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }


    /**
     * Submits all the products from the multiple product addition form
     */
    public function submitAction()
    {
        try {
            $oRequest = $this->getRequest();
            $aSkus = array_filter($oRequest->getParam('sku'));
            $aQtys = $oRequest->getParam('qty');

            $oCart = $this->getCart();
            foreach ($aSkus as $iKey => $sSku) {
                $iQty = isset($aQtys[$iKey]) ? $aQtys[$iKey] : 1;

                /** @var Mage_Catalog_Model_Product $oProduct */
                $oProduct = Mage::getModel('catalog/product');
                $iProductId = $oProduct->getIdBySku($sSku);
                if (isset($iProductId)) {
                    $oCart->addProduct($iProductId, $iQty);
                }
            }
            $oCart->save();

            $this->getCheckoutSession()->setCartWasUpdated(true);

            if (!$this->getCheckoutSession()->getNoCartRedirect(true)) {
                if (!$oCart->getQuote()->getHasError()) {
                    $sSuccessMessage = $this->__('Your product(s) where added to your shopping cart.');
                    $this->getCheckoutSession()->addSuccess($sSuccessMessage);
                }
                $this->_redirect('checkout/cart');
            }
        } catch (Mage_Core_Exception $e) {
            $this->getCustomerSession()->addError(
                $this->__('Please enter valid product sku.')
            );

            $this->_redirect('multipleorder/order/form');
        }
    }

    /**
     * Get checkout session model instance
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get the current customer session
     *
     * @return Mage_Customer_Model_Session
     */
    protected function getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }
}