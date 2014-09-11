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
            $sResponse = json_encode(
                array(
                    'result' => 0,
                    'sku' => $oProduct->getSku(),
                    'name' => $oProduct->getName(),
					'availability' =>  $oProduct->getStockItem()->getQty(), //gets product availability in stock (pcs)
					'image' => $oProduct->getImageUrl(), //gets product image
                    'price' => Mage::helper('core')->currency($oProduct->getPrice()),
                    'finalprice' => Mage::helper('core')->currency($oProduct->getFinalPrice()),
                    'qty' => $oProduct->getStockItem()->getMinSaleQty(),
                )
            );
            $this->getResponse()->setHeader('Content-type', 'text/json');
            $this->getResponse()->setBody($sResponse);
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
	
	
}
