/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'mage/translate',
    'jquery/ui'
], function ($, alert, $t) {
    'use strict';

    $.widget('mage.beehexaRegisterStore', {
        options: {
            url: '',
            hexasync_url: '',
            elementId: '',
            successText: '',
            failedText: ''
        },

        /**
         * Bind handlers to events
         */
        _create: function () {
            this._on({
                'click': $.proxy(this._connect, this)
            });
        },

        _getServiceEndpoint: function(){
            var hexasync_url = $('#' + 'beehexa_hexasync_production_service').val();
            hexasync_url = hexasync_url.trim().replace(/\/+$/, "");
            return hexasync_url;
        },
        /**
         * Method triggers an AJAX request to check search engine connection
         * @private
         */
        _connect: function () {
            var result = this.options.failedText,
                element = $('#' + this.options.elementId),
                self = this,
                msg = '',
                fieldToCheck = this.options.fieldToCheck || 'success';
            element.removeClass('success').addClass('fail');
            var serviceEndpoint = this._getServiceEndpoint();
            if(!serviceEndpoint){
                alert($t("Service Endpoint is required."));
                return;
            }
            var params = {
                register: true,
                'service_endpoint': serviceEndpoint,
            };
            $.ajax({
                url: this.options.url,
                showLoader: true,
                data: params,
                headers: this.options.headers || {}
            }).done(function (response) {
                if (response[fieldToCheck]) {
                    element.removeClass('fail').addClass('success');
                    result = self.options.successText;
                    var $tokenKey = response['encrypt'];
                    this.child = window.open(
                        serviceEndpoint + '/callback/magento' +
                        '?' + 'k=' + $tokenKey,
                        "Register Store",
                        'width=700,height=620,left=1862,top=326'
                    );
                } else {
                    msg = response.errorMessage;

                    if (msg) {
                        alert({
                            content: msg
                        });
                    }
                }
            }).always(function () {
                $('#' + self.options.elementId + '_result').text(result);
            });
        },
        _checkChild: function () {
            var self = this,
                timer;

            timer = setInterval(function () {
                if (self.child.closed) {
                    window.location.reload();
                    clearInterval(timer);
                }
            }, 1000);
        },
    });

    return $.mage.beehexaRegisterStore;
});
