
Update 1  
ordercontroller.php
-----

Updated line 49 so it's displaying right error – why the products wasn't added to cart. In future I'd like to show which product wasn't add and add to cart all that has passed the if checking.


Update 2 
-----

###Changes:

* Added product image,
* Added checking if product is in stock - if no, it's not found,
* Added checking if product has minimal allowed quantity and uses increments -> if yes than replacing innerHTML of td.qty with Select instead of input,
* Added Polish language pack,

Planning to do:
-------
If failed to add to cart - restore entered products in list from session. 


Update 3
-----

###Changes

* Before there was always $oProduct->getPrice which in case that product had special price was displaying on frontend not the right price.
* Added 'finalprice'=>Mage::helper('core')->currency($oProduct->getFinalPrice()), in ProductController.php on line 41.
* Added in JS:
* Checking if price is equal to final product price. If it is - then show only price. If its not then add class names (old-price) to price, and display additional final product price.
* Some CSS cleaning as some attributes were specified in bad way

To Do
------
* Check if form.html don't have some custom id's/classes from Foundation framework. (i.e. <i class="fi", <button class="btn-cart", etc.

Update 4
-----

###Changes
* Cleaned form.html and css from remaining custom theme elements
* All updates included, optimalized for Default Magento theme

