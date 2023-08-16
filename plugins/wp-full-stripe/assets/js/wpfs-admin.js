/*
 Plugin Name: WP Full Stripe
 Plugin URI: https://paymentsplugin.com
 Description: Complete Stripe payments integration for Wordpress
 Author: Mammothology
 Version: 5.5.2
 Author URI: https://paymentsplugin.com
 */

jQuery.noConflict();
(function ($) {
    $(function () {

        const PAYMENT_TYPE_SPECIFIED_AMOUNT = 'specified_amount';
        const PAYMENT_TYPE_CARD_CAPTURE = 'card_capture';
        const CURRENCY_USD = "usd";

        function logException(source, response) {
            if (window.console && response) {
                if (response.ex_msg) {
                    console.log('ERROR: source=' + source + ', message=' + response.ex_msg);
                }
                if (response.ex_stack) {
                    console.log('ERROR: source=' + source + ', stack=' + response.ex_stack);
                }
            }
        }

        function createAdminCurrencyFormatter() {
            var decimalSeparator = wpfsAdminOptions.currencyDecimalSeparatorSymbol;
            var showCurrencySymbolInsteadOfCode = wpfsAdminOptions.currencyShowSymbolInsteadOfCode;
            var showCurrencySignAtFirstPosition = wpfsAdminOptions.currencyShowIdentifierOnLeft;
            var putWhitespaceBetweenCurrencyAndAmount = wpfsAdminOptions.currencyPutSpaceBetweenCurrencyAndAmount;
            logInfo('createAdminCurrencyFormatter', 'decimalSeparator=' + JSON.stringify(decimalSeparator));
            logInfo('createAdminCurrencyFormatter', 'showCurrencySymbolInsteadOfCode=' + JSON.stringify(showCurrencySymbolInsteadOfCode));
            logInfo('createAdminCurrencyFormatter', 'showCurrencySignAtFirstPosition=' + JSON.stringify(showCurrencySignAtFirstPosition));
            logInfo('createAdminCurrencyFormatter', 'putWhitespaceBetweenCurrencyAndAmount=' + JSON.stringify(putWhitespaceBetweenCurrencyAndAmount));

            return WPFSCurrencyFormatter(
                decimalSeparator,
                showCurrencySymbolInsteadOfCode,
                showCurrencySignAtFirstPosition,
                putWhitespaceBetweenCurrencyAndAmount
            );
        }

        function findTemplateById() {
            var selectedTemplate;
            if (emailReceipts.hasOwnProperty(emailReceipts.currentTemplateId)) {
                selectedTemplate = emailReceipts[emailReceipts.currentTemplateId];
            }
            return selectedTemplate;
        }

        function saveEmailReceiptTemplateValues($) {
            var selectedTemplate = findTemplateById();
            if (selectedTemplate) {
                selectedTemplate.subject = $('#email_receipt_subject').val();
                selectedTemplate.html = $('#email_receipt_html').val();
            }
            return selectedTemplate;
        }

        var regexPattern_AN_DASH_U = /^[a-zA-Z0-9-_]+$/;
        var regexPattern_NUMBER = /^[0-9]+$/;
        var regexPattern_NUMBER_WITH_UP_TO_4_DECIMALS = /^\d+\.?\d{0,4}$/;

        var $loading = $(".showLoading");
        var $update = $("#updateDiv");
        $loading.hide();
        $update.hide();

        $('#receiptEmailTypePlugin').click(function () {
            $('#email_receipt_row').show();
            $('#email_receipt_sender_address_row').show();
            $('#admin_payment_receipt_row').show();
        });
        $('#receiptEmailTypeStripe').click(function () {
            $('#email_receipt_row').hide();
            $('#email_receipt_sender_address_row').hide();
            $('#admin_payment_receipt_row').hide();
        });

        $('#email_receipt_template').change(function () {

            // tnagy save current values
            var selectedTemplate = saveEmailReceiptTemplateValues($);

            emailReceipts.currentTemplateId = $('#email_receipt_template').val();

            // tnagy update subject and html fields
            selectedTemplate = findTemplateById();
            if (selectedTemplate) {
                $('#email_receipt_subject').val(selectedTemplate.subject);
                $('#email_receipt_html').val(selectedTemplate.html);
            }
        });

        // tnagy select first template on page load
        $('#email_receipt_template option[selected="selected"]').each(function () {
            $(this).removeAttr('selected');
        });
        $("#email_receipt_template option:first").attr('selected', 'selected').change();

        function resetForm($form) {
            $form.find('input:text, input:password, input:file, select, textarea').val('');
            $form.find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
        }

        function activateFieldTab(field) {
            if (field) {
                var aField = field;
                if (field.length > 1) {
                    aField = $(field[0]);
                }
                if (aField.parents('.wpfs-tab-content')) {
                    var tab = aField.parents('.wpfs-tab-content');
                    var tabAnchor = 'a[href="#' + tab.attr('id') + '"]';
                    $(tabAnchor).click();
                    aField.focus();
                }
            }
        }

        function validField(field, fieldName, fieldToFocus) {
            var valid = true;
            if (field.is("select")) {
                if (field.children(":selected").val() === "") {
                    valid = false;
                }
            } else {
                if (field.val() === "") {
                    valid = false;
                }
            }
            if (!valid) {
                showError(fieldName + " must contain a value");
                if (fieldToFocus) {
                    activateFieldTab(fieldToFocus);
                } else {
                    activateFieldTab(field);
                }
            }
            return valid;
        }

        function validFieldByRegex(field, regexPattern, errorMessage) {
            var valid = true;
            if (!regexPattern.test(field.val())) {
                valid = false;
                showError(errorMessage);
                activateFieldTab(field);
            }
            return valid;
        }

        function validFieldByLength(field, len, errorMessage) {
            var valid = true;
            if (field.val().length > len) {
                valid = false;
                showError(errorMessage);
                activateFieldTab(field);
            }
            return valid;
        }

        function validFieldWithMsg(field, msg) {
            var valid = true;
            if (field.val() === "") {
                valid = false;
                showError(msg);
                activateFieldTab(field);
            }
            return valid;
        }

        function validVATFields() {
            var valid = true;
            if ('fixed_vat' == $('#formVATRateTypeSelect').children(':selected').val()) {
                valid = validFieldByRegex($('#form_vat_percent'), regexPattern_NUMBER_WITH_UP_TO_4_DECIMALS, 'VAT Percent should contain only numbers up to 4 decimal places.');
            }
            return valid;
        }

        function showError(message) {
            showMessage('error', 'updated', message);
        }

        function showUpdate(message) {
            showMessage('updated', 'error', message);
        }

        function showMessage(addClass, removeClass, message) {
            $update.removeClass(removeClass);
            $update.addClass(addClass);
            $update.html("<p>" + message + "</p>");
            $update.show();
            document.body.scrollTop = document.documentElement.scrollTop = 0;
        }

        function clearUpdateAndError() {
            $update.html("");
            $update.removeClass('error');
            $update.removeClass('update');
            $update.hide();
            $(".error").remove();
        }

        // for uploading images using WordPress media library
        var custom_uploader;

        function uploadImage(inputID) {
            // If the uploader object has already been created, reopen the dialog
            if (custom_uploader) {
                custom_uploader.open();
                return;
            }

            // Extend the wp.media object
            custom_uploader = wp.media.frames.file_frame = wp.media({
                title: 'Choose Image',
                button: {
                    text: 'Choose Image'
                },
                multiple: false
            });

            // When a file is selected, grab the URL and set it as the text field's value
            custom_uploader.on('select', function () {
                attachment = custom_uploader.state().get('selection').first().toJSON();
                $(inputID).val(attachment.url);
            });

            // Open the uploader dialog
            custom_uploader.open();
        }

        // called on form submit when we know includeCustomFields = 1
        function validCustomFields(form) {
            var valid = true;
            var count = $('#customInputNumberSelect').val();
            var customValues = '';
            for (var i = 1; i <= count; i++) {
                // first validate the field
                var field = '#form_custom_input_label_' + i;
                var fieldName = 'Custom Input Label ' + i;
                valid = validField($(field), fieldName);
                valid = valid && validFieldByLength($(field), 40, 'You can enter up to 40 characters for ' + fieldName);
                if (!valid) return false;
                // save the value, stripping all single & double quotes
                customValues += $(field).val().replace(/['"]+/g, '');
                if (i < count)
                    customValues += '{{';
            }

            // now append to the form
            form.find('input[name=customInputs]').remove();
            form.append('<input type="hidden" name="customInputs" value="' + customValues + '"/>');

            return valid;
        }

        function validRedirect() {
            var validRedirect;
            if ($('#do_redirect_yes').prop('checked')) {
                if ($('#form_redirect_to_page_or_post').prop('checked')) {
                    validRedirect = validFieldWithMsg($('#form_redirect_page_or_post_id'), 'Select page or post to redirect to');
                    $('.page_or_post-combobox-input').focus();
                } else if ($('#form_redirect_to_url').prop('checked')) {
                    validRedirect = validFieldWithMsg($('#form_redirect_url'), 'Enter an URL to redirect to');
                } else {
                    validRedirect = false;
                    showError('You must check at least one redirect type');
                    activateFieldTab($('#form_redirect_to_page_or_post'));
                }
            } else {
                validRedirect = true;
            }
            return validRedirect;
        }

        function do_admin_ajax_post(form, successMessage, doRedirect) {
            do_ajax_post(admin_ajaxurl, form, successMessage, doRedirect);
        }

        /**
         * Post via AJAX
         *
         * @param ajaxUrl
         * @param form
         * @param successMessage
         * @param doRedirect
         */
        function do_ajax_post(ajaxUrl, form, successMessage, doRedirect) {
            $loading.show();
            // Disable the submit button
            form.find('button').prop('disabled', true);

            $.ajax({
                type: "POST",
                url: ajaxUrl,
                data: form.serialize(),
                cache: false,
                dataType: "json",
                success: function (data) {
                    if (data.success) {
                        showUpdate(successMessage);
                        resetForm(form);

                        if (doRedirect) {
                            setTimeout(function () {
                                window.location = data.redirectURL;
                            }, 1000);
                        }
                    } else {
                        // show the errors on the form
                        if (data.msg) {
                            showError(data.msg);
                        }
                        logException('do_ajax_post', data);
                        if (data.validation_result) {
                            var elementWithError = null;
                            for (var f in data.validation_result) {
                                if (data.validation_result.hasOwnProperty(f)) {
                                    $('input[name=' + f + ']').after('<div class="error"><p>' + data.validation_result[f] + '</p></div>');
                                    elementWithError = f;
                                }
                            }
                            if (elementWithError) {
                                var $el = $('input[name=' + elementWithError + ']');
                                if ($el && $el.offset() && $el.offset().top);
                                $('html, body').animate({
                                    scrollTop: $el.offset().top
                                }, 2000);
                            }
                        }
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    showError('An error occurred. See Javascript console for details');
                    logError('do_ajax_post', jqXHR, textStatus, errorThrown);
                },
                complete: function () {
                    $loading.hide();
                    form.find('button').prop('disabled', false);
                    document.body.scrollTop = document.documentElement.scrollTop = 0;
                }
            });
        }

        function enable_combobox() {
            $('.page_or_post-combobox-input').prop('disabled', false);
            $('.page_or_post-combobox-toggle').button('option', 'disabled', false);
            $('.page_or_post-combobox-input').focus();
        }

        function disable_combobox() {
            $('.page_or_post-combobox-input').prop('disabled', true);
            $('.page_or_post-combobox-toggle').button('option', 'disabled', true);
        }

        function init_page_or_post_redirect() {
            $('#form_redirect_to_url').prop('checked', false);
            $('#form_redirect_to_page_or_post').prop('checked', true);
            $('#form_redirect_to_page_or_post').prop('disabled', false);
            $('#form_redirect_to_url').prop('disabled', false);
            $('#showDetailedSuccessPage').prop('disabled', false);
            $('#form_redirect_page_or_post_id').prop('disabled', false);
            $('#form_redirect_url').prop('disabled', false);
            enable_combobox();
        }

        function updatePaymentAmountsAndDescriptions() {
            var amounts = $('#payment_amount_list').sortable('toArray', {attribute: 'data-payment-amount-value'});
            var descriptions = $('#payment_amount_list').sortable('toArray', {attribute: 'data-payment-amount-description'});
            $('input[name=payment_amount_values]').val(amounts);
            $('input[name=payment_amount_descriptions]').val(descriptions);
        }

        function updateDonationAmounts() {
            var amounts = $('#donation_amount_list').sortable('toArray', {attribute: 'data-donation-amount-value'});
            $('input[name=donation_amount_values]').val(amounts);
        }

        function tabFocusRestrictor(lastItem, firstItem) {
            $(lastItem).blur(function () {
                $(firstItem).focus();
            });
        }

        $('.plan_checkbox_list').sortable({
            placeholder: "ui-sortable-placeholder",
            stop: function (event, ui) {
                var plan_id_order = $(this).sortable('toArray', {attribute: 'data-plan-id'});
                $('input[name=plan_order]').val(encodeURIComponent(JSON.stringify(plan_id_order)));
            }
        });

        $('#create-subscription-plan').submit(function (e) {
            clearUpdateAndError();

            var valid = validField($('#sub_id'), 'ID');
            valid = valid && validField($('#sub_name'), 'Name');
            valid = valid && validField($('#currency'), 'Currency', $('.currency-combobox-input'));
            valid = valid && validField($('#sub_amount'), 'Amount');
            valid = valid && validField($('#sub_setup_fee'), 'Setup fee');
            valid = valid && validFieldByRegex($('#sub_setup_fee'), regexPattern_NUMBER, 'Setup Fee should only contain numbers.');
            valid = valid && validField($('#sub_cancellation_count'), 'Payment Cancellation Count');
            valid = valid && validField($('#sub_trial'), 'Trial Days');

            if (valid) {
                var $form = $(this);
                do_admin_ajax_post($form, "Plan created.", true);
            }

            return false;

        });

        $('#edit-subscription-plan').submit(function (e) {
            clearUpdateAndError();

            var valid = validField($('#form_plan_display_name'), 'Display name');
            valid = valid && validField($('#form_plan_setup_fee'), 'Setup fee');
            valid = valid && validFieldByRegex($('#form_plan_setup_fee'), regexPattern_NUMBER, 'Setup Fee should only contain numbers.');
            if (valid) {
                var $form = $(this);
                do_admin_ajax_post($form, "Subscription plan updated.", true);
            }

            return false;
        });

        $('#create-subscription-form').submit(function (e) {
            clearUpdateAndError();

            var valid = validField($('#form_name'), 'Form Name');
            valid = valid && validFieldByRegex($('#form_name'), regexPattern_AN_DASH_U, 'Form Name should contain only alphanumerical characters, dashes, and underscores.');
            var checkedPlans = $('.plan_checkbox:checkbox:checked').map(function () {
                return decodeURIComponent(this.value);
            }).get();
            var plans = encodeURIComponent(JSON.stringify(checkedPlans));
            if (valid && checkedPlans.length === 0) {
                showError("You must check at least one subscription plan");
                activateFieldTab($('.plan_checkbox'));
                valid = false;
            }
            valid = valid && validVATFields();
            // tnagy WPFS-740: remove form title
            // valid = valid && validField($('#form_title'), 'Form Title');
            var showTermsOfUse = $('input[name=show_terms_of_use]:checked', '#create-subscription-form').val();
            if (showTermsOfUse == 1) {
                valid = valid && validField($('#terms_of_use_label'), 'Checkbox Label');
                valid = valid && validField($('#terms_of_use_not_checked_error_message'), 'Not Checked Error Message');
            }
            var includeCustom = $('input[name=form_include_custom_input]:checked', '#create-subscription-form').val();
            if (includeCustom == 1) {
                valid = valid && validCustomFields($(this));
            }

            valid = valid && validRedirect();

            if (valid) {
                var $form = $(this);
                // create a plans field for all the checked plans
                $("<input>").attr("type", "hidden").attr("name", "selected_plans").attr("value", plans).appendTo($form);
                do_admin_ajax_post($form, "Subscription form created.", true);
            }

            return false;
        });

        $('#edit-subscription-form').submit(function (e) {
            clearUpdateAndError();

            var valid = validField($('#form_name'), 'Form Name');
            valid = valid && validFieldByRegex($('#form_name'), regexPattern_AN_DASH_U, 'Form Name should contain only alphanumerical characters, dashes, and underscores.');
            var checkedPlans = $('.plan_checkbox:checkbox:checked').map(function () {
                return decodeURIComponent(this.value);
            }).get();
            var plans = encodeURIComponent(JSON.stringify(checkedPlans));
            if (valid && checkedPlans.length === 0) {
                showError("You must check at least one subscription plan");
                activateFieldTab($('.plan_checkbox'));
                valid = false;
            }
            valid = valid && validVATFields();
            // tnagy WPFS-740: remove form title
            // valid = valid && validField($('#form_title'), 'Form Title');
            var showTermsOfUse = $('input[name=show_terms_of_use]:checked', '#edit-subscription-form').val();
            if (showTermsOfUse == 1) {
                valid = valid && validField($('#terms_of_use_label'), 'Checkbox Label');
                valid = valid && validField($('#terms_of_use_not_checked_error_message'), 'Not Checked Error Message');
            }
            var includeCustom = $('input[name=form_include_custom_input]:checked', '#edit-subscription-form').val();
            if (includeCustom == 1) {
                valid = valid && validCustomFields($(this));
            }
            valid = valid && validRedirect();

            if (valid) {
                var $form = $(this);
                // create a plans field for all the checked plans
                $("<input>").attr("type", "hidden").attr("name", "selected_plans").attr("value", plans).appendTo($form);
                do_admin_ajax_post($form, "Subscription form updated.", true);
            }

            return false;
        });

        $('#set_specific_amount, #set_custom_amount').click(function () {
            $("#currency").currency_combobox().change();
            $('#payment_amount_list_row').hide();
            $('#payment_currency_row').show();
            $('#payment_amount_row').show();
            $('#include_amount_on_button_row').show();
            $('#use_alipay_row').show();
            $('#stripe_description_row').show();
        });
        $('#set_amount_list').click(function () {
            $("#currency").currency_combobox().change();
            $('#payment_amount_row').hide();
            $('#payment_currency_row').show();
            $('#payment_amount_list_row').show();
            $('#payment_amount_value').focus();
            $('#include_amount_on_button_row').show();
            $('#use_alipay_row').show();
            $('#stripe_description_row').show();
        });

        tabFocusRestrictor('#payment_amount_description', '#add_payment_amount_button');

        $('#add_payment_amount_button').click(function (event) {
            event.preventDefault();
            $('.tooltip_error').remove();
            $('.field_error').removeClass('field_error');

            var currencyFormatter = createAdminCurrencyFormatter();

            var currencySymbol = $('#currency').children(':selected').data('currency-symbol');
            var currencyCode = $('#currency').children(':selected').val();
            var zeroDecimalSupport = $('#currency').children(':selected').data('zero-decimal-support');
            var value = $('#payment_amount_value').val();
            var amount = formatCurrencyAmount( value, zeroDecimalSupport );
            var description = $('#payment_amount_description').val();
            var encodedDescription = encodeURIComponent(description);
            var validation_result = [];
            if (!value) {
                validation_result['payment_amount_value'] = "Required";
            } else if (isNaN(value)) {
                validation_result['payment_amount_value'] = "Numbers only";
            } else if (value.length > 8) {
                validation_result['payment_amount_value'] = "Too long";
            }
            if (!description) {
                validation_result['payment_amount_description'] = "Required";
            } else if (value.length > 128) {
                validation_result['payment_amount_description'] = "Too long";
            }

            if (!validation_result.hasOwnProperty('payment_amount_value') && !validation_result.hasOwnProperty('payment_amount_description')) {
                $('#payment_amount_list')
                    .append(
                        $('<li>')
                            .addClass('ui-state-default')
                            .attr('title', 'You can reorder this list by using drag\'n\'drop.')
                            .attr('data-toggle', 'tooltip')
                            .attr('data-payment-amount-value', value)
                            .attr('data-payment-amount-description', encodedDescription)
                            .append(
                                $('<a>')
                                    .addClass('dd_delete')
                                    .attr('href', '#')
                                    .html('Delete')
                                    .click(function (event) {
                                        event.preventDefault();
                                        $(this).closest('li').remove();
                                    }))
                            .append($('<span>').addClass('amount').html(currencyFormatter.format( amount, currencyCode.toUpperCase(), currencySymbol, zeroDecimalSupport )))
                            .append($('<span>').addClass('desc').html(description))
                    );

                $('#payment_amount_value').val('');
                $('#payment_amount_description').val('');
            } else {
                if (validation_result.hasOwnProperty('payment_amount_description')) {
                    $('#payment_amount_description').addClass('field_error').prop('data-toggle', 'tooltip').prop('title', validation_result.payment_amount_description).focus();
                }
                if (validation_result.hasOwnProperty('payment_amount_value')) {
                    $('#payment_amount_value').addClass('field_error').prop('data-toggle', 'tooltip').prop('title', validation_result.payment_amount_value).focus();
                }
            }
        });

        $('#add_donation_amount_button').click(function (event) {
            event.preventDefault();
            $('.tooltip_error').remove();
            $('.field_error').removeClass('field_error');

            var currencyFormatter = createAdminCurrencyFormatter();

            var currencySymbol = $('#currency').children(':selected').data('currency-symbol');
            var currencyCode = $('#currency').children(':selected').val();
            var zeroDecimalSupport = $('#currency').children(':selected').data('zero-decimal-support');
            var value = $('#donation_amount_value').val();
            var amount = formatCurrencyAmount( value, zeroDecimalSupport );
            var validation_result = [];
            if (!value) {
                validation_result['donation_amount_value'] = "Required";
            } else if (isNaN(value)) {
                validation_result['donation_amount_value'] = "Numbers only";
            } else if (value.length > 8) {
                validation_result['donation_amount_value'] = "Too long";
            }

            if (!validation_result.hasOwnProperty('donation_amount_value')) {
                $('#donation_amount_list')
                    .append(
                        $('<li>')
                            .addClass('ui-state-default')
                            .attr('title', 'You can reorder this list by using drag\'n\'drop.')
                            .attr('data-toggle', 'tooltip')
                            .attr('data-donation-amount-value', value)
                            .append($('<span>').addClass('amount').html(currencyFormatter.format( amount, currencyCode.toUpperCase(), currencySymbol, zeroDecimalSupport )))
                            .append(
                                $('<a>')
                                    .addClass('dd_delete')
                                    .attr('href', '#')
                                    .html('Delete')
                                    .click(function (event) {
                                        event.preventDefault();
                                        $(this).closest('li').remove();
                                    }))
                    );

                $('#donation_amount_value').val('');
            } else {
                if (validation_result.hasOwnProperty('donation_amount_value')) {
                    $('#donation_amount_value').addClass('field_error').prop('data-toggle', 'tooltip').prop('title', validation_result.donation_amount_value).focus();
                }
            }
        });

        $('#payment_amount_list li a.dd_delete').click(function (event) {
            event.preventDefault();
            $(this).closest('li').remove();
        });
        $('#payment_amount_list').sortable({
            placeholder: "ui-sortable-placeholder",
            stop: function (event, ui) {
                var amounts = $(this).sortable('toArray', {attribute: 'data-payment-amount-value'});
                var descriptions = $(this).sortable('toArray', {attribute: 'data-payment-amount-description'});
                $('input[name=payment_amount_values]').val(amounts);
                $('input[name=payment_amount_descriptions]').val(descriptions);
            }
        });

        $('#donation_amount_list').sortable({
            placeholder: "ui-sortable-placeholder",
            stop: function (event, ui) {
                var amounts = $(this).sortable('toArray', {attribute: 'data-donation-amount-value'});
                $('input[name=donation_amount_values]').val(amounts);
            }
        });
        $('#donation_amount_list li a.dd_delete').click(function (event) {
            event.preventDefault();
            $(this).closest('li').remove();
        });


        $('#create-payment-form').submit(function (e) {
            clearUpdateAndError();

            updatePaymentAmountsAndDescriptions();

            var valid = validField($('#form_name'), 'Form Name');
            valid = valid && validFieldByRegex($('#form_name'), regexPattern_AN_DASH_U, 'Form Name should contain only alphanumerical characters, dashes, and underscores.');
            var customAmount = $('input[name=form_custom]:checked', '#create-payment-form').val();
            if (customAmount != PAYMENT_TYPE_CARD_CAPTURE) {
                valid = valid && validField($('#currency'), 'Currency', $('.currency-combobox-input'));
            }
            if (customAmount == PAYMENT_TYPE_SPECIFIED_AMOUNT) {
                valid = valid && validField($('#form_amount'), 'Amount');
                valid = valid && validFieldByRegex($('#form_amount'), regexPattern_NUMBER, 'Form Amount should only contain numbers.');
            }
            valid = valid && validField($('#prod_desc'), 'Product Name');
            var showTermsOfUse = $('input[name=show_terms_of_use]:checked', '#create-payment-form').val();
            if (showTermsOfUse == 1) {
                valid = valid && validField($('#terms_of_use_label'), 'Checkbox Label');
                valid = valid && validField($('#terms_of_use_not_checked_error_message'), 'Not Checked Error Message');
            }
            var includeCustom = $('input[name=form_include_custom_input]:checked', '#create-payment-form').val();
            if (includeCustom == 1) {
                valid = valid && validCustomFields($(this));
            }
            valid = valid && validRedirect();

            if (valid) {
                var $form = $(this);
                var successMessage = "Payment form created.";
                if (customAmount == PAYMENT_TYPE_CARD_CAPTURE) {
                    successMessage = "Inline saved card form created.";
                }
                do_admin_ajax_post($form, successMessage, true);
            }

            return false;
        });

        $('#edit-payment-form').submit(function (e) {
            clearUpdateAndError();

            updatePaymentAmountsAndDescriptions();

            var valid = validField($('#form_name'), 'Form Name');
            valid = valid && validFieldByRegex($('#form_name'), regexPattern_AN_DASH_U, 'Form Name should contain only alphanumerical characters, dashes, and underscores.');
            var customAmount = $('input[name=form_custom]:checked', '#edit-payment-form').val();
            if (customAmount != PAYMENT_TYPE_CARD_CAPTURE) {
                valid = valid && validField($('#currency'), 'Currency', $('.currency-combobox-input'));
            }
            if (customAmount == PAYMENT_TYPE_SPECIFIED_AMOUNT) {
                valid = valid && validField($('#form_amount'), 'Amount');
                valid = valid && validFieldByRegex($('#form_amount'), regexPattern_NUMBER, 'Form Amount should only contain numbers.');
            }
            valid = valid && validField($('#prod_desc'), 'Product Name');
            var showTermsOfUse = $('input[name=show_terms_of_use]:checked', '#edit-payment-form').val();
            if (showTermsOfUse == 1) {
                valid = valid && validField($('#terms_of_use_label'), 'Checkbox Label');
                valid = valid && validField($('#terms_of_use_not_checked_error_message'), 'Not Checked Error Message');
            }
            var includeCustom = $('input[name=form_include_custom_input]:checked', '#edit-payment-form').val();
            if (includeCustom == 1) {
                valid = valid && validCustomFields($(this));
            }
            valid = valid && validRedirect();

            if (valid) {
                var $form = $(this);
                var successMessage = "Payment form updated.";
                if (customAmount == PAYMENT_TYPE_CARD_CAPTURE) {
                    successMessage = "Inline saved card form updated.";
                }
                do_admin_ajax_post($form, successMessage, true);
            }

            return false;
        });

        $('#create-checkout-form').submit(function (e) {
            clearUpdateAndError();

            updatePaymentAmountsAndDescriptions();

            var valid = validField($('#form_name'), 'Form Name');
            valid = valid && validFieldByRegex($('#form_name'), regexPattern_AN_DASH_U, 'Form Name should contain only alphanumerical characters, dashes, and underscores.');
            var customAmount = $('input[name=form_custom]:checked', '#create-checkout-form').val();
            if (customAmount != PAYMENT_TYPE_CARD_CAPTURE) {
                valid = valid && validField($('#currency'), 'Currency', $('.currency-combobox-input'));
            }
            if (customAmount == PAYMENT_TYPE_SPECIFIED_AMOUNT) {
                valid = valid && validField($('#form_amount'), 'Amount');
                valid = valid && validFieldByRegex($('#form_amount'), regexPattern_NUMBER, 'Form Amount should only contain numbers.');
            }
            valid = valid && validField($('#prod_desc'), 'Product Name');
            var showTermsOfUse = $('input[name=show_terms_of_use]:checked', '#create-checkout-form').val();
            if (showTermsOfUse == 1) {
                valid = valid && validField($('#terms_of_use_label'), 'Checkbox Label');
                valid = valid && validField($('#terms_of_use_not_checked_error_message'), 'Not Checked Error Message');
            }
            var includeCustom = $('input[name=form_include_custom_input]:checked', '#create-checkout-form').val();
            if (includeCustom == 1) {
                valid = valid && validCustomFields($(this));
            }
            valid = valid && validRedirect();

            if (valid) {
                var $form = $(this);
                var successMessage = "Checkout payment form created.";
                if (customAmount == PAYMENT_TYPE_CARD_CAPTURE) {
                    successMessage = "Checkout save card form created.";
                }
                do_admin_ajax_post($form, successMessage, true);
            }

            return false;
        });

        $('#edit-checkout-form').submit(function (e) {
            clearUpdateAndError();

            updatePaymentAmountsAndDescriptions();

            var valid = validField($('#form_name'), 'Form Name');
            valid = valid && validFieldByRegex($('#form_name'), regexPattern_AN_DASH_U, 'Form Name should contain only alphanumerical characters, dashes, and underscores.');
            var customAmount = $('input[name=form_custom]:checked', '#edit-checkout-form').val();
            if (customAmount != PAYMENT_TYPE_CARD_CAPTURE) {
                valid = valid && validField($('#currency'), 'Currency', $('.currency-combobox-input'));
            }
            if (customAmount == PAYMENT_TYPE_SPECIFIED_AMOUNT) {
                valid = valid && validField($('#form_amount'), 'Amount');
                valid = valid && validFieldByRegex($('#form_amount'), regexPattern_NUMBER, 'Form Amount should only contain numbers.');
            }
            valid = valid && validField($('#prod_desc'), 'Product Name');
            var showTermsOfUse = $('input[name=show_terms_of_use]:checked', '#edit-checkout-form').val();
            if (showTermsOfUse == 1) {
                valid = valid && validField($('#terms_of_use_label'), 'Checkbox Label');
                valid = valid && validField($('#terms_of_use_not_checked_error_message'), 'Not Checked Error Message');
            }
            var includeCustom = $('input[name=form_include_custom_input]:checked', '#edit-checkout-form').val();
            if (includeCustom == 1) {
                valid = valid && validCustomFields($(this));
            }
            valid = valid && validRedirect();

            if (valid) {
                var $form = $(this);
                var successMessage = "Checkout payment form updated.";
                if (customAmount == PAYMENT_TYPE_CARD_CAPTURE) {
                    successMessage = "Checkout save card form updated.";
                }
                do_admin_ajax_post($form, successMessage, true);
            }

            return false;
        });

        $('#create-checkout-subscription-form').submit(function (e) {
            clearUpdateAndError();

            var valid = validField($('#form_name'), 'Form Name');
            valid = valid && validFieldByRegex($('#form_name'), regexPattern_AN_DASH_U, 'Form Name should contain only alphanumerical characters, dashes, and underscores.');
            var checkedPlans = $('.plan_checkbox:checkbox:checked').map(function () {
                return decodeURIComponent(this.value);
            }).get();
            var plans = encodeURIComponent(JSON.stringify(checkedPlans));
            if (valid && checkedPlans.length === 0) {
                showError("You must check at least one subscription plan");
                activateFieldTab($('.plan_checkbox'));
                valid = false;
            }
            // valid = valid && validField($('#form_title'), 'Form Title');
            valid = valid && validField($('#company_name'), 'Form Title');
            valid = valid && validVATFields();
            var showTermsOfUse = $('input[name=show_terms_of_use]:checked', '#create-checkout-subscription-form').val();
            if (showTermsOfUse == 1) {
                valid = valid && validField($('#terms_of_use_label'), 'Checkbox Label');
                valid = valid && validField($('#terms_of_use_not_checked_error_message'), 'Not Checked Error Message');
            }
            var includeCustom = $('input[name=form_include_custom_input]:checked', '#create-checkout-subscription-form').val();
            if (includeCustom == 1) {
                valid = valid && validCustomFields($(this));
            }
            valid = valid && validRedirect();

            if (valid) {
                var $form = $(this);
                // create a plans field for all the checked plans
                $("<input>").attr("type", "hidden").attr("name", "selected_plans").attr("value", plans).appendTo($form);
                do_admin_ajax_post($form, "Checkout subscription form created.", true);
            }

            return false;

        });

        $('#edit-checkout-subscription-form').submit(function (e) {
            clearUpdateAndError();

            var valid = validField($('#form_name'), 'Form Name');
            valid = valid && validFieldByRegex($('#form_name'), regexPattern_AN_DASH_U, 'Form Name should contain only alphanumerical characters, dashes, and underscores.');
            var checkedPlans = $('.plan_checkbox:checkbox:checked').map(function () {
                return decodeURIComponent(this.value);
            }).get();
            var plans = encodeURIComponent(JSON.stringify(checkedPlans));
            if (valid && checkedPlans.length === 0) {
                showError("You must check at least one subscription plan");
                activateFieldTab($('.plan_checkbox'));
                valid = false;
            }
            // valid = valid && validField($('#form_title'), 'Form Title');
            valid = valid && validField($('#company_name'), 'Form Title');
            valid = valid && validVATFields();
            var showTermsOfUse = $('input[name=show_terms_of_use]:checked', '#edit-checkout-subscription-form').val();
            if (showTermsOfUse == 1) {
                valid = valid && validField($('#terms_of_use_label'), 'Checkbox Label');
                valid = valid && validField($('#terms_of_use_not_checked_error_message'), 'Not Checked Error Message');
            }
            var includeCustom = $('input[name=form_include_custom_input]:checked', '#edit-checkout-subscription-form').val();
            if (includeCustom == 1) {
                valid = valid && validCustomFields($(this));
            }
            valid = valid && validRedirect();

            if (valid) {
                var $form = $(this);
                // create a plans field for all the checked plans
                $("<input>").attr("type", "hidden").attr("name", "selected_plans").attr("value", plans).appendTo($form);
                do_admin_ajax_post($form, "Checkout subscription form updated.", true);
            }

            return false;

        });

        $('#create-donation-form').submit(function (e) {
            e.preventDefault();

            clearUpdateAndError();
            updateDonationAmounts();

            var valid = validField($('#form_name'), 'Form Name');
            valid = valid && validFieldByRegex($('#form_name'), regexPattern_AN_DASH_U, 'Form Name should contain only alphanumerical characters, dashes, and underscores.');

            var showTermsOfUse = $('input[name=show_terms_of_use]:checked', '#create-donation-form').val();
            if (showTermsOfUse == 1) {
                valid = valid && validField($('#terms_of_use_label'), 'Checkbox Label');
                valid = valid && validField($('#terms_of_use_not_checked_error_message'), 'Not Checked Error Message');
            }
            var includeCustom = $('input[name=form_include_custom_input]:checked', '#create-donation-form').val();
            if (includeCustom == 1) {
                valid = valid && validCustomFields($(this));
            }
            valid = valid && validRedirect();

            if (valid) {
                var $form = $(this);
                var successMessage = "Donation form created.";
                do_admin_ajax_post($form, successMessage, true);
            }

            return false;
        });

        $('#edit-donation-form').submit(function (e) {
            e.preventDefault();

            clearUpdateAndError();
            updateDonationAmounts();

            var valid = validField($('#form_name'), 'Form Name');
            valid = valid && validFieldByRegex($('#form_name'), regexPattern_AN_DASH_U, 'Form Name should contain only alphanumerical characters, dashes, and underscores.');

            var showTermsOfUse = $('input[name=show_terms_of_use]:checked', '#edit-donation-form').val();
            if (showTermsOfUse == 1) {
                valid = valid && validField($('#terms_of_use_label'), 'Checkbox Label');
                valid = valid && validField($('#terms_of_use_not_checked_error_message'), 'Not Checked Error Message');
            }
            var includeCustom = $('input[name=form_include_custom_input]:checked', '#edit-donation-form').val();
            if (includeCustom == 1) {
                valid = valid && validCustomFields($(this));
            }
            valid = valid && validRedirect();

            if (valid) {
                var $form = $(this);
                var successMessage = "Donation form updated.";
                do_admin_ajax_post($form, successMessage, true);
            }

            return false;
        });

        $('#create-checkout-donation-form').submit(function (e) {
            e.preventDefault();

            clearUpdateAndError();
            updateDonationAmounts();

            var valid = validField($('#form_name'), 'Form Name');
            valid = valid && validFieldByRegex($('#form_name'), regexPattern_AN_DASH_U, 'Form Name should contain only alphanumerical characters, dashes, and underscores.');

            valid = valid && validField($('#prod_desc'), 'Product Name');

            var showTermsOfUse = $('input[name=show_terms_of_use]:checked', '#create-checkout-donation-form').val();
            if (showTermsOfUse == 1) {
                valid = valid && validField($('#terms_of_use_label'), 'Checkbox Label');
                valid = valid && validField($('#terms_of_use_not_checked_error_message'), 'Not Checked Error Message');
            }
            var includeCustom = $('input[name=form_include_custom_input]:checked', '#create-checkout-donation-form').val();
            if (includeCustom == 1) {
                valid = valid && validCustomFields($(this));
            }
            valid = valid && validRedirect();

            if (valid) {
                var $form = $(this);
                var successMessage = "Donation form created.";
                do_admin_ajax_post($form, successMessage, true);
            }

            return false;
        });

        $('#edit-checkout-donation-form').submit(function (e) {
            e.preventDefault();

            clearUpdateAndError();
            updateDonationAmounts();

            var valid = validField($('#form_name'), 'Form Name');
            valid = valid && validFieldByRegex($('#form_name'), regexPattern_AN_DASH_U, 'Form Name should contain only alphanumerical characters, dashes, and underscores.');

            valid = valid && validField($('#prod_desc'), 'Product Name');

            var showTermsOfUse = $('input[name=show_terms_of_use]:checked', '#edit-checkout-donation-form').val();
            if (showTermsOfUse == 1) {
                valid = valid && validField($('#terms_of_use_label'), 'Checkbox Label');
                valid = valid && validField($('#terms_of_use_not_checked_error_message'), 'Not Checked Error Message');
            }
            var includeCustom = $('input[name=form_include_custom_input]:checked', '#edit-checkout-donation-form').val();
            if (includeCustom == 1) {
                valid = valid && validCustomFields($(this));
            }
            valid = valid && validRedirect();

            if (valid) {
                var $form = $(this);
                var successMessage = "Donation form updated.";
                do_admin_ajax_post($form, successMessage, true);
            }

            return false;
        });

        //upload checkout form images
        $('#upload_image_button').click(function (e) {
            e.preventDefault();
            uploadImage('#form_checkout_image');
        });

        $('#settings-stripe-form').submit(function (e) {
            clearUpdateAndError();
            var $form = $(this);
            do_admin_ajax_post($form, "Settings updated.", true);
            return false;
        });
        $('#settings-appearance-form').submit(function (e) {
            clearUpdateAndError();
            var $form = $(this);
            do_admin_ajax_post($form, "Settings updated.", true);
            return false;
        });
        $('#settings-users-form').submit(function (e) {
            clearUpdateAndError();
            var $form = $(this);
            do_admin_ajax_post($form, "Settings updated.", true);
            return false;
        });
        $('#settings-email-receipts-form').submit(function (e) {
            clearUpdateAndError();

            // tnagy save current email receipt template values before post
            saveEmailReceiptTemplateValues($);
            delete emailReceipts.currentTemplateId;
            $('#email_receipts').val(encodeURIComponent(JSON.stringify(emailReceipts)));

            var $form = $(this);
            do_admin_ajax_post($form, "Settings updated.", true);
            return false;
        });

        // tnagy forms shortcode button
        $("[data-shortcode]").tooltip({
            items: "span.shortcode-tooltip",
            position: {
                my: "right top",
                at: "center bottom+15"
            },
            content: function () {
                var shortcode = $(this).data('shortcode');
                var shortcodeInput = $("<input>").attr("type", "text").attr("class", "large-text").attr("size", shortcode.length).attr("readonly", "").attr("value", shortcode);
                shortcodeInput.data("item", $(this).attr("id"));
                shortcodeInput.focus(function (event, handler) {
                    $(this).select();
                });
                shortcodeInput.blur(function (event, handler) {
                    var item = $(this).data("item");
                    $("#" + item).tooltip("close");
                });
                return shortcodeInput;
            },
            open: function (event, ui) {
                $(document).find("div.ui-tooltip input.large-text").focus();
            }
        });
        $("span.shortcode-tooltip").on("tooltipopen", function (event, ui) {
            $(this).data("tooltip-visible", true);
        });
        $("span.shortcode-tooltip").on("tooltipclose", function (event, ui) {
            $(this).data("tooltip-visible", false);
        });
        $("a.shortcode-payment").click(function () {
            var formId = $(this).data("form-id");
            var $tooltip = $("#shortcode-payment-tooltip__" + formId);
            if ($tooltip.data("tooltip-visible")) {
                $tooltip.tooltip("close");
            } else {
                $tooltip.tooltip("open");
            }
        });
        $("a.shortcode-checkout").click(function () {
            var formId = $(this).data("form-id");
            var $tooltip = $("#shortcode-checkout-tooltip__" + formId);
            if ($tooltip.data("tooltip-visible")) {
                $tooltip.tooltip("close");
            } else {
                $tooltip.tooltip("open");
            }
        });
        $("a.shortcode-subscription").click(function () {
            var formId = $(this).data("form-id");
            var $tooltip = $("#shortcode-subscription-tooltip__" + formId);
            if ($tooltip.data("tooltip-visible")) {
                $tooltip.tooltip("close");
            } else {
                $tooltip.tooltip("open");
            }
        });
        $("a.shortcode-checkout-subscription").click(function () {
            var formId = $(this).data("form-id");
            var $tooltip = $("#shortcode-checkout-subscription-tooltip__" + formId);
            if ($tooltip.data("tooltip-visible")) {
                $tooltip.tooltip("close");
            } else {
                $tooltip.tooltip("open");
            }
        });
        $("a.shortcode-donation").click(function () {
            var formId = $(this).data("form-id");
            var $tooltip = $("#shortcode-donation-tooltip__" + formId);
            if ($tooltip.data("tooltip-visible")) {
                $tooltip.tooltip("close");
            } else {
                $tooltip.tooltip("open");
            }
        });
        $("a.shortcode-checkout-donation").click(function () {
            var formId = $(this).data("form-id");
            var $tooltip = $("#shortcode-checkout-donation-tooltip__" + formId);
            if ($tooltip.data("tooltip-visible")) {
                $tooltip.tooltip("close");
            } else {
                $tooltip.tooltip("open");
            }
        });

        //The forms delete button
        $('button.delete').click(function () {
            var id = $(this).attr('data-id');
            var type = $(this).attr('data-type');
            var to_confirm = $(this).attr('data-confirm');
            if (to_confirm == null) {
                to_confirm = 'true';
            }
            var confirm_message = 'Are you sure you want to delete the record?';
            var update_message = 'Record deleted.';
            var action = '';
            if (type === 'paymentForm') {
                action = 'wp_full_stripe_delete_payment_form';
                confirm_message = 'Are you sure you want to delete this inline payment form?';
                update_message = 'Inline payment form deleted.';
            } else if (type === 'inlineCardCaptureForm') {
                action = 'wp_full_stripe_delete_inline_card_capture_form';
                confirm_message = 'Are you sure you want to delete this inline save card form?';
                update_message = 'Inline save card form deleted.';
            } else if (type === 'subscriptionForm') {
                action = 'wp_full_stripe_delete_subscription_form';
                confirm_message = 'Are you sure you want to delete this inline subscription form?';
                update_message = 'Inline subscription form deleted.';
            } else if (type === 'checkoutSubscriptionForm') {
                action = 'wp_full_stripe_delete_checkout_subscription_form';
                confirm_message = 'Are you sure you want to delete this checkout subscription form?';
                update_message = 'Checkout subscription form deleted.';
            } else if (type === 'checkoutForm') {
                action = 'wp_full_stripe_delete_checkout_form';
                confirm_message = 'Are you sure you want to delete this checkout payment form?';
                update_message = 'Checkout payment form deleted.';
            } else if (type === 'popupCardCaptureForm') {
                action = 'wp_full_stripe_delete_popup_card_capture_form';
                confirm_message = 'Are you sure you want to delete this checkout save card form?';
                update_message = 'Checkout save card form deleted.';
            } else if (type === 'inlineDonationForm') {
                action = 'wp_full_stripe_delete_inline_donation_form';
                confirm_message = 'Are you sure you want to delete this inline donation form?';
                update_message = 'Inline donation form deleted.';
            } else if (type === 'checkoutDonationForm') {
                action = 'wp_full_stripe_delete_checkout_donation_form';
                confirm_message = 'Are you sure you want to delete this checkout donation form?';
                update_message = 'Checkout donation form deleted.';
            } else if (type === 'subscriber') {
                action = 'wp_full_stripe_cancel_subscription';
                confirm_message = 'Are you sure you would like to cancel this subscription?';
                update_message = 'Subscription cancelled.'
            } else if (type === 'subscription_record') {
                action = 'wp_full_stripe_delete_subscription_record';
                confirm_message = 'Are you sure you want to delete this subscription record from the Wordpress database?';
                update_message = 'Subscription record deleted.'
            } else if (type === 'payment') {
                action = 'wp_full_stripe_delete_payment';
            } else if (type === 'subscriptionPlan') {
                action = 'wp_full_stripe_delete_subscription_plan';
                confirm_message = 'Are you sure you want to delete this subscription plan?';
                update_message = 'Subscription plan deleted.';
            } else if (type === 'cardCapture') {
                action = 'wp_full_stripe_delete_card_capture';
                confirm_message = 'Are you sure you want to delete this saved card record from the Wordpress database?';
                update_message = 'Saved card deleted.';
            } else if (type === 'donationRecord') {
                action = 'wp_full_stripe_delete_donation';
                confirm_message = 'Are you sure you want to delete this donation record from the Wordpress database?';
                update_message = 'Donation deleted.';
            }

            var row = $(this).parents('tr:first');

            var confirmed = true;
            if (to_confirm === 'true' || to_confirm === 'yes') {
                confirmed = confirm(confirm_message);
            }
            if (confirmed == true) {
                $.ajax({
                    type: "POST",
                    url: admin_ajaxurl,
                    data: {id: id, action: action},
                    cache: false,
                    dataType: "json",
                    success: function (data) {

                        if (data.success) {
                            var remove = true;
                            if (data.remove == false) {
                                remove = false;
                            }
                            if (remove == true) {
                                $(row).remove();
                            }

                            if (data.redirectURL) {
                                setTimeout(function () {
                                    window.location = data.redirectURL;
                                }, 1000);
                            }
                            showUpdate(update_message);
                        } else {
                            logException('button.delete.click', data);
                        }

                    }
                });
            }

            return false;

        });

        $('button.action').click(function () {
            var operation = $(this).data('operation');
            var id = $(this).data('id');
            var type = $(this).data('type');
            var to_confirm = $(this).attr('confirm');
            if (to_confirm == null) {
                to_confirm = 'true';
            }
            var action;
            var confirm_message;
            var update_message;
            if (type === 'payment' && operation === 'capture') {
                action = 'wp_full_stripe_capture_payment';
                confirm_message = 'Are you sure you want to capture the payment?';
                update_message = 'Payment captured.';
            } else if (type === 'payment' && operation === 'refund') {
                action = 'wp_full_stripe_refund_payment';
                confirm_message = 'Are you sure you want to refund the payment?';
                update_message = 'Payment refunded.';
            } else if (type === 'payment' && operation === 'release') {
                action = 'wp_full_stripe_refund_payment';
                confirm_message = 'Are you sure you want to release the payment?';
                update_message = 'Payment released.';
            } else if (type === 'donationPayment' && operation === 'refund') {
                action = 'wp_full_stripe_refund_donation';
                confirm_message = 'Are you sure you want to refund the donation (initial amount)?';
                update_message = 'Donation refunded.';
            } else if (type === 'donationSubscription' && operation === 'cancel') {
                action = 'wp_full_stripe_cancel_donation';
                confirm_message = 'Are you sure you want to cancel the donation?';
                update_message = 'Donation canceled.';
            }

            var confirmed = true;
            if (to_confirm === 'true' || to_confirm === 'yes') {
                confirmed = confirm(confirm_message);
            }
            if (confirmed == true) {
                $.ajax({
                    type: "POST",
                    url: admin_ajaxurl,
                    data: {id: id, action: action},
                    cache: false,
                    dataType: "json",
                    success: function (data) {

                        if (data.success) {
                            if (data.redirectURL) {
                                setTimeout(function () {
                                    window.location = data.redirectURL;
                                }, 1000);
                            }
                            showUpdate(update_message);
                        } else {
                            logException('button.delete.click', data);
                        }

                    }
                });
            }

            return false;

        });

        $('input#stripe-webhook-url').focus(function () {
            $(this).select();
        });

        $('#formVATRateTypeSelect').change(function () {
            var selectedValue = $(this).val();
            if (selectedValue == 'fixed_vat') {
                $('#formVATPercentRow').show();
            } else {
                $('#formVATPercentRow').hide();
            }
        }).change();

        $('#customInputNumberSelect').change(function () {
            var customInputFieldCount = $(this).val();
            console.log('customInputFieldCount=' + customInputFieldCount);
            $('.wpfs-admin-form-custom-field').each(function () {
                var rowNumber = $(this).data('row-number');
                console.log('rowNumber=' + rowNumber);
                if (rowNumber <= customInputFieldCount) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }).change();

        //payment type toggle
        $('#set_custom_amount').click(function () {
            $('#form_amount').prop('disabled', true);
        });
        $('#set_specific_amount').click(function () {
            $('#form_amount').prop('disabled', false);
        });

        $('#form_redirect_to_page_or_post').change(function () {
            if ($(this).prop('checked')) {
                enable_combobox();
                $('#showDetailedSuccessPage').prop('disabled', false);
                $('#redirect_to_page_or_post_section').show();
                $('#redirect_to_url_section').hide();
            } else {
                disable_combobox();
                $('#showDetailedSuccessPage').prop('disabled', true);
                $('#redirect_to_page_or_post_section').hide();
            }
        });
        $('#form_redirect_to_url').change(function () {
            if ($(this).prop('checked')) {
                $('#redirect_to_page_or_post_section').hide();
                $('#redirect_to_url_section').show();
            } else {
                $('#redirect_to_url_section').hide();
            }
        });
        $('#do_redirect_no').click(function () {
            $('#form_redirect_page_or_post_id').val($('#form_redirect_page_or_post_id').prop('defaultSelected'));
            $('#form_redirect_url').val('');
            $('#form_redirect_to_page_or_post').prop('disabled', true);
            $('#form_redirect_to_url').prop('disabled', true);
            disable_combobox();
            $('#showDetailedSuccessPage').prop('disabled', true);
            $('#form_redirect_page_or_post_id').prop('disabled', true);
            $('#form_redirect_url').prop('disabled', true);
            $('#redirect_to_row').hide();
        });
        $('#do_redirect_yes').click(function () {
            $('#redirect_to_row').show();
            $('#redirect_to_url_section').hide();
            init_page_or_post_redirect();
            $('#redirect_to_page_or_post_section').show();
        });

        /**
         * @deprecated
         */
        $('#set_recipient_bank_account').click(function () {
            $("#createRecipientCard").hide();
            $("#createRecipientBank").show();
        });
        /**
         * @deprecated
         */
        $('#set_recipient_debit_card').click(function () {
            $("#createRecipientCard").show();
            $("#createRecipientBank").hide();
        });
        // terms of use
        $('#show_terms_of_use_no').click(function () {
            $('#termsOfUseSection').hide();
        });
        $('#show_terms_of_use_yes').click(function () {
            $('#termsOfUseSection').show();
        });
        // custom inputs
        $('#noinclude_custom_input').click(function () {
            $('#customInputSection').hide();
        });
        $('#include_custom_input').click(function () {
            $('#customInputSection').show();
        });
        // page or post combobox
        $.widget("custom.page_or_post_combobox", {
            _create: function () {
                this.wrapper = $("<span>")
                    .addClass("page_or_post-combobox")
                    .insertAfter(this.element);

                this.element.hide();
                this._createAutocomplete();
                this._createShowAllButton();
            },

            _createAutocomplete: function () {
                var selected = this.element.children(":selected"),
                    value = selected.val() ? selected.text() : "";

                this.input = $("<input>")
                    .appendTo(this.wrapper)
                    .val(value)
                    .prop("disabled", true)
                    .attr("title", "")
                    .attr("placeholder", "Select from the list or start typing")
                    .addClass("ui-widget")
                    .addClass("ui-widget-content")
                    .addClass("ui-corner-left")
                    .addClass("page_or_post-combobox-input")
                    .autocomplete({
                        delay: 0,
                        minLength: 0,
                        source: $.proxy(this, "_source")
                    })
                    .tooltip({
                        tooltipClass: "ui-state-highlight"
                    });
                this._on(this.input, {
                    autocompleteselect: function (event, ui) {
                        ui.item.option.selected = true;
                        this._trigger("select", event, {
                            item: ui.item.option
                        });
                    },

                    autocompletechange: "_removeIfInvalid"
                });
            },

            _createShowAllButton: function () {
                var input = this.input,
                    wasOpen = false;

                $("<a>")
                    .attr("tabIndex", -1)
                    .attr("title", "Show all page and post")
                    .tooltip()
                    .appendTo(this.wrapper)
                    .button({
                        icons: {
                            primary: "ui-icon-triangle-1-s"
                        },
                        text: false,
                        disabled: true
                    })
                    .removeClass("ui-corner-all")
                    .addClass("page_or_post-combobox-toggle ui-corner-right")
                    .mousedown(function () {
                        wasOpen = input.autocomplete("widget").is(":visible");
                    })
                    .click(function () {
                        input.focus();

                        // Close if already visible
                        if (wasOpen) {
                            return;
                        }

                        // Pass empty string as value to search for, displaying all results
                        input.autocomplete("search", "");
                    });
            },

            _source: function (request, response) {
                var matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term), "i");
                response(this.element.children('option').map(function () {
                    var text = $(this).text();
                    if (this.value && ( !request.term || matcher.test(text) ))
                        return {
                            label: text,
                            value: text,
                            option: this
                        };
                }));
            },

            _removeIfInvalid: function (event, ui) {

                // Selected an item, nothing to do
                if (ui.item) {
                    return;
                }

                // Search for a match (case-insensitive)
                var value = this.input.val(),
                    valueLowerCase = value.toLowerCase(),
                    valid = false;
                this.element.children('option').each(function () {
                    if ($(this).text().toLowerCase() === valueLowerCase) {
                        this.selected = valid = true;
                        return false;
                    }
                });

                // Found a match, nothing to do
                if (valid) {
                    return;
                }

                // Remove invalid value
                this.input
                    .val("")
                    .attr("title", value + " didn't match any item")
                    .tooltip("open");
                this.element.val("");
                this._delay(function () {
                    this.input.tooltip("close").attr("title", "");
                }, 2500);
                this.input.autocomplete("instance").term = "";
            },

            _destroy: function () {
                this.wrapper.remove();
                this.element.show();
            }
        });

        $("#form_redirect_page_or_post_id").page_or_post_combobox();

        // currency combobox
        $.widget("custom.currency_combobox", {
            _create: function () {
                this.wrapper = $("<span>")
                    .addClass("currency-combobox")
                    .insertAfter(this.element);

                this.element.hide();
                this._createAutocomplete();
                this._createShowAllButton();
            },

            _createAutocomplete: function () {
                var selected = this.element.children(":selected"),
                    value = selected.val() ? selected.text() : "";

                this.input = $("<input>")
                    .appendTo(this.wrapper)
                    .val(value)
                    .attr("title", "")
                    .attr("placeholder", "Select from the list or start typing")
                    .addClass("ui-widget")
                    .addClass("ui-widget-content")
                    .addClass("ui-corner-left")
                    .addClass("currency-combobox-input")
                    .autocomplete({
                        delay: 0,
                        minLength: 0,
                        source: $.proxy(this, "_source")
                    })
                    .tooltip({
                        tooltipClass: "ui-state-highlight"
                    });
                this._on(this.input, {
                    autocompleteselect: function (event, ui) {
                        ui.item.option.selected = true;
                        // tnagy enable/disable bitcoin/alipay options
                        var selectedValue = $(ui.item.option).val();
                        if (CURRENCY_USD === selectedValue) {
                            $('#alipay_usage_info_panel').hide();
                            $('#alipay_usage_panel').show();
                        } else {
                            $('#alipay_usage_info_panel').show();
                            $('#alipay_usage_panel').hide();
                        }
                        this._trigger("select", event, {
                            item: ui.item.option
                        });
                    },

                    autocompletechange: "_removeIfInvalid"
                });
            },

            _createShowAllButton: function () {
                var input = this.input,
                    wasOpen = false;

                $("<a>")
                    .attr("tabIndex", -1)
                    .attr("title", "Show all currencies")
                    .tooltip()
                    .appendTo(this.wrapper)
                    .button({
                        icons: {
                            primary: "ui-icon-triangle-1-s"
                        },
                        text: false,
                        disabled: false
                    })
                    .removeClass("ui-corner-all")
                    .addClass("currency-combobox-toggle ui-corner-right")
                    .mousedown(function () {
                        wasOpen = input.autocomplete("widget").is(":visible");
                    })
                    .click(function () {
                        input.focus();

                        // Close if already visible
                        if (wasOpen) {
                            return;
                        }

                        // Pass empty string as value to search for, displaying all results
                        input.autocomplete("search", "");
                    });
            },

            _source: function (request, response) {
                var matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term), "i");
                response(this.element.children('option').map(function () {
                    var text = $(this).text();
                    if (this.value && ( !request.term || matcher.test(text) ))
                        return {
                            label: text,
                            value: text,
                            option: this
                        };
                }));
            },

            _removeIfInvalid: function (event, ui) {

                // Selected an item, nothing to do
                if (ui.item) {
                    return;
                }

                // Search for a match (case-insensitive)
                var value = this.input.val(),
                    valueLowerCase = value.toLowerCase(),
                    valid = false;
                this.element.children('option').each(function () {
                    if ($(this).text().toLowerCase() === valueLowerCase) {
                        this.selected = valid = true;
                        return false;
                    }
                });

                // Found a match, nothing to do
                if (valid) {
                    return;
                }

                // Remove invalid value
                this.input
                    .val("")
                    .attr("title", value + " didn't match any item")
                    .tooltip("open");
                this.element.val("");
                this._delay(function () {
                    this.input.tooltip("close").attr("title", "");
                }, 2500);
                this.input.autocomplete("instance").term = "";
            },

            _destroy: function () {
                this.wrapper.remove();
                this.element.show();
            }
        });

        $("#currency").currency_combobox().change();

        // tnagy tab handling for create/edit forms
        $(".wpfs-admin-form .wpfs-tab-content").hide();
        $(".wpfs-admin-form-tabs a").on('click', function (event) {
            event.preventDefault();
            $(".wpfs-admin-form-tabs a").removeClass('nav-tab-active');
            $('.wpfs-admin-form .wpfs-tab-content').hide();
            $(this).addClass('nav-tab-active');
            var href = $(this).attr('href');
            $(href).show();
        });
        $(".wpfs-admin-form-tabs a:first").click();

        $('#form_name').focus();

        $('#sub_id').focus();

        // tnagy default billing address handling
        $('#hide_address_input').click(function () {
            $('#defaultBillingCountryRow').hide();
        });
        $('#show_address_input').click(function () {
            $('#defaultBillingCountryRow').show();
        });
        $('#hide_shipping_address_input').click(function () {
        });
        $('#show_shipping_address_input').click(function () {
            $('#show_address_input').click();
        });

        function hideCaptchaApiFields() {
            $('#google_recaptcha_site_key_row').hide();
            $('#google_recaptcha_secret_key_row').hide();
        }

        function showCaptchaApiFields() {
            $('#google_recaptcha_site_key_row').show();
            $('#google_recaptcha_secret_key_row').show();
        }

        $('#secure_subscription_update_with_google_recaptcha_yes').click(function () {
            showCaptchaApiFields();
        });
        $('#secure_inline_forms_with_google_recaptcha_yes').click(function () {
            showCaptchaApiFields();
        });
        $('#secure_checkout_forms_with_google_recaptcha_yes').click(function () {
            showCaptchaApiFields();
        });
        $('#secure_subscription_update_with_google_recaptcha_no').click(function () {
            if (
                $('#secure_inline_forms_with_google_recaptcha_no').is(':checked')
                && $('#secure_checkout_forms_with_google_recaptcha_no').is(':checked')
            ) {
                hideCaptchaApiFields();
            }
        });
        $('#secure_inline_forms_with_google_recaptcha_no').click(function () {
            if (
                $('#secure_subscription_update_with_google_recaptcha_no').is(':checked')
                && $('#secure_checkout_forms_with_google_recaptcha_no').is(':checked')
            ) {
                hideCaptchaApiFields();
            }
        });
        $('#secure_checkout_forms_with_google_recaptcha_no').click(function () {
            if (
                $('#secure_inline_forms_with_google_recaptcha_no').is(':checked')
                && $('#secure_subscription_update_with_google_recaptcha_no').is(':checked')
            ) {
                hideCaptchaApiFields();
            }
        });

        if ($("#settings-users-form").length) {
            if (
                $('#secure_subscription_update_with_google_recaptcha_yes').is(':checked')
                || $('#secure_inline_forms_with_google_recaptcha_yes').is(':checked')
                || $('#secure_checkout_forms_with_google_recaptcha_yes').is(':checked')
            ) {
                showCaptchaApiFields();
            }
        }

        $('#allow_multiple_subscriptions_input_no').click(function () {
            $('#maximum_number_of_subscriptions_row').hide();
        });
        $('#allow_multiple_subscriptions_input_yes').click(function () {
            $('#maximum_number_of_subscriptions_row').show();
        });

        $('#anchor_billing_cycle_no').click(function () {
            $('#billing_cycle_anchor_day_select').prop('disabled', true);
            $('#prorate_until_anchor_day_row').hide();
        });
        $('#anchor_billing_cycle_yes').click(function () {
            $('#billing_cycle_anchor_day_select').prop('disabled', false);
            $('#prorate_until_anchor_day_row').show();
        });

        function copyToClipboard(str) {
            var el = document.createElement('textarea');
            el.value = str;
            el.setAttribute('readonly', '');
            el.style.position = 'absolute';
            el.style.left = '-9999px';
            document.body.appendChild(el);
            var selected =
                document.getSelection().rangeCount > 0
                    ? document.getSelection().getRangeAt(0)
                    : false;
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
            if (selected) {
                document.getSelection().removeAllRanges();
                document.getSelection().addRange(selected);
            }
        }

        $(".wpfsadm-copy-to-clipboard").click(function () {
            copyToClipboard($(this).attr("data-form-id"));
        });

        $(".wpfsadm-ro-clipboard").on("click", function () {
            $(this).select();
        });

    });
})(jQuery);