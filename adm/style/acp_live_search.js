    (function ($) {    // Avoid conflicts with other libraries
        enmGrpAction = {
            GROUP_LOCAL:1,
            FORUM_LOCAL:2,
            GROUP_POSITION_GENERAL:3,
            GROUP_POSITION_SPECIAL:4,
            GROUP_MANAGE:5,
            USER_GROUPS:6,
            ADMIN_GLOBAL:7,
            GROUP_EMAIL:8,

        };
        $().ready(function () {
            changeEnable($('#live_search_on_off_forum'), 'setting_f');
            changeEnable($('#live_search_on_off_topic'), 'setting_t');
            changeEnable($('#live_search_on_off_user'), 'setting_u');
            changeEnable($('#live_search_on_off_similartopic'), 'setting_st');
            changeEnable($('#live_search_on_off_acp'), 'setting_acp');
            changeEnable($('#live_search_on_off_mcp'), 'setting_mcp');

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
            $('#live_search_on_off_acp').on('change', function () {
                changeEnable(this, 'setting_acp');
            });
            $('#live_search_on_off_mcp').on('change', function () {
                changeEnable(this, 'setting_mcp');
            });

            if (S_LIVESEARCH_ACP)
            {   
                initAcpLiveSearch();
            }

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
        function initAcpLiveSearch()
        {
            if (S_FORUM_PRUNE)
            {
                var elem =  $("#forumsearch_ls");
                var cbo = $("#forum");
                forum_search(elem, cbo);
            }
            if (S_FORUM_MANAGE)
            {
                var elem =  $("#forumsearch_ls");
                var cbo = $("#fselect").find ("select[name='parent_id']");
                forum_search(elem, cbo);
            }
            if (S_FORUM_PARENT_MANAGE)
            {
                var elem =  $("#forumsearch_ls");
                var cbo = $("#forumedit").find ("select[name='forum_parent_id']");
                forum_search(elem, cbo);
            }

            if (S_FORUM_LOG)
            {
               var elem =  $("#forumsearch_ls");
                var cbo = $("#list").find ("select[name='f']");
                forum_search(elem, cbo);
            }

            if (S_FORUM_MULTIPLE)
            {
             var elem =  $("#forumPermMultipleSearch");
                var cbo;
               if(S_USER_GROUP_LOCAL || S_GROUP_LOCAL)
                {
                    cbo = $("#forum");
                }
                else
                {
                     cbo = $("#select_victim");
                }
                forum_search(elem, cbo);

                var elem =  $("#forumPermMultipleSubforumSearch");
                var cbo;
               if(S_USER_GROUP_LOCAL || S_GROUP_LOCAL)
                {
                    cbo = $("#forum");
                }
                else
                {
                     cbo = $("#select_subforum");
                }
                forum_search(elem, cbo);
            }
            if(S_FORUM_PERMISSIONS_COPY)
            {
               var elem =  $("#forumsearch_ls_from");
                var cbo = $("#src_forum");
                forum_search(elem, cbo);

                var elem =  $("#forumsearch_ls_to");
                var cbo = $("#dest_forums");
                forum_search(elem, cbo);
            }
            if(  S_USER_PRUNE)
            {
                var elem = $("#usersearch_ls");
                var txtarea =  $('#users');
                user_search(elem, txtarea);
            }
            if( (S_SELECT_USER && S_CAN_SELECT_USER ) && S_SETTING_USER_LOCAL)
            {
                $("#username").addClass("ls-acp-search inputbox search").attr({
                  autocomplete: "off",
                  type: "search",
                  placeholder: L_LIVESEARCH_USER_TXT,
                  title: L_LIVESEARCH_USER_T
                });
                var elem = $("#username");
                var txtarea = null;
                user_search(elem, txtarea);
            }

            if(S_GROUP_LOCAL)
            {
                var elem = $("#groupsearch_ls");
                group_search(elem, enmGrpAction.GROUP_LOCAL);
            }
            if( S_FORUM_LOCAL)
            {
               var elem = $("#usersearch_ls");
                var txtarea = $('#username');
                user_search(elem, txtarea);
                
                elem = $("#groupsearch_ls");
                group_search(elem, enmGrpAction.FORUM_LOCAL);

            }
            if(S_SELECT_USER && S_FIND_USER_ACP)
            {
              $("#username").addClass("inputbox search").attr({type: "search", placeholder: L_LIVESEARCH_USER_TXT, title: L_LIVESEARCH_USER_T, autocomplete:"off"});
                var elem = $("#username");
                var txtArea = null;
                user_search(elem, txtArea)
            }

            if(S_USER_GROUPS && S_GROUP_OPTIONS)
            {
                var elem = $("#groupsearch_ls");
                group_search(elem, enmGrpAction.USER_GROUPS);
            }

            if(S_SETTING_USER_GLOBAL)
            {
              $("#username").addClass("inputbox search").attr({type: "search", placeholder: L_LIVESEARCH_USER_TXT, title: L_LIVESEARCH_USER_T, autocomplete:"off"});
                var elem = $("#username");
                var txtarea = null;
                user_search(elem, txtarea);
            }
            if(S_GROUP_MANAGE)
            {
                var elem = $("#groupsearch_ls");
                group_search(elem, enmGrpAction.GROUP_MANAGE);

                var elem = $("#usersearch_ls");
                var txtarea = $('#usernames');;
                user_search(elem, txtarea);
            }
            if(S_GROUP_POSITION)
            {
                var elem = $("#groupsearch_ls_general");
                group_search(elem, enmGrpAction.GROUP_POSITION_GENERAL);

                var elem = $("#groupsearch_ls_special");
                group_search(elem, enmGrpAction.GROUP_POSITION_SPECIAL);
            }



            if(S_USER_BAN)
            {
                var elem = $("#usersearch_ls");
                var txtarea = $('#ban');
                user_search(elem, txtarea);
            }
            if(S_ADMIN_GLOBAL)
            {
               $("#username").addClass("inputbox search").attr({type: "search", placeholder: L_LIVESEARCH_USER_TXT, title: L_LIVESEARCH_USER_T, autocomplete:"off"});
                var elem = $("#username");
                var txtarea = null;
                user_search(elem, txtarea);

                elem = $("#groupsearch_ls");
                group_search(elem, enmGrpAction.ADMIN_GLOBAL);
            }
            if(S_EMAIL)
            {
                var elem = $("#groupsearch_ls");
                group_search(elem, enmGrpAction.GROUP_EMAIL);

                elem = $("#usersearch_ls");
                var txtarea = $('#usernames');;
                user_search(elem, txtarea);
            }
            $("#btnGroupManage").on("click", function(e)
            {
                e.preventDefault();

                var chk = $('input[name=group_manage]:checked').val();
                var action = $('#acp_groups').attr('action');
                switch (parseInt(chk))
                {
                    case 0:
                        action += '&action=edit&g=' + $("#hGroupManage").val();
                        break;
                    case 1:
                        action += '&action=list&g=' + $("#hGroupManage").val();
                        break;
                    case 2:
                        action += '&action=delete&g=' + $("#hGroupManage").val();
                        break;
                }
                window.location.href =  action;
            });

        }




    function hilight(value, term) {
        return value.replace(new RegExp("(?![^&;]+;)(?!<[^<>]*)(" + term.replace(/([\^\$\(\)\[\]\{\}\*\.\+\?\|\\])/gi, "\\$1") + ")(?![^<>]*>)(?![^&;]+;)", "gi"), "<strong>$1</strong>");
    }


//*************common hande functions*****
    function select_combo(item, cbo)
    {
       $(cbo).find("option[value='" + item.data[0] + "']").attr("selected","selected");
    }

    function add_user_to_textarea(item, txtarea)
    {
        var user = item.value;
        var new_val = $.trim( $(txtarea).val());
        if (new_val.indexOf(user) <0)
        {
            if (new_val != ''  ) 
                new_val = new_val + '\n';
            new_val = new_val + item.value;
        }
        $(txtarea).val(new_val);
    }

    function forum_search(elem, cbo)
    {
                $(elem).autocomplete_ls(
                {
                    url: U_ADMIN_LIVESEARCH_PATH + 'forum/0/0/0',
                    sortResults: false,
                    width: 600,
                    maxItemsToShow: LIVE_SEARCH_MAX_ITEMS_TO_SHOW_ACP,
                    selectFirst: true,
                    fixedPos:false,
                    minChars: LIVE_SEARCH_MIN_NUM_SYMBLOLS_FORUM_ACP,
                    showResult: function (value, data) {
                        return '<span style="">' + hilight(value, $(elem).val()) + '</span>';
                    },
                    onItemSelect: function (item) {
                    select_combo(item, cbo);
                    },
    
                });    
    }

    function user_search(elem, txtarea)
    {
        $(elem).autocomplete_ls(
        {
            url: U_ADMIN_LIVESEARCH_PATH + 'user/0/0/0',
            sortResults: false,
            width: 600,
            maxItemsToShow: LIVE_SEARCH_MAX_ITEMS_TO_SHOW_ACP,
            selectFirst: true,
            fixedPos:false,
            minChars: LIVE_SEARCH_MIN_NUM_SYMBLOLS_USER_ACP,
            showResult: function (value, data) {
                return '<span style="">' + hilight(value, $(elem).val()) + '</span>';
            },
            onItemSelect: function (item) {
                if(txtarea !=null)
                    add_user_to_textarea(item, txtarea);
            },
    
        });
    
    }

    function group_search(elem, grpAction)
    {
        $(elem).autocomplete_ls(
        {
            url: U_ADMIN_LIVESEARCH_PATH + 'group/0/0/0',
            sortResults: false,
            width: 600,
            maxItemsToShow: LIVE_SEARCH_MAX_ITEMS_TO_SHOW_ACP,
            selectFirst: true,
            fixedPos:false,
            minChars: LIVE_SEARCH_MIN_NUM_SYMBLOLS_GROUP_ACP,
            showResult: function (value, data) {
                return '<span style="">' + hilight(value, $(elem).val()) + '</span>';
            },
            onItemSelect: function (item) {
                    group_action(item, grpAction);
            },
    
        });
   
    }
    function group_action(item, grpAction)
    {
        switch (grpAction)
        {
            case enmGrpAction.GROUP_LOCAL:
                var cbo = $("#select_victim");
                select_combo(item, cbo);
                break;
            case enmGrpAction.FORUM_LOCAL:
               var new_option = '<option value="' + item.data[0] + '">' + item.value + '</option>';
                var id = item.data[0];
                $("#groups").find("select[name='group_id[]']").append(new_option);
                $("#add_groups").find("select[name='group_id[]'] option[value='" + id + "']").remove();
                break;
            case enmGrpAction.GROUP_POSITION_GENERAL:
                $("#hGroupManage").val(item.data[0]);
                var cbo = $("#teampage_add_group").find ("select[name='g']");
                select_combo(item, cbo);
                break;
            case enmGrpAction.GROUP_POSITION_SPECIAL:
                $("#hGroupManage").val(item.data[0]);
                var cbo = $("#legend_add_group").find ("select[name='g'] ");
                select_combo(item, cbo);
                break;
            case enmGrpAction.GROUP_MANAGE:
                $("#hGroupManage").val(item.data[0]);
                break;
            case enmGrpAction.USER_GROUPS:
                var cbo = $("#user_groups").find ("select[name='g']");
                select_combo(item, cbo);
                break;
            case enmGrpAction.ADMIN_GLOBAL:
                var cbo = $("#group_select");
                select_combo(item, cbo);
                break;
            case enmGrpAction.GROUP_EMAIL:
                var cbo = $("#group");
                select_combo(item, cbo);
                break;

        }
    }
//*************end common hande functions*****


    })(jQuery);                                                    // Avoid conflicts with other libraries
