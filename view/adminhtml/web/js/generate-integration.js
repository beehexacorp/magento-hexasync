/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'jquery',
    'Magento_Ui/js/modal/confirm',
    'Magento_Ui/js/modal/alert',
    'jquery/ui'
], function ($, confirm, alert) {
    'use strict';

    $.widget('mage.beehexaGenerateIntegration', {
        options: {
            url: '',
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

        /**
         * Method triggers an AJAX request to check search engine connection
         * @private
         */
        _connect: function () {
            var result = this.options.failedText,
                element =  $('#' + this.options.elementId),
                self = this,
                params = {regenerate: true},
                msg = '',
                fieldToCheck = this.options.fieldToCheck || 'success';

            element.removeClass('success').addClass('fail');
            $.ajax({
                url: this.options.url,
                showLoader: true,
                data: params,
                headers: this.options.headers || {}
            }).done(function (response) {
                if (response[fieldToCheck]) {
                    element.removeClass('fail').addClass('success');
                    result = self.options.successText;
                    confirmSetLocation('Active Integration Account?',
                        $("[data-ui-id=\"menu-magento-integration-system-integrations\"] a").attr('href'));
                } else {
                    msg = response.message ? response.message : response.errorMessage;

                    if (msg) {
                        alert({
                            content: msg
                        });
                    }
                }
            }).always(function () {
                $('#' + self.options.elementId + '_result').text(result);
            });
        }
    });

    return $.mage.beehexaGenerateIntegration;
});
