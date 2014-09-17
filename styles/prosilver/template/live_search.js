(function ($) {  // Avoid conflicts with other libraries

    $().ready(function () {

  
        $("#leavesearch_btn").hoverIntent(function ()
        {
             $('#leavesearch_btn').fadeOut("slow");
            $('#leavesearch').fadeIn("slow");
       } );

        $('#leavesearch_btn_close').on('click', function (e) {
            $('#forum_live_search').val("");
            $('#topic_live_search').val("");
            $('#user_live_search').val("");
            $('#leavesearch_btn').fadeIn("slow");
            $('#leavesearch').fadeOut("slow");
        });

//        $('#pmheader-postingbox').find('input[name=add_to]').on('click', function (e) {
//                e.preventDefault();
//                if ($('#group_list :selected').length >0)
//               $('#postform').submit();
//               alert('3');

//       });


        $(document).click(function (event) {
            if ($(event.target).closest("#user_handle").length || $(event.target).closest(".acResults").length || $(event.target).closest("#user_live_search").length || $(event.target).closest("#leavesearch").length ) return;
            $("#user_handle").hide("slow");
            event.stopPropagation();
        });


        $("#user_live_search").on('keyup', function (e) {
            $('#user_handle').hide();
        });
        
        //live search forum
 //       var topic_path = './app.php/liveSearch/forum/' + S_FORUM_ID + '/0';
        //"./../../../liveSearch/usertopic/0/54?'
        $("#forum_live_search").autocomplete(
		    {
		        //url: topic_path,
		        url: U_FORUM_LS_PATH,
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
		        },
	
		    });

        //live search topic
       // var topic_path = './app.php/liveSearch/topic/' + S_FORUM_ID + '/0';
        $("#topic_live_search").autocomplete(
		    {
		        url: U_TOPIC_LS_PATH,
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
            url: U_USER_LS_PATH,
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

        //Leave search user pm
        var user_path = './app.php/liveSearch/userpm/0/0';
        $("#user_live_search_pm").autocomplete({
            url: U_USER_PM_LS_PATH,
            selectFirst: true,
            minChars: minChars_user,
           // addClassUl: 'drg',
           fixedPos:false,
            showResult: function (value, data) {

                return '<div class="draggable">' + hilight(value, $("#user_live_search").val()) + '</div>';
          },
//            onStart: function (item) {
//           var position = $("#user_live_search_pm").position();
//            var t = (position.top + 9) + 'px';
//            var l = position.left + 'px';
//            $('#leavesearch_pm').find('.acResults').css({ 'top': t, 'left': l});
//            },
            onItemSelect: function (item) {
                goto_user_pm(item);
            }
        });



    });


    function hilight(value, term) {
        return value.replace(new RegExp("(?![^&;]+;)(?!<[^<>]*)(" + term.replace(/([\^\$\(\)\[\]\{\}\*\.\+\?\|\\])/gi, "\\$1") + ")(?![^<>]*>)(?![^&;]+;)", "gi"), "<strong>$1</strong>");
    }
    function goto_forum(item) {
        var f = item.data[0];
        if (f) 
        {
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
                },
                stop: function (event, ui) {
                }
            });




            var position = $("#user_live_search").position();
            var t = (position.top + 9) + 'px';
            var l = position.left + 'px';
            var w =  $("#user_live_search").width() + 'px'; 
            //alert(w);
            $('#user_handle').css({ 'top': t, 'left': l});
            $('#user_handle').show();

            var username = item.value;
            var user_id = item.data[0];
            var user_email = item.data[1];

            $('.leave_search_contact-icon').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('icon-profile')) {
                    //window.location = './memberlist.php?mode=viewprofile&u=' + user_id;
                    window.location = U_MEMBERLIST_LS_PATH + 'viewprofile&u=' + user_id;
                }
                if ($(this).hasClass('pm-icon')) {
                    window.location = U_UCP_LS_PATH + 'compose&i=pm&u=' + user_id;
                }
                if ($(this).hasClass('email-icon')) {
                    window.location = U_MEMBERLIST_LS_PATH + 'email&u=' + user_id;
                }
            });
            $('#topics_live_search').on('click', function (e) {
                e.preventDefault();
                        //var topic_path = './app.php/leave_search_by_user/topic/' + S_FORUM_ID + '/' + user_id;

                //window.location = 'search.php?author_id=' + user_id + '&sr=topics&ls=1&forum_id=' + S_FORUM_ID;
                U_USERTOPIC_LS_PATH
                 var arr = U_USERTOPIC_LS_PATH.split('/');
                var l = arr.length;
                arr[arr.length-2] = S_FORUM_ID;
                arr[arr.length-1] = user_id;
                var usertopic_path = arr.join('/');
                alert(usertopic_path);
               //window.location = './app.php/liveSearch/usertopic/' + S_FORUM_ID + '/' + user_id;
               window.location = usertopic_path;
            });
            $('#posts_live_search').on('click', function (e) {
                e.preventDefault();
                window.location = 'search.php?author_id=' + user_id + '&sr=posts&ls=1&forum_id=' + S_FORUM_ID + '&topic_id=' + S_TOPIC_ID;
            });

        }


        return false;
    }

    function  goto_user_pm(item) {
        if (item == null || item.value == null || item.value == undefined) return;
    console.log(item);
        	var old_value = $("#username_list").val();
			var new_value = (old_value)? old_value+'\n'+item.value : item.value;
			$("#username_list").val(new_value);

    }


})(jQuery);                                                                 // Avoid conflicts with other libraries


