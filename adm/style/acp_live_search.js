    (function ($) {    // Avoid conflicts with other libraries
        $().ready(function () {
            $('#live_search_on_off_forum').on('change', function () {
                //$('#setting_f').css('display', $(this).prop('checked') ? '' : 'none');
                $("#setting_f").children().prop('disabled', !$(this).prop('checked'));
            });
//            $('#live_search_on_off_topic').on('change', function () {
//                $('#setting_t').css('display', $(this).prop('checked') ? '' : 'none');
//            });

//            $('#live_search_on_off_user').on('change', function () {
//                $('#setting_u').css('display', $(this).prop('checked') ? '' : 'none');
//            });

        });

    })(jQuery);                                    // Avoid conflicts with other libraries
