define([
    'jquery',
    'uiComponent',
    'Magento_Ui/js/modal/modal',
    'Magento_Ui/js/modal/alert',
    'mage/url',
    'mage/translate',
], function ($, Component, modal, alert, url) {
    'use strict';

    return Component.extend({

        submit: function (form) {
            let billingDetailsForm = $('#billing-details-form');
            if (!billingDetailsForm.valid()) {
                return false;
            }

            const formData = new FormData(form);

            $.ajax({
                url: url.build("sampleproduct/submit/index"),
                data: formData,
                type: 'POST',
                cache: false,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    $('body').trigger('processStart');
                },
                complete: function () {
                    $('body').trigger('processStop');
                    form.reset();
                },
                success: (function (data) {
                    if (data.status === true) {
                        this.showSuccessAlert(data.message);

                    } else {
                        this.showErrorAlert(data.message);
                    }
                }).bind(this),
                error: (function (err) {
                    $('body').trigger('processStop');
                    this.showErrorAlert('Error while submitting your request.');
                }).bind(this)
            });
        },

        closeModal: function () {
            $('#billing-details-popup').modal('closeModal');
        },

        showSuccessAlert: function ($msg) {
            this.closeModal();

            alert({
                title: $.mage.__('Successful'),
                content : $.mage.__($msg),
                modalClass: 'modal-slide',
                buttons: [{
                    text: $.mage.__('Ok'),
                    class: 'button action action--medium primary',
                }],
                focus: "button.action.action--medium.primary",
                actions: {
                    always: function (){}
                }
            });
        },

        showErrorAlert: function ($msg) {
            this.closeModal();

            alert({
                content : $.mage.__($msg),
                modalClass: 'modal-slide',
                buttons: [{
                    text: $.mage.__('Ok'),
                    class: 'button action action--medium primary',
                }],
                focus: "button.action.action--medium.primary",
                actions: {
                    always: function (){}
                }
            });
        },
    });
});
