document.observe(
    'dom:loaded',
    /**
     * initializes first product
     */
        function() {
        var oProduct = new OrderProduct($$('form#order_form .product')[0]);
    }
);

/**
 * presentation of a product row
 */
var OrderProduct = Class.create(
    {
        /**
         * prototype element of the .product element
         */
        _oLine: null,

        /**
         * URL of loading image
         */
        _sLoadingImage: null,

        /**
         * initializes observer for sku
         *
         * @param oLine
         */
        initialize: function (oLine) {
            if (typeof oLine == 'undefined') {
                return;
            }
            this._oLine = oLine;
            this.getElement('input.sku').observe(
                'change',
                this._onChangeSku.bind(this)
            );
            this.getElement('input.qty').style.display = 'none';
            this.getElement('td.qty').style.display = 'table-cell';
            this._sLoadingImage = $$('.sitewards-quickorders-order-form .loading').first().src;

            var that = this;
            this.getElement('a.remove')
                .observe('click', function (oEvent) {
                    that._onRemove();
                    oEvent.preventDefault();
                    return false;
                })
                .hide()
            ;
        },

        /**
         * displays more information about product, if allowed
         *
         * @private
         */
        _onChangeSku: function () {
            new Ajax.Request('/quickorders/product/info', {
                method: 'get',
                parameters: {
                    'sku' : this.getElement('input.sku').value
                },
                requestHeaders: {
                    Accept: 'application/json'
                },
                onSuccess: this._onSuccess.bind(this),
                onFailure: this._onFailure.bind(this)
            });
            this.getElement('.name').update('<img src="'+this._sLoadingImage+'">');
            this.getElement('input.qty').style.display = 'none';
            this.getElement('td.qty').style.display = 'table-cell';
        },

        /**
         * returns element of this product element
         *
         * @param sSelector
         * @returns {*}
         */
        getElement: function (sSelector) {
            return this._oLine.down(sSelector);
        },

        /**
         * duplicates current line
         *
         * @private
         */
        _duplicateLine: function () {
            var oParent = this._oLine.up();
            oParent.insert(this._oLine.outerHTML);
            var oProduct = new OrderProduct(oParent.childElements().last());
            oProduct._reset();
        },

        /**
         * Build the quantity select inner html
         *
         * @param oResponse
         * @param oQty
         * @private
         */
        _buildQuantitySelect: function (oResponse, oQty) {
            if (this.getElement('input.qty').value > 1) { //if product has minimal allowed quantity bigger than 1
                var sQuantityHtml, iQuantityNumber;
                iQuantityNumber = parseInt(this.getElement('input.qty').value, 10);
                sQuantityHtml = '<select type="text" name="qty[]" class="qty" selected="selected">';
                //do it while number is lower than available pcs
                for (var i = 0; iQuantityNumber < oResponse.availability; ++i) {
                    sQuantityHtml += '<option value="' + iQuantityNumber + '">' + iQuantityNumber + '</option>';
                    iQuantityNumber = iQuantityNumber + oResponse.qty; //number=minimum quantity incrementing
                }
                sQuantityHtml += '</select>';
                this.getElement('td.qty').innerHTML = sQuantityHtml; //replace innerhtml of td qty with html value
                oQty.value = e.options[e.selectedIndex].value; //get selected value from dropdown list
            }
        },

        /**
         * Build the price html element and content
         *
         * @param oResponse
         * @private
         */
        _buildPriceElement: function (oResponse) {
            //Check if normal price is equal to final product price
            if (oResponse.price == oResponse.finalprice) {
                this.getElement('.price').update(oResponse.price); //display only normal product price
                this.getElement('.price').className = 'price'; //if its same, then do not stylize
            } else { //the normal price is not equal to final price
                this.getElement('.price').update(oResponse.price);
                this.getElement('.price').className += ' old-price'; //the normal price is bigger then special price so add stylizing
                this.getElement('.finalprice').update(oResponse.finalprice); //display final product price
            }
        },

        /**
         * displays information about product
         *
         * @param transport
         * @private
         */
        _onSuccess: function(transport) {
            this._clearMessages();
            var oResponse = transport.responseText.evalJSON(true);
            if (oResponse.result === 0) {
                var oQty = this.getElement('input.qty');
                oQty.value = Math.max(1, oResponse.qty);
                oQty.disabled = false;
                this.getElement('.name').update(oResponse.name);
                this.getElement('.img').update('<img src="' + oResponse.image + '" class="product-img" />');
                this._buildPriceElement(oResponse);
                if (this._hasEmptyLineInForm() === false) {
                    this._duplicateLine();
                }

                this.getElement('input.qty').style.display = 'block';
                oQty.focus();
                oQty.select();

                if (this.getElement('input.sku').value.length > 0) {
                    this.getElement('a.remove').show();
                }
            } else {
                this._reset();
                this.getElement('input.sku').value = '';
                this._showMessage(oResponse.error);
                this.getElement('input.sku').focus();
            }

            this._buildQuantitySelect(oResponse, oQty);
        },

        /**
         * Show standard magento message
         *
         * @param sText
         * @param sType
         * @private
         */
        _showMessage : function (sText) {
            $$('.messages')[0].style.display = 'block';
            $$('.messages>li>ul>li')[0].update(sText);
        },

        /**
         * Remove all messages
         *
         * @private
         */
        _clearMessages : function () {
            $$('.messages>li>ul>li')[0].update('');
            $$('.messages')[0].style.display = 'none';
        },

        /**
         * Determines if there is an empty line in the form
         *
         * @return {Boolean} true if there is an empty line
         * @private
         */
        _hasEmptyLineInForm : function () {
            var aLines = this._oLine.up('tbody').select('tr');
            for (var i=0; i < aLines.length; i++) {
                var oInput = $(aLines[i]).select('input').first();
                if (oInput.value === '') {
                    return true;
                }
            }
            return false;
        },

        /**
         * resets information about product
         *
         * @private
         */
        _reset: function () {
            this.getElement('input.qty').update('');
            this.getElement('input.qty').disabled = 'disabled';
            this.getElement('.name').update('');
            this.getElement('.price').update('');
            this.getElement('.finalprice').update('');
            this.getElement('.img').update('');
            this.getElement('.availability').update('');
        },

        /**
         * remove last empty row after a failed ajax request, except current row is last row
         *
         * @private
         */
        _removeEmptyRows: function () {
            var oLastLine = this._oLine.up().childElements().last();
            if (oLastLine != this._oLine) {
                oLastLine.remove();
            }
        },

        /**
         * removes the line from the form
         *
         * @private
         */
        _onRemove: function () {
            var oLinesContainer = this._oLine.up();
            this._oLine.remove();
            // hide "remove line" if only one line is left
            if (oLinesContainer.childElements().length <= 1) {
                oLinesContainer.down('a.remove').hide();
            }
        },

        /**
         * called on ajax request failure
         *
         * @private
         */
        _onFailure: function () {
            this._reset();
            this._removeEmptyRows();
            this._showMessage(Translator.translate('The product does not exist.'));
            this.getElement('.name').focus();
        }
    }
);