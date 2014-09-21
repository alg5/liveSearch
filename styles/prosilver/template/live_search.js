(function ($) {  

    //****show-hide button update*****
    // check if ext  ExtendedControls is set on
//	if ($('#extended-ShowHideMenuBtn').length == 0){


    // create ShowHide button
	//$("#leavesearch_btn").before('<div style="float: right;"><div id="leavesearch-ShowHideBtn" style="background: url(/ext/alg/liveSearch/styles/prosilver/theme/images/show-hide.png) 0 14px; cursor: pointer; position: fixed; top: 14px; width:14px; height:14px; visibility: hidden;" ></div></div>');


    // get setting for this button
//	var I = localStorage.getItem('extended_menu_hide_show');
//	if (I == null || isNaN(I)) { I = 1;}				// проверяем, существуют ли настройки
//	//if (I == 0) { $('#leavesearch-ShowHideBtn').css('background','url(/ext/alg/liveSearch/styles/prosilver/theme/images/show-hide.png) 0 0'); }


    // set on blocks
//	$(document).ready(function () {
//		setTimeout(function() {
//			$('#leavesearch-ShowHideBtn').css({opacity: 0.0, visibility: "visible"}).animate({opacity: '1.0'},1000);
//		        showHideleaveSearch();
//		}, 1000);
//	});

    // togle show/hide
	$("#leavesearch-ShowHideBtn").click(function ()
        {
		//I = 1 - I;
	        //showHideleaveSearch();
            if ($(this).hasClass('leavesearch-ShowHideBtn_open'))
            {
                $(this).removeClass('leavesearch-ShowHideBtn_open').addClass('leavesearch-ShowHideBtn_close').attr('title', LIVE_SEARCH_EYE_BUTTON_OPEN_T);
 			    $('#leavesearch_btn').hide();
			    $('#leavesearch').hide();
           }
            else
            {
                $(this).removeClass('leavesearch-ShowHideBtn_close').addClass('leavesearch-ShowHideBtn_open').attr('title', LIVE_SEARCH_EYE_BUTTON_CLOSE_T);
  			    $('#leavesearch_btn').show();
           }
	});
//	}


//	function showHideleaveSearch() {
//        	if (I == 0) {
//			//$('#leavesearch-ShowHideBtn').css('background','url(/ext/alg/liveSearch/styles/prosilver/theme/images/show-hide.png) 0 0');
//			$('#leavesearch_btn').hide();
//			$('#leavesearch').hide();
//		} else {
//			//$('#leavesearch-ShowHideBtn').css('background','url(/ext/alg/liveSearch/styles/prosilver/theme/images/show-hide.png) 0 14px');
//			$('#leavesearch_btn').css({opacity: 0.0, visibility: "visible", display: "block"}).animate({opacity: "1.0"},1000);
//		}
//        	localStorage.setItem('extended_menu_hide_show', I); 		// сохраняем настройку показа меню
//	}


    //calculate witdh for search panel
	var leavesearchWidth = 0;
	if ($('#topic_live_search').length > 0) {leavesearchWidth = $('#topic_live_search').outerWidth() + 75 }
	if ($('#forum_live_search').length > 0) {leavesearchWidth = leavesearchWidth + $('#forum_live_search').outerWidth() + 90 }
	if ($('#user_live_search').length > 0) {leavesearchWidth = leavesearchWidth + $('#user_live_search').outerWidth() + 125 }

	if (leavesearchWidth > 300 && leavesearchWidth < 550 ) { leavesearchWidth = leavesearchWidth - 23; }
	else if (leavesearchWidth > 500 ) { leavesearchWidth = leavesearchWidth - 45; }

	$('#leavesearch').css('width', leavesearchWidth);
    //****show-hide button update*****
    
    
    $().ready(function () {

       // showHideleaveSearch();
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
        $("#user_live_search").autocomplete({
            url: U_USER_LS_PATH,
            selectFirst: true,
            minChars: minChars_user,

            showResult: function (value, data) {
                return hilight(value, $("#user_live_search").val()) ;
            },
            onFinish: function (item) {
                goto_user(item);
            }
        });

        //Leave search user pm
        $("#user_live_search_pm").autocomplete({
            url: U_USER_PM_LS_PATH,
            selectFirst: true,
            minChars: minChars_user,
           // addClassUl: 'drg',
           fixedPos:false,
            showResult: function (value, data) {

                return hilight(value, $("#user_live_search").val()) ;
          },
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

            var position = $("#user_live_search").position();
            var t = (position.top + 9) + 'px';
            var l = position.left + 'px';
            var w =  $("#user_live_search").width() + 'px'; 
            $('#user_handle').css({ 'top': t, 'left': l});
            $('#user_handle').show();

            var username = item.value;
            var user_id = item.data[0];
            var allow_pm = item.data[1];
            var allow_email = item.data[2];
            var icq = item.data[3];
            var website = item.data[4];
            var wlm = item.data[5];
           var yahoo = item.data[6];
           var aol = item.data[7];
           var facebook = item.data[8];
           var googleplus = item.data[9];
           var skype = item.data[10];
           var twitter = item.data[11];
           var youtube = item.data[12];

            $('span.leave_search_contact-icon').each(function () {
                 $(this).parent().hide();
            });
            $('#user_handle').find('span.icon-profile').parent().show();

             if (allow_pm == 1) $('#user_handle').find('span.pm-icon').parent().show();
             if (allow_email == 1) $('#user_handle').find('span.email-icon').parent().show();
             if (icq != '') $('#user_handle').find('span.phpbb_icq-icon').parent().show();
             if (website != '') $('#user_handle').find('span.phpbb_website-icon').parent().show();
             if (wlm != '') $('#user_handle').find('span.phpbb_wlm-icon').parent().show();
             if (yahoo != '') $('#user_handle').find('span.phpbb_yahoo-icon').parent().show();
             if (aol != '') $('#user_handle').find('span.phpbb_aol-icon').parent().show();
             if (facebook != '') $('#user_handle').find('span.phpbb_facebook-icon').parent().show();
             if (googleplus != '') $('#user_handle').find('span.phpbb_googleplus-icon').parent().show();
             if (skype != '') $('#user_handle').find('span.phpbb_skype-icon').parent().show();
             if (twitter != '') $('#user_handle').find('span.phpbb_twitter-icon').parent().show();
             if (youtube != '') $('#user_handle').find('span.phpbb_youtube-icon').parent().show();




            $('.leave_search_contact-icon').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('icon-profile')) {
                    window.location = U_MEMBERLIST_LS_PATH + 'viewprofile&u=' + user_id;
                }
                if ($(this).hasClass('pm-icon')) {
                    window.location = U_UCP_LS_PATH + 'compose&i=pm&u=' + user_id;
                }
                if ($(this).hasClass('email-icon')) {
                    window.location = U_MEMBERLIST_LS_PATH + 'email&u=' + user_id;
                }
                if ($(this).hasClass('phpbb_icq-icon')) {
                    window.location = 'https://www.icq.com/people/' + icq + '/';
                }
                if ($(this).hasClass('phpbb_website-icon')) {
                    window.location = website;
                }
                if ($(this).hasClass('phpbb_wlm-icon')) {
                    //window.location = 'skype:' + skype + '?userinfo';
                }
                if ($(this).hasClass('phpbb_yahoo-icon')) {
                    window.location = 'http://edit.yahoo.com/config/send_webmesg?.target=' + yahho  + '&.src=pg';
                }
                if ($(this).hasClass('phpbb_aol-icon')) {
                   // window.location = 'http://edit.yahoo.com/config/send_webmesg?.target=' + yahho  + '&.src=pg';
                }
                if ($(this).hasClass('phpbb_facebook-icon')) {
                    window.location = 'http://facebook.com/' + facebook + '/';
                }
                if ($(this).hasClass('phpbb_googleplus-icon')) {
                    window.location = 'http://plus.google.com/'+googleplus;
                }
                if ($(this).hasClass('phpbb_skype-icon')) {
                    window.location = 'skype:' + skype + '?userinfo';
                }
                if ($(this).hasClass('phpbb_twitter-icon')) {
                    window.location = 'http://twitter.com/' + twitter;
                }
                if ($(this).hasClass('phpbb_youtube-icon')) {
                    window.location = 'http://youtube.com/user/' + youtube;
                }
            });
            $('#topics_live_search').on('click', function (e) {
                e.preventDefault();
                        //var topic_path = './app.php/leave_search_by_user/topic/' + S_FORUM_ID + '/' + user_id;

                //window.location = 'search.php?author_id=' + user_id + '&sr=topics&ls=1&forum_id=' + S_FORUM_ID;
                //U_USERTOPIC_LS_PATH
                 var arr = U_USERTOPIC_LS_PATH.split('/');
                var l = arr.length;
                arr[arr.length-2] = S_FORUM_ID;
                arr[arr.length-1] = user_id;
                var usertopic_path = arr.join('/');
               // alert(usertopic_path);
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


