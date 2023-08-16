jQuery.noConflict();
(function ($) {

    "use strict";

    $(function () {

        const INVOICE_DISPLAY_MODE_FEW = 0;
        const INVOICE_DISPLAY_MODE_HEAD = 1;
        const INVOICE_DISPLAY_MODE_ALL = 2;

        var reCAPTCHAWidgetId = null;
        var googleReCAPTCHA = null;
        window.addEventListener('load', function () {
            var emailFormCAPTCHA = document.getElementById('wpfs-enter-email-address-form-recaptcha');
            //noinspection JSUnresolvedVariable
            if (window.grecaptcha !== 'undefined' && emailFormCAPTCHA !== null) {
                //noinspection JSUnresolvedVariable
                googleReCAPTCHA = window.grecaptcha;
                //noinspection JSUnresolvedVariable
                var parameters = {
                    "sitekey": wpfsGoogleReCAPTCHASiteKey
                };
                reCAPTCHAWidgetId = googleReCAPTCHA.render(emailFormCAPTCHA, parameters);
            }
        }, true);

        function scrollToElement($anElement) {
            if ($anElement && $anElement.offset() && $anElement.offset().top) {
                $('html, body').animate({
                    scrollTop: $anElement.offset().top - 100
                }, 1000);
            }
        }

        function logError(handlerName, jqXHR, textStatus, errorThrown) {
            if (window.console) {
                console.log(handlerName + '.error(): textStatus=' + textStatus);
                console.log(handlerName + '.error(): errorThrown=' + errorThrown);
                if (jqXHR) {
                    console.log(handlerName + '.error(): jqXHR.status=' + jqXHR.status);
                    console.log(handlerName + '.error(): jqXHR.responseText=' + jqXHR.responseText);
                }
            }
        }

        function resetCaptcha() {
            if (googleReCAPTCHA != null && reCAPTCHAWidgetId != null) {
                googleReCAPTCHA.reset(reCAPTCHAWidgetId);
            }
        }

        function showLoadingIcon($form) {
            $form.find('button').addClass('wpfs-btn-primary--loader');
        }

        function hideLoadingIcon($form) {
            $form.find('button').removeClass('wpfs-btn-primary--loader');
        }

        function disableSubmitButton($form) {
            $form.find('button').prop('disabled', true);
        }

        function enableSubmitButton($form) {
            $form.find('button').prop('disabled', false);
        }

        function handleSetupIntentAction($form, card, data) {
            stripe.handleCardSetup(data.setupIntentClientSecret).then(function (result) {
                // console.log('handleSetupIntentAction(): result=' + JSON.stringify(result));
                if (result.error) {
                    logError('handleSetupIntentAction', null, result.error.message, result.error);
                    showFormFeedBackError($form, result.error.message);
                } else {
                    disableSubmitButton($form);
                    showLoadingIcon($form);
                    submitCardData($form, card, result.setupIntent.payment_method, result.setupIntent.id);
                }
            });
        }

        function submitCardData($form, card, paymentMethodId, setupIntentId) {
            // console.log('submitCardData(): CALLED, params: paymentMethodId=' + paymentMethodId + ', setupIntentId=' + setupIntentId);
            clearFormFeedBack($form);
            clearFieldErrors($form);
            $.ajax({
                type: "POST",
                url: wpfsAjaxURL,
                data: {
                    action: 'wp_full_stripe_update_card',
                    sessionId: wpfsCardUpdateSessionData.sessionId,
                    paymentMethodId: paymentMethodId,
                    setupIntentId: setupIntentId
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    // console.log('submitCardData(): data=' + JSON.stringify(data));
                    if (data.success) {
                        showFormFeedBackSuccess($form, data.message);
                        setTimeout(function () {
                            window.location = window.location.pathname;
                        }, 1000);
                    } else if (data.requiresAction) {
                        handleSetupIntentAction($form, card, data);
                    } else if (data.ex_message) {
                        showFormFeedBackError($form, data.ex_message);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    logError('submitCardData', jqXHR, textStatus, errorThrown);
                },
                complete: function () {
                    hideLoadingIcon($form);
                    enableSubmitButton($form);
                }
            });
        }

        function showFieldError($field, fieldErrorMessage) {
            $field.addClass('wpfs-form-control--error');
            var $fieldError = $('<div>', {
                class: 'wpfs-form-error-message'
            }).html(fieldErrorMessage);
            $fieldError.insertAfter($field);
        }

        function clearFieldErrors($form) {
            $('.wpfs-form-control--error', $form).removeClass('wpfs-form-control--error');
            $('div.wpfs-form-error-message', $form).remove();
        }

        function getParentForm(element) {
            return $(element).parents('form:first');
        }

        function clearFormFeedBack($form) {
            var $formFeedBack = $form.prev('.wpfs-form-message');
            if ($formFeedBack.length > 0) {
                $formFeedBack.remove();
            }
        }

        function showFormFeedBackSuccess($form, message) {
            var $formFeedBack = $('<div>', {
                class: 'wpfs-form-message wpfs-form-message--correct wpfs-form-message--sm-icon '
            }).html(message);
            $formFeedBack.insertBefore($form);
        }

        function showFormFeedBackError($form, message) {
            var $formFeedBack = $('<div>', {
                class: 'wpfs-form-message wpfs-form-message--incorrect wpfs-form-message--sm-icon '
            }).html(message);
            $formFeedBack.insertBefore($form);
        }

        /**
         * @deprecated
         * @param $form
         * @param token
         */
        function stripeTokenHandler($form, token) {

            clearFormFeedBack($form);
            clearFieldErrors($form);

            //noinspection JSUnresolvedVariable
            $.ajax({
                type: 'POST',
                url: wpfsAjaxURL,
                data: {
                    action: 'wp_full_stripe_update_card',
                    sessionId: wpfsCardUpdateSessionData.sessionId,
                    token: token
                },
                cache: false,
                dataType: 'json',
                success: function (data) {
                    if (data.success) {
                        showFormFeedBackSuccess($form, data.message);
                        setTimeout(function () {
                            window.location = window.location.pathname;
                        }, 1000);
                    } else {
                        //noinspection JSUnresolvedVariable
                        if (data.ex_message) {
                            //noinspection JSUnresolvedVariable
                            showFormFeedBackError($form, data.ex_message);
                        } else {
                            console.log(JSON.stringify(data));
                        }
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    logError('stripeTokenHandler', jqXHR, textStatus, errorThrown);
                },
                complete: function () {
                    hideLoadingIcon($form);
                    enableSubmitButton($form);
                }
            });

        }

        function updateCancelSubscriptionSubmitButton() {
            var selectedSubscriptionCount = $('.wpfs-form-check-input:checked').length;
            var cancelSubscriptionSubmitButtonCaption = null;
            if (selectedSubscriptionCount > 0) {
                $('#wpfs-button-cancel-subscription').prop('disabled', false);
                if (selectedSubscriptionCount == 1) {
                    //noinspection JSUnresolvedVariable
                    if (wpfsCardUpdateSessionData !== 'undefined') {
                        //noinspection JSUnresolvedVariable
                        cancelSubscriptionSubmitButtonCaption = wpfsCardUpdateSessionData.i18n.cancelSubscriptionSubmitButtonCaptionSingular;
                        $('#wpfs-button-cancel-subscription').html(cancelSubscriptionSubmitButtonCaption);
                    }
                } else {
                    //noinspection JSUnresolvedVariable
                    if (wpfsCardUpdateSessionData !== 'undefined') {
                        //noinspection JSUnresolvedVariable
                        cancelSubscriptionSubmitButtonCaption = vsprintf(wpfsCardUpdateSessionData.i18n.cancelSubscriptionSubmitButtonCaptionPlural, [selectedSubscriptionCount]);
                        $('#wpfs-button-cancel-subscription').html(cancelSubscriptionSubmitButtonCaption);
                    }
                }
            } else {
                $('#wpfs-button-cancel-subscription').prop('disabled', true);
                //noinspection JSUnresolvedVariable
                if (wpfsCardUpdateSessionData !== 'undefined') {
                    //noinspection JSUnresolvedVariable
                    cancelSubscriptionSubmitButtonCaption = wpfsCardUpdateSessionData.i18n.cancelSubscriptionSubmitButtonCaptionDefault;
                    $('#wpfs-button-cancel-subscription').html(cancelSubscriptionSubmitButtonCaption);
                }
            }
        }

        //noinspection JSUnresolvedVariable
        var stripe = Stripe(wpfsStripeKey);

        var WPFS = {};

        WPFS.initStepper = function () {
            var $stepper = $('[data-toggle="stepper"]');
            $stepper.each(function () {
                var $this = $(this);
                var defaultValue = $this.data('defaultValue') || 1;

                if ($this.val() === '') {
                    $this.val(defaultValue);
                }

                $this.spinner({
                    min: $this.data('min') || 1,
                    max: $this.data('max') || 9999,
                    icons: {
                        down: 'wpfs-icon-decrease',
                        up: 'wpfs-icon-increase'
                    },
                    change: function (e, ui) {
                        var $this = $(this);
                        if ($this.spinner('isValid')) {
                            defaultValue = $this.val();
                        } else {
                            $this.val(defaultValue);
                        }
                    },
                    spin: function (e, ui) {
                        var $this = $(this);
                        var uiSpinner = $this.data('uiSpinner');
                        var min = uiSpinner.options.min;
                        var max = uiSpinner.options.max;
                        var $container = $this.parent();
                        var disabledClassName = 'ui-state-disabled';
                        var up = $container.find('.ui-spinner-up');
                        var down = $container.find('.ui-spinner-down');

                        up.removeClass(disabledClassName);
                        down.removeClass(disabledClassName);

                        if (ui.value === max) {
                            up.addClass(disabledClassName);
                        }

                        if (ui.value === min) {
                            down.addClass(disabledClassName);
                        }
                    }
                })
                    .parent()
                    .find('.ui-icon').text('');
            });
        };
        WPFS.initEnterEmailAddressForm = function () {
            $('#wpfs-enter-email-address-form').submit(function (e) {

                e.preventDefault();

                var $form = $(this);

                clearFieldErrors($form);
                disableSubmitButton($form);
                showLoadingIcon($form);

                var emailAddress = $form.find('input[name="wpfs-email-address"]').val();
                var googleReCAPTCHAResponse = $form.find('textarea[name="g-recaptcha-response"]').val();

                $.ajax({
                    type: 'POST',
                    url: wpfsAjaxURL,
                    data: {
                        action: 'wp_full_stripe_create_card_update_session',
                        emailAddress: emailAddress,
                        googleReCAPTCHAResponse: googleReCAPTCHAResponse
                    },
                    cache: false,
                    dataType: 'json',
                    success: function (data) {
                        if (data.success) {
                            window.location = window.location.pathname;
                        } else {
                            var $field;
                            if (data.fieldError && 'emailAddress' === data.fieldError) {
                                $field = $('input[name="wpfs-email-address"]', $form);
                            } else if (data.fieldError && 'googleReCAPTCHAResponse' === data.fieldError) {
                                $field = $('div#wpfs-enter-email-address-form-recaptcha', $form);
                            }
                            showFieldError($field, data.message);
                            resetCaptcha();
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        logError('wpfs-enter-email-address-form.submit', jqXHR, textStatus, errorThrown);
                    },
                    complete: function () {
                        enableSubmitButton($form);
                        hideLoadingIcon($form);
                    }
                });

                return false;
            });
        };
        WPFS.initEnterSecurityCodeForm = function () {
            $('.wpfs-nav-back-to-email-address').click(function (e) {

                e.preventDefault();

                var $form = getParentForm(this);

                disableSubmitButton($form);
                showLoadingIcon($form);

                $.ajax({
                    type: 'POST',
                    url: wpfsAjaxURL,
                    data: {
                        action: 'wp_full_stripe_reset_card_update_session',
                        sessionId: wpfsCardUpdateSessionData.sessionId
                    },
                    cache: false,
                    dataType: 'json',
                    success: function (data) {
                        window.location = window.location.pathname;
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        logError('.wpfs-nav-back-to-email-address.click', jqXHR, textStatus, errorThrown);
                    },
                    complete: function () {
                        enableSubmitButton($form);
                        hideLoadingIcon($form);
                    }
                });

                return false;
            });
            $('#wpfs-enter-security-code-form').submit(function (e) {

                e.preventDefault();

                var $form = $(this);

                disableSubmitButton($form);
                clearFieldErrors($form);
                showLoadingIcon($form);

                var securityCode = $('input[name="wpfs-security-code"]', $form).val();

                //noinspection JSUnresolvedVariable
                $.ajax({
                    type: 'POST',
                    url: wpfsAjaxURL,
                    data: {
                        action: 'wp_full_stripe_validate_security_code',
                        sessionId: wpfsCardUpdateSessionData.sessionId,
                        securityCode: securityCode
                    },
                    cache: false,
                    dataType: 'json',
                    success: function (data) {
                        if (data.success) {
                            window.location = window.location.pathname;
                        } else {
                            showFieldError($('input[name="wpfs-security-code"]', $form), data.message);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        logError('#wpfs-enter-security-code-form.submit', jqXHR, textStatus, errorThrown);
                    },
                    complete: function () {
                        enableSubmitButton($form);
                        hideLoadingIcon($form);
                    }
                });

                return false;
            });
        };
        WPFS.initUpdateCardForm = function () {
            // tnagy init Stripe Elements Card
            var $card = $('#wpfs-update-card-form [data-toggle="card"]');
            var elements;
            var card;
            if ($card.length > 0) {
                elements = stripe.elements();
                card = elements.create('card', {
                    hidePostalCode: true,
                    classes: {
                        base: 'wpfs-form-card',
                        empty: 'wpfs-form-control--empty',
                        focus: 'wpfs-form-control--focus',
                        complete: 'wpfs-form-control--complete',
                        invalid: 'wpfs-form-control--error'
                    },
                    style: {
                        base: {
                            color: '#2F2F37',
                            fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Oxygen-Sans", Ubuntu, Cantarell, "Helvetica Neue", sans-serif',
                            fontSmoothing: 'antialiased',
                            fontSize: '15px',
                            '::placeholder': {
                                color: '#7F8393'
                            }
                        },
                        invalid: {
                            color: '#2F2F37',
                            iconColor: '#CC3434'
                        }
                    }
                });

                card.mount('#wpfs-update-card-form [data-toggle="card"]');
                card.addEventListener('change', function (event) {
                    var $form = getParentForm(this);
                    if (event.error) {
                        clearFieldErrors($form);
                        showFieldError($('#wpfs-card', $form), event.error.message);
                    } else {
                        clearFieldErrors($form);
                    }
                });
            }

            // tnagy init card update form
            $('#wpfs-anchor-logout').click(function (e) {
                e.preventDefault();
                //noinspection JSUnresolvedVariable
                $.ajax({
                    type: 'POST',
                    url: wpfsAjaxURL,
                    data: {
                        action: 'wp_full_stripe_reset_card_update_session',
                        sessionId: wpfsCardUpdateSessionData.sessionId
                    },
                    cache: false,
                    dataType: 'json',
                    success: function (data) {
                        window.location = window.location.pathname;
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        logError('#wpfs-anchor-logout.click', jqXHR, textStatus, errorThrown);
                    },
                    complete: function () {
                    }
                });
            });
            $('#wpfs-anchor-update-card').click(function () {
                if (card != null) {
                    card.clear();
                }
                $('#wpfs-default-card-form').hide();
                $('#wpfs-update-card-form').show();
                card.focus();
            });
            $('#wpfs-anchor-discard-card-changes').click(function () {
                if (card != null) {
                    card.clear();
                }
                $('#wpfs-default-card-form').show();
                $('#wpfs-update-card-form').hide();
            });
            $('#wpfs-update-card-form').submit(function (e) {

                e.preventDefault();

                var $form = $(this);

                disableSubmitButton($form);
                showLoadingIcon($form);
                clearFormFeedBack($form);

                stripe.createPaymentMethod('card', card, {}).then(
                    function (createPaymentMethodResult) {
                        console.log('initUpdateCardForm(): ' + 'PaymentMethod creation result=' + JSON.stringify(createPaymentMethodResult));
                        clearFieldErrors($form);
                        if (createPaymentMethodResult.error) {
                            enableSubmitButton($form);
                            hideLoadingIcon($form);
                            showFieldError($('#wpfs-card', $form), createPaymentMethodResult.error.message);
                        } else {
                            var paymentMethodId = null;
                            if (
                                typeof(createPaymentMethodResult) !== 'undefined'
                                && createPaymentMethodResult.hasOwnProperty('paymentMethod')
                                && createPaymentMethodResult.paymentMethod.hasOwnProperty('id')
                            ) {
                                paymentMethodId = createPaymentMethodResult.paymentMethod.id;
                            }
                            submitCardData($form, card, paymentMethodId);
                        }
                    }
                );

                return false;
            });
        };

        function attachCancelSubscriptionFormEvents() {
            $('#wpfs-cancel-subscription-form').submit(function (e) {
                e.preventDefault();

                var $form = $(this);

                disableSubmitButton($form);
                showLoadingIcon($form);
                clearFormFeedBack($form);

                // tnagy create form data array
                var data = $form.serializeArray();
                // tnagy add action and session ID
                data.push({name: "action", value: 'wp_full_stripe_cancel_my_subscription'});
                //noinspection JSUnresolvedVariable
                data.push({name: "sessionId", value: wpfsCardUpdateSessionData.sessionId});

                // tnagy collect selected subscription IDs
                var selectedSubscriptionIds = [];
                for (var i = 0; i < data.length; i++) {
                    var item = data[i];
                    if (item && item.name && item.name == 'wpfs-subscription-id[]') {
                        selectedSubscriptionIds.push(item.value);
                    }
                }

                // tnagy validate selection
                var valid = true;
                if (selectedSubscriptionIds.length == 0) {
                    valid = false;
                    //noinspection JSUnresolvedVariable
                    showFormFeedBackError($form, wpfsCardUpdateSessionData.i18n.selectAtLeastOneSubscription);
                }

                if (valid) {

                    //noinspection JSUnresolvedVariable
                    var confirmationResult = confirm(wpfsCardUpdateSessionData.i18n.confirmSubscriptionCancellationMessage);

                    if (confirmationResult == true) {
                        //noinspection JSUnresolvedVariable
                        $.ajax({
                            type: 'POST',
                            url: wpfsAjaxURL,
                            data: $.param(data),
                            cache: false,
                            dataType: 'json',
                            success: function (data) {
                                if (data.success) {
                                    showFormFeedBackSuccess($form, data.message);
                                    setTimeout(function () {
                                        window.location = window.location.pathname;
                                    }, 1000);
                                } else {
                                    showFormFeedBackError($form, data.message);
                                }
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                logError('#wpfs-cancel-subscription-form.submit', jqXHR, textStatus, errorThrown);
                            },
                            complete: function () {
                                enableSubmitButton($form);
                                hideLoadingIcon($form);
                            }
                        });
                    } else {
                        enableSubmitButton($form);
                        hideLoadingIcon($form);
                    }
                } else {
                    enableSubmitButton($form);
                    hideLoadingIcon($form);
                }

                return false;

            });
        }

        WPFS.initCancelSubscriptionForm = function () {
            if (wpfsMyAccount.options.letSubscribersCancelSubscriptions) {
                attachCancelSubscriptionFormEvents();
            } else {
                $("#wpfs-subscriptions-actions").hide();
            }
        };

        function submitInvoiceViewToggle() {
            $.ajax({
                type: "POST",
                url: wpfsAjaxURL,
                data: {
                    action: 'wp_full_stripe_toggle_invoice_view',
                    sessionId: wpfsCardUpdateSessionData.sessionId
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    if (data.success) {
                        window.location = window.location.pathname;
                    } else {
                        // How we should display invoice view toggle errors?
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    logError('submitCardData', jqXHR, textStatus, errorThrown);
                },
                complete: function () {
                    // Nop so far
                }
            });
        }

        WPFS.initManagedSubscriptions = function () {
                if ($('#wpfs-subscriptions-table').length == 1) {
                    var WPFS_MS = {};
                    WPFS_MS.debugMode = false;
                    WPFS_MS.isDebugEnabled = function () {
                        return WPFS_MS.debugMode;
                    };
                    WPFS_MS.Subscription = Backbone.Model.extend({
                        defaults: {
                            id: '',
                            idAttribute: '',
                            nameAttribute: '',
                            planName: '',
                            planQuantity: 1,
                            allowMultipleSubscriptions: false,
                            maximumPlanQuantity: 0,
                            planLabel: '',
                            status: '',
                            statusClass: '',
                            priceAndIntervalLabel: '',
                            created: '',
                            summary: ''
                        }
                    });
                    WPFS_MS.SubscriptionList = Backbone.Collection.extend({
                        model: WPFS_MS.Subscription,
                        url: function () {
                            return wpfsRESTURL + 'wp-full-stripe/v1' + '/manage-subscriptions' + '/subscription';
                        }
                    });
                    WPFS_MS.subscriptionList = new WPFS_MS.SubscriptionList(wpfsCardUpdateSessionData.stripe.subscriptions);
                    WPFS_MS.UpdateQuantitySuccessMessageView = Backbone.View.extend({
                        tagName: 'div',
                        className: 'wpfs-form-message wpfs-form-message--correct wpfs-form-message--sm-icon',
                        template: _.template($('#wpfs-subscription-update-quantity-success-message').html()),
                        render: function () {
                            this.$el.html(this.template());
                            return this;
                        }
                    });
                    WPFS_MS.UpdateQuantityErrorMessageView = Backbone.View.extend({
                        tagName: 'div',
                        className: 'wpfs-form-message wpfs-form-message--incorrect wpfs-form-message--sm-icon',
                        template: _.template($('#wpfs-subscription-update-quantity-error-message').html()),
                        render: function () {
                            this.$el.html(this.template());
                            return this;
                        }
                    });
                    WPFS_MS.CancelSubscriptionSuccessMessageView = Backbone.View.extend({
                        tagName: 'div',
                        className: 'wpfs-form-message wpfs-form-message--correct wpfs-form-message--sm-icon',
                        template: _.template($('#wpfs-subscription-cancel-success-message').html()),
                        render: function () {
                            this.$el.html(this.template());
                            return this;
                        }
                    });
                    WPFS_MS.CancelSubscriptionErrorMessageView = Backbone.View.extend({
                        tagName: 'div',
                        className: 'wpfs-form-message wpfs-form-message--incorrect wpfs-form-message--sm-icon',
                        template: _.template($('#wpfs-subscription-cancel-error-message').html()),
                        render: function () {
                            this.$el.html(this.template());
                            return this;
                        }
                    });


                    WPFS_MS.EmptySubscriptionListView = Backbone.View.extend({
                        tagName: 'div',
                        className: 'wpfs-no-subscription',
                        template: _.template($('#wpfs-subscription-empty-subscription-list').html()),
                        render: function () {
                            this.$el.html(this.template());
                            return this;
                        }
                    });
                    WPFS_MS.SubscriptionView = Backbone.View.extend({
                        tagName: 'div',
                        className: 'wpfs-subscription',
                        template: _.template($('#wpfs-subscription-show-row').html()),
                        updateQuantityTemplate: _.template($('#wpfs-subscription-update-row').html()),
                        render: function () {
                            var modelAsJSON = this.model.toJSON();
                            if (WPFS_MS.isDebugEnabled()) {
                                console.log('SubscriptionView.render(): modelAsJSON=' + JSON.stringify(modelAsJSON));
                            }
                            this.$el.html(this.template(modelAsJSON));
                            return this;
                        },
                        initialize: function () {
                            this.model.on('change', this.render, this);
                        },
                        events: {
                            'click a.wpfs-subscription-update-quantity-action': 'updateQuantity',
                            'click a.wpfs-subscription-cancel-action': 'cancelSubscription',
                            'click button.wpfs-subscription-button-update-quantity': 'saveQuantity',
                            'click a.wpfs-subscription-link-cancel-update-quantity': 'cancelUpdateQuantity'
                        },
                        updateQuantity: function (e) {
                            e.preventDefault();

                            this.$el.addClass('wpfs-subscription--update-quantity');
                            this.$el.html(this.updateQuantityTemplate(this.model.toJSON()));
                            WPFS.initStepper();
                            // var $planQuantityStepper = this.$el.find('input[name="wpfs-subscription-plan-quantity"]');
                            // scrollToElement($planQuantityStepper);

                            return this;
                        },
                        cancelUpdateQuantity: function (e) {
                            e.preventDefault();
                            this.$el.removeClass('wpfs-subscription--update-quantity');
                            this.render();
                            // scrollToElement(this.$el);

                            return this;
                        },
                        saveQuantity: function (e) {
                            e.preventDefault();
                            $('.wpfs-form-message').remove();
                            var newPlanQuantity = this.$el.find('input[name="wpfs-subscription-plan-quantity"]').val();
                            var planName = this.model.get('planName');
                            // tnagy update plan label to reflect changes properly in the model and view
                            var newPlanLabel = sprintf('%d%s %s', newPlanQuantity, 'x', planName);
                            var newAttributes = {
                                planQuantity: newPlanQuantity,
                                planLabel: newPlanLabel
                            };
                            this.model.save(
                                newAttributes,
                                {
                                    wait: true,
                                    success: function (model, response) {
                                        var successMessage = new WPFS_MS.UpdateQuantitySuccessMessageView();
                                        $('#wpfs-subscriptions-subtitle').after(successMessage.render().el);
                                        setTimeout(function () {
                                            window.location = window.location.pathname;
                                        }, 1000);
                                    },
                                    error: function (model, error) {
                                        console.log('SubscriptionView.model.save().error(): CALLED, error=' + error + ', model=' + JSON.stringify(model));
                                        var errorMessage = new WPFS_MS.UpdateQuantityErrorMessageView();
                                        $('#wpfs-subscriptions-subtitle').after(errorMessage.render().el);
                                    }
                                }
                            );

                            this.render();
                            this.$el.removeClass('wpfs-subscription--update-quantity');

                            return this;
                        },
                        cancelSubscription: function (e) {
                            e.preventDefault();
                            $('.wpfs-form-message').remove();

                            //noinspection JSUnresolvedVariable
                            var confirmationResult = confirm(wpfsCardUpdateSessionData.i18n.confirmSingleSubscriptionCancellationMessage);

                            if (confirmationResult == true) {
                                var newAttributes = {
                                    action: 'cancel'
                                };
                                this.model.save(
                                    newAttributes,
                                    {
                                        wait: true,
                                        success: function (model, response) {
                                            var successMessage = new WPFS_MS.CancelSubscriptionSuccessMessageView();
                                            $('#wpfs-subscriptions-subtitle').after(successMessage.render().el);
                                            setTimeout(function () {
                                                window.location = window.location.pathname;
                                            }, 1000);
                                        },
                                        error: function (model, error) {
                                            console.log('SubscriptionView.model.save().error(): CALLED, error=' + error + ', model=' + JSON.stringify(model));
                                            var errorMessage = new WPFS_MS.CancelSubscriptionErrorMessageView();
                                            $('#wpfs-subscriptions-subtitle').after(errorMessage.render().el);
                                        }
                                    }
                                );

                                this.render();

                            }


                            return this;
                        }
                    });
                    WPFS_MS.SubscriptionsTableView = Backbone.View.extend({
                        initialize: function () {
                            WPFS_MS.subscriptionList.on('add', this.addOne, this);
                            WPFS_MS.subscriptionList.on('reset', this.addAll, this);
                            WPFS_MS.subscriptionList.on('all', this.render, this);
                            // tnagy create views for subscriptionList elements
                            this.addAll();
                        },
                        render: function () {
                            $('.wpfs-form-check-input').off('change').on('change', function (e) {
                                updateCancelSubscriptionSubmitButton();
                            }).change();
                        },
                        clearContent: function () {
                            this.$el.empty();
                        },
                        addOne: function (subscription) {
                            var showSubscriptionView = new WPFS_MS.SubscriptionView({model: subscription});
                            this.$el.append(showSubscriptionView.render().el);
                            this.checkCancelButtonVisibility();
                        },
                        addAll: function () {
                            this.clearContent();
                            if (WPFS_MS.subscriptionList.length === 0) {
                                var emptySubscriptionListView = new WPFS_MS.EmptySubscriptionListView();
                                $('#wpfs-subscriptions-table').append(emptySubscriptionListView.render().el);
                                $('#wpfs-subscriptions-actions').hide();
                            } else {
                                WPFS_MS.subscriptionList.each(this.addOne, this);
                            }

                            // tnagy update checkbox change handlers
                            $('.wpfs-form-check-input').off('change').on('change', function (e) {
                                updateCancelSubscriptionSubmitButton();
                            }).change();
                        },
                        checkCancelButtonVisibility: function () {
                            if (WPFS_MS.subscriptionList.length === 0) {
                                $('#wpfs-button-cancel-subscription').css("visibility", "hidden");
                            } else {
                                $('#wpfs-button-cancel-subscription').css("visibility", "visible");
                            }
                        }
                    });
                    WPFS_MS.subscriptionsTableView = new WPFS_MS.SubscriptionsTableView({
                        el: $('#wpfs-subscriptions-table')
                    });

                    if (wpfsMyAccount.options.showInvoicesSection) {
                        WPFS_MS.Invoice = Backbone.Model.extend({
                            defaults: {
                                id: '',
                                planName: '',
                                planQuantity: 1,
                                priceLabel: '',
                                created: '',
                                invoiceNumber: '',
                                invoiceUrl: ''
                            }
                        });
                        WPFS_MS.InvoiceList = Backbone.Collection.extend({
                            model: WPFS_MS.Invoice
                        });
                        WPFS_MS.invoiceList = new WPFS_MS.InvoiceList(wpfsCardUpdateSessionData.stripe.invoices);
                        WPFS_MS.InvoiceView = Backbone.View.extend({
                            tagName: 'div',
                            className: 'wpfs-invoice',
                            template: _.template($('#wpfs-invoice-show-row').html()),
                            render: function () {
                                var modelAsJSON = this.model.toJSON();
                                if (WPFS_MS.isDebugEnabled()) {
                                    console.log('InvoiceView.render(): modelAsJSON=' + JSON.stringify(modelAsJSON));
                                }
                                this.$el.html(this.template(modelAsJSON));
                                return this;
                            },
                            initialize: function () {
                                this.model.on('change', this.render, this);
                            },
                        });
                        WPFS_MS.EmptyInvoiceListView = Backbone.View.extend({
                            tagName: 'div',
                            className: 'wpfs-no-subscription',
                            template: _.template($('#wpfs-subscription-empty-invoice-list').html()),
                            render: function () {
                                this.$el.html(this.template());
                                return this;
                            }
                        });
                        WPFS_MS.InvoicesTableView = Backbone.View.extend({
                            initialize: function () {
                                WPFS_MS.invoiceList.on('add', this.addOne, this);
                                WPFS_MS.invoiceList.on('reset', this.addAll, this);
                                WPFS_MS.invoiceList.on('all', this.render, this);
                                // tnagy create views for subscriptionList elements
                                this.addAll();
                            },
                            render: function () {
                            },
                            clearContent: function () {
                                this.$el.empty();
                            },
                            addOne: function (invoice) {
                                var showInvoiceView = new WPFS_MS.InvoiceView({model: invoice});
                                this.$el.append(showInvoiceView.render().el);
                            },
                            addAll: function () {
                                this.clearContent();
                                if (WPFS_MS.invoiceList.length === 0) {
                                    var emptyInvoicesListView = new WPFS_MS.EmptyInvoiceListView();
                                    $('#wpfs-invoices-table').append(emptyInvoicesListView.render().el);
                                } else {
                                    WPFS_MS.invoiceList.each(this.addOne, this);
                                }
                            }
                        });
                        WPFS_MS.invoiceTableView = new WPFS_MS.InvoicesTableView({
                            el: $('#wpfs-invoices-table')
                        });

                        WPFS_MS.ShowAllInvoicesView = Backbone.View.extend({
                            tagName: 'div',
                            id: 'wpfs-invoices-actions',
                            className: 'wpfs-invoices-actions',
                            template: _.template($('#wpfs-invoices-actions-show-all').html()),
                            render: function () {
                                this.$el.html(this.template());
                                return this;
                            }
                        });
                        WPFS_MS.ShowAllInvoicesLoadingView = Backbone.View.extend({
                            tagName: 'div',
                            id: 'wpfs-invoices-actions',
                            className: 'wpfs-invoices-actions',
                            template: _.template($('#wpfs-invoices-actions-show-all-loading').html()),
                            render: function () {
                                this.$el.html(this.template());
                                return this;
                            }
                        });
                        WPFS_MS.ShowLatestInvoicesView = Backbone.View.extend({
                            tagName: 'div',
                            id: 'wpfs-invoices-actions',
                            className: 'wpfs-invoices-actions',
                            template: _.template($('#wpfs-invoices-actions-show-latest').html()),
                            render: function () {
                                this.$el.html(this.template());
                                return this;
                            }
                        });
                        WPFS_MS.ShowLatestInvoicesLoadingView = Backbone.View.extend({
                            tagName: 'div',
                            id: 'wpfs-invoices-actions',
                            className: 'wpfs-invoices-actions',
                            template: _.template($('#wpfs-invoices-actions-show-latest-loading').html()),
                            render: function () {
                                this.$el.html(this.template());
                                return this;
                            }
                        });

                        var invoiceActionsView = null;
                        switch (wpfsMyAccount.options.invoiceDisplayMode) {
                            case INVOICE_DISPLAY_MODE_HEAD:
                                invoiceActionsView = new WPFS_MS.ShowAllInvoicesView();
                                break;

                            case INVOICE_DISPLAY_MODE_ALL:
                                invoiceActionsView = new WPFS_MS.ShowLatestInvoicesView();
                                break;

                            case INVOICE_DISPLAY_MODE_FEW:
                            default:
                                // Hide the button, no template
                                break;
                        }

                        if (invoiceActionsView) {
                            $('#wpfs-invoices-table').after(invoiceActionsView.render().el);
                        }

                        $('#wpfs-invoices-view-toggle').click(function (e) {
                            e.preventDefault();

                            var invoicesLoadingView = null;
                            switch (wpfsMyAccount.options.invoiceDisplayMode) {
                                case INVOICE_DISPLAY_MODE_HEAD:
                                    invoicesLoadingView = new WPFS_MS.ShowAllInvoicesLoadingView();
                                    break;

                                case INVOICE_DISPLAY_MODE_ALL:
                                    invoicesLoadingView = new WPFS_MS.ShowLatestInvoicesLoadingView();
                                    break;

                                case INVOICE_DISPLAY_MODE_FEW:
                                default:
                                    // Hide the button, no template
                                    break;
                            }

                            $('#wpfs-invoices-actions').remove();
                            if (invoicesLoadingView) {
                                $('#wpfs-invoices-table').after(invoicesLoadingView.render().el);
                            }

                            submitInvoiceViewToggle();

                            return false;
                        });
                    } else {
                        $("#wpfs-invoices-subtitle").hide();
                        $("#wpfs-view-invoices-form").hide();
                    }

                    Backbone.history.start();
                }
            };

            function setCookie(name, value, days) {
                var expires = "";
                if (days) {
                    var date = new Date();
                    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                    expires = "; expires=" + date.toUTCString();
                }
                document.cookie = name + "=" + (value || "") + expires + "; path=/";
            }

            function eraseCookie(name) {
                document.cookie = name + '=; Max-Age=-99999999;';
            }

            WPFS.runCookieAction = function () {
                if (typeof(wpfsCardUpdateSessionData.action) !== 'undefined') {
                    switch (wpfsCardUpdateSessionData.action) {
                        case 'setCookie':
                            setCookie(wpfsCardUpdateSessionData.cookieName,
                                wpfsCardUpdateSessionData.sessionId,
                                wpfsCardUpdateSessionData.cookieValidUntilHours);
                            break;
                        case 'removeCookie':
                            eraseCookie(wpfsCardUpdateSessionData.cookieName);
                            break;
                        default:
                            break;
                    }
                }
            };

            WPFS.ready = function () {
                // tnagy scroll to forms gently
                var $wpfsEnterEmailAddressForm = $('#wpfs-enter-email-address-form');
                var $wpfsEnterSecurityCodeForm = $('#wpfs-enter-security-code-form');
                var $wpfsManageSubscriptionsContainer = $('#wpfs-manage-subscriptions-container');
                if ($wpfsEnterEmailAddressForm.length > 0) {
                    scrollToElement($wpfsEnterEmailAddressForm);
                }
                if ($wpfsEnterSecurityCodeForm.length > 0) {
                    scrollToElement($wpfsEnterSecurityCodeForm);
                }
                if ($wpfsManageSubscriptionsContainer.length > 0) {
                    scrollToElement($wpfsManageSubscriptionsContainer);
                }
            };

            WPFS.initEnterEmailAddressForm();
            WPFS.initEnterSecurityCodeForm();
            WPFS.initUpdateCardForm();
            WPFS.initCancelSubscriptionForm();
            WPFS.initManagedSubscriptions();
            WPFS.runCookieAction();

            WPFS.ready();
        }
    );

})(jQuery);