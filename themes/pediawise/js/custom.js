(function($) {

    $(document).ready(function () {

        if ( $('#gift-ways-form-modal').length ) {

            $('a[href="#gift-ways-form-modal"]').magnificPopup({
                type: 'inline',
                preloader: false,
                modal: true
            });

            $(document).on('click', '.popup-modal-dismiss', function (e) {
                e.preventDefault();
                $.magnificPopup.close();
            });     

        }

    });

}(jQuery));