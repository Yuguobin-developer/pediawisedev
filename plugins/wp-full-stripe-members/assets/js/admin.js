jQuery(document).ready(function ($) {
    var $tips = $('.tips');
    var $del_tips = $('.delete_tips');
    var $change_tips = $('.change_tips');
    var $loading = $(".showLoading");
    var $del_loading = $(".delete_showLoading");
    var $change_loading = $(".change_showLoading");
    $loading.hide();
    $del_loading.hide();
    $change_loading.hide();

    /**
     * Delete Member
     */
    $('.wp-list-table.members button.delete').click(function () {
        var id = $(this).attr('data-id');
        var type = $(this).attr('data-type');
        var to_confirm = $(this).attr('data-confirm');
        if (to_confirm == null) {
            to_confirm = 'true';
        }
        var confirm_message = 'Are you sure you want to delete the record?';
        var update_message = 'Record deleted.';
        var action = '';
        if (type === 'member') {
            action = 'wpfs_members_delete_member';
            confirm_message = 'Are you sure you want to delete this member?';
            update_message = 'Member deleted.';
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
                    $loading.hide();
                    document.body.scrollTop = document.documentElement.scrollTop = 0;

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
                        // show the errors on the form
                        $tips.addClass('alert alert-error');
                        $tips.html(update_message);
                        $tips.fadeIn(500).fadeOut(500).fadeIn(500);
                    } else {
                        console.log('button.delete.click:' + JSON.stringify(data));
                    }

                }
            });
        }

        return false;

    });

    $('#wpfs-members-role-plans-form').submit(function (e) {
        $tips.removeClass('alert alert-error');
        $tips.html("");

        $loading.show();
        var $form = $(this);

        // Disable the submit button
        $form.find('button').prop('disabled', true);

        $.ajax({
            type: "POST",
            url: admin_ajaxurl,
            data: $form.serialize(),
            cache: false,
            dataType: "json",
            success: function (data) {
                $loading.hide();
                document.body.scrollTop = document.documentElement.scrollTop = 0;

                if (data.success) {
                    $("#updateMessage").text("Roles saved.");
                    $("#updateDiv").addClass('updated').show();
                    $form.find('button').prop('disabled', false);
                }
                else {
                    // re-enable the submit button
                    $form.find('button').prop('disabled', false);
                    // show the errors on the form
                    $tips.addClass('alert alert-error');
                    $tips.html(data.msg);
                    $tips.fadeIn(500).fadeOut(500).fadeIn(500);
                }
            }
        });

        return false;
    });

    $('#wpfs-members-settings-form').submit(function (e) {
        $loading.show();
        var $form = $(this);

        // Disable the submit button
        $form.find('button').prop('disabled', true);

        $.ajax({
            type: "POST",
            url: admin_ajaxurl,
            data: $form.serialize(),
            cache: false,
            dataType: "json",
            success: function () {
                $loading.hide();
                document.body.scrollTop = document.documentElement.scrollTop = 0;
                $("#updateMessage").text("Settings Updated.");
                $("#updateDiv").addClass('updated').show();
                $form.find('button').prop('disabled', false);
            }
        });

        return false;
    });


    // edit members page cancel subscription button
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
                $del_loading.hide();

                if (data.success) {
                    document.body.scrollTop = document.documentElement.scrollTop = 0;
                    $("#updateMessage").text("Membership canceled successfully.");
                    $("#updateDiv").addClass('updated').show();
                    $form.find('button').prop('disabled', false);

                    setTimeout(function () {
                        document.location.reload(true);
                    }, 1000);
                }
                else {
                    // re-enable the submit button
                    $form.find('button').prop('disabled', false);
                    // show the errors on the form
                    $del_tips.addClass('alert alert-error');
                    $del_tips.html(data.msg);
                    $del_tips.fadeIn(500).fadeOut(500).fadeIn(500);
                }
            }
        });

        return false;
    });


    //edit members page change subscription level
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
                $loading.hide();

                if (data.success) {
                    document.body.scrollTop = document.documentElement.scrollTop = 0;
                    $("#updateMessage").text("Membership updated successfully.");
                    $("#updateDiv").addClass('updated').show();
                    $form.find('button').prop('disabled', false);

                    setTimeout(function () {
                        document.location.reload(true);
                    }, 1000);
                }
                else {
                    // re-enable the submit button
                    $form.find('button').prop('disabled', false);
                    // show the errors on the form
                    $tips.addClass('alert alert-error');
                    $tips.html(data.msg);
                    $tips.fadeIn(500).fadeOut(500).fadeIn(500);
                }
            }
        });

        return false;
    });


    // edit members page change role button
    $('#wpfs_members_change_role').submit(function (e) {
        $change_tips.removeClass('alert alert-error');
        $change_tips.html("");

        $change_loading.show();
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
                $change_loading.hide();

                if (data.success) {
                    document.body.scrollTop = document.documentElement.scrollTop = 0;
                    $("#updateMessage").text("Member role changed successfully.");
                    $("#updateDiv").addClass('updated').show();
                    $form.find('button').prop('disabled', false);

                    setTimeout(function () {
                        document.location.reload(true);
                    }, 1000);
                }
                else {
                    // re-enable the submit button
                    $form.find('button').prop('disabled', false);
                    // show the errors on the form
                    $change_tips.addClass('alert alert-error');
                    $change_tips.html(data.msg);
                    $change_tips.fadeIn(500).fadeOut(500).fadeIn(500);
                }
            }
        });

        return false;
    });

    $('#wpfs-members-create-form').submit(function (e) {
        $("#updateMessage").text("");
        $("#updateDiv").removeClass('error');

        //validate
        var email = $('#wpfs_members_email').val();
        if (!email) {
            document.body.scrollTop = document.documentElement.scrollTop = 0;
            $("#updateMessage").text("Please enter a valid email address");
            $("#updateDiv").addClass('error').show();
            return false;
        }

        $loading.show();
        var $form = $(this);

        // Disable the submit button
        $form.find('button').prop('disabled', true);

        $.ajax({
            type: "POST",
            url: admin_ajaxurl,
            data: $form.serialize(),
            cache: false,
            dataType: "json",
            success: function () {
                $loading.hide();
                document.body.scrollTop = document.documentElement.scrollTop = 0;
                $("#updateMessage").text("Member Created.");
                $("#updateDiv").addClass('updated').show();
                $form.find('button').prop('disabled', false);
            }
        });

        return false;
    });

    if ($('#wpfsm-import-wizard').length) {
        $('#wpfsm-import-wizard').modalSteps({
            disableNextButton: true,
            btnCancelHtml: 'Close',
            callbacks: {
                '1': importWizardStep1,
                '2': importWizardStep2,
                '3': importWizardStep3,
                '4': importWizardStep4,
                '5': importWizardStep5,
                '6': importWizardStep6
            },
            completeCallback: importWizardFinish
        });
        jQuery('#wpfsm-import-wizard button[data-orientation=cancel]').click(function () {
            location.reload();
        });
    }

});

var options = {
    lines: 13 // The number of lines to draw
    , length: 12 // The length of each line
    , width: 6 // The line thickness
    , radius: 20 // The radius of the inner circle
    , scale: 1 // Scales overall size of the spinner
    , corners: 1 // Corner roundness (0..1)
    , color: '#000' // #rgb or #rrggbb or array of colors
    , opacity: 0.25 // Opacity of the lines
    , rotate: 0 // The rotation offset
    , direction: 1 // 1: clockwise, -1: counterclockwise
    , speed: 1 // Rounds per second
    , trail: 60 // Afterglow percentage
    , fps: 20 // Frames per second when using setTimeout() as a fallback for CSS
    , zIndex: 2e9 // The z-index (defaults to 2000000000)
    , className: 'import-spinner' // The CSS class to assign to the spinner
    , top: '50%' // Top position relative to parent
    , left: '50%' // Left position relative to parent
    , shadow: false // Whether to render a shadow
    , hwaccel: false // Whether to use hardware acceleration
    , position: 'absolute' // Element positioning
};

function drawTestModeTable(data) {
    if (!data || data.length == 0) {
        return;
    }
    jQuery('#post_import_summary_test_mode tbody').empty();
    var stripeUrl = 'https://dashboard.stripe.com/' + (data.live_mode === true ? '' : 'test/') + 'customers/';
    for (var i = 0; i < data.length; i++) {
        drawTestModeRow(data[i], stripeUrl);
    }
}

function drawCannotImportTable(data) {
    if (!data || data.length == 0) {
        return;
    }
    jQuery('#post_import_summary_cannot_import tbody').empty();
    var stripeUrl = 'https://dashboard.stripe.com/' + (data.live_mode === true ? '' : 'test/') + 'customers/';
    for (var i = 0; i < data.length; i++) {
        drawCannotImportRow(data[i], stripeUrl);
    }
}

function drawCanImportManuallyTable(data) {
    if (!data || data.length == 0) {
        jQuery('#post_import_summary_can_import_manually').hide();
        return;
    }
    jQuery('#post_import_summary_can_import_manually tbody').empty();
    var stripeUrl = 'https://dashboard.stripe.com/' + (data.live_mode === true ? '' : 'test/') + 'customers/';
    for (var i = 0; i < data.length; i++) {
        drawCanImportManuallyRow(data[i], stripeUrl);
    }
}

function drawTestModeRow(data, stripeUrl) {
    var row = jQuery('<tr />');
    jQuery('#post_import_summary_test_mode tbody').append(row);
    row.append(jQuery('<td><a href="' + stripeUrl + data.customer_id + '" target="_blank">' + (data.name ? data.name : '&lt;No name provided&gt;') + '</a> (' + data.email + ')' + '</td>'));
    row.append(jQuery('<td>Plugin is not allowed to create members in test mode. Check the plugin\'s Settings tab.</td>'));
}

function drawCannotImportRow(data, stripeUrl) {
    var row = jQuery('<tr />');
    jQuery('#post_import_summary_cannot_import tbody').append(row);
    row.append(jQuery('<td><a href="' + stripeUrl + data.customer_id + '" target="_blank">' + (data.name ? data.name : '&lt;No name provided&gt;') + '</a> (' + data.email + ')' + '</td>'));
    row.append(jQuery('<td>No suitable subscription plan found.</td>'));
}

function drawCanImportManuallyRow(data, stripeUrl) {
    var row = jQuery('<tr />');
    jQuery('#post_import_summary_can_import_manually tbody').append(row);
    row.append(jQuery('<td><a href="' + stripeUrl + data.customer_id + '" target="_blank">' + data.name + '</a> (' + data.email + ')' + '</td>'));
    var planSelector = jQuery('<select />');
    planSelector.attr('name', data.customer_id);
    row.append(jQuery('<td />').append(planSelector));
    planSelector.append('<option value="">Do not import</option>');
    for (var j = 0; j < data.available_plans.length; j++) {
        var p = data.available_plans[j];
        if (p.plan !== null) {
            planSelector.append('<option value="' + p.plan + '">' + p.display_name + '</option>');
        }
    }
}

var importWizardStep1 = function () {
    jQuery('#wpfsm-import-wizard button[data-orientation=next]').hide();
    var target1 = document.getElementById('spinner1');
    var spinner1 = new Spinner(options).spin(target1);
    var data = {
        action: 'wpfs_members_import_subscribers_from_stripe_step1'
    };
    jQuery.post(ajaxurl, data, function (response) {
        console.log('Response arrived for step 1: ' + JSON.stringify(response));

        spinner1.stop();

        if (response && response.status && 'OK' == response.status) {
            if (response.total_count_with_subscriptions) {
                jQuery('div[data-step=2] #pre_import_summary').html('You are about to import <strong>' + response.total_count_with_subscriptions + '</strong> subscribers from Stripe.');
                jQuery('#wpfsm-import-wizard button[data-orientation=next]').click();
            } else {
                jQuery('div[data-step=1] .well').html('An error occurred during import!');
            }
        } else {
            jQuery('div[data-step=1] .well').html('An error occurred during import!');
        }
    });
};
var importWizardStep2 = function () {
    jQuery('#wpfsm-import-wizard button[data-orientation=next]').removeAttr('disabled');
    jQuery('#wpfsm-import-wizard button[data-orientation=next]').html('Import >>>');
    jQuery('#wpfsm-import-wizard button[data-orientation=next]').show();
};
var importWizardStep3 = function () {
    var target3 = document.getElementById('spinner3');
    var spinner3 = new Spinner(options).spin(target3);
    var data = {
        action: 'wpfs_members_import_subscribers_from_stripe_step3'
    };
    jQuery.post(ajaxurl, data, function (response) {
        console.log('Response arrived for step 3: ' + JSON.stringify(response));

        spinner3.stop();

        if (response && response.status && 'OK' == response.status) {

            jQuery('#post_import_summary_imported_successfully').html('<strong>' + response.imported_successfully + '</strong> subscribers have been imported successfully.');
            if (response.test_mode && response.test_mode.length > 0) {
                jQuery('#post_import_summary_test_mode_count').html('<strong>' + response.test_mode.length + '</strong> subscribers won\'t be imported because plugin is not allowed to create members in test mode. <a role="button" data-toggle="collapse" href="#post_import_summary_test_mode_collapse" aria-expanded="false" aria-controls="post_import_summary_test_mode_collapse">Click here for show/hide details</a>.');
            }
            if (response.cannot_import && response.cannot_import.length > 0) {
                jQuery('#post_import_summary_cannot_import_count').html('<strong>' + response.cannot_import.length + '</strong> subscribers cannot be imported. <a role="button" data-toggle="collapse" href="#post_import_summary_cannot_import_collapse" aria-expanded="false" aria-controls="post_import_summary_cannot_import_collapse">Click here for show/hide details</a>.');
            }
            if (response.can_import_manually && response.can_import_manually.length > 0) {
                jQuery('#post_import_summary_can_import_manually_count').html('<strong>' + response.can_import_manually.length + '</strong> subscribers can be imported by setting their membership manually.');
            }

            drawTestModeTable(response.test_mode);
            
            drawCannotImportTable(response.cannot_import);

            drawCanImportManuallyTable(response.can_import_manually);

            jQuery('#wpfsm-import-wizard button[data-orientation=next]').click();

            if (!response.can_import_manually || response.can_import_manually.length == 0) {
                jQuery('#wpfsm-import-wizard button[data-orientation=next]').hide();
            }

        } else {
            jQuery('div[data-step=3] .well').html('An error occurred during import!');
        }
    });
};
var importWizardStep4 = function () {
    jQuery('#wpfsm-import-wizard button[data-orientation=next]').removeAttr('disabled');
    jQuery('#wpfsm-import-wizard button[data-orientation=next]').html('Import selected >>>');
};

var importWizardStep5 = function () {
    var target5 = document.getElementById('spinner5');
    var spinner5 = new Spinner(options).spin(target5);

    var plans = jQuery('#plan_selector_form').serializeArray();

    var data = {
        action: 'wpfs_members_import_subscribers_from_stripe_step5',
        plans: plans
    };

    jQuery.post(ajaxurl, data, function (response) {
        console.log('Response arrived for step 5: ' + JSON.stringify(response));

        spinner5.stop();

        if (response && response.status && 'OK' == response.status) {
            jQuery('#post_manual_import_summary_imported_successfully').html('<strong>' + response.imported_successfully + '</strong> subscribers have been imported successfully.');
            jQuery('#wpfsm-import-wizard button[data-orientation=next]').click();
        } else {
            jQuery('div[data-step=5] .well').html('An error occurred during import!');
        }

    });
};

var importWizardStep6 = function () {
    jQuery('#wpfsm-import-wizard button[data-orientation=next]').removeAttr('disabled');
};

var importWizardFinish = function () {
    location.reload();
};
