<?php

/**
 * Sitewards_QuickOrders_ProductController
 *  - implements infoAction to validate a product request by sku
 *
 * @category    Sitewards
 * @package     Sitewards_QuickOrders
 * @copyright   Copyright (c) 2013 Sitewards GmbH (http://www.sitewards.com/)
 */
class Sitewards_QuickOrders_ProductController extends Mage_Core_Controller_Front_Action
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
     * gets a sku as input parameter
     * sets JSON response with product data if it is allowed to be viewed
     */
    public function infoAction()
    {
        $sSku = $this->getRequest()->getParam('sku');

        /* @var Mage_Catalog_Model_Product $oProduct */
        $oProduct = Mage::getModel('catalog/product')->loadByAttribute('sku', $sSku);
        if ($this->isProductActive($oProduct) && $oProduct->isAvailable(true)) {
            $this->getResponse()->setHeader('Content-type', 'text/json');
            $this->getResponse()->setBody($this->getInfoResponse($oProduct));
        } else {
            $this->getResponse()->setHttpResponseCode(404);
        }
    }

    /**
     * Check to see if the product is active on the current website
     *
     * @param Mage_Catalog_Model_Product $oProduct
     * @return bool
     */
    protected function isProductActive($oProduct)
    {
        $aCurrentWebsiteId = Mage::app()->getStore()->getWebsiteId();
        return $oProduct
            && $oProduct->getId()
            && is_array($oProduct->getWebsiteIds())
            && in_array($aCurrentWebsiteId, $oProduct->getWebsiteIds());
    }

    /**
     * For a given product produce a json string of all the information
     *
     * @param Mage_Catalog_Model_Product $oProduct
     * @return string
     */
    protected function getInfoResponse($oProduct)
    {
        /** @var Mage_Catalog_Helper_Image $oImageHelper */
        $oImageHelper = Mage::helper('catalog/image');
        /** @var Mage_Core_Helper_Data $oCoreHelper */
        $oCoreHelper = Mage::helper('core');
        /** @var Mage_CatalogInventory_Model_Stock_Item $oStockItem */
        $oStockItem = $oProduct->getStockItem();
        return json_encode(
            array(
                'result'       => 0,
                'sku'          => $oProduct->getSku(),
                'name'         => $oProduct->getName(),
                'image'        => (string)$oImageHelper->init($oProduct, 'image')->resize(40, 40), //gets product image
                'price'        => $oCoreHelper->currency($oProduct->getPrice()),
                'finalprice'   => $oCoreHelper->currency($oProduct->getFinalPrice()),
                'qty'          => $oStockItem->getMinSaleQty(),
            )
        );
    }
}