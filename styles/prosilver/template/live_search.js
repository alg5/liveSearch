(function ($) {  
if (LIVE_SEARCH_USE_EYE_BUTTON)
{
    var obj = { };
   $(obj).eye({
            name: 'ls_eye',
            title_open:LIVE_SEARCH_EYE_BUTTON_OPEN_T,
            title_close:LIVE_SEARCH_EYE_BUTTON_CLOSE_T,
            id: ['leavesearch_btn', 'leavesearch'],
	    });
}
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
		            return '<span style="">' + hilight(value, $("#topic_live_search").val()) + data[2] + '</span>';
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
        //Live search similar topic during new topic
 
        if (S_SIMILARTOPIC_SHOW)
        {
            var new_topic = $('.ls_similartopics').next().find('input[name=subject]');
            $(new_topic).attr('autocomplete', 'off').css('padding-left', '2px');
            $('.ls_similartopics').next().find('input[name=subject]').autocomplete({
 		            url: U_SIMILARTOPIC_LS_PATH,
		            sortResults: false,
		            width: 600,
		            maxItemsToShow: maxItemsToShow_topic,
		            selectFirst: true,
		            minChars: minChars_topic,
                    fixedPos:false,

		            showResult: function (value, data) {
		                return '<span style="">' + hilight(value, $("#topic_live_search").val()) + '</span>';
		            },
		            onItemSelect: function (item) {
		                goto_topic(item);
		            }
            });

        }



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
             $('#ls_contacts').empty();
             var new_contact  = '';
             var len = item.data.length -1;
             var contacts_arr = item.data.clone();
            contacts_arr.shift();
            for (var  i=0; i<contacts_arr.length; i++)
            {
                var arr = contacts_arr[i].split('^');
                var contact_name = arr[0];
                var contact_desc = arr[1];
                var contact_url = arr[2];
                var REMAINDER =i % 4;
               // var new_contact  = '';
                var class_contact;        
                //var url_contact;        
                switch ( contact_name)
                {
                    case 'profile':
                        class_contact =  'leave_search_contact-icon   icon-profile';
                        contact_url = U_PROFILE + user_id;
                        break;
                     case 'pm':
                        class_contact =  'leave_search_contact-icon contact-icon ' + arr[0] + '-icon';
                        contact_url = U_PM + user_id;
                        break;
                     case 'email':
                        class_contact =  'leave_search_contact-icon contact-icon ' + arr[0] + '-icon';
                       contact_url = U_MAIL + user_id;
                        break;
                     case 'jabber':
                        class_contact =  'leave_search_contact-icon contact-icon ' + arr[0] + '-icon';
                       contact_url = U_JABBER + user_id;
                        break;
                   default:
                        class_contact =  'leave_search_contact-icon contact-icon ' + arr[0] + '-icon';
                       contact_url = arr[2];
                }                
                var S_LAST_CELL = ((REMAINDER == 3) || (i == (contacts_arr.length-1)  && contacts_arr.length < 4)) ;
				if (REMAINDER == 0)
                {
                    new_contact = new_contact + '<div>';
                }
                new_contact =  new_contact + '<a href="' + contact_url + '" title="'  + contact_desc + '"';
                if (S_LAST_CELL)
                {
                    var new_contact = new_contact + ' class="last-cell"';
                }
                //debug
//                if (contact_name == 'profile')
//                {
//                    alert ('listenrer: ' + U_PROFILE + '; controller: ' + contact_url);
//                }
                if (contact_name == 'jabber')
                {
                    var new_contact = new_contact + ' onclick="popup(this.href, 750, 320); return false;"';
                }
                var new_contact = new_contact + '>';
                var new_contact = new_contact + '<span class="';
                new_contact = new_contact + class_contact + '"></span></a>';
                if ( REMAINDER == 3 || i ==  (contacts_arr.length-1))
                {
					new_contact = new_contact + '</div>';
				}
            }
            $('#ls_contacts').append(new_contact);
   
            $('#topics_live_search').on('click', function (e) {
                e.preventDefault();
                var usertopic_path = U_USERTOPIC_LS_PATH  + S_FORUM_ID + '/' + user_id;
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
        	var old_value = $("#username_list").val();
			var new_value = (old_value)? old_value+'\n'+item.value : item.value;
			$("#username_list").val(new_value);

    }

    if (!Array.prototype.clone)
    {
        Array.prototype.clone = function () 
        {
            var arr1 = new Array();
            for (var property in this) 
            {
                arr1[property] = typeof (this[property]) == 'object' ? this[property].clone() : this[property]
            }
            return arr1;
        }
    }


})(jQuery);                                                                 // Avoid conflicts with other libraries


