/**
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/modal/confirm',
    'mage/translate',
    'jquery-ui-modules/widget'
], function ($, alert, confirm, $t) {
    'use strict';

    $.widget('mage.beehexaRegisterStore', {
        options: {
            url: '',
            hexasyncUrl: '',
            allowInsecure: false,
            alreadyActive: false,
            endpointSelector: '#beehexa_hexasync_production_service',
            elementId: '',
            successText: '',
            failedText: '',
            popupName: 'hexasyncRegisterStore',
            popupWidth: 700,
            popupHeight: 620
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
         * Endpoint typed in the form, falling back to the saved configuration value.
         *
         * @returns {String}
         * @private
         */
        _getServiceEndpoint: function () {
            var input = $(this.options.endpointSelector),
                value = input.length ? input.val() : this.options.hexasyncUrl;

            return (value || this.options.hexasyncUrl || '').trim().replace(/\/+$/, '');
        },

        /**
         * Magento posts the store credentials to this address, so only accept https (http while developing).
         *
         * @param {String} endpoint
         * @returns {Boolean}
         * @private
         */
        _isValidEndpoint: function (endpoint) {
            var parsed;

            try {
                parsed = new URL(endpoint);
            } catch (e) {
                return false;
            }

            return parsed.protocol === 'https:' || this.options.allowInsecure && parsed.protocol === 'http:';
        },

        /**
         * Open the popup centered over the current window.
         *
         * It is opened directly from the click handler, before the AJAX call, otherwise popup blockers stop it.
         *
         * @returns {Window|null}
         * @private
         */
        _openPopup: function () {
            var width = this.options.popupWidth,
                height = this.options.popupHeight,
                left = Math.max(0, Math.round(window.screenX + (window.outerWidth - width) / 2)),
                top = Math.max(0, Math.round(window.screenY + (window.outerHeight - height) / 2));

            return window.open(
                'about:blank',
                this.options.popupName,
                'width=' + width + ',height=' + height + ',left=' + left + ',top=' + top
            );
        },

        /**
         * Validate the endpoint, then ask the admin to confirm before any access is granted.
         *
         * @private
         */
        _connect: function () {
            var self = this,
                serviceEndpoint = this._getServiceEndpoint(),
                message;

            if (!serviceEndpoint) {
                alert({
                    content: $t('Service Endpoint is required.')
                });

                return;
            }

            if (!this._isValidEndpoint(serviceEndpoint)) {
                alert({
                    content: $t('Service Endpoint must be a valid https URL, for example https://app.hexasync.com')
                });

                return;
            }

            message = $t('HexaSync will be given API access to this store. You will sign in to HexaSync in a new window to finish.'); // eslint-disable-line max-len

            if (this.options.alreadyActive) {
                message += '<br/><br/>' + $t('This store is already connected. Registering again replaces the current access keys, so syncing pauses until you finish signing in.'); // eslint-disable-line max-len
            }

            confirm({
                title: $t('Register this store with HexaSync?'),
                content: message,
                actions: {
                    /**
                     * Runs inside the click on "OK", so the browser still allows the popup.
                     */
                    confirm: function () {
                        self._startHandshake(serviceEndpoint);
                    }
                }
            });
        },

        /**
         * Start Magento's OAuth handshake and send the admin to the HexaSync sign-in page.
         *
         * Magento posts the consumer credentials to HexaSync server to server; the browser only ever carries the
         * public consumer key and the URL to return to.
         *
         * @param {String} serviceEndpoint
         * @private
         */
        _startHandshake: function (serviceEndpoint) {
            var self = this,
                result = this.options.failedText,
                element = $('#' + this.options.elementId),
                popup = this._openPopup();

            element.removeClass('success').addClass('fail');

            $.ajax({
                url: this.options.url,
                type: 'POST',
                dataType: 'json',
                showLoader: true,
                data: {
                    'form_key': window.FORM_KEY,
                    'service_endpoint': serviceEndpoint
                }
            }).done(function (response) {
                var target;

                if (response && response.success) {
                    target = response['identity_link_url'] + '?' + $.param({
                        'oauth_consumer_key': response['oauth_consumer_key'],
                        'success_call_back': response['success_call_back']
                    });

                    if (!popup || popup.closed) {
                        popup = window.open(target, self.options.popupName);
                    } else {
                        popup.location.href = target;
                    }

                    if (!popup) {
                        alert({
                            content: $t('Please allow popups for this site, then click Register Store again.')
                        });

                        return;
                    }

                    element.removeClass('fail').addClass('success');
                    result = self.options.successText;
                    self._reloadWhenClosed(popup);

                    return;
                }

                self._closePopup(popup);
                alert({
                    content: response && response.errorMessage ? response.errorMessage : self.options.failedText
                });
            }).fail(function () {
                self._closePopup(popup);
                alert({
                    content: self.options.failedText
                });
            }).always(function () {
                $('#' + self.options.elementId + '_result').text(result);
            });
        },

        /**
         * @param {Window|null} popup
         * @private
         */
        _closePopup: function (popup) {
            if (popup && !popup.closed) {
                popup.close();
            }
        },

        /**
         * Reload the configuration page once the popup is closed so the registered information is refreshed.
         *
         * @param {Window} popup
         * @private
         */
        _reloadWhenClosed: function (popup) {
            var timer = setInterval(function () {
                if (popup.closed) {
                    clearInterval(timer);
                    window.location.reload();
                }
            }, 1000);
        }
    });

    return $.mage.beehexaRegisterStore;
});
