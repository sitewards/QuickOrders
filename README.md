QuickOrders Dev-Patch-03
=============
Changes
-------------
* Before there was always $oProduct->getPrice which in case that product had special price was displaying on frontend not the right price.
* Added 'finalprice'=>Mage::helper('core')->currency($oProduct->getFinalPrice()), in ProductController.php on line 41. 
* Added in JS:
* * Checking if price is equal to final product price. If it is - then show only price. If its not then add class names (old-price) to price, and display additional final product price. 
*Some CSS cleaning as some attributes were specified in bad way

To Do
---------
*Check if form.html don't have some custom id's/classes from Foundation framework. (i.e. <i class="fi", <button class="btn-cart", etc.) 

