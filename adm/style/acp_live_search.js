    (function ($) {    // Avoid conflicts with other libraries
        $().ready(function () {
            changeEnable($('#live_search_on_off_forum'), 'setting_f');
            changeEnable($('#live_search_on_off_topic'), 'setting_t');
            changeEnable($('#live_search_on_off_user'), 'setting_u');

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
                    $(div).find('dl').css('opacity', '1');
                }
                else {
                    $(div).children().prop('disabled', true);
                    $(div).find('dl').css('opacity', '0.3');
                }
            }


        });

    })(jQuery);                                               // Avoid conflicts with other libraries
