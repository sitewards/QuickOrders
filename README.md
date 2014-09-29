QuickOrders
=============

[![Build Status](https://travis-ci.org/sitewards/QuickOrders.png?branch=master)](https://travis-ci.org/sitewards/QuickOrders)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/sitewards/QuickOrders/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/sitewards/QuickOrders/?branch=master)

The Sitewards QuickOrders extension adds a new form to the customer section that allows a user to add multiple products to the cart via sku.

Features
------------------
* Adds new form to the customer account section
* Form allows to add multiple products to the cart via sku

CHANGES v0.3
-----
* Product image displayed on list
* Extensions checks if product is in stock – if it's not then it's show same error as it wasn't found. 
* Extension checks if product has setup minimal quantity allowed with increments usage (i.e. sold only in packages of 10 pcs): if it is then extension's replacing InnerHTML in <strong>td.qty</strong> which normally contains <strong>INPUT</strong> with a <strong>SELECT</strong> with values incrementation. 
* Extension checks if product normal price is bigger than final price – if it is then extension's adding class name .old-price to price, and additionally displays final price (most often – special price), if it's not then show price in normal way. 
* Polish language pack.

TO DO 
----
* More testing on Magneto 1.8 and any versions that matters this days. 
* Find a way to store products added to list in some session, so if adding fails then list stays as it was - <strong>Contribution seriously appreciated</strong>
* Separate messages for errors with sku/product name which caused the error – many more possibilites to highlight product, etc.
* <strike>Block Enter key to process adding to cart – type="submit"</strike>
* Some CSS fixes for Default Magento theme (border, width %, etc.)


Contact
------------------
magento@sitewards.com
contributor: versedi@gmail.com
License: OSL 3.0

Contribution is appreciated, even new issues!
