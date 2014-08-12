(function ($) {  // Avoid conflicts with other libraries

    $().ready(function () {

        //        $('#leavesearch_btn').on('mouseover', function (e) {
        //            $(this).hide();
        //            $('#leavesearch').show();
        //        });
        $('#leavesearch_panel').hide();
        //$('#leavesearch').hide();

        $('#leavesearch_btn').on('click', function (e) {
//            $('#leavesearch_panel').slideToggle(3000);
//            $('#leavesearch').slideToggle(3000);
            $('#leavesearch_btn').fadeOut("slow");
            $('#leavesearch_panel').fadeIn("slow");
            $('#leavesearch').fadeIn("slow");
        });

        $('#leavesearch_btn_close').on('click', function (e) {
            $('#leavesearch_btn').fadeIn("slow");
            $('#leavesearch_panel').fadeOut("slow");
            //$('#leavesearch').fadeIn("slow");
        });

        $(document).click(function (event) {
            if ($(event.target).closest("#user_handle").length || $(event.target).closest(".acResults").length || $(event.target).closest("#user_live_search").length || $(event.target).closest("#leavesearch").length) return;
            $("#user_handle").hide("slow");
            if ($(event.target).closest("#leavesearch_panel").length)
                $("#leavesearch_panel").fadeOut("slow");
            event.stopPropagation();
        });


        $("#user_live_search").on('keyup', function (e) {
            $('#user_handle').hide();
        });

        //live search forum
        var topic_path = './app.php/liveSearch/forum/' + S_FORUM_ID + '/0';
        $("#forum_live_search").autocomplete(
		    {
		        url: topic_path,
		        sortResults: false,
		        width: 600,
		        maxItemsToShow: maxItemsToShow_forum,
		        selectFirst: true,
		        minChars: minChars_forum,

		        showResult: function (value, data) {
		            return '<span style="">' + hilight(value, $("#forum_live_search").val()) + '</span>';
		        },
		        onItemSelect: function (item) {
		            goto_forum(item);
		        }
		    });

        //live search topic
        var topic_path = './app.php/liveSearch/topic/' + S_FORUM_ID + '/0';
        $("#topic_live_search").autocomplete(
		    {
		        url: topic_path,
		        sortResults: false,
		        width: 600,
		        maxItemsToShow: maxItemsToShow_topic,
		        selectFirst: true,
		        minChars: minChars_topic,

		        showResult: function (value, data) {
		            return '<span style="">' + hilight(value, $("#topic_live_search").val()) + '</span>';
		        },
		        onItemSelect: function (item) {
		            goto_topic(item);
		        }
		    });

        //Leave search user
        var user_path = './app.php/liveSearch/user/0/0';
        $("#user_live_search").autocomplete({
            url: user_path,
            selectFirst: true,
            minChars: minChars_user,
            addClassUl: 'drg',

            showResult: function (value, data) {

                return '<div class="draggable">' + hilight(value, $("#user_live_search").val()) + '</div>';
            },
            onFinish: function (item) {
                goto_user(item);
            }
        });



    });


    function hilight(value, term) {
        return value.replace(new RegExp("(?![^&;]+;)(?!<[^<>]*)(" + term.replace(/([\^\$\(\)\[\]\{\}\*\.\+\?\|\\])/gi, "\\$1") + ")(?![^<>]*>)(?![^&;]+;)", "gi"), "<strong>$1</strong>");
    }
    function goto_forum(item) {
        var f = item.data[0];
        if (f) {
            $("#forum_live_search").val('');
            var wnd = LIVE_SEARCH_SHOW_IN_NEW_WINDOW ? '_blank' : '_parent';
            window.open(U_FORUM_REDIRECT + '?f=' + f, wnd);
        }
        return false;
    }

    function goto_topic(item) {
        var t = item.data[0];
        var f = item.data[1];
        if (t) {
            $("#live_search").val('');
            var wnd = LIVE_SEARCH_SHOW_IN_NEW_WINDOW ? '_blank' : '_parent';
            window.open(U_TOPIC_REDIRECT + '?f=' + f + '&t=' + t, wnd);
        }
        return false;
    }
    function goto_user(item) {

        if (item) {
            var newVal = '<div class="user_drag">' + $("#user_live_search").val() + '</div>';
            $("#user_live_search").html(newVal);
            $('#user_live_search').accordion();
            $("div.user_drag").draggable({
                cursor: 'move',
                revert: 'invalid', // <-- Revert invalid drops
                appendTo: "body",
                helper: "clone"
            });


            $(".drg").find("li").each(function () {
                $(this).draggable({
                    revert: 'invalid',
                    helper: 'clone',

                    start: function (event, ui) {
                        $(this).fadeTo('fast', 0.5);
                    },

                    stop: function (event, ui) {
                        $(this).fadeTo(0, 1);
                    }
                });
            });


            $("#username_list").droppable({
                drop: function (event, ui) {
                    console.log('11111111111');
                    console.log(event);
                    console.log(ui);
                },
                stop: function (event, ui) {
                    console.log('3333333333');
                    console.log(event);
                    console.log(ui);
                }
            });




            var position = $("#user_live_search").position();
            var t = (position.top + 9) + 'px';
            var l = position.left + 'px';
            $('#user_handle').css({ 'top': t, 'left': l });
            $('#user_handle').show();

            var username = item.value;
            var user_id = item.data[0];
            var user_email = item.data[1];

            $('.leave_search_contact-icon').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('icon-profile')) {
                    window.location = './memberlist.php?mode=viewprofile&u=' + user_id;
                }
                if ($(this).hasClass('pm-icon')) {
                    window.location = './ucp.php?i=pm&mode=compose&u=' + user_id;
                }
                if ($(this).hasClass('email-icon')) {
                    window.location = './memberlist.php?mode=email&u=' + user_id;
                }
            });
            $('#topics_live_search').on('click', function (e) {
                e.preventDefault();
                window.location = 'search.php?author_id=' + user_id + '&sr=topics&ls=1&forum_id=' + S_FORUM_ID;
            });
            $('#posts_live_search').on('click', function (e) {
                e.preventDefault();
                window.location = 'search.php?author_id=' + user_id + '&sr=posts&ls=1&forum_id=' + S_FORUM_ID + '&topic_id=' + S_TOPIC_ID;
            });

        }


        return false;
    }


})(jQuery);                                                            // Avoid conflicts with other libraries


