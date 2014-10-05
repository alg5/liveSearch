    (function ($) {    // Avoid conflicts with other libraries
        $().ready(function () {
            changeEnable($('#live_search_on_off_forum'), 'setting_f');
            changeEnable($('#live_search_on_off_topic'), 'setting_t');
            changeEnable($('#live_search_on_off_user'), 'setting_u');
            changeEnable($('#live_search_on_off_similartopic'), 'setting_st');

            $('#live_search_on_off_forum').on('change', function () {
                changeEnable(this, 'setting_f');
            });
            $('#live_search_on_off_topic').on('change', function () {
                changeEnable(this, 'setting_t');
            });
            $('#live_search_on_off_user').on('change', function () {
                changeEnable(this, 'setting_u');
            });
            $('#live_search_on_off_similartopic').on('change', function () {
                changeEnable(this, 'setting_st');
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

        //***ACP***
        console.log($('form#select_user').find('.username'));
        $('form#select_user').find('.username').on('change', function (e) {
            e.preventDefaults();
            alert('aaa');
        });
        $('form#select_user').find('.username').autocomplete({
            url: U_USER_LS_ACP_PATH,
            selectFirst: true,
            minChars: minChars_user,
            // addClassUl: 'drg',
            fixedPos: false,
            showResult: function (value, data) {

                return hilight(value, $("#user_live_search").val());
            },
            onItemSelect: function (item) {
                goto_acp_user_perm(item);
            }
        });


        function hilight(value, term) {
            return value.replace(new RegExp("(?![^&;]+;)(?!<[^<>]*)(" + term.replace(/([\^\$\(\)\[\]\{\}\*\.\+\?\|\\])/gi, "\\$1") + ")(?![^<>]*>)(?![^&;]+;)", "gi"), "<strong>$1</strong>");
        }

        function goto_acp_user_perm(item) {
            alert(item);
            //            if (item == null || item.value == null || item.value == undefined) return;
            //            var old_value = $("#username_list").val();
            //            var new_value = (old_value) ? old_value + '\n' + item.value : item.value;
            //            $("#username_list").val(new_value);

        }




    })(jQuery);                                                   // Avoid conflicts with other libraries
