jQuery.noConflict();
(function ($) {

    "use strict";

    $(function () {

            //noinspection JSUnresolvedVariable
            var stripe = Stripe(wpfsStripeKey);

            var WPFS = {};

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
                            var $this = $(this);
                            var $selectMenuButton = $this.next();
                            $selectMenuButton.addClass($this.attr('class'));
                            if ($this.find('option:selected:disabled').length > 0) {
                                $selectMenuButton.addClass('ui-state-placeholder');
                            }
                        },
                        open: function () {
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
                            var $this = $(this);
                            var $button = $this.data('custom-wpfsSelectmenu').button;
                            $button.removeClass('ui-selectmenu-button-open');
                            $button.addClass('ui-selectmenu-button-closed');
                        },
                        change: function () {
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

            WPFS.initUpdateCardForm = function () {
                var $card = $('[data-toggle="card"]');
                if ($card.length === 0) {
                    return;
                }

                var elements = stripe.elements();
                var card = elements.create('card', {
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

                card.mount('[data-toggle="card"]');
            };


            WPFS.initSelectmenu();
            WPFS.initUpdateCardForm();

        }
    );

})(jQuery);