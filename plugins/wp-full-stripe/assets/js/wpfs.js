jQuery.noConflict();
(function ($) {

    "use strict";

    $(function () {

            const FORM_TYPE_INLINE_PAYMENT = 'inline_payment';
            const FORM_TYPE_POPUP_PAYMENT = 'popup_payment';
            const FORM_TYPE_INLINE_SUBSCRIPTION = 'inline_subscription';
            const FORM_TYPE_POPUP_SUBSCRIPTION = 'popup_subscription';
            const FORM_TYPE_INLINE_SAVE_CARD = 'inline_save_card';
            const FORM_TYPE_POPUP_SAVE_CARD = 'popup_save_card';
            const FORM_TYPE_INLINE_DONATION = 'inline_donation';
            const FORM_TYPE_POPUP_DONATION = 'popup_donation';

            const PAYMENT_TYPE_LIST_OF_AMOUNTS = 'list_of_amounts';
            const PAYMENT_TYPE_CUSTOM_AMOUNT = 'custom_amount';
            const PAYMENT_TYPE_SPECIFIED_AMOUNT = 'specified_amount';
            const PAYMENT_TYPE_CARD_CAPTURE = 'card_capture';
            const AMOUNT_OTHER = 'other';

            const FIELD_DESCRIPTOR_MACRO_FIELD_ID = '{fieldId}';
            const MACRO_SUBMIT_BUTTON_CAPTION_AMOUNT = '{{amount}}';
            const ERROR_MESSAGE_FIELD_CLASS = 'wpfs-form-error-message';

            const AMOUNT_SELECTOR_STYLE_RADIO_BUTTONS = 'radio-buttons';
            const AMOUNT_SELECTOR_STYLE_DROPDOWN = 'dropdown';
            const AMOUNT_SELECTOR_STYLE_BUTTON_GROUP = 'button-group';

            var debugLog = false;

            //noinspection JSUnresolvedVariable
            var stripe = Stripe(wpfsStripeKey);

            var reCAPTCHAWidgetIds = [];
            var googleReCAPTCHA = null;

            function createCurrencyFormatter($form) {
                if ($form) {
                    var decimalSeparator = $form.data('wpfs-decimal-separator');
                    var showCurrencySymbolInsteadOfCode = $form.data('wpfs-show-currency-symbol-instead-of-code');
                    var showCurrencySignAtFirstPosition = $form.data('wpfs-show-currency-sign-at-first-position');
                    var putWhitespaceBetweenCurrencyAndAmount = $form.data('wpfs-put-whitespace-between-currency-and-amount');

                    if (debugLog) {
                        logInfo('createCurrencyFormatter', 'form=' + $form.data('wpfs-form-id'));
                        logInfo('createCurrencyFormatter', 'decimalSeparator=' + JSON.stringify(decimalSeparator));
                        logInfo('createCurrencyFormatter', 'showCurrencySymbolInsteadOfCode=' + JSON.stringify(showCurrencySymbolInsteadOfCode));
                        logInfo('createCurrencyFormatter', 'showCurrencySignAtFirstPosition=' + JSON.stringify(showCurrencySignAtFirstPosition));
                        logInfo('createCurrencyFormatter', 'putWhitespaceBetweenCurrencyAndAmount=' + JSON.stringify(putWhitespaceBetweenCurrencyAndAmount));
                    }
                    return WPFSCurrencyFormatter(
                        decimalSeparator,
                        showCurrencySymbolInsteadOfCode,
                        showCurrencySignAtFirstPosition,
                        putWhitespaceBetweenCurrencyAndAmount
                    );
                }
                logWarn('createCurrencyFormatter', '$form is null');
                return null;
            }

        /**
         *
         * @param currency
         * @param amount
         * @param coupon
         * @returns {{amountCurrency: *, amount: *, couponCurrency: (OPTIONS.currency|{importance, type, default}|*|Array), discount: number, discountType: *, total: number, error: *}}
         */
        function applyCoupon(currency, amount, coupon) {
            // console.log('applyCoupon(): CALLED, currency=' + currency + ', amount=' + amount + ', coupon=' + JSON.stringify(coupon));
            var discount = 0;
            var discountType = null;
            var error = null;
            if (coupon != null) {
                //noinspection JSUnresolvedVariable
                if (coupon.hasOwnProperty('percent_off') && coupon.percent_off != null) {
                    discountType = 'percent_off';
                    //noinspection JSUnresolvedVariable
                    var percentOff = parseInt(coupon.percent_off) / 100;
                    discount = Math.round(amount * percentOff);
                } else {
                    //noinspection JSUnresolvedVariable
                    if (coupon.hasOwnProperty('amount_off') && coupon.amount_off != null) {
                        discountType = 'amount_off';
                        if (coupon.hasOwnProperty('currency')) {
                            if (coupon.currency == currency) {
                                //noinspection JSUnresolvedVariable
                                discount = parseInt(coupon.amount_off);
                            } else {
                                error = 'currency mismatch';
                            }
                        } else {
                            error = 'currency mismatch';
                        }
                    } else {
                        error = 'invalid coupon';
                    }
                }
            }
            var amountAsInteger = parseInt(amount);
            var total = amountAsInteger - discount;

            var result = {
                amountCurrency: currency,
                amount: amountAsInteger,
                couponCurrency: (coupon != null && coupon.hasOwnProperty('currency') ? coupon.currency : null),
                discount: discount,
                discountType: discountType,
                discountPercentOff: (coupon != null && coupon.hasOwnProperty('percent_off') ? coupon.percent_off : null),
                total: total,
                error: error
            };

            // console.log('applyCoupon(): result=' + JSON.stringify(result));

            return result;
        }


        function resetCaptcha($form) {
                if ($form) {
                    var formHash = $form.data('wpfs-form-hash');
                    if (formHash) {
                        if (googleReCAPTCHA != null) {
                            if (reCAPTCHAWidgetIds[formHash] !== "undefined") {
                                googleReCAPTCHA.reset(reCAPTCHAWidgetIds[formHash]);
                            }
                        }
                    }
                }
            }

            function getParentForm(element) {
                return $(element).parents('form:first');
            }

            function isInViewport($anElement) {
                var $window = $(window);

                //noinspection JSValidateTypes
                var viewPortTop = $window.scrollTop();
                var viewPortBottom = viewPortTop + $window.height();

                var elementTop = $anElement.offset().top;
                var elementBottom = elementTop + $anElement.outerHeight();

                if (debugLog) {
                    console.log('isInViewport(): elementBottom=' + elementBottom + ', viewPortBottom=' + viewPortBottom + ', elementTop=' + elementTop + ', viewPortTop=' + viewPortTop);
                }

                return ((elementBottom <= viewPortBottom) && (elementTop >= viewPortTop));
            }

            function getGlobalMessageContainerTitle($messageContainer, title) {
                var $messageContainerTitle = $('.wpfs-form-message-title', $messageContainer);
                if (0 == $messageContainerTitle.length) {
                    $('<div>', {class: 'wpfs-form-message-title'}).prependTo($messageContainer);
                    $messageContainerTitle = $('.wpfs-form-message-title', $messageContainer);
                }
                $messageContainerTitle.html(title);
                return $messageContainerTitle;
            }

            function getGlobalMessageContainer($form, message) {
                var $messageContainer = $('.wpfs-form-message', $form);
                if (0 == $messageContainer.length) {
                    $('<div>', {class: 'wpfs-form-message'}).prependTo($form);
                    $messageContainer = $('.wpfs-form-message', $form);
                }
                $messageContainer.html(message);
                return $messageContainer;
            }

            function scrollToElement($anElement, fade) {
                if ($anElement && $anElement.offset() && $anElement.offset().top) {
                    if (!isInViewport($anElement)) {
                        $('html, body').animate({
                            scrollTop: $anElement.offset().top - 100
                        }, 1000);
                    }
                }
                if ($anElement && fade) {
                    $anElement.fadeIn(500).fadeOut(500).fadeIn(500);
                }
            }

            function clearGlobalMessage($form) {
                var $messageContainer = getGlobalMessageContainer($form, '');
                $messageContainer.remove();
            }

            function __showGlobalMessage($form, messageTitle, message) {
                var $globalMessageContainer = getGlobalMessageContainer($form, message);
                getGlobalMessageContainerTitle($globalMessageContainer, messageTitle);
                return $globalMessageContainer;
            }

            function showSuccessGlobalMessage($form, messageTitle, message) {
                var $globalMessageContainer = __showGlobalMessage($form, messageTitle, message);
                $globalMessageContainer.addClass('wpfs-form-message--correct');
                scrollToElement($globalMessageContainer, false);
            }

            function showErrorGlobalMessage($form, messageTitle, message) {
                var $globalMessageContainer = __showGlobalMessage($form, messageTitle, message);
                $globalMessageContainer.addClass('wpfs-form-message--incorrect');
                scrollToElement($globalMessageContainer, false);
            }

            function clearFieldErrors($form) {
                $('.wpfs-form-error-message', $form).remove();
                $('.wpfs-form-control', $form).removeClass('wpfs-form-control--error');
                $('.wpfs-input-group', $form).removeClass('wpfs-input-group--error');
                $('.wpfs-form-control--error', $form).removeClass('wpfs-form-control--error');
                $('.wpfs-form-check-input--error', $form).removeClass('wpfs-form-check-input--error');
            }

            function clearFieldError($form, fieldName, fieldId) {
                var formType = $form.data('wpfs-form-type');
                var fieldDescriptor = getFieldDescriptor(formType, fieldName);
                if (debugLog) {
                    logInfo('clearFieldError', 'fieldName=' + fieldName + ', fieldId=' + fieldId);
                    logInfo('clearFieldError', JSON.stringify(fieldDescriptor));
                }
                if (fieldDescriptor != null) {

                    // tnagy read field descriptor
                    var fieldType = fieldDescriptor.type;
                    var fieldClass = fieldDescriptor.class;
                    var fieldSelector = fieldDescriptor.selector;
                    var fieldErrorClass = fieldDescriptor.errorClass;
                    var fieldErrorSelector = fieldDescriptor.errorSelector;

                    // tnagy initialize field
                    var theFieldSelector;
                    if (fieldId != null) {
                        theFieldSelector = '#' + fieldId;
                    } else {
                        theFieldSelector = fieldSelector;
                    }
                    var $field = $(theFieldSelector, $form);

                    // tnagy remove error class, remove error message
                    var errorMessageFieldSelector = '.' + ERROR_MESSAGE_FIELD_CLASS;
                    if ('input' === fieldType) {
                        if (fieldErrorSelector != null) {
                            if (fieldErrorSelector.indexOf(FIELD_DESCRIPTOR_MACRO_FIELD_ID) !== -1) {
                                fieldErrorSelector = fieldErrorSelector.replace(/\{fieldId}/g, fieldId);
                            }
                            $field.closest(fieldErrorSelector).removeClass(fieldErrorClass);
                        }
                        $field.closest(errorMessageFieldSelector).remove();
                    } else if ('input-group' === fieldType) {
                        if (fieldErrorSelector != null) {
                            if (fieldErrorSelector.indexOf(FIELD_DESCRIPTOR_MACRO_FIELD_ID) !== -1) {
                                fieldErrorSelector = fieldErrorSelector.replace(/\{fieldId}/g, fieldId);
                            }
                            $field.closest(fieldErrorSelector).removeClass(fieldErrorClass);
                        }
                        $field.closest(fieldErrorSelector).siblings(errorMessageFieldSelector).remove();
                    } else if ('input-custom' === fieldType) {
                        if (fieldErrorSelector != null) {
                            $field.closest(fieldErrorSelector).removeClass(fieldErrorClass);
                        }
                        $field.closest(errorMessageFieldSelector).remove();
                    } else if ('select-menu' === fieldType) {
                        if (fieldErrorSelector != null) {
                            if (fieldErrorSelector.indexOf(FIELD_DESCRIPTOR_MACRO_FIELD_ID) !== -1) {
                                fieldErrorSelector = fieldErrorSelector.replace(/\{fieldId}/g, fieldId);
                            }
                            $field.closest('.' + fieldClass).removeClass(fieldErrorClass);
                            $(fieldErrorSelector).removeClass(fieldErrorClass);
                        }
                        $field.closest(errorMessageFieldSelector).remove();
                    } else if ('checkbox' === fieldType) {
                        if (fieldErrorSelector != null) {
                            $field.closest(fieldErrorSelector).removeClass(fieldErrorClass);
                        }
                        $field.closest(errorMessageFieldSelector).remove();
                    } else if ('card' === fieldType) {
                        if (fieldErrorSelector != null) {
                            var $cardContainer = $('.' + fieldClass, $form);
                            $cardContainer.closest(fieldErrorSelector).removeClass(fieldErrorClass);
                        }
                        $field.closest(errorMessageFieldSelector).remove();
                    }
                } else {
                    logInfo('showFieldError', 'FieldDescription not found!');
                }
            }

            function getFieldDescriptor(formType, fieldName) {
                var fieldDescriptor = null;
                if (FORM_TYPE_INLINE_PAYMENT === formType && wpfsFormFields.hasOwnProperty('inlinePayment')) {
                    var inlinePaymentFormFields = wpfsFormFields.inlinePayment;
                    if (inlinePaymentFormFields.hasOwnProperty(fieldName)) {
                        fieldDescriptor = inlinePaymentFormFields[fieldName];
                    }
                } else if (FORM_TYPE_INLINE_SUBSCRIPTION === formType && wpfsFormFields.hasOwnProperty('inlineSubscription')) {
                    var inlineSubscriptionFormFields = wpfsFormFields.inlineSubscription;
                    if (inlineSubscriptionFormFields.hasOwnProperty(fieldName)) {
                        fieldDescriptor = inlineSubscriptionFormFields[fieldName];
                    }
                } else if (FORM_TYPE_POPUP_PAYMENT === formType && wpfsFormFields.hasOwnProperty('popupPayment')) {
                    var popupPaymentFormFields = wpfsFormFields.popupPayment;
                    if (popupPaymentFormFields.hasOwnProperty(fieldName)) {
                        fieldDescriptor = popupPaymentFormFields[fieldName];
                    }
                } else if (FORM_TYPE_POPUP_SUBSCRIPTION === formType && wpfsFormFields.hasOwnProperty('popupSubscription')) {
                    var popupSubscriptionFormFields = wpfsFormFields.popupSubscription;
                    if (popupSubscriptionFormFields.hasOwnProperty(fieldName)) {
                        fieldDescriptor = popupSubscriptionFormFields[fieldName];
                    }
                } else if (FORM_TYPE_INLINE_SAVE_CARD === formType && wpfsFormFields.hasOwnProperty('inlineCardCapture')) {
                    var inlineCardCaptureFormFields = wpfsFormFields.inlineCardCapture;
                    if (inlineCardCaptureFormFields.hasOwnProperty(fieldName)) {
                        fieldDescriptor = inlineCardCaptureFormFields[fieldName];
                    }
                } else if (FORM_TYPE_POPUP_SAVE_CARD === formType && wpfsFormFields.hasOwnProperty('popupCardCapture')) {
                    var popupCardCaptureFormFields = wpfsFormFields.popupCardCapture;
                    if (popupCardCaptureFormFields.hasOwnProperty(fieldName)) {
                        fieldDescriptor = popupCardCaptureFormFields[fieldName];
                    }
                } else if (FORM_TYPE_INLINE_DONATION === formType && wpfsFormFields.hasOwnProperty('inlineDonation')) {
                    var inlineDonationFormFields = wpfsFormFields.inlineDonation;
                    if (inlineDonationFormFields.hasOwnProperty(fieldName)) {
                        fieldDescriptor = inlineDonationFormFields[fieldName];
                    }
                } else if (FORM_TYPE_POPUP_DONATION === formType && wpfsFormFields.hasOwnProperty('popupDonation')) {
                    var popupDonationFormFields = wpfsFormFields.popupDonation;
                    if (popupDonationFormFields.hasOwnProperty(fieldName)) {
                        fieldDescriptor = popupDonationFormFields[fieldName];
                    }
                }
                return fieldDescriptor;
            }

            function showFormError($form, fieldName, fieldId, errorTitle, errorMessage, scrollTo) {
                var formType = $form.data('wpfs-form-type');
                var fieldDescriptor = getFieldDescriptor(formType, fieldName);
                if (fieldDescriptor != null) {
                    var fieldIsHidden = fieldDescriptor.hidden;
                    if (true == fieldIsHidden) {
                        showErrorGlobalMessage($form, errorTitle, errorMessage);
                    } else {
                        showFieldError($form, fieldName, fieldId, errorMessage, scrollTo);
                    }
                    if (fieldName.startsWith('wpfs-billing')) {
                        $('.wpfs-billing-address-switch', $form).click();
                    }
                    if (fieldName.startsWith('wpfs-shipping')) {
                        $('.wpfs-shipping-address-switch', $form).click();
                    }
                }
            }

            function showFieldError($form, fieldName, fieldId, fieldErrorMessage, scrollTo) {
                var formType = $form.data('wpfs-form-type');
                var fieldDescriptor = getFieldDescriptor(formType, fieldName);
                if (debugLog) {
                    logInfo('showFieldError', 'fieldName=' + fieldName + ', fieldId=' + fieldId + ', fieldErrorMessage=' + fieldErrorMessage);
                    logInfo('showFieldError', JSON.stringify(fieldDescriptor));
                    logInfo('showFieldError', fieldErrorMessage);
                }
                if (fieldDescriptor != null) {

                    // tnagy read field descriptor
                    var fieldType = fieldDescriptor.type;
                    var fieldClass = fieldDescriptor.class;
                    var fieldSelector = fieldDescriptor.selector;
                    var fieldErrorClass = fieldDescriptor.errorClass;
                    var fieldErrorSelector = fieldDescriptor.errorSelector;

                    // tnagy initialize field
                    var theFieldSelector;
                    if (fieldId != null) {
                        theFieldSelector = '#' + fieldId;
                    } else {
                        theFieldSelector = fieldSelector;
                    }
                    var $field = $(theFieldSelector, $form);

                    // tnagy create error message
                    var $fieldError = $('<div>', {
                        class: ERROR_MESSAGE_FIELD_CLASS,
                        'data-wpfs-field-error-for': fieldId
                    }).html(fieldErrorMessage);

                    // tnagy add error class, insert error message
                    if ('input' === fieldType) {
                        if (fieldErrorSelector != null) {
                            if (fieldErrorSelector.indexOf(FIELD_DESCRIPTOR_MACRO_FIELD_ID) !== -1) {
                                fieldErrorSelector = fieldErrorSelector.replace(/\{fieldId}/g, fieldId);
                            }
                            $field.closest(fieldErrorSelector).addClass(fieldErrorClass);
                        }
                        $fieldError.insertAfter($field);
                    } else if ('input-group' === fieldType) {
                        if (fieldErrorSelector != null) {
                            if (fieldErrorSelector.indexOf(FIELD_DESCRIPTOR_MACRO_FIELD_ID) !== -1) {
                                fieldErrorSelector = fieldErrorSelector.replace(/\{fieldId}/g, fieldId);
                            }
                            $field.closest(fieldErrorSelector).addClass(fieldErrorClass);
                        }
                        $fieldError.insertAfter($field.closest(fieldErrorSelector));
                    } else if ('input-custom' === fieldType) {
                        if (fieldErrorSelector != null) {
                            $field.closest(fieldErrorSelector).addClass(fieldErrorClass);
                        }
                        $fieldError.insertAfter($field);
                    } else if ('dropdown' === fieldType) {
                        if (fieldErrorSelector != null) {
                            if (fieldErrorSelector.indexOf(FIELD_DESCRIPTOR_MACRO_FIELD_ID) !== -1) {
                                fieldErrorSelector = fieldErrorSelector.replace(/\{fieldId}/g, fieldId);
                            }
                            $field.closest('.' + fieldClass).addClass(fieldErrorClass);
                            $(fieldErrorSelector).addClass(fieldErrorClass);
                        }
                        $fieldError.appendTo($field.parent());
                    } else if ('checkbox' === fieldType) {
                        if (fieldErrorSelector != null) {
                            $field.closest(fieldErrorSelector).addClass(fieldErrorClass);
                        }
                        $fieldError.appendTo($field.parent());
                    } else if ('card' === fieldType) {
                        if (fieldErrorSelector != null) {
                            var $cardContainer = $('.' + fieldClass, $form);
                            $cardContainer.closest(fieldErrorSelector).addClass(fieldErrorClass);
                            $fieldError.insertAfter($cardContainer);
                        }
                    } else if ('captcha' === fieldType) {
                        if (fieldErrorSelector != null) {
                            var $captchaContainer = $('.' + fieldClass, $form);
                            $captchaContainer.closest(fieldErrorSelector).addClass(fieldErrorClass);
                            $fieldError.insertAfter($captchaContainer);
                        }
                    }

                    if (typeof scrollTo != "undefined") {
                        if (scrollTo) {
                            scrollToElement($field, false);
                        }
                    }
                } else {
                    logInfo('showFieldError', 'FieldDescription not found!');
                }
            }

            function processValidationErrors($form, data) {
                var formId = $form.data('wpfs-form-id');
                var formHash = $form.data('wpfs-form-hash');
                var hasErrors = false;
                if (data && data.bindingResult) {
                    if (data.bindingResult.fieldErrors && data.bindingResult.fieldErrors.errors) {
                        var fieldErrors = data.bindingResult.fieldErrors.errors;
                        for (var index in fieldErrors) {
                            var fieldError = fieldErrors[index];
                            var fieldId = fieldError.id;
                            var fieldName = fieldError.name;
                            var fieldErrorMessage = fieldError.message;
                            showFormError($form, fieldName, fieldId, data.bindingResult.fieldErrors.title, fieldErrorMessage);
                            if (!hasErrors) {
                                hasErrors = true;
                            }
                        }
                        var firstErrorFieldId = $('.wpfs-form-error-message', $form).first().data('wpfs-field-error-for');
                        if (firstErrorFieldId) {
                            scrollToElement($('#' + firstErrorFieldId, $form), false);
                        }
                    }
                    if (data.bindingResult.globalErrors) {
                        var globalErrorMessages = '';
                        for (var i = 0; i < data.bindingResult.globalErrors.errors.length; i++) {
                            globalErrorMessages += data.bindingResult.globalErrors.errors[i] + '<br/>';
                        }
                        if ('' !== globalErrorMessages) {
                            showErrorGlobalMessage($form, data.bindingResult.globalErrors.title, globalErrorMessages);
                            if (!hasErrors) {
                                hasErrors = true;
                            }
                        }
                    }
                } else {
                    showErrorGlobalMessage($form, data.messageTitle, data.message);
                    logResponseException('WPFS form=' + formId, data);
                    hasErrors = true;
                }
                if (hasErrors) {
                    resetCaptcha($form);
                }
            }

            function clearAndShowAndEnableAndFocusCustomAmount($form) {
                $('div[data-wpfs-amount-row=custom-amount]', $form).show();
                $('input[name=wpfs-custom-amount-unique]', $form).val('').prop('disabled', false).focus();
            }

            function clearAndHideAndDisableCustomAmount($form) {
                $('div[data-wpfs-amount-row="custom-amount"]', $form).hide();
                $('input[name="wpfs-custom-amount-unique"]', $form).val('').prop('disabled', true);
            }

            function updateButtonCaption($form, buttonTitle, currencyCode, currencySymbol, amount, zeroDecimalSupport) {
                if (debugLog) {
                    console.log('updateButtonCaption(): params=' + JSON.stringify([currencyCode, currencySymbol, amount, zeroDecimalSupport]));
                    var formId = $form.data('wpfs-form-id');
                    console.log('updateButtonCaption(): formId=' + formId);
                }

                var formatter = createCurrencyFormatter($form);
                var captionPattern;
                var caption;
                var amountMacroRegExp = new RegExp(MACRO_SUBMIT_BUTTON_CAPTION_AMOUNT, "g");
                if (currencySymbol == null || amount == null) {
                    caption = buttonTitle;
                    if (caption.indexOf(MACRO_SUBMIT_BUTTON_CAPTION_AMOUNT) !== -1) {
                        caption = caption.replace(amountMacroRegExp, "").trim();
                    }
                } else {
                    var amountPart = formatter.format(amount, currencyCode, currencySymbol, zeroDecimalSupport);
                    if (buttonTitle.indexOf(MACRO_SUBMIT_BUTTON_CAPTION_AMOUNT) !== -1) {
                        captionPattern = buttonTitle.replace(amountMacroRegExp, "%s").trim();
                    } else {
                        captionPattern = buttonTitle + " %s";
                    }

                    var captionPatternParams = [];
                    captionPatternParams.push(amountPart);

                    caption = vsprintf(captionPattern, captionPatternParams);
                }
                $form.find('button[type=submit]').html(caption);
            }

            function disableFormButtons($form) {
                $form.find('button').prop('disabled', true);
            }

            function enableFormButtons($form) {
                $form.find('button').prop('disabled', false);
            }

            function disableFormInputsSelectsButtons($form) {
                if (debugLog) {
                    console.log('disableFormInputsSelectsButtons(): CALLED');
                }
                $form.find('input, select:not([data-wpfs-select]), button').prop('disabled', true);
            }

            function enableFormInputsSelectsButtons($form) {
                if (debugLog) {
                    console.log('enableFormInputsSelectsButtons(): CALLED');
                }
                $form.find('input, select:not([data-wpfs-select]), button').prop('disabled', false);
            }

            function resetFormFields($form, card) {
                var formId = $form.data('wpfs-form-id');
                var formHash = $form.data('wpfs-form-hash');
                $form.find('input[type=text], input[type=password], input[type=email]').val('');
                $form.find('input[data-toggle="stepper"]').each(function () {
                    var defaultValue = 1;
                    if (typeof $(this).data('default-value') !== 'undefined') {
                        defaultValue = $(this).data('default-value');
                    }
                    $(this).val(defaultValue);
                });
                $form.find('select').prop('selectedIndex', 0).change();
                $form.find('[data-toggle="selectmenu"]').wpfsSelectmenu('refresh');
                $form.find('input[type=radio], input[type=checkbox]').prop('checked', false);
                if (card != null) {
                    card.clear();
                }
                $('.wpfs-custom-amount', $form).first().click();
                $('.wpfs-same-billing-and-shipping-address', $form).prop('checked', true).change();
                var coupon = WPFS.getCoupon(formId);
                if (coupon != null) {
                    WPFS.removeCoupon(formId);
                }
                resetCaptcha($form);
                removeHiddenFormFields($form);
            }

            function removeCustomAmountIndexInput($form) {
                $form.find('input[name="wpfs-custom-amount-index"]').remove();
            }

            function removeHiddenFormFields($form) {
                removePaymentMethodIdInput($form);
                removePaymentIntentIdInput($form);
                removeSetupIntentIdInput($form);
                removeSubscriptionIdInput($form);
                removeCustomAmountIndexInput($form);
                removeWPFSNonceInput($form);
            }

            function showLoadingAnimation($form) {
                $form.find('button[type=submit]').addClass('wpfs-btn-primary--loader');
            }

            function hideLoadingAnimation($form) {
                $form.find('button[type=submit]').removeClass('wpfs-btn-primary--loader');
            }

            function clearPaymentDetails($form) {
                var discountLabelPattern = $('td[data-wpfs-summary-row-label="discount"]', $form).data('wpfs-summary-row-label-value');
                var discountLabel = vsprintf(discountLabelPattern, ['']);
                $('td[data-wpfs-summary-row-label="discount"]', $form).text(discountLabel);
                var vatLabelPattern = $('td[data-wpfs-summary-row-label="vat"]', $form).data('wpfs-summary-row-label-value');
                var vatLabel = vsprintf(vatLabelPattern, ['']);
                $('td[data-wpfs-summary-row-label="vat"]', $form).text(vatLabel);
                $('td[data-wpfs-summary-row-value="setup-fee"]', $form).html('&nbsp;');
                $('td[data-wpfs-summary-row-value="subscription"]', $form).html('&nbsp;');
                $('td[data-wpfs-summary-row-value="vat"]', $form).html('&nbsp;');
                $('td[data-wpfs-summary-row-value="discount"]', $form).html('&nbsp;');
                $('td[data-wpfs-summary-row-value="total"]', $form).html('&nbsp;');
            }

            function updatePaymentDetails($form, paymentDetailsData) {
                if (debugLog) {
                    console.log('updatePaymentDetails(): paymentDetailsData=' + JSON.stringify(paymentDetailsData));
                    var formId = $form.data('wpfs-form-id');
                    console.log('updatePaymentDetails(): formId=' + formId);
                }

                var formatter = createCurrencyFormatter($form);

                // tnagy setup fee
                if (paymentDetailsData.planSetupFeeInSmallestCommonCurrency > 0) {
                    var setupFee = formatter.format(paymentDetailsData.planSetupFee, paymentDetailsData.currency.toUpperCase(), paymentDetailsData.currencySymbol, paymentDetailsData.zeroDecimalSupport);
                    $('td[data-wpfs-summary-row-value="setup-fee"]', $form).text(setupFee);
                    $('tr[data-wpfs-summary-row="setup-fee"]', $form).show();
                } else {
                    $('tr[data-wpfs-summary-row="setup-fee"]', $form).hide();
                }
                // tnagy subscription
                if (paymentDetailsData.planAmountInSmallestCommonCurrency > 0) {
                    var planAmount = formatter.format(paymentDetailsData.planAmount, paymentDetailsData.currency.toUpperCase(), paymentDetailsData.currencySymbol, paymentDetailsData.zeroDecimalSupport);
                    var subscriptionLabelPattern = $('td[data-wpfs-summary-row-label="subscription"]', $form).data('wpfs-summary-row-label-value');
                    var subscriptionLabel = paymentDetailsData.planQuantity > 1 ? vsprintf(subscriptionLabelPattern, [paymentDetailsData.planQuantity, 'x', paymentDetailsData.planName]) : paymentDetailsData.planName;
                    $('td[data-wpfs-summary-row-label="subscription"]', $form).text(subscriptionLabel);
                    $('td[data-wpfs-summary-row-value="subscription"]', $form).text(planAmount);
                    $('tr[data-wpfs-summary-row="subscription"]', $form).show();
                } else {
                    $('tr[data-wpfs-summary-row="subscription"]', $form).hide();
                }
                // tnagy discount
                if (paymentDetailsData.discountInSmallestCommonCurrency > 0) {
                    var discountString = '';
                    if ('percent_off' == paymentDetailsData.discountType) {
                        discountString = vsprintf('%s%%', [paymentDetailsData.discountPercentOff]);
                    } else if ('amount_off' == paymentDetailsData.discountType) {
                        discountString = formatter.format(paymentDetailsData.discount, paymentDetailsData.currency.toUpperCase(), paymentDetailsData.currencySymbol, paymentDetailsData.zeroDecimalSupport);
                    }
                    var discountLabelPattern = $('td[data-wpfs-summary-row-label="discount"]', $form).data('wpfs-summary-row-label-value');
                    var discountLabel = vsprintf(discountLabelPattern, [discountString]);
                    $('td[data-wpfs-summary-row-label="discount"]', $form).text(discountLabel);
                    var discount = '-' + formatter.format(paymentDetailsData.discount, paymentDetailsData.currency.toUpperCase(), paymentDetailsData.currencySymbol, paymentDetailsData.zeroDecimalSupport);
                    $('td[data-wpfs-summary-row-value="discount"]', $form).text(discount);
                    $('tr[data-wpfs-summary-row="discount"]', $form).show();
                } else {
                    $('tr[data-wpfs-summary-row="discount"]', $form).hide();
                }
                // tnagy VAT
                if (paymentDetailsData.vatAmountInSmallestCommonCurrency > 0) {
                    var vatLabelPattern = $('td[data-wpfs-summary-row-label="vat"]', $form).data('wpfs-summary-row-label-value');
                    var vatLabel = vsprintf(vatLabelPattern, [paymentDetailsData.vatPercent]);
                    $('td[data-wpfs-summary-row-label="vat"]', $form).text(vatLabel);
                    var vatAmount = formatter.format(paymentDetailsData.vatAmount, paymentDetailsData.currency.toUpperCase(), paymentDetailsData.currencySymbol, paymentDetailsData.zeroDecimalSupport);
                    $('td[data-wpfs-summary-row-value="vat"]', $form).text(vatAmount);
                    $('tr[data-wpfs-summary-row="vat"]', $form).show();
                } else {
                    $('tr[data-wpfs-summary-row="vat"]', $form).hide();
                }
                // tnagy total
                if (paymentDetailsData.total >= 0) {
                    var total = formatter.format(paymentDetailsData.total, paymentDetailsData.currency.toUpperCase(), paymentDetailsData.currencySymbol, paymentDetailsData.zeroDecimalSupport);
                    $('td[data-wpfs-summary-row-value="total"]', $form).text(total);
                    $('tr[data-wpfs-summary-row="total"]', $form).show();
                } else {
                    $('tr[data-wpfs-summary-row="total"]', $form).hide();
                }

                // tnagy prepare subscription details label
                var interval = paymentDetailsData.interval;
                var intervalCount = paymentDetailsData.intervalCount;
                var cancellationCount = paymentDetailsData.cancellationCount;
                var subscriptionDetailsLabel = null;
                if (cancellationCount > 0) {
                    if (intervalCount > 1) {
                        if ('day' === interval) {
                            subscriptionDetailsLabel = vsprintf(wpfsL10n.subscription_charge_interval_templates.x_times_y_days, [intervalCount, cancellationCount]);
                        } else if ('week' === interval) {
                            subscriptionDetailsLabel = vsprintf(wpfsL10n.subscription_charge_interval_templates.x_times_y_weeks, [intervalCount, cancellationCount]);
                        } else if ('month' === interval) {
                            subscriptionDetailsLabel = vsprintf(wpfsL10n.subscription_charge_interval_templates.x_times_y_months, [intervalCount, cancellationCount]);
                        } else if ('year' === interval) {
                            subscriptionDetailsLabel = vsprintf(wpfsL10n.subscription_charge_interval_templates.x_times_y_years, [intervalCount, cancellationCount]);
                        }
                    } else {
                        if ('day' === interval) {
                            subscriptionDetailsLabel = vsprintf(wpfsL10n.subscription_charge_interval_templates.x_times_daily, [cancellationCount]);
                        } else if ('week' === interval) {
                            subscriptionDetailsLabel = vsprintf(wpfsL10n.subscription_charge_interval_templates.x_times_weekly, [cancellationCount]);
                        } else if ('month' === interval) {
                            subscriptionDetailsLabel = vsprintf(wpfsL10n.subscription_charge_interval_templates.x_times_monthly, [cancellationCount]);
                        } else if ('year' === interval) {
                            subscriptionDetailsLabel = vsprintf(wpfsL10n.subscription_charge_interval_templates.x_times_yearly, [cancellationCount]);
                        }
                    }
                } else {
                    if (intervalCount > 1) {
                        if ('day' === interval) {
                            subscriptionDetailsLabel = vsprintf(wpfsL10n.subscription_charge_interval_templates.y_days, [intervalCount]);
                        } else if ('week' === interval) {
                            subscriptionDetailsLabel = vsprintf(wpfsL10n.subscription_charge_interval_templates.y_weeks, [intervalCount]);
                        } else if ('month' === interval) {
                            subscriptionDetailsLabel = vsprintf(wpfsL10n.subscription_charge_interval_templates.y_months, [intervalCount]);
                        } else if ('year' === interval) {
                            subscriptionDetailsLabel = vsprintf(wpfsL10n.subscription_charge_interval_templates.y_years, [intervalCount]);
                        }
                    } else {
                        if ('day' === interval) {
                            subscriptionDetailsLabel = wpfsL10n.subscription_charge_interval_templates.daily;
                        } else if ('week' === interval) {
                            subscriptionDetailsLabel = wpfsL10n.subscription_charge_interval_templates.weekly;
                        } else if ('month' === interval) {
                            subscriptionDetailsLabel = wpfsL10n.subscription_charge_interval_templates.monthly;
                        } else if ('year' === interval) {
                            subscriptionDetailsLabel = wpfsL10n.subscription_charge_interval_templates.yearly;
                        }
                    }
                }

                // tnagy update subscription details label
                $('.wpfs-summary-description', $form).text(subscriptionDetailsLabel);
            }

            function addPaymentMethodIdInput($form, result) {
                if (typeof(result) !== 'undefined' && result.hasOwnProperty('paymentMethod') && result.paymentMethod.hasOwnProperty('id')) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'wpfs-stripe-payment-method-id',
                        value: result.paymentMethod.id
                    }).appendTo($form);
                }
            }

            function addPaymentIntentIdInput($form, result) {
                if (typeof(result) !== 'undefined' && result.hasOwnProperty('paymentIntent') && result.paymentIntent.hasOwnProperty('id')) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'wpfs-stripe-payment-intent-id',
                        value: result.paymentIntent.id
                    }).appendTo($form);
                }
            }

            function addSetupIntentIdInput($form, result) {
                if (typeof(result) !== 'undefined' && result.hasOwnProperty('setupIntent') && result.setupIntent.hasOwnProperty('id')) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'wpfs-stripe-setup-intent-id',
                        value: result.setupIntent.id
                    }).appendTo($form);
                }
            }

            function addWPFSNonceInput($form, data) {
                if (typeof(data) !== 'undefined' && data.hasOwnProperty('nonce')) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'wpfs-nonce',
                        value: data.nonce
                    }).appendTo($form);
                }
            }

            function removePaymentMethodIdInput($form) {
                $('input[name="wpfs-stripe-payment-method-id"]', $form).remove();
            }

            function removeWPFSNonceInput($form) {
                $('input[name="wpfs-nonce"]', $form).remove();
            }

            function removePaymentIntentIdInput($form) {
                $('input[name="wpfs-stripe-payment-intent-id"]', $form).remove();
            }

            function removeSetupIntentIdInput($form) {
                $('input[name="wpfs-stripe-setup-intent-id"]', $form).remove();
            }

            function removeSubscriptionIdInput($form) {
                $('input[name="wpfs-stripe-subscription-id"]', $form).remove();
            }

            function findListOfAmountsElement($form) {
                var $element = null;
                var amountSelectorStyle = $form.data('wpfs-amount-selector-style');
                if (AMOUNT_SELECTOR_STYLE_RADIO_BUTTONS === amountSelectorStyle) {
                    $element = $form.find('input[name="wpfs-custom-amount"]');
                } else if (AMOUNT_SELECTOR_STYLE_BUTTON_GROUP === amountSelectorStyle) {
                    $element = $form.find('input[name="wpfs-custom-amount"]');
                } else if (AMOUNT_SELECTOR_STYLE_DROPDOWN === amountSelectorStyle) {
                    $element = $form.find('select[name="wpfs-custom-amount"]');
                }
                return $element;
            }

            function findCustomAmountElement($form) {
                return $form.find('input[name="wpfs-custom-amount-unique"]');
            }

            function findSelectedAmountFromListOfAmounts($form) {
                var amount = null;
                var amountSelectorStyle = $form.data('wpfs-amount-selector-style');
                if (AMOUNT_SELECTOR_STYLE_RADIO_BUTTONS === amountSelectorStyle) {
                    amount = $form.find('input[name="wpfs-custom-amount"]:checked');
                } else if (AMOUNT_SELECTOR_STYLE_BUTTON_GROUP === amountSelectorStyle) {
                    amount = $form.find('input[name="wpfs-custom-amount"]:checked');
                } else if (AMOUNT_SELECTOR_STYLE_DROPDOWN === amountSelectorStyle) {
                    amount = $form.find('select[name="wpfs-custom-amount"] option:selected');
                }
                return amount;
            }

            function addCustomAmountIndexInput($form) {
                var $selectedAmount = findSelectedAmountFromListOfAmounts($form);
                if ($selectedAmount !== null && $selectedAmount.length > 0) {
                    var amountIndex = $selectedAmount.data('wpfs-amount-index');
                    if (typeof(amountIndex) !== 'undefined') {
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'wpfs-custom-amount-index',
                            value: encodeURIComponent(amountIndex)
                        }).appendTo($form);
                    }
                }
            }

            function addCurrentURL($form) {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'wpfs-referrer',
                    value: encodeURI(window.location.href)
                }).appendTo($form);
            }

            function setPageParametersField($form) {
                var params = getQueryStringIntoArray();
                var paramsJson = JSON.stringify(params);
                $('input[name="wpfs-form-get-parameters"]', $form).val(encodeURIComponent(paramsJson));
            }

            function submitPaymentData($form, card) {

                if (debugLog) {
                    logInfo('submitPaymentData', 'CALLED');
                }

                clearGlobalMessage($form);
                clearFieldErrors($form);

                $.ajax({
                    type: "POST",
                    url: wpfsAjaxURL,
                    data: $form.serialize(),
                    cache: false,
                    dataType: "json",
                    success: function (data) {

                        if (debugLog) {
                            logInfo('submitPaymentData', 'SUCCESS response=' + JSON.stringify(data));
                        }

                        if (data.nonce) {
                            removeWPFSNonceInput($form);
                            addWPFSNonceInput($form, data);
                        }

                        if (data.success) {

                            if (data.redirect) {
                                window.location = data.redirectURL;
                            } else {
                                // tnagy reset form fields
                                resetFormFields($form, card);

                                // tnagy show success message
                                showSuccessGlobalMessage($form, data.messageTitle, data.message);

                                // tnagy enable submit button
                                enableFormButtons($form);
                            }

                        } else if (data.requiresAction) {
                            handleStripeIntentAction($form, card, data);
                        } else {
                            processValidationErrors($form, data);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        logError('submitPaymentData', jqXHR, textStatus, errorThrown);
                        showErrorGlobalMessage($form, wpfsL10n.validation_errors.internal_error_title, wpfsL10n.validation_errors.internal_error);
                    },
                    complete: function () {
                        enableFormButtons($form);
                        hideLoadingAnimation($form);
                    }
                });

            }

            function handleStripeIntentAction($form, card, data) {
                if (debugLog) {
                    logInfo('handleStripeIntentAction', 'CALLED, params: data=' + JSON.stringify(data));
                }
                if (FORM_TYPE_INLINE_PAYMENT === data.formType) {
                    stripe.handleCardAction(data.paymentIntentClientSecret).then(function (result) {
                        if (debugLog) {
                            logInfo('handleStripeIntentAction', 'result=' + JSON.stringify(result));
                        }
                        if (result.error) {
                            logWarn('handleStripeIntentAction', result.error.message);
                            showErrorGlobalMessage($form, wpfsL10n.stripe_errors.internal_error_title, result.error.message);
                        } else {
                            removePaymentIntentIdInput($form);
                            addPaymentIntentIdInput($form, result);
                            disableFormButtons($form);
                            showLoadingAnimation($form);
                            submitPaymentData($form, card);
                        }
                    });
                }
                if (FORM_TYPE_INLINE_DONATION === data.formType) {
                    stripe.handleCardAction(data.paymentIntentClientSecret).then(function (result) {
                        if (debugLog) {
                            logInfo('handleStripeIntentAction', 'result=' + JSON.stringify(result));
                        }
                        if (result.error) {
                            logWarn('handleStripeIntentAction', result.error.message);
                            showErrorGlobalMessage($form, wpfsL10n.stripe_errors.internal_error_title, result.error.message);
                        } else {
                            removePaymentIntentIdInput($form);
                            addPaymentIntentIdInput($form, result);
                            disableFormButtons($form);
                            showLoadingAnimation($form);
                            submitPaymentData($form, card);
                        }
                    });
                }
                if (FORM_TYPE_INLINE_SAVE_CARD === data.formType) {
                    stripe.handleCardSetup(data.setupIntentClientSecret).then(function (result) {
                        if (debugLog) {
                            logInfo('handleStripeIntentAction', 'result=' + JSON.stringify(result));
                        }
                        if (result.error) {
                            logWarn('handleStripeIntentAction', result.error.message);
                            showErrorGlobalMessage($form, wpfsL10n.stripe_errors.internal_error_title, result.error.message);
                        } else {
                            removeSetupIntentIdInput($form);
                            addSetupIntentIdInput($form, result);
                            disableFormButtons($form);
                            showLoadingAnimation($form);
                            submitPaymentData($form, card);
                        }
                    });
                }
                if (FORM_TYPE_INLINE_SUBSCRIPTION === data.formType) {
                    if (data.hasOwnProperty('paymentIntentClientSecret') && data.paymentIntentClientSecret != null) {
                        stripe.handleCardPayment(data.paymentIntentClientSecret).then(function (result) {
                            if (debugLog) {
                                logInfo('handleStripeIntentAction', 'result=' + JSON.stringify(result));
                            }
                            if (result.error) {
                                logWarn('handleStripeIntentAction', result.error.message);
                                showErrorGlobalMessage($form, wpfsL10n.stripe_errors.internal_error_title, result.error.message);
                            } else {
                                removePaymentIntentIdInput($form);
                                removeSetupIntentIdInput($form);
                                addPaymentIntentIdInput($form, result);
                                addSetupIntentIdInput($form, result);
                                disableFormButtons($form);
                                showLoadingAnimation($form);
                                submitPaymentData($form, card);
                            }
                        });
                    } else if (data.hasOwnProperty('setupIntentClientSecret') && data.setupIntentClientSecret != null) {
                        stripe.handleCardSetup(data.setupIntentClientSecret).then(function (result) {
                            if (debugLog) {
                                logInfo('handleStripeIntentAction', 'result=' + JSON.stringify(result));
                            }
                            if (result.error) {
                                logWarn('handleStripeIntentAction', result.error.message);
                                showErrorGlobalMessage($form, wpfsL10n.stripe_errors.internal_error_title, result.error.message);
                            } else {
                                removePaymentIntentIdInput($form);
                                removeSetupIntentIdInput($form);
                                addPaymentIntentIdInput($form, result);
                                addSetupIntentIdInput($form, result);
                                disableFormButtons($form);
                                showLoadingAnimation($form);
                                submitPaymentData($form, card);
                            }
                        });
                    }

                }
            }

            function handleCustomAmountChange($customAmountSelectElement, $form) {
                var amountType = $form.data('wpfs-amount-type');
                var showAmount = $customAmountSelectElement.data('wpfs-show-amount');
                var buttonTitle = $customAmountSelectElement.data('wpfs-button-title');
                var currencyCode = $customAmountSelectElement.data('wpfs-currency');
                var currencySymbol = $customAmountSelectElement.data('wpfs-currency-symbol');
                var zeroDecimalSupport = $customAmountSelectElement.data('wpfs-zero-decimal-support') === "true";
                var amountValue = $customAmountSelectElement.val();
                if (PAYMENT_TYPE_LIST_OF_AMOUNTS === amountType) {
                    if (AMOUNT_OTHER === amountValue) {
                        clearAndShowAndEnableAndFocusCustomAmount($form);
                        if (1 === showAmount) {
                            updateButtonCaption($form, buttonTitle, null, null, null, null);
                        }
                    } else {
                        clearAndHideAndDisableCustomAmount($form);
                        var returnSmallestCommonCurrencyUnit = false;
                        var amount = parseCurrencyAmount(amountValue, zeroDecimalSupport, returnSmallestCommonCurrencyUnit);
                        if (1 === showAmount && !isNaN(amount)) {
                            updateButtonCaption($form, buttonTitle, currencyCode, currencySymbol, amount, zeroDecimalSupport);
                        }
                    }
                }
            }

            function refreshPlans($form, selectedCountry, sourceElement) {
                if (debugLog) {
                    console.log('refreshPlans(): CALLED');
                }
                var formId = $form.data('wpfs-form-id');
                var formType = $form.data('wpfs-form-type');
                var customInputValues = $('[data-wpfs-custom-input-field]', $form).map(function () {
                    return $(this).val();
                }).get();
                clearPaymentDetails($form);
                showLoadingAnimation($form);
                disableFormInputsSelectsButtons($form);
                $.ajax({
                    type: 'POST',
                    url: wpfsAjaxURL,
                    data: {
                        action: 'wp_full_stripe_calculate_plan_amounts_and_setup_fees',
                        formId: formId,
                        formType: formType,
                        selectedCountry: selectedCountry,
                        customInputValues: customInputValues
                    },
                    cache: false,
                    dataType: 'json',
                    success: function (data) {
                        if (data.success && data.plans) {
                            for (var i = 0; i < data.plans.length; i++) {
                                var plan = data.plans[i];
                                var planElement = findPlanElementByPlanId($form, plan.id);
                                if (planElement.length == 1) {
                                    /*
                                     tnagy we use attr('data-*') instead of data('wpfs-*') for a purpose: we update the data attributes
                                     on the elements dynamically but using data() do not reflect changes on source elements
                                     */
                                    planElement.attr('data-wpfs-plan-amount', plan.planAmount);
                                    planElement.attr('data-wpfs-plan-amount-in-smallest-common-currency', plan.planAmountInSmallestCommonCurrency);
                                    planElement.attr('data-wpfs-plan-setup-fee', plan.planSetupFee);
                                    planElement.attr('data-wpfs-plan-setup-fee-in-smallest-common-currency', plan.planSetupFeeInSmallestCommonCurrency);
                                    planElement.attr('data-wpfs-vat-percent', data.vatPercent);
                                }
                            }
                            handlePlanChange($form);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        logError('refreshPlans', jqXHR, textStatus, errorThrown);
                    },
                    complete: function () {
                        hideLoadingAnimation($form);
                        enableFormInputsSelectsButtons($form);
                        if (sourceElement) {
                            var nextInput = $(sourceElement).parents('div.wpfs-form-group').next('div.wpfs-form-group').find('input, select, button');
                            nextInput.focus();
                            if (debugLog) {
                                console.log('refreshPlans(): focus to={id: ' + nextInput.id + ', name: ' + nextInput.name + '}');
                            }
                        }
                    }
                });
            }

            function handleBillingAddressCountryChange($billingAddressSelectElement, $form) {
                if (debugLog) {
                    console.log('handleBillingAddressCountryChange(): CALLED');
                }
                var vatRateType = $billingAddressSelectElement.data('wpfs-vat-rate-type');
                if ('custom_vat' === vatRateType) {
                    var selectedCountry = $('option:selected', $billingAddressSelectElement).val();
                    refreshPlans($form, selectedCountry, $billingAddressSelectElement);
                }
            }

            function findPlanElementByPlanId($form, planId) {
                var $planElement;
                if ($('.wpfs-subscription-plan-select', $form).length > 0) {
                    $planElement = $('select[name="wpfs-plan"] option[data-wpfs-value="' + planId + '"]', $form);
                } else if ($('.wpfs-subscription-plan-radio', $form).length > 0) {
                    $planElement = $('input[name="wpfs-plan"][data-wpfs-value="' + planId + '"]', $form);
                } else if ($('input[name="wpfs-plan"]', $form).length > 0) {
                    $planElement = $('input[name="wpfs-plan"][data-wpfs-value="' + planId + '"]', $form);
                } else {
                    logError('handlePlanChange', 'WARNING: Subscription Plan Selector UI element not found!');
                }
                return $planElement;
            }

            function handlePlanChange($form) {
                if (debugLog) {
                    console.log('handlePlanChange(): CALLED');
                }
                if ($('.wpfs-subscription-plan-select', $form).length > 0) {
                    handlePlanSelectChange($form);
                } else if ($('.wpfs-subscription-plan-radio', $form).length > 0) {
                    handlePlanRadioButtonChange($form);
                } else if ($('.wpfs-subscription-plan-hidden', $form).length > 0) {
                    handlePlanHiddenChange($form);
                } else {
                    logError('handlePlanChange', 'WARNING: Subscription Plan Selector UI element not found!');
                }
            }

            function handlePlanSelectChange($form) {
                if (debugLog) {
                    console.log('handlePlanSelectChange(): CALLED');
                }
                var $subscriptionPlanSelectElement = $('.wpfs-subscription-plan-select', $form);
                var selectedPlanId = $subscriptionPlanSelectElement.val();
                var $selectedPlanElement = $('option[value="' + selectedPlanId + '"]', $form);
                handleSubscriptionPlanChange($form, $selectedPlanElement);
            }

            function handlePlanRadioButtonChange($form) {
                if (debugLog) {
                    console.log('handlePlanRadioButtonChange(): CALLED');
                }
                var $selectedPlanElement = $('.wpfs-subscription-plan-radio:checked', $form);
                if (debugLog) {
                    console.log('handlePlanRadioButtonChange(): selectedPlanElement=' + JSON.stringify($selectedPlanElement));
                }
                handleSubscriptionPlanChange($form, $selectedPlanElement);
            }

            function handlePlanHiddenChange($form) {
                if (debugLog) {
                    console.log('handlePlanHiddenChange(): CALLED');
                }
                var $selectedPlanElement = $('.wpfs-subscription-plan-hidden', $form);
                if (debugLog) {
                    console.log('handlePlanHiddenChange(): selectedPlanElement=' + JSON.stringify($selectedPlanElement));
                }
                handleSubscriptionPlanChange($form, $selectedPlanElement);
            }

            function handleSubscriptionPlanChange($form, $selectedPlanElement) {

                if (debugLog) {
                    console.log('handleSubscriptionPlanChange(): CALLED');
                }

                // tnagy remove coupon related field from previous submit, JIRA reference: WPFS-458
                $('input[name="wpfs-amount-with-coupon-applied"]', $form).remove();

                /*
                 tnagy we use attr('data-*') instead of data('wpfs-*') for a purpose: we update the data attributes
                 on the elements dynamically but using data() do not reflect changes on source elements
                 */
                var interval = $selectedPlanElement.attr('data-wpfs-interval');
                var intervalCount = parseInt($selectedPlanElement.attr('data-wpfs-interval-count'));
                var cancellationCount = parseInt($selectedPlanElement.attr('data-wpfs-cancellation-count'));
                var planAmount = $selectedPlanElement.attr('data-wpfs-plan-amount');
                var planAmountInSmallestCommonCurrency = $selectedPlanElement.attr('data-wpfs-plan-amount-in-smallest-common-currency');
                var planSetupFee = $selectedPlanElement.attr('data-wpfs-plan-setup-fee');
                var planSetupFeeInSmallestCommonCurrency = $selectedPlanElement.attr('data-wpfs-plan-setup-fee-in-smallest-common-currency');
                var currency = $selectedPlanElement.attr('data-wpfs-currency');
                var currencySymbol = $selectedPlanElement.attr('data-wpfs-currency-symbol');
                var zeroDecimalSupport = $selectedPlanElement.attr('data-wpfs-zero-decimal-support') === "true";
                var vatPercent = $selectedPlanElement.attr('data-wpfs-vat-percent');
                var planId = $selectedPlanElement.attr('data-wpfs-value');
                var planName = $selectedPlanElement.attr('data-wpfs-plan-name');
                var planQuantity = 1;
                var $planQuantityElement = $('input[name="wpfs-plan-quantity"]', $form);
                if ($planQuantityElement.length > 0) {
                    if ('stepper' == $planQuantityElement.data('toggle')) {
                        planQuantity = $planQuantityElement.spinner('value');
                    } else {
                        planQuantity = $planQuantityElement.val();
                    }
                }

                var formId = $form.data('wpfs-form-id');
                var coupon = WPFS.getCoupon(formId);

                var paymentDetailsData = {
                    formId: formId,
                    interval: interval,
                    intervalCount: parseInt(intervalCount),
                    cancellationCount: parseInt(cancellationCount),
                    currency: currency,
                    currencySymbol: currencySymbol,
                    zeroDecimalSupport: zeroDecimalSupport,
                    planId: planId,
                    planName: planName,
                    planAmount: zeroDecimalSupport ? parseInt(planQuantity * planAmount) : parseFloat(planQuantity * planAmount).toFixed(2),
                    planAmountInSmallestCommonCurrency: parseInt(planQuantity * planAmountInSmallestCommonCurrency),
                    planSetupFee: parseFloat(planQuantity * planSetupFee).toFixed(2),
                    planSetupFeeInSmallestCommonCurrency: parseInt(planQuantity * planSetupFeeInSmallestCommonCurrency),
                    planQuantity: planQuantity,
                    discount: 0,
                    discountInSmallestCommonCurrency: 0,
                    vatPercent: parseFloat(vatPercent),
                    vatAmount: 0,
                    vatAmountInSmallestCommonCurrency: 0,
                    total: null,
                    totalInSmallestCommonCurrency: null
                };
                var setupFeeAndPlanAmount = parseInt(paymentDetailsData.planSetupFeeInSmallestCommonCurrency) + parseInt(paymentDetailsData.planAmountInSmallestCommonCurrency);
                var couponResult = applyCoupon(currency, setupFeeAndPlanAmount, coupon);
                paymentDetailsData.discountType = couponResult.discountType;
                paymentDetailsData.discountPercentOff = couponResult.discountPercentOff;
                paymentDetailsData.discountInSmallestCommonCurrency = couponResult.discount;
                paymentDetailsData.discount = formatCurrencyAmount(paymentDetailsData.discountInSmallestCommonCurrency, zeroDecimalSupport);
                var subTotal = paymentDetailsData.planSetupFeeInSmallestCommonCurrency + paymentDetailsData.planAmountInSmallestCommonCurrency - paymentDetailsData.discountInSmallestCommonCurrency;
                paymentDetailsData.vatAmountInSmallestCommonCurrency = calculateVATAmount(subTotal, paymentDetailsData.vatPercent);
                paymentDetailsData.vatAmount = formatCurrencyAmount(paymentDetailsData.vatAmountInSmallestCommonCurrency, zeroDecimalSupport);
                paymentDetailsData.totalInSmallestCommonCurrency = subTotal + paymentDetailsData.vatAmountInSmallestCommonCurrency;
                paymentDetailsData.total = formatCurrencyAmount(paymentDetailsData.totalInSmallestCommonCurrency, zeroDecimalSupport);

                updatePaymentDetails($form, paymentDetailsData);

            }

            function showCouponToRedeemRow($form) {
                $('.wpfs-coupon-to-redeem-row', $form).show();
            }

            function hideCouponToRedeemRow($form) {
                $('.wpfs-coupon-to-redeem-row', $form).hide();
            }

            function showRedeemedCouponRow($form, couponName) {
                var redeemedCouponLabelPattern = $('.wpfs-coupon-redeemed-label', $form).data('wpfs-coupon-redeemed-label');
                var redeemedCouponLabel = vsprintf(redeemedCouponLabelPattern, [couponName]);
                if (debugLog) {
                    console.log('showRedeemedCouponRow(): redeemCouponLabelPattern=' + redeemedCouponLabelPattern + ', couponName=' + couponName + ', redeemCouponLabel=' + redeemedCouponLabel);
                }
                $('.wpfs-coupon-redeemed-label', $form).html(redeemedCouponLabel);
                $('.wpfs-coupon-redeemed-row', $form).show();
            }

            function hideRedeemedCouponRow($form) {
                $('.wpfs-coupon-redeemed-label', $form).html('');
                $('.wpfs-coupon-redeemed-row', $form).hide();
            }

            function showRedeemLoadingAnimation($form) {
                var $redeemLink = $('.wpfs-coupon-redeem-link', $form);
                $redeemLink.blur();
                if ($redeemLink.hasClass('wpfs-input-group-link--loader')) {
                    return;
                }
                $redeemLink.addClass('wpfs-input-group-link--loader');
            }

            function hideRedeemLoadingAnimation($form) {
                var $redeemLink = $('.wpfs-coupon-redeem-link', $form);
                $redeemLink.blur();
                $redeemLink.removeClass('wpfs-input-group-link--loader');
            }

            function findPaymentAmountData($form) {
                var result = {};
                var currency;
                var amount;
                var amountType = $form.data('wpfs-amount-type');
                var amountSelectorStyle = $form.data('wpfs-amount-selector-style');
                var zeroDecimalSupport;
                var returnSmallestCommonCurrencyUnit = true;
                var customAmountValue;
                var formatter;
                if (debugLog) {
                    console.log('findPaymentAmountData(): ' + 'amountType=' + amountType + ', ' + 'amountSelectorStyle=' + amountSelectorStyle);
                }
                if (PAYMENT_TYPE_SPECIFIED_AMOUNT === amountType) {
                    result.paymentType = PAYMENT_TYPE_SPECIFIED_AMOUNT;
                    result.customAmount = false;
                    currency = $form.data('wpfs-currency');
                    amount = parseInt($form.data('wpfs-amount'));
                } else if (PAYMENT_TYPE_LIST_OF_AMOUNTS === amountType) {
                    result.paymentType = PAYMENT_TYPE_LIST_OF_AMOUNTS;
                    var $listOfAmounts = findListOfAmountsElement($form);
                    currency = $listOfAmounts.data('wpfs-currency');
                    zeroDecimalSupport = 1 == $listOfAmounts.data('wpfs-zero-decimal-support');
                    var allowCustomAmountValue = 1 == $form.data('wpfs-allow-list-of-amounts-custom');
                    var $selectedAmount = findSelectedAmountFromListOfAmounts($form);
                    if ($selectedAmount && $selectedAmount.length > 0) {
                        amount = $selectedAmount.val();
                    }
                    if (debugLog) {
                        console.log('findPaymentAmountData(): ' + 'zeroDecimalSupport=' + zeroDecimalSupport + ', ' + 'allowCustomAmountValue=' + allowCustomAmountValue + ', ' + 'amount=' + amount);
                    }
                    if (allowCustomAmountValue && AMOUNT_OTHER == amount) {
                        result.customAmount = true;
                        customAmountValue = $('input[name="wpfs-custom-amount-unique"]', $form).val();
                        formatter = createCurrencyFormatter($form);
                        if (formatter.validForParse(customAmountValue)) {
                            clearFieldError($form, 'wpfs-custom-amount-unique', $('input[name="wpfs-custom-amount-unique"]', $form).attr('id'));
                            amount = parseCurrencyAmount(formatter.parse(customAmountValue), zeroDecimalSupport, returnSmallestCommonCurrencyUnit);
                        } else {
                            amount = null;
                            clearFieldError($form, 'wpfs-custom-amount-unique', $('input[name="wpfs-custom-amount-unique"]', $form).attr('id'));
                            showFieldError($form, 'wpfs-custom-amount-unique', $('input[name="wpfs-custom-amount-unique"]', $form).attr('id'), wpfsL10n.validation_errors.custom_payment_amount_value_is_invalid, true);
                        }
                        if (debugLog) {
                            console.log('findPaymentAmountData(): ' + 'custom amount=' + amount);
                        }
                    } else {
                        result.customAmount = false;
                        amount = parseCurrencyAmount($selectedAmount.val(), zeroDecimalSupport, returnSmallestCommonCurrencyUnit);
                    }
                } else if (PAYMENT_TYPE_CUSTOM_AMOUNT == amountType) {
                    result.customAmount = true;
                    var customAmountElement = findCustomAmountElement($form);
                    currency = customAmountElement.data('wpfs-currency');
                    zeroDecimalSupport = 1 == customAmountElement.data('wpfs-zero-decimal-support');
                    customAmountValue = customAmountElement.val();
                    formatter = createCurrencyFormatter($form);
                    if (formatter.validForParse(customAmountValue)) {
                        clearFieldError($form, 'wpfs-custom-amount-unique', $('input[name="wpfs-custom-amount-unique"]', $form).attr('id'));
                        amount = parseCurrencyAmount(formatter.parse(customAmountValue), zeroDecimalSupport, returnSmallestCommonCurrencyUnit);
                    } else {
                        amount = null;
                        clearFieldError($form, 'wpfs-custom-amount-unique', $('input[name="wpfs-custom-amount-unique"]', $form).attr('id'));
                        showFieldError($form, 'wpfs-custom-amount-unique', $('input[name="wpfs-custom-amount-unique"]', $form).attr('id'), wpfsL10n.validation_errors.custom_payment_amount_value_is_invalid, true);
                    }
                } else if (PAYMENT_TYPE_CARD_CAPTURE === amountType) {
                    result.paymentType = PAYMENT_TYPE_CARD_CAPTURE;
                    result.customAmount = false;
                    currency = $form.data('wpfs-currency');
                    amount = parseInt($form.data('wpfs-amount'));
                }
                if (amount === null || isNaN(amount) || amount < 0) {
                    result.valid = false;
                } else {
                    result.valid = true;
                    result.currency = currency;
                    result.amount = amount;
                }

                if (debugLog) {
                    console.log('findPaymentAmountData(): ' + 'result=' + JSON.stringify(result));
                }

                return result;
            }

            function findPlanAmountData($form) {
                var result = {};
                var planAmount, setupFee, currency, currencySymbol, vatPercent, vatAmount, discountAmount;
                var $selectedPlan;
                if (1 == $form.data('wpfs-simple-button-layout')) {
                    $selectedPlan = $('input[name="wpfs-plan"]', $form);
                } else {
                    if ($('.wpfs-subscription-plan-select', $form).length > 0) {
                        $selectedPlan = $('select[name="wpfs-plan"] option:selected', $form);
                    } else if ($('.wpfs-subscription-plan-radio', $form).length > 0) {
                        $selectedPlan = $('.wpfs-subscription-plan-radio:checked', $form);
                    } else if ($('.wpfs-subscription-plan-hidden', $form).length > 0) {
                        $selectedPlan = $('.wpfs-subscription-plan-hidden', $form);
                    } else {
                        logError('findPlanAmountData', 'WARNING: Subscription Plan Selector UI element not found!');
                    }
                }
                if ($selectedPlan && $selectedPlan.length == 1) {
                    planAmount = $selectedPlan.data('wpfs-plan-amount-in-smallest-common-currency');
                    setupFee = $selectedPlan.data('wpfs-plan-setup-fee-in-smallest-common-currency');
                    currency = $selectedPlan.data('wpfs-currency');
                    currencySymbol = $selectedPlan.data('wpfs-currency-symbol');
                    vatPercent = $selectedPlan.data('wpfs-vat-percent');
                }
                if (planAmount === null || isNaN(planAmount) || planAmount <= 0) {
                    result.valid = false;
                } else {
                    var formId = $form.data('wpfs-form-id');
                    var coupon = WPFS.getCoupon(formId);
                    var couponResult = applyCoupon(currency, planAmount + setupFee, coupon);
                    discountAmount = couponResult.discount;
                    var subTotal = planAmount + setupFee - discountAmount;
                    vatAmount = calculateVATAmount(subTotal, vatPercent);
                    result.valid = true;
                    result.amount = subTotal + vatAmount;
                    result.currency = currency;
                    result.currencySymbol = currencySymbol;
                }
                return result;
            }

            $('input[data-wpfs-custom-input-field]').focus(function () {
                $(this).data('wpfs-last-value', $(this).val());
            });

            $('input[data-wpfs-custom-input-field]').blur(function (e) {
                var lastValue = $(this).data('wpfs-last-value');
                var currentValue = $(this).val();
                if (lastValue != currentValue) {
                    var $form = getParentForm(this);
                    var addressCountrySelector = $('.wpfs-billing-address-country-select', $form);
                    if (addressCountrySelector.length > 0) {
                        var vatRateType = addressCountrySelector.data('wpfs-vat-rate-type');
                        if ('custom_vat' == vatRateType) {
                            var selectedCountry = addressCountrySelector.find('option:selected').val();
                            refreshPlans($form, selectedCountry, this);
                        }
                    }
                }
            });

            var WPFS = {};
            WPFS.couponMap = {};
            /**
             * @param formId
             * @param coupon
             */
            WPFS.setCoupon = function (formId, coupon) {
                WPFS.couponMap[formId] = coupon;
            };
            /**
             * @param formId
             * @returns {*}
             */
            WPFS.getCoupon = function (formId) {
                if (WPFS.couponMap.hasOwnProperty(formId)) {
                    return WPFS.couponMap[formId];
                }
                return null;
            };
            /**
             * @param formId
             */
            WPFS.removeCoupon = function (formId) {
                if (WPFS.couponMap.hasOwnProperty(formId)) {
                    delete WPFS.couponMap[formId];
                }
            };

            WPFS.initCustomAmount = function () {
                $('input.wpfs-custom-amount').change(function (e) {
                    var $form = getParentForm(this);
                    handleCustomAmountChange($(this), $form);
                });
                $('input.wpfs-custom-amount').each(function (index) {
                    var $form = getParentForm(this);
                    $form.find('input.wpfs-custom-amount').first().click();
                });
                $('.wpfs-custom-amount--unique').blur(function () {
                    var $form = getParentForm(this);
                    var showAmount = $(this).data('wpfs-show-amount');
                    var buttonTitle = $(this).data('wpfs-button-title');
                    var currencyCode = $(this).data('wpfs-currency');
                    var currencySymbol = $(this).data('wpfs-currency-symbol');
                    var zeroDecimalSupport = $(this).data('wpfs-zero-decimal-support') === "true";
                    var customAmountValue = $(this).val();
                    var formatter = createCurrencyFormatter($form);
                    if (1 === showAmount) {
                        var returnSmallestCommonCurrencyUnit = false;
                        var amount;
                        if (formatter.validForParse(customAmountValue)) {
                            clearFieldError($form, 'wpfs-custom-amount-unique', $('input[name="wpfs-custom-amount-unique"]', $form).attr('id'));
                            amount = parseCurrencyAmount(formatter.parse(customAmountValue), zeroDecimalSupport, returnSmallestCommonCurrencyUnit);
                        } else {
                            amount = null;
                            clearFieldError($form, 'wpfs-custom-amount-unique', $('input[name="wpfs-custom-amount-unique"]', $form).attr('id'));
                            showFieldError($form, 'wpfs-custom-amount-unique', $('input[name="wpfs-custom-amount-unique"]', $form).attr('id'), wpfsL10n.validation_errors.custom_payment_amount_value_is_invalid, true);
                        }
                        if (amount === null || isNaN(amount)) {
                            updateButtonCaption($form, buttonTitle, null, null, null, null);
                        } else {
                            updateButtonCaption($form, buttonTitle, currencyCode, currencySymbol, zeroDecimalSupport ? amount : amount.toFixed(2), zeroDecimalSupport);
                        }
                    }
                });
            };

            WPFS.initStripeJSCard = function () {
                var $cards = $('form[data-wpfs-form-type] [data-toggle="card"]');

                if ($cards.length === 0) {
                    return;
                }

                $cards.each(function (index) {

                    // tnagy get references for form, formId
                    var $form = getParentForm(this);
                    var formId = $form.data('wpfs-form-id');
                    var elementsLocale = $form.data('wpfs-preferred-language');

                    // tnagy create Stripe Elements\Card
                    var elements = stripe.elements({
                        locale: elementsLocale
                    });
                    var cardElement = elements.create('card', {
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
                    cardElement.mount('div.wpfs-form-control[data-wpfs-form-id="' + formId + '"]');

                    // tnagy handle form submission
                    $form.submit(function (event) {

                        event.preventDefault();

                        /*
                         tnagy disable submit button and show loading animation,
                         clear message panel, reset token and amount index
                         */
                        disableFormButtons($form);
                        showLoadingAnimation($form);
                        clearFieldErrors($form);
                        clearGlobalMessage($form);
                        removeHiddenFormFields($form);

                        // Add get parameters to the form
                        setPageParametersField($form);

                        // tnagy add amount index
                        addCustomAmountIndexInput($form);

                        //tnagy validate fields
                        var errorMessage = null;

                        // tnagy validate terms of use if necessary
                        var showTermsOfUse = $form.data('wpfs-show-terms-of-use');
                        if (1 == showTermsOfUse) {
                            var termsOfUseAccepted = $form.find('input[name=wpfs-terms-of-use-accepted]').prop('checked');
                            if (termsOfUseAccepted == false) {
                                errorMessage = $form.data('wpfs-terms-of-use-not-checked-error-message');
                                showFieldError($form, 'wpfs-terms-of-use-accepted', null, errorMessage);
                                scrollToElement($('input[name=wpfs-terms-of-use-accepted]', $form), false);
                                enableFormButtons($form);
                                hideLoadingAnimation($form);
                                return false;
                            }
                        }

                        var paymentMethodData = {};
                        var cardHolderNameField = $('input[name="wpfs-card-holder-name"]', $form);
                        if (cardHolderNameField.length > 0) {
                            var cardHolderName = cardHolderNameField.val();
                            if (cardHolderName != null && cardHolderName != '') {
                                paymentMethodData.billing_details = {
                                    name: cardHolderName
                                };
                            }
                        }
                        var billingAddressCountryField = $('input[name="wpfs-billing-address-country"]', $form);
                        if (billingAddressCountryField.length > 0) {
                            var billingAddressCountry = billingAddressCountryField.val();
                            if (billingAddressCountry != null && billingAddressCountry != '') {
                                paymentMethodData.billing_details.address = {
                                    country: billingAddressCountry
                                };
                            }
                        }

                        if (debugLog) {
                            console.log('form.submit(): ' + 'Creating PaymentMethod...');
                        }
                        stripe.createPaymentMethod(
                            'card',
                            cardElement,
                            paymentMethodData
                        ).then(function (createPaymentMethodResult) {
                            if (debugLog) {
                                console.log('form.submit(): ' + 'PaymentMethod creation result=' + JSON.stringify(createPaymentMethodResult));
                            }
                            clearFieldErrors($form);
                            if (createPaymentMethodResult.error) {
                                enableFormButtons($form);
                                hideLoadingAnimation($form);
                                showFieldError($form, 'cardnumber', null, createPaymentMethodResult.error.message);
                                scrollToElement($('.wpfs-form-card', $form), false);
                            } else {
                                addPaymentMethodIdInput($form, createPaymentMethodResult);
                                submitPaymentData($form, cardElement);
                            }
                        });

                        return false;
                    });
                });
            };

            WPFS.initCoupon = function () {
                var couponFieldName = 'wpfs-coupon';
                $('.wpfs-coupon-remove-link').click(function (event) {
                    event.preventDefault();
                    var $form = getParentForm(this);
                    var formId = $form.data('wpfs-form-id');
                    // tnagy remove stored coupon
                    WPFS.removeCoupon(formId);
                    // tnagy show coupon to redeem
                    var $coupon = $('input[name="' + couponFieldName + '"]', $form);
                    if ($coupon.length > 0) {
                        $coupon.val('');
                    }
                    hideRedeemedCouponRow($form);
                    showCouponToRedeemRow($form);
                    // tnagy update plans and payment summary
                    handlePlanChange($form);
                });
                $('.wpfs-coupon-redeem-link').click(function (event) {
                    event.preventDefault();
                    var $form = getParentForm(this);
                    disableFormButtons($form);
                    var formId = $form.data('wpfs-form-id');
                    var $coupon = $('input[name="' + couponFieldName + '"]', $form);
                    if ($coupon.length > 0) {
                        clearFieldErrors($form);
                        $coupon.prop('disabled', true);
                        showRedeemLoadingAnimation($form);
                        var couponValue = $coupon.val();
                        $.ajax({
                            type: 'POST',
                            url: wpfsAjaxURL,
                            data: {action: 'wp_full_stripe_check_coupon', code: couponValue},
                            cache: false,
                            dataType: 'json',
                            success: function (data) {
                                if (data.valid) {
                                    // tnagy store coupon
                                    WPFS.setCoupon(formId, data.coupon);
                                    // tnagy show redeemed coupon
                                    hideCouponToRedeemRow($form);
                                    showRedeemedCouponRow($form, data.coupon.name);
                                    // tnagy update plans and payment summary
                                    handlePlanChange($form);
                                } else {
                                    var couponFieldId = $coupon.attr('id');
                                    showFieldError($form, couponFieldName, couponFieldId, data.msg);
                                    scrollToElement($coupon, false);
                                }
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                logError('coupon', jqXHR, textStatus, errorThrown);
                                showErrorGlobalMessage($form, wpfsL10n.stripe_errors.internal_error);
                            },
                            complete: function () {
                                $coupon.prop('disabled', false);
                                hideRedeemLoadingAnimation($form);
                                enableFormButtons($form);
                            }
                        });
                    }
                });
            };

            WPFS.initInputGroup = function () {
                var inputGroupPrependClass = '.wpfs-input-group-prepend';
                $(document).on('click', inputGroupPrependClass, function (e) {
                    var $target = $(e.target);
                    if ($target.hasClass('wpfs-input-group-link')) {
                        return;
                    }

                    if ($target.parents('.wpfs-input-group-link').length > 0) {
                        return;
                    }

                    $(this).next().focus();
                });

                $(document).on('mouseenter', inputGroupPrependClass, function () {
                    $(this).next().mouseenter();
                });

                $(document).on('mouseleave', inputGroupPrependClass, function () {
                    $(this).next().mouseleave();
                });

                var inputGroupAppendClass = '.wpfs-input-group-append';
                $(document).on('click', inputGroupAppendClass, function (e) {
                    var $target = $(e.target);
                    if ($target.hasClass('wpfs-input-group-link')) {
                        return;
                    }

                    if ($target.parents('.wpfs-input-group-link').length > 0) {
                        return;
                    }

                    $(this).prev().focus();
                });

                $(document).on('mouseenter', inputGroupAppendClass, function () {
                    $(this).prev().mouseenter();
                });

                $(document).on('mouseleave', inputGroupAppendClass, function () {
                    $(this).prev().mouseleave();
                });
            };

            WPFS.initSelectmenu = function () {
                $.widget('custom.wpfsSelectmenu', $.ui.selectmenu, {
                    _renderItem: function (ul, item) {
                        var $li = $('<li>');
                        var wrapper = $('<div>', {
                            class: 'menu-item-wrapper ui-menu-item-wrapper',
                            text: item.label
                        });

                        if (item.disabled) {
                            $li.addClass('ui-state-disabled');
                        }

                        return $li.append(wrapper).appendTo(ul);
                    }
                });

                var $selectmenus = $('[data-toggle="selectmenu"]');
                $selectmenus.each(function () {
                    if (typeof $(this).select2 === "function") {
                        try {
                            $(this).select2('destroy');
                        } catch (err) {
                        }
                    }

                    var $selectmenu = $(this).wpfsSelectmenu({
                        classes: {
                            'ui-selectmenu-button': 'wpfs-form-control wpfs-selectmenu-button',
                            'ui-selectmenu-menu': 'wpfs-ui wpfs-selectmenu-menu'
                        },
                        icons: {
                            button: "wpfs-icon-arrow"
                        },
                        create: function () {
                            if (debugLog) {
                                console.log('selectmenu.create(): CALLED');
                            }
                            var $this = $(this);
                            var $selectMenuButton = $this.next();
                            $selectMenuButton.addClass($this.attr('class'));
                            if ($this.find('option:selected:disabled').length > 0) {
                                $selectMenuButton.addClass('ui-state-placeholder');
                            }
                        },
                        open: function () {
                            if (debugLog) {
                                console.log('selectmenu.open(): CALLED');
                            }
                            var $this = $(this);
                            var $button = $this.data('custom-wpfsSelectmenu').button;
                            $button.removeClass('ui-selectmenu-button-closed');
                            $button.addClass('ui-selectmenu-button-open');
                            var selectedClass = 'ui-state-selected';
                            var selectedIndex = $this.find('option').index($this.find('option:selected'));
                            $('.ui-selectmenu-open .ui-menu-item-wrapper').removeClass(selectedClass);
                            var $menuItem = $('.ui-selectmenu-open .ui-menu-item').eq(selectedIndex);
                            if (!$menuItem.hasClass('ui-state-disabled')) {
                                $menuItem.find('.ui-menu-item-wrapper').addClass(selectedClass);
                            }
                        },
                        close: function () {
                            if (debugLog) {
                                console.log('selectmenu.close(): CALLED')
                            }
                            var $this = $(this);
                            var $button = $this.data('custom-wpfsSelectmenu').button;
                            $button.removeClass('ui-selectmenu-button-open');
                            $button.addClass('ui-selectmenu-button-closed');
                        },
                        change: function (event, ui) {
                            if (debugLog) {
                                console.log('selectmenu.change(): CALLED');
                            }
                            var $selectElement = $(event.target);
                            var $form = getParentForm(event.target);
                            if ($selectElement.length > 0) {
                                var selectElementType = $selectElement.data('wpfs-select');
                                if ('wpfs-billing-address-country-select' === selectElementType) {
                                    var $billingAddressCountrySelectElement = $('.wpfs-billing-address-country-select', $form);
                                    handleBillingAddressCountryChange($billingAddressCountrySelectElement, $form);
                                } else if ('wpfs-custom-amount-select' === selectElementType) {
                                    var amountType = $form.data('wpfs-amount-type');
                                    if ('list_of_amounts' === amountType) {
                                        var $customAmountSelectElement = $('.wpfs-custom-amount-select', $form);
                                        handleCustomAmountChange($customAmountSelectElement, $form);
                                    }
                                } else if ('wpfs-subscription-plan-select' === selectElementType) {
                                    handlePlanChange($form);
                                }
                            }
                            $(this).next().removeClass('ui-state-placeholder');
                        }
                    });

                    var $selectmenuParent = $selectmenu.parent();
                    $selectmenuParent.find('.ui-selectmenu-button')
                        .addClass('wpfs-form-control')
                        .addClass('wpfs-selectmenu-button')
                        .addClass('ui-button');

                    $selectmenu.data('custom-wpfsSelectmenu').menuWrap
                        .addClass('wpfs-ui')
                        .addClass('wpfs-selectmenu-menu');

                });
            };

            WPFS.initAddressSwitcher = function () {
                $('.wpfs-same-billing-and-shipping-address').change(function () {
                    if (debugLog) {
                        logInfo('.wpfs-same-billing-and-shipping-address CHANGE', 'CALLED');
                    }
                    var addressSwitcherId = $(this).data('wpfs-address-switcher-id');
                    var billingAddressSwitchId = $(this).data('wpfs-billing-address-switch-id');
                    if ($(this).prop('checked')) {
                        $('#' + addressSwitcherId).hide();
                        $('#' + billingAddressSwitchId).click();
                    } else {
                        $('#' + addressSwitcherId).show();
                        $('#' + billingAddressSwitchId).click();
                    }
                    $('.wpfs-billing-address-switch').change();
                    $('.wpfs-shipping-address-switch').change();
                });
                $('.wpfs-billing-address-switch').change(function () {
                    if (debugLog) {
                        logInfo('.wpfs-billing-address-switch CHANGE', 'CALLED');
                    }
                    var billingAddressPanelId = $(this).data('wpfs-billing-address-panel-id');
                    var shippingAddressPanelId = $(this).data('wpfs-shipping-address-panel-id');
                    if ($(this).prop('checked')) {
                        $('#' + billingAddressPanelId).show();
                        $('#' + shippingAddressPanelId).hide();
                    }
                });
                $('.wpfs-shipping-address-switch').change(function () {
                    var billingAddressPanelId = $(this).data('wpfs-billing-address-panel-id');
                    var shippingAddressPanelId = $(this).data('wpfs-shipping-address-panel-id');
                    if ($(this).prop('checked')) {
                        $('#' + billingAddressPanelId).hide();
                        $('#' + shippingAddressPanelId).show();
                    }
                });
            };

            WPFS.initTooltip = function () {
                $.widget.bridge('wpfstooltip', $.ui.tooltip);

                $('[data-toggle="tooltip"]').wpfstooltip({
                    items: '[data-toggle="tooltip"]',
                    content: function () {
                        var contentId = $(this).data('tooltip-content');
                        return $('[data-tooltip-id="' + contentId + '"]').html();
                    },
                    position: {
                        my: 'left top+5',
                        at: 'left bottom+5',
                        using: function (position, feedback) {
                            var $this = $(this);
                            $this.css(position);
                            $this
                                .addClass(feedback.vertical)
                                .addClass(feedback.horizontal);
                        }
                    },
                    classes: {
                        'ui-tooltip': 'wpfs-ui wpfs-tooltip'
                    },
                    tooltipClass: 'wpfs-ui wpfs-tooltip'
                }).on({
                    "click": function (event) {
                        event.preventDefault();
                        $(this).tooltip("open");
                    }
                });
            };

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
                            var $spinnerElement = $(e.target);
                            if ($spinnerElement.length > 0) {
                                var spinnerElementType = $spinnerElement.data('wpfs-stepper');
                                if ('wpfs-plan-quantity' === spinnerElementType) {
                                    var $form = getParentForm(e.target);
                                    handlePlanChange($form);
                                }
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
                        },
                        stop: function (e, ui) {
                            console.log('STOP, value=' + ui.value);
                            var $spinnerElement = $(e.target);
                            if ($spinnerElement.length > 0) {
                                var spinnerElementType = $spinnerElement.data('wpfs-stepper');
                                if ('wpfs-plan-quantity' === spinnerElementType) {
                                    var $form = getParentForm(e.target);
                                    handlePlanChange($form);
                                }
                            }
                        }
                    })
                        .parent()
                        .find('.ui-icon').text('');
                });
            };

            WPFS.initDatepicker = function () {
                $('[data-toggle="datepicker"]').each(function () {
                    var $this = $(this);
                    var dateFormat = $this.data('dateFormat') || 'dd/mm/yyyy';
                    var defaultValue = $this.data('defaultValue') || '';

                    if ($this.val() === '') {
                        $this.val(defaultValue);
                    }

                    $this
                        .attr('placeholder', dateFormat)
                        .inputmask({
                            alias: dateFormat,
                            showMaskOnHover: false,
                            showMaskOnFocus: false
                        })
                        .datepicker({
                            prevText: '',
                            nextText: '',
                            hideIfNoPrevNext: true,
                            firstDay: 1,
                            dateFormat: dateFormat.replace(/yy/g, 'y'),
                            showOtherMonths: true,
                            selectOtherMonths: true,
                            onChangeMonthYear: function (year, month, inst) {
                                if (inst.dpDiv.hasClass('bottom')) {
                                    setTimeout(function () {
                                        inst.dpDiv.css('top', inst.input.offset().top - inst.dpDiv.outerHeight());
                                    });
                                }
                            },
                            beforeShow: function (el, inst) {
                                var $el = $(el);
                                inst.dpDiv.addClass('wpfs-ui wpfs-datepicker-div');
                                setTimeout(function () {
                                    if ($el.offset().top > inst.dpDiv.offset().top) {
                                        inst.dpDiv.removeClass('top');
                                        inst.dpDiv.addClass('bottom');
                                    } else {
                                        inst.dpDiv.removeClass('bottom');
                                        inst.dpDiv.addClass('top');
                                    }
                                });
                            }
                        })
                        .on('blur', function () {
                            var $this = $(this);
                            setTimeout(function () {
                                var date = $this.val();
                                var isValid = Inputmask.isValid(date, {
                                    alias: dateFormat
                                });
                                if (isValid) {
                                    defaultValue = date;
                                } else {
                                    $this.val(defaultValue);
                                }
                            }, 50);
                        });
                });
            };

            WPFS.initCombobox = function () {
                $.widget('custom.combobox', {
                    _selectOptions: [],
                    _lastValidValue: null,
                    _create: function () {
                        this.wrapper = $('<div>')
                            .addClass('wpfs-input-group wpfs-combobox')
                            .addClass(this.element.attr('class'))
                            .insertAfter(this.element);

                        this._selectOptions = this.element.children('option').map(function () {
                            var $this = $(this);
                            var text = $this.text();
                            var value = $this.val();
                            if (value && value !== '') {
                                return {
                                    label: text,
                                    value: text,
                                    option: this
                                };
                            }
                        });

                        this.element.hide();
                        this._createAutocomplete();
                        this._createShowAllButton();
                    },
                    _createAutocomplete: function () {
                        var selected = this.element.children(':selected');
                        var value = selected.val() ? selected.text() : '';

                        this.input = $('<input>')
                            .attr('placeholder', this.element.data('placeholder'))
                            .appendTo(this.wrapper)
                            .val(value)
                            .addClass('wpfs-input-group-form-control')
                            .autocomplete({
                                delay: 0,
                                minLength: 0,
                                source: $.proxy(this, '_source'),
                                position: {
                                    my: 'left-1px top+2px',
                                    at: 'left-1px bottom+2px',
                                    using: function (position, feedback) {
                                        var $this = $(this);
                                        $this.css(position);
                                        $this.width(feedback.target.width + 46);
                                    }
                                },
                                classes: {
                                    'ui-autocomplete': 'wpfs-ui wpfs-combobox-menu'
                                },
                                open: function () {
                                    $(this).parent().addClass('wpfs-combobox--open');
                                },
                                close: function () {
                                    var $this = $(this);
                                    $this.parent().removeClass('wpfs-combobox--open');
                                    $this.blur();
                                },
                                search: function (e, ui) {
                                    // Fix autocomplete combobox memory leak
                                    $(this).data('uiAutocomplete').menu.bindings = $();
                                }
                            })
                            .on('focus', function () {
                                $(this).data('uiAutocomplete').search('');
                            })
                            .on('keydown', function (e) {
                                if (e.keyCode === 13) {
                                    $(this).blur();
                                }
                            });

                        this.input.data('uiAutocomplete')._renderItem = this._renderItem;

                        this._on(this.input, {
                            autocompleteselect: function (e, ui) {
                                if (!ui.item.noResultsItem) {
                                    ui.item.option.selected = true;
                                    this._trigger('select', e, {
                                        item: ui.item.option
                                    });
                                }
                                this._trigger('blur');
                            },
                            autocompletechange: '_validateValue'
                        });
                    },
                    _createShowAllButton: function () {
                        var input = this.input;
                        var wasOpen = false;
                        var html = '<div class="wpfs-input-group-append"><span class="wpfs-input-group-icon"><span class="wpfs-icon-arrow"></span></span></div>';

                        $(html)
                            .appendTo(this.wrapper)
                            .on('mousedown', function () {
                                wasOpen = input.autocomplete('widget').is(':visible');
                            })
                            .on('click', function (e) {
                                e.stopPropagation();
                                if (wasOpen) {
                                    input.autocomplete('close');
                                } else {
                                    input.trigger('focus');
                                }
                            });
                    },
                    _source: function (request, response) {
                        var matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term), 'i');
                        var results = this._selectOptions.map(function (i, option) {
                            if (option.value && (!request.term || matcher.test(option.label))) {
                                return option;
                            }
                        });

                        if (results.length > 0) {
                            response(results);
                        } else {
                            response([{
                                label: this.element.data('noResultsMessage'),
                                value: request.term,
                                noResultsItem: true
                            }]);
                        }
                    },
                    _validateValue: function (e, ui) {
                        // Selected an item, nothing to do
                        if (ui.item) {
                            return;
                        }

                        // Search for a match (case-insensitive)
                        var value = this.input.val();
                        var valueLowerCase = value.toLowerCase();
                        var valid = false;
                        var selectedText = null;
                        this.element.children('option').each(function () {
                            var text = $(this).text();
                            if (text.toLowerCase() === valueLowerCase) {
                                selectedText = text;
                                this.selected = valid = true;
                                return false;
                            }
                        });

                        if (valid) {
                            // Fix valid value
                            this.input.val(selectedText);
                            this._lastValidValue = selectedText;
                        } else if (!valid && this._lastValidValue !== null) {
                            // Set last valid value
                            this.input.val(this._lastValidValue);
                        } else {
                            // Remove invalid value
                            this.input.val('');
                            this.element.val('');
                            this.input.autocomplete('instance').term = '';
                        }
                    },
                    _renderItem: function (ul, item) {
                        var t = '';
                        var idx = item.label.toLowerCase().indexOf(this.term.toLowerCase());
                        var sameLabelAndTerm = item.label.toLowerCase() === this.term.toLowerCase();

                        if (idx !== -1 && !sameLabelAndTerm && this.term !== '') {
                            var termLength = this.term.length;
                            t += item.label.substring(0, idx);
                            t += '<strong>' + item.label.substr(idx, termLength) + '</strong>';
                            t += item.label.substr(idx + termLength);
                        } else {
                            t = item.label;
                        }

                        var className = '';
                        var $li = $('<li></li>');
                        var $div = $('<div class="ui-menu-item-wrapper">' + t + '</div>');
                        if (!item.noResultsItem) {
                            $li.data('item.autocomplete', item);
                            if (sameLabelAndTerm) {
                                $div.addClass('ui-state-selected');
                            }
                        } else {
                            $li.addClass('ui-state-disabled');
                        }

                        ul
                            .addClass('wpfs-ui')
                            .addClass('wpfs-combobox-menu');

                        return $li
                            .append($div)
                            .appendTo(ul);
                    },
                    _destroy: function () {
                        this.wrapper.remove();
                        this.element.show();
                    }
                });

                $('[data-toggle="combobox"]').combobox();
            };

            WPFS.initCheckoutForms = function () {

                if (debugLog) {
                    logInfo('initCheckoutForms', 'CALLED');
                }

                var popupFormsSelector = 'form[data-wpfs-form-type="' + FORM_TYPE_POPUP_PAYMENT + '"], form[data-wpfs-form-type="' + FORM_TYPE_POPUP_SUBSCRIPTION + '"], form[data-wpfs-form-type="' + FORM_TYPE_POPUP_SAVE_CARD + '"], form[data-wpfs-form-type="' + FORM_TYPE_POPUP_DONATION + '"]';
                var $popupForms = $(popupFormsSelector);
                $popupForms.each(function (index, popupForm) {
                    var $form = $(popupForm);

                    if (debugLog) {
                        logInfo('initCheckoutForms', 'form=' + $form.data('wpfs-form-id'));
                    }

                    $form.submit(function (event) {

                        if (debugLog) {
                            logInfo('checkoutFormSubmit', 'CALLED, formId=' + $form.data('wpfs-form-id'));
                        }

                        event.preventDefault();

                        /*
                         tnagy disable submit button and show loading animation,
                         clear message panel, reset token and amount index
                         */
                        disableFormButtons($form);
                        showLoadingAnimation($form);
                        clearFieldErrors($form);
                        clearGlobalMessage($form);
                        removeCustomAmountIndexInput($form);

                        // Add get parameters to the form
                        setPageParametersField($form);

                        // tnagy add amount index
                        addCustomAmountIndexInput($form);

                        // tnagy validate form
                        var hasErrors = false;
                        var $firstInvalidField = null;
                        var fieldErrorMessage = null;

                        // tnagy validate custom fields
                        var customInputRequired = $form.data('wpfs-custom-input-required');
                        if (1 == customInputRequired) {
                            var customInputFieldsWithMissingValue = [];
                            var customInputValues = $('input[name="wpfs-custom-input"]', $form);
                            if (customInputValues.length == 0) {
                                customInputValues = $('input[name="wpfs-custom-input[]"]', $form);
                            }
                            if (customInputValues) {
                                customInputValues.each(function () {
                                    if ($(this).val().length == 0) {
                                        customInputFieldsWithMissingValue.push(this);
                                    }
                                });
                            }
                            if (customInputFieldsWithMissingValue.length > 0) {
                                for (var index in customInputFieldsWithMissingValue) {
                                    var $customInputField = $(customInputFieldsWithMissingValue[index]);
                                    if ($customInputField.length > 0) {
                                        if ($firstInvalidField == null) {
                                            $firstInvalidField = $customInputField;
                                        }
                                        var id = $customInputField.attr('id');
                                        var name = $customInputField.attr('name');
                                        if (name) {
                                            name = name.replace(/\[]/g, '');
                                        }
                                        var label = $customInputField.data('wpfs-custom-input-label');
                                        fieldErrorMessage = vsprintf(wpfsL10n.validation_errors.mandatory_field_is_empty, [label]);
                                        showFieldError($form, name, id, fieldErrorMessage, false);
                                    }
                                }
                                hasErrors = true;
                            }
                        }

                        // tnagy validate terms of use if necessary
                        var showTermsOfUse = $form.data('wpfs-show-terms-of-use');
                        if (1 == showTermsOfUse) {
                            var termsOfUseAccepted = $form.find('input[name=wpfs-terms-of-use-accepted]').prop('checked');
                            if (termsOfUseAccepted == false) {
                                if ($firstInvalidField == null) {
                                    $firstInvalidField = $('input[name=wpfs-terms-of-use-accepted]', $form);
                                }
                                fieldErrorMessage = $form.data('wpfs-terms-of-use-not-checked-error-message');
                                showFieldError($form, 'wpfs-terms-of-use-accepted', null, fieldErrorMessage, false);
                                hasErrors = true;
                            }
                        }

                        // tnagy prevent submit on validation errors
                        if (hasErrors) {
                            if ($firstInvalidField && $firstInvalidField.length > 0) {
                                scrollToElement($firstInvalidField, false);
                            }
                            enableFormButtons($form);
                            hideLoadingAnimation($form);
                            return false;
                        }

                        // tnagy continue with valid data
                        var formType = $form.data('wpfs-form-type');
                        var amountData;
                        if (FORM_TYPE_POPUP_PAYMENT === formType || FORM_TYPE_POPUP_SAVE_CARD === formType || FORM_TYPE_POPUP_DONATION === formType) {
                            amountData = findPaymentAmountData($form);
                        } else if (FORM_TYPE_POPUP_SUBSCRIPTION === formType) {
                            amountData = findPlanAmountData($form);
                        }

                        if (debugLog) {
                            logInfo('initCheckoutForms', 'amountData=' + JSON.stringify(amountData));
                        }

                        if (false == amountData.valid) {
                            if (amountData.customAmount) {
                                showFieldError($form, 'wpfs-custom-amount-unique', $('input[name="wpfs-custom-amount-unique"]', $form).attr('id'), wpfsL10n.validation_errors.custom_payment_amount_value_is_invalid, true);
                            } else {
                                showErrorGlobalMessage($form, wpfsL10n.validation_errors.invalid_payment_amount_title, wpfsL10n.validation_errors.invalid_payment_amount);
                            }
                            enableFormButtons($form);
                            hideLoadingAnimation($form);
                            return false;
                        }

                        addCurrentURL($form);

                        $.ajax({
                            type: "POST",
                            url: wpfsAjaxURL,
                            data: $form.serialize(),
                            cache: false,
                            dataType: "json",
                            success: function (data) {

                                if (debugLog) {
                                    logInfo('initCheckoutForms', 'SUCCESS response=' + JSON.stringify(data));
                                }

                                if (data.success) {
                                    if (data.checkoutSessionId) {
                                        stripe.redirectToCheckout({
                                            sessionId: data.checkoutSessionId
                                        }).then(function (result) {
                                            // If `redirectToCheckout` fails due to a browser or network
                                            // error, display the localized error message to your customer
                                            // using `result.error.message`.
                                            if (debugLog) {
                                                logInfo('initCheckoutForms', 'result=' + JSON.stringify(result));
                                            }

                                        });
                                    } else {
                                        // tnagy no checkoutSessionId supplied
                                        logWarn('initCheckoutForms', 'No CheckoutSession supplied!');
                                    }
                                } else {
                                    if (data && (data.messageTitle || data.message)) {
                                        showErrorGlobalMessage($form, data.messageTitle, data.message);
                                    }
                                    processValidationErrors($form, data);
                                }
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                logError('submitPaymentData', jqXHR, textStatus, errorThrown);
                                showErrorGlobalMessage($form, wpfsL10n.stripe_errors.internal_error_title, wpfsL10n.stripe_errors.internal_error);
                            },
                            complete: function () {
                                enableFormButtons($form);
                                hideLoadingAnimation($form);
                            }
                        });

                        return false;

                    });
                });
            };

            WPFS.initReCaptcha = function () {
                window.addEventListener('load', function () {
                    var reCAPTCHAWidgetId;
                    var formHash;
                    var formCAPTCHAs = document.getElementsByClassName('wpfs-form-captcha');
                    //noinspection JSUnresolvedVariable
                    if (window.grecaptcha !== 'undefined' && formCAPTCHAs !== null && formCAPTCHAs.length > 0) {
                        //noinspection JSUnresolvedVariable
                        googleReCAPTCHA = window.grecaptcha;
                        Array.prototype.forEach.call(formCAPTCHAs, function (element) {
                            formHash = element.getAttribute('data-wpfs-form-hash');
                            //noinspection JSUnresolvedVariable
                            reCAPTCHAWidgetId = googleReCAPTCHA.render(element, {
                                'sitekey': wpfsGoogleReCAPTCHASiteKey
                            });
                            reCAPTCHAWidgetIds[formHash] = reCAPTCHAWidgetId;
                        });
                    }
                }, true);
            };

            WPFS.initPlanRadioButton = function () {
                $('input.wpfs-subscription-plan-radio').change(function (e) {
                    var $form = getParentForm(this);
                    handlePlanChange($form);
                });
            };

            WPFS.ready = function () {

                if (debugLog) {
                    logInfo('ready', 'CALLED');
                }

                // tnagy trigger plan change to initialize payment summary
                var $planSelectElements = $('select.wpfs-subscription-plan-select');
                $planSelectElements.each(function () {
                    var $form = getParentForm(this);
                    if ($('option:selected', $(this)).length) {
                        $(this).val($('option:selected', $(this)).val());
                    } else {
                        $(this).val($('option:first', $(this)).val());
                    }
                    handlePlanChange($form);
                });
                var $planRadioElements = $('input.wpfs-subscription-plan-radio');
                $planRadioElements.each(function () {
                    var $form = getParentForm(this);
                    if ($('input:radio[name="wpfs-plan"]:not(:disabled):checked', $form).length === 0) {
                        $('input:radio[name="wpfs-plan"]:not(:disabled):first', $form).attr('checked', true);
                    }
                    handlePlanChange($form);
                });
                var $planHiddenElements = $('input.wpfs-subscription-plan-hidden');
                $planHiddenElements.each(function () {
                    var $form = getParentForm(this);
                    handlePlanChange($form);
                });

            };

            // tnagy initialize components
            WPFS.initInputGroup();
            WPFS.initSelectmenu();
            WPFS.initAddressSwitcher();
            WPFS.initTooltip();
            WPFS.initStepper();
            WPFS.initDatepicker();
            WPFS.initCombobox();
            WPFS.initCustomAmount();
            WPFS.initStripeJSCard();
            WPFS.initCoupon();
            WPFS.initCheckoutForms();
            WPFS.initReCaptcha();
            WPFS.initPlanRadioButton();

            WPFS.ready();

        }
    );

})(jQuery);
