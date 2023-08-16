Stripe.setPublishableKey(stripekey);

jQuery(document).ready(function ($) {

    function scrollTo($elem) {
        if ($elem && $elem.offset() && $elem.offset().top) {
            if (!isInViewport($elem)) {
                $('html, body').animate({
                    scrollTop: $elem.offset().top - 100
                }, 1000);
            }
        }
        if ($elem) {
            $elem.fadeIn(500).fadeOut(500).fadeIn(500);
        }
    }

    function isInViewport($elem) {
        var $window = $(window);

        var docViewTop = $window.scrollTop();
        var docViewBottom = docViewTop + $window.height();

        var elemTop = $elem.offset().top;
        var elemBottom = elemTop + $elem.height();

        return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
    }

    var $tips = $('.tips');
    var $loading = $(".showLoading");
    var $del_tips = $('.delete_tips');
    var $del_loading = $(".delete_showLoading");
    var $up_tips = $('.update_tips');
    var $up_loading = $(".update_showLoading");

    $loading.hide();
    $del_loading.hide();
    $up_loading.hide();

    $('#wpfs_members_level').change(function () {
        var plan = $(this).val();
        var option = $("#wpfs_members_level").find("option[value='" + plan + "']");
        var currency = option.data("currency");
        var currencySymbol = option.data("currency-symbol");
        var zeroDecimalSupport = option.data("zero-decimal-support");
        var amount;
        console.log('zeroDecimalSupport=' + zeroDecimalSupport);
        if (zeroDecimalSupport == true) {
            amount = parseInt(option.data("amount"));
            console.log('parseInt');
        } else {
            amount = parseFloat(option.data("amount") / 100);
            console.log('parseFloat');
        }
        var interval = option.data("interval");
        var intervalCount = parseInt(option.data("interval-count"));
        var details = null;
        if (intervalCount > 1) {
            details = sprintf(wpfsm_L10n.plan_details_with_plural_interval, currencySymbol, amount, intervalCount, interval);
        } else {
            details = sprintf(wpfsm_L10n.plan_details_with_singular_interval, currencySymbol, amount, interval);
        }
        $(".wpfs_members_level_details").text(details);
    }).change();


    $('#wpfs_members_change_level').submit(function () {
        $tips.removeClass('alert alert-error');
        $tips.html("");

        $loading.show();
        var $form = $(this);

        // Disable the submit button
        $form.find('button').prop('disabled', true);
        // Attach the selected role to save getting it again later
        var plan = $('#wpfs_members_level').val();
        var option = $("#wpfs_members_level").find("option[value='" + plan + "']");
        var role = option.attr("data-role");
        $form.append("<input type='hidden' name='role' value='" + role + "' />");

        $.ajax({
            type: "POST",
            url: ajaxurl,
            data: $form.serialize(),
            cache: false,
            dataType: "json",
            success: function (data) {

                if (data.success) {
                    $tips.addClass('alert alert-success');
                    $tips.html(wpfsm_L10n.changeLevelSuccessMessage);
                    scrollTo($tips);
                    setTimeout(function () {
                        document.location.reload(true);
                    }, 1000);
                } else {
                    // show the errors on the form
                    $tips.addClass('alert alert-error');
                    $tips.html(data.msg);
                    scrollTo($tips);
                }
            },
            complete: function () {
                $loading.hide();
                // re-enable the submit button
                $form.find('button').prop('disabled', false);
            }
        });

        return false;
    });

    $('#wpfs_members_cancel_membership').click(function (e) {
        e.preventDefault();
        $('#wpfs_members_cancel_question').toggle();
        return false;
    });

    $('#wpfs_members_cancel').submit(function (e) {
        $del_tips.removeClass('alert alert-error');
        $del_tips.html("");

        $del_loading.show();
        var $form = $(this);

        // Disable the submit button
        $form.find('button').prop('disabled', true);

        $.ajax({
            type: "POST",
            url: ajaxurl,
            data: $form.serialize(),
            cache: false,
            dataType: "json",
            success: function (data) {
                if (data.success) {
                    $del_tips.addClass('alert alert-success');
                    $del_tips.html(wpfsm_L10n.cancelMembershipSuccessMessage);
                    scrollTo($del_tips);
                    setTimeout(function () {
                        document.location.reload(true);
                    }, 1000);
                } else {
                    // show the errors on the form
                    $del_tips.addClass('alert alert-error');
                    $del_tips.html(data.msg);
                    scrollTo($del_tips);
                }
            },
            complete: function () {
                $del_loading.hide();
                // re-enable the submit button
                $form.find('button').prop('disabled', false);
            }
        });

        return false;
    });

    $('#wpfs_members_cancel_membership_no').click(function (e) {
        e.preventDefault();
        $('#wpfs_members_cancel_question').hide();
        return false;
    });


    $('#wpfs_members_update_card_button').click(function (e) {
        e.preventDefault();
        $('#wpfs_members_update_card_section').toggle();
        return false;
    });

    // update card form
    $('#wpfs_members_update_card').submit(function (e) {
        $up_loading.show();

        $up_tips.removeClass('alert alert-error');
        $up_tips.html("");

        var $form = $(this);

        // Disable the submit button
        $form.find('button').prop('disabled', true);

        Stripe.createToken($form, stripeResponseHandler);
        return false;
    });

    var stripeResponseHandler = function (status, response) {
        var $form = $('#wpfs_members_update_card');

        if (response.error) {
            // Show the errors
            $up_tips.addClass('alert alert-error');
            if (response.error.code && wpfsm_L10n.hasOwnProperty(response.error.code)) {
                $up_tips.html(wpfsm_L10n[response.error.code]);
            } else {
                $up_tips.html(response.error.message);
            }
            scrollTo($up_tips);
            $form.find('button').prop('disabled', false);
            $up_loading.hide();
        } else {
            // token contains id, last4, and card type
            var token = response.id;
            $form.append("<input type='hidden' name='stripeToken' value='" + token + "' />");

            //post payment via ajax
            $.ajax({
                type: "POST",
                url: ajaxurl,
                data: $form.serialize(),
                cache: false,
                dataType: "json",
                success: function (data) {
                    if (data.success) {
                        //clear form fields
                        $form.find('input:text, input:password').val('');
                        $('#wpfs_members_update_card_section').hide();
                        //inform user of success
                        $up_tips.addClass('alert alert-success');
                        $up_tips.html(wpfsm_L10n.updateCardSuccessMessage);
                        scrollTo($up_tips);
                        setTimeout(function () {
                            document.location.reload(true);
                        }, 1000);
                    } else {
                        // show the errors on the form
                        $up_tips.addClass('alert alert-error');
                        $up_tips.html(data.msg);
                        scrollTo($up_tips);
                    }
                },
                complete: function () {
                    $up_loading.hide();
                    // re-enable the submit button
                    $form.find('button').prop('disabled', false);
                }
            });
        }
    };

});