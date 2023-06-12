define([
    'jquery',
    'uiComponent',
    'Magento_Ui/js/modal/modal',
], function ($, Component, modal) {
    'use strict';

    return Component.extend({
        defaults: {
            productSku: '',
            tracks: {
                productSku: false
            },
        },

        initialize: function (config) {
            this.productSku = config.productSku
        },

        showBillingPopup: function () {
            const productField = $('#product_sku');
            productField.val(this.productSku);

            const modalBillingDetails = $('#billing-details-popup');
            const options = {
                type: 'popup',
                modalClass: 'billing-modal',
                wrapperClass: 'modals-wrapper',
                buttons: [],
                opened: function () {
                    $(".modal-header").hide();
                }
            };

            modal(options, modalBillingDetails);
            modalBillingDetails.modal('openModal');
        }
    });
});
