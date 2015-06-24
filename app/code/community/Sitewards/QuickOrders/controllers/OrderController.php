<?php

/**
 * Sitewards_QuickOrders_OrderController
 *  - implements actions for quick order form
 *
 * @category    Sitewards
 * @package     Sitewards_QuickOrders
 * @copyright   Copyright (c) 2014 Sitewards GmbH (http://www.sitewards.com/)
 */
class Sitewards_QuickOrders_OrderController extends Mage_Core_Controller_Front_Action
{

    /**
     * Check customer authentication and extension flag
     */
    public function preDispatch()
    {
        parent::preDispatch();
        Mage::helper('sitewards_quickorders')->isDispatchAllowed($this);
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
            $oCart = $this->tryAddProducts();

            if (!$this->getCheckoutSession()->getNoCartRedirect(true)) {
                if (!$oCart->getQuote()->getHasError()) {
                    $sSuccessMessage = $this->__('Your product(s) were added to your shopping cart.');
                    $this->getCheckoutSession()->addSuccess($sSuccessMessage);
                }
                $this->_redirect('checkout/cart');
            }
        } catch (Mage_Core_Exception $oSubmitException) {
            $this->getCustomerSession()->addError($oSubmitException->getMessage());
            $this->_redirect('quickorders/order/form');
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

    /**
     * Try to add products to the cart from the request
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function tryAddProducts()
    {
        $oRequest = $this->getRequest();
        $aSkus = array_filter($oRequest->getParam('sku'));
        $aQtys = $oRequest->getParam('qty');

        foreach ($aSkus as $iKey => $sSku) {
            $iQuantity = isset($aQtys[$iKey]) ? $aQtys[$iKey] : 1;
            $this->addSingleProduct($sSku, $iQuantity);
        }
        $oCart = $this->getCart();
        $oCart->save();

        $this->getCheckoutSession()->setCartWasUpdated(true);
        return $oCart;
    }

    /**
     * Try to add a given product to the cart via sku
     *
     * @param string $sSku
     * @param int $iQuantity
     */
    protected function addSingleProduct($sSku, $iQuantity)
    {
        /** @var Mage_Catalog_Model_Product $oProduct */
        $oProduct = Mage::getModel('catalog/product');
        $iProductId = $oProduct->getIdBySku($sSku);
        if ($this->checkProduct($iProductId, $sSku, $iQuantity) == true) {
            $this->getCart()->addProduct($iProductId, $iQuantity);
        }
    }
    /**
     * Check if variable $iProductId has been set,
     * proceed with adding other products to cart if one has failed.
     * Add checkout session error message with products that were failed to process.
     * @TODO de_DE translation of used strings
     * @param string $sSku
     * @param int $iQuantity
     */
    protected function checkProduct($iProductId, $sSku, $iQuantity)
    {
        if (isset($iProductId)) {
            if ($this->checkProductStockAvailability($iProductId, $iQuantity) == true) {
                return true;
            } else {
                $this->getCheckoutSession()->addError($this->__('Product %s couldn\'t be added to cart because requested quantity exceeded stock availability.', $sSku));
            }
        } else {
            $this->getCheckoutSession()->addError($this->__('Product with code %s doesn\'t exists.', $sSku));
        }
    }
    /**
     * Check if requested product quantity is available in stock
     *
     * @param int $iQuantity
     */
    protected function checkProductStockAvailability($iProductId, $iQuantity)
    {
        /** @var Mage_Catalog_Model_Product $oProduct */
        $oProduct = Mage::getModel('catalog/product');
        $iProductData = $oProduct->load($iProductId);
        $iProductStockQty = Mage::getModel('cataloginventory/stock_item')->loadByProduct($iProductData)->getQty();
        if ($iProductId) {
            if ($iQuantity <= $iProductStockQty) {
                return true;
            }
        }
    }
}