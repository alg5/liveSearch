    (function ($) {    // Avoid conflicts with other libraries
        $().ready(function () {
            $('#live_search_on_off_forum').on('change', function () {
                changeEnable(this, 'setting_f');
            });
            $('#live_search_on_off_topic').on('change', function () {
                changeEnable(this, 'setting_t');
            });
            $('#live_search_on_off_user').on('change', function () {
                changeEnable(this, 'setting_u');
            });

            function changeEnable(el, idDiv) {
                var div = $('#' + idDiv);
                if ($(el).prop('checked')) {

                    $(div).children().prop('disabled', false);
                    var fieldset = $(div).find('fieldset');
                    $(fieldset).css('background-color', '');
                    $(div).css({ 'opacity': '1', 'color': '#536482' });
                }
                else {
                    $(div).children().prop('disabled', true);
                    var fieldset = $(div).find('fieldset');
                    $(fieldset).css('background-color', 'Gray');
                    $(div).css({ 'opacity': '0.2', 'color': 'White' });
                }
            }


        });

    })(jQuery);                                              // Avoid conflicts with other libraries
