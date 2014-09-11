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
            this.getElement('.qty').style.display = 'none';
            this._sLoadingImage = $$('.sitewards-quickorders-order-form .loading').first().src;

            var that = this;
            this.getElement('.remove')
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
            new Ajax.Request('../../../quickorders/product/info', {
                method: 'get',
                parameters: {
                    'sku' : this.getElement('input.sku').value
                },
                requestHeaders: {Accept: 'application/json'},
                onSuccess: this._onSuccess.bind(this),
                onFailure: this._onFailure.bind(this)
            });
            this.getElement('.name').update('<img src="'+this._sLoadingImage+'">');
            this.getElement('.qty').style.display = 'none';
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
         * displaysinformation about product
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
                this.getElement('.price').update(oResponse.price);
                if (this._hasEmptyLineInForm() === false) {
                    this._duplicateLine();
                }
                this.getElement('.qty').style.display = 'block';
                oQty.focus();
                oQty.select();

                if (this.getElement('input.sku').value.length > 0) {
                    this.getElement('.remove').show();
                }
            } else {
                this._reset();
                this.getElement('input.sku').value = '';
                this._showMessage(oResponse.error);
                this.getElement('input.sku').focus();
            }
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
                oLinesContainer.down('.remove').hide();
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
