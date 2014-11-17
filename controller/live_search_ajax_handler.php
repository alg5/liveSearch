<?php
/**
*
* @author Alg
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace alg\liveSearch\controller;

class live_search_ajax_handler
{
	protected $thankers = array();
	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\auth\auth $auth, \phpbb\template\template $template, \phpbb\user $user, \phpbb\cache\service $cache, $phpbb_root_path, $php_ext, \phpbb\request\request_interface $request, $table_prefix, $phpbb_container, \phpbb\pagination $pagination, \phpbb\content_visibility $content_visibility, $table_prefix,  \phpbb\profilefields\manager $profilefields_manager)
	{
		$this->config = $config;
		$this->db = $db;
		$this->auth = $auth;
		$this->template = $template;
		$this->user = $user;
		$this->cache = $cache;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
		$this->request = $request;
		$this->phpbb_container = $phpbb_container;
		$this->pagination =  $pagination;
		$this->content_visibility = $content_visibility;
		$this->table_prefix = $table_prefix;
		$this->profilefields_manager = $profilefields_manager;
		$this->return = array(); // save returned data in here
		$this->error = array(); // save errors in here

	}

	public function main($action, $forum, $user)
	{
		// Grab data
		$q = utf8_strtoupper(utf8_normalize_nfc($this->request->variable('q', '',true)));
		if(!$q && $action != 'usertopic' )
		{
			exit();
		}
		$this->user->add_lang_ext('alg/liveSearch', 'live_search');

		switch ($action)
		{
			case 'forum':
				$this->live_search_forum($action, $forum, $q);
			break;
			case 'topic':
			case 'similartopic':
				$this->live_search_topic($action, $forum, $q);
			break;
			case 'user':
			case 'userpm':
				$this->live_search_user($action, $q);
			break;
			case 'usertopic':
				$this->live_search_usertopic( $forum, $user);
			break;

			default:
				$this->error[] = array('error' => $this->user->lang['INCORRECT_SEARCH']);

		}

	}

	private function live_search_forum($action, $forum_id, $q)
	{
		global $phpbb_container;
		$phpbb_content_visibility = $phpbb_container->get('content.visibility');
		$topic_visibility = $phpbb_content_visibility->get_visibility_sql('topic', $forum_id, 't.');
		$sql = "SELECT  f.forum_id, f.forum_name, pf.forum_name as forum_parent_name  " .
				" FROM " . FORUMS_TABLE . " f LEFT JOIN " . FORUMS_TABLE . " pf on f.parent_id = pf.forum_id " .
				" WHERE UPPER(f.forum_name) " . $this->db->sql_like_expression($this->db->get_any_char()  . $this->db->sql_escape($q) . $this->db->get_any_char() ) .
				" ORDER BY f.forum_name";
		$result = $this->db->sql_query($sql);
		$arr_res = $arr_priority1 = $arr_priority2 = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			if ($this->auth->acl_get('f_read', $row['forum_id']) )

			{
				$pos = strpos(utf8_strtoupper($row['forum_name']), $q);
				if ($pos !== false )
				{
					$row['pos'] = $pos;
					if($pos == 0)
					{
						$arr_priority1[] = $row;
					}
					else
					{
						$arr_priority2[] = $row;
					}
				}
			}
		}
		$this->db->sql_freeresult($result);

		$arr_res = array_merge((array) $arr_priority1, (array) $arr_priority2);
		$message = '';
		foreach ($arr_res as $forum_info)
		{
			$forum_id = $forum_info['forum_id'];
			$key = htmlspecialchars_decode($forum_info['forum_name']) ;
			if ($forum_info['forum_parent_name'] )
			{
				$key .= ' (' . htmlspecialchars_decode($forum_info['forum_parent_name']) . ')'  ;
			}
			$message .=  $key . "|$forum_id\n";
		}
		$json_response = new \phpbb\json_response;
			$json_response->send($message);
	}

	private function live_search_topic($action, $forum_id, $q)
	{
		$sql = "SELECT t.topic_id, t.topic_title, t.topic_status, t.topic_moved_id, t.forum_id, f.forum_name " .
		" FROM " . TOPICS_TABLE .
		" t JOIN " . FORUMS_TABLE . " f on t.forum_id = f.forum_id " .
		" WHERE t.topic_status <> " . ITEM_MOVED .
		" AND t.topic_visibility = " . ITEM_APPROVED .
		"  AND UPPER(t.topic_title) " . $this->db->sql_like_expression($this->db->get_any_char() .  $this->db->sql_escape($q) . $this->db->get_any_char()) .
		//$this->build_subforums_search($forum_id) .
		" ORDER BY topic_title";
		$result = $this->db->sql_query($sql);
		$topic_list = array();
		$arr_res = $arr_priority1 = $arr_priority2 = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$pos = strpos(utf8_strtoupper($row['topic_title']), $q);
			if ($pos !== false && $this->auth->acl_get('f_read', $row['forum_id']) )
			{
				$row['pos'] = $pos;
				if($pos == 0)
				{
					$arr_priority1[] = $row;
				}
				else
				{
					$arr_priority2[] = $row;
				}
			}
		}
		$this->db->sql_freeresult($result);

		$arr_res = array_merge((array) $arr_priority1, (array) $arr_priority2);
		$message = '';
		foreach ($arr_res as $topic_info)
		{
			$forum_id = $topic_info['forum_id'];
			$topic_id = ($topic_info['topic_status'] == 2) ? (int) $topic_info['topic_moved_id'] : (int) $topic_info['topic_id'];
			$topic_info['topic_title'] = str_replace('|', ' ', $topic_info['topic_title']);
			$key = htmlspecialchars_decode($topic_info['topic_title']   );
			$forum_name = htmlspecialchars_decode( ' (' . $topic_info['forum_name'] . ')'  );
			$message .= $key . "|$topic_id|$forum_id|$forum_name\n";
		}
		$json_response = new \phpbb\json_response;
		$json_response->send($message);

	}

	private function live_search_user($action, $q)
	{
		$sql =	"SELECT field_name, field_contact_desc, field_contact_url  FROM " . PROFILE_FIELDS_TABLE . " WHERE field_is_contact=1 AND field_active=1 ORDER BY field_order" ;
		$result = $this->db->sql_query($sql);
        $fields_list = '';
        $user_contacts = array();
        while ($row = $this->db->sql_fetchrow($result))
		{
            $user_contacts[] = $row;
            $fields_list .=  ', pf_' . $row['field_name'] ;
        }
 		$this->db->sql_freeresult($result);
  
		// Initialize \phpbb\db\tools object
		//$this->db_tools = new \phpbb\db\tools($this->db);

		$sql = "SELECT u.user_id, u.username, user_allow_pm, user_allow_viewemail, user_type, user_inactive_reason, user_jabber, user_email " . $fields_list . " FROM " . USERS_TABLE .
					" u LEFT JOIN " . PROFILE_FIELDS_DATA_TABLE . " pf on u.user_id = pf.user_id" .
					" WHERE (user_type = " . USER_NORMAL . " OR user_type = " . USER_FOUNDER . ")" .
					" AND username_clean " . $this->db->sql_like_expression(utf8_clean_string( $this->db->sql_escape($q)) . $this->db->get_any_char());
					" ORDER BY username";
                    
                    

		$result = $this->db->sql_query($sql);
        $user_info = array();
        $id_cache = array();
        while ($row = $this->db->sql_fetchrow($result))
        {
            $user_info[] = $row;
            //$id_cache[] = $row['user_id'];
        }
        //// Grab all profile fields from users in id cache for later use - similar to the poster cache
        //$profile_fields_tmp =$this->profilefields_manager->grab_profile_fields_data($id_cache);
        
        //// filter out fields not to be displayed 
        //$profile_fields_cache = array();
        //foreach ($profile_fields_tmp as $profile_user_id => $profile_fields)
        //{
        //    $profile_fields_cache[$profile_user_id] = array();
        //    foreach ($profile_fields as $used_ident => $profile_field)
        //    {
        //        if ($profile_field['data']['field_is_contact'] == 1 && $profile_field['data']['field_active'] ==1)
        //        {
        //            $profile_fields_cache[$profile_user_id][$used_ident] = $profile_field;
        //        }
        //    }
        //}
        //unset($profile_fields_tmp);
        //print_r($profile_fields_cache);

       
		$message = '';
		//while ($row = $this->db->sql_fetchrow($result))
        foreach($user_info as $row)
		{
			$user_id = (int) $row['user_id'];
			$message .= $row['username'] . '|' . $user_id;
            if( $this->user->data['user_id']!= ANONYMOUS)
            {
                //add user profile
                $url = append_sid("{$this->phpbb_root_path}memberlist.$this->php_ext", 'mode=viewprofile&amp;u=' . $user_id);
                $message .= '|profile^' . $this->user->lang['LIVE_SEARCH_GO_PROFILE'] . '^' . $url;
            }

            $url = $this->get_url_pm($row);
            if($url)
            {
                $message .= '|pm^' . $this->user->lang['SEND_PRIVATE_MESSAGE'] . '^' . $url ;
            }
            $url = $this->get_url_email($row);

            if($url)
            {
                $message .= '|email^' . $this->user->lang['SEND_EMAIL'] . '^' . $url ;
            }
            $url = $this->get_url_jabber($row);
            if($url)
            {
                $message .= '|jabber^' . $this->user->lang['SEND_EMAIL'] . '^' . $url ;
            }
            
            foreach ($row as $f_name=>$f_value)
            {
                if (strpos($f_name , 'pf_phpbb_') === 0 && $f_value != '')
                {
                    $contact = $this->build_user_contact_by_name($user_contacts, $f_name, $f_value);
                    $message .= "|$contact";
                }
                
            }
            $message .= "\n";

		}
		$this->db->sql_freeresult($result);
		$json_response = new \phpbb\json_response;
			$json_response->send($message);

	}

	private function live_search_usertopic($forum, $user)
	{
		include_once($this->phpbb_root_path . 'includes/functions_display.' . $this->php_ext);
		$this->user->add_lang(array( 'search'));
		$forum_id = $forum;
		$author_id = $user;

		// Grab icons
		$icons = $this->cache->obtain_icons();

		// define some vars for urls
		// A single wildcard will make the search results look ugly
		$limit_days		= array(0 => $this->user->lang['ALL_RESULTS'], 1 => $this->user->lang['1_DAY'], 7 => $this->user->lang['7_DAYS'], 14 => $this->user->lang['2_WEEKS'], 30 => $this->user->lang['1_MONTH'], 90 => $this->user->lang['3_MONTHS'], 180 => $this->user->lang['6_MONTHS'], 365 => $this->user->lang['1_YEAR']);
		$sort_by_text	= array('a' => $this->user->lang['SORT_AUTHOR'], 't' => $this->user->lang['SORT_TIME'], 'f' => $this->user->lang['SORT_FORUM'], 'i' => $this->user->lang['SORT_TOPIC_TITLE'], 's' => $this->user->lang['SORT_POST_SUBJECT']);

		$s_limit_days = $s_sort_key = $s_sort_dir = $u_sort_param = '';
		gen_sort_selects($limit_days, $sort_by_text, $sort_days, $sort_key, $sort_dir, $s_limit_days, $s_sort_key, $s_sort_dir, $u_sort_param);

		$show_results	= 'topics';
		$u_search = append_sid("{$this->phpbb_root_path}liveSearch/usertopic/$forum_id/$author_id");

		// Define initial vars
		$page_title = $this->user->lang['SEARCH'];
		$template_html = 'live_search_results.html';
		$start = $this->request->variable('start', 0);
		$per_page = $this->config['posts_per_page'];
		$default_key = 't.topic_last_post_time';
		$sort_key = $this->request->variable('sk', $default_key);
		$sort_dir = $this->request->variable('sd', 'desc');
		$forum_id = $forum;
		$author_id = $user;

		// clear arrays
		$id_ary = array();
		$author_id_ary[] = $author_id;

		// Select which method we'll use to obtain the post_id or topic_id information
		$error = false;
		$search_type = $this->config['search_type'];
		$search = new $search_type($error, $this->phpbb_root_path, $this->php_ext, $this->auth, $this->config, $this->db, $author_id);
		if ($error)
		{
			trigger_error($error);
		}

		// Which forums should not be searched? Author searches are also carried out in unindexed forums
		$ex_fid_ary = array_keys($this->auth->acl_getf('!f_read', true));
		$not_in_fid = (sizeof($ex_fid_ary)) ? 'WHERE ' . $this->db->sql_in_set('f.forum_id', $ex_fid_ary, true) . " OR (f.forum_password <> '' AND fa.user_id <> " . (int) $this->user->data['user_id'] . ')' : "";

		// find out in which forums the user is allowed to view posts
		$m_approve_posts_fid_sql = $this->content_visibility->get_global_visibility_sql('post', $ex_fid_ary, 'p.');
		$m_approve_topics_fid_sql = $this->content_visibility->get_global_visibility_sql('topic', $ex_fid_ary, 't.');
		// define some variables needed for retrieving post_id/topic_id information
		$sort_by_sql = array('a' => 'u.username_clean', 't' => (($show_results == 'posts') ? 'p.post_time' : 't.topic_last_post_time'), 'f' => 'f.forum_id', 'i' => 't.topic_title', 's' => (($show_results == 'posts') ? 'p.post_subject' : 't.topic_title'));

		$total_match_count = 0;
		// Set limit for the $total_match_count to reduce server load
		$total_matches_limit = 1000;
		$found_more_search_matches = false;

		// make sure that some arrays are always in the same order
		sort($ex_fid_ary);
		sort($author_id_ary);

		//$l_search_title = $this->user->lang['SEARCH_ACTIVE_TOPICS'];
		$sql = "SELECT count(t.topic_id) as total_count, u.username" .
					" FROM " .TOPICS_TABLE . " t LEFT JOIN " . FORUMS_TABLE . " f ON (f.forum_id = t.forum_id)" .
					" LEFT JOIN " . TOPICS_TRACK_TABLE . " tt ON (tt.user_id = " . $author_id .
					" AND t.topic_id = tt.topic_id) " .
					" LEFT JOIN " . FORUMS_TRACK_TABLE . " ft ON (ft.user_id = " . $author_id .
					" AND ft.forum_id = f.forum_id) " .
					" LEFT JOIN " . USERS_TABLE . " u ON t.topic_poster = u.user_id" .
					" WHERE  t.topic_status <> " . ITEM_MOVED .
					" AND t.topic_visibility = " . ITEM_APPROVED .
					" AND t.topic_poster = " . $author_id . $this->build_subforums_search($forum_id) ;
					if (sizeof($ex_fid_ary))
					{
						$sql .= " AND " . $this->db->sql_in_set('f.forum_id', $ex_fid_ary, true);
					}
                    if ($forum_id)
                    {
						$sql .= $this->build_subforums_search($forum_id) ;
                    }
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$total_count = (int) $row['total_count'];
		$username = $row['username'];
		$this->db->sql_freeresult($result);
		$forum_name = '';
		$forum_has_subforums = false;
		if($forum_id)
		{
			$sql = 	" SELECT forum_name, left_id, right_id FROM " . FORUMS_TABLE .  " WHERE forum_id=" . $forum_id;
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$forum_name = $row['forum_name'] ;
			$forum_has_subforums = ($row['right_id'] - $row['left_id'] > 1) ? true : false ;
			$this->db->sql_freeresult($result);
		}

		if ($total_count)
		{
				$sql = "SELECT t.*, f.forum_id, f.forum_name, tt.mark_time, ft.mark_time as f_mark_time" .
								" FROM " .TOPICS_TABLE . " t LEFT JOIN " . FORUMS_TABLE . " f ON (f.forum_id = t.forum_id)" .
								" LEFT JOIN " . TOPICS_TRACK_TABLE . " tt ON (tt.user_id = " . $author_id .
								" AND t.topic_id = tt.topic_id) " .
								" LEFT JOIN " . FORUMS_TRACK_TABLE . " ft ON (ft.user_id = " . $author_id .
								" AND ft.forum_id = f.forum_id) " .
								" WHERE t.topic_status <> " . ITEM_MOVED .
								" AND t.topic_visibility = " . ITEM_APPROVED .
								" AND t.topic_poster = " . $author_id ;
								if (sizeof($ex_fid_ary))
								{
									$sql .= " AND " . $this->db->sql_in_set('f.forum_id', $ex_fid_ary, true);
								}
                                if ($forum_id)
                                {
									$sql .= $this->build_subforums_search($forum_id) ;
                                }
								$sql .= " ORDER BY " . $sort_key . " " . $sort_dir;
						$result = $this->db->sql_query_limit($sql, $per_page, $start);
						//print_r($sql);
				$row_count = 0;
				$rowset = array();
				while ($row = $this->db->sql_fetchrow($result))
				{
					$ls_forum_id = (int) $row['forum_id'];
					$ls_topic_id = (int) $row['topic_id'];
					$rowset[$ls_topic_id] = $row;
					if ($this->auth->acl_get('f_read',$ls_forum_id))
					{
						$row_count++;
						// Get topic tracking info
						if ($this->user->data['is_registered'] && $this->config['load_db_lastread'] && !$this->config['ls_topics_cache'])   //todo ls_topics_cache
						{
							$topic_tracking_info = get_topic_tracking($ls_forum_id, $ls_topic_id, $rowset, array($ls_forum_id => $row['f_mark_time']));
						}
						else if ($this->config['load_anon_lastread'] || $this->user->data['is_registered'])
						{
							$topic_tracking_info = get_complete_topic_tracking($ls_forum_id, $ls_topic_id);

							if (!$this->user->data['is_registered'])
							{
								$this->user->data['user_lastmark'] = (isset($tracking_topics['l'])) ? (int) (base_convert($tracking_topics['l'], 36, 10) + $this->config['board_startdate']) : 0;
							}
						}

						$replies = $this->content_visibility->get_count('topic_posts', $row, $ls_forum_id) - 1;
						$folder_img = $folder_alt = $topic_type = '';
						$unread_topic = (isset($topic_tracking_info[$ls_topic_id]) && $row['topic_last_post_time'] > $topic_tracking_info[$ls_topic_id]) ? true : false;

						topic_status($row, $replies, $unread_topic, $folder_img, $folder_alt, $topic_type);
						//topic_status($row, $replies, (isset($topic_tracking_info[$forum_id][$row['topic_id']]) && $row['topic_last_post_time'] > $topic_tracking_info[$forum_id][$row['topic_id']]) ? true : false, $folder_img, $folder_alt, $topic_type);

						$topic_unapproved = ($row['topic_visibility'] == ITEM_UNAPPROVED && $this->auth->acl_get('m_approve', $ls_forum_id)) ? true : false;
						//$topic_unapproved = (($row['topic_visibility'] == ITEM_UNAPPROVED || $row['topic_visibility'] == ITEM_REAPPROVE) && $this->auth->acl_get('m_approve', $ls_forum_id)) ? true : false;

						$posts_unapproved = ($row['topic_visibility'] == ITEM_APPROVED && $row['topic_posts_unapproved'] && $this->auth->acl_get('m_approve', $ls_forum_id)) ? true : false;
						//$posts_unapproved = ($row['topic_visibility'] == ITEM_APPROVED && $row['topic_posts_unapproved'] && $this->auth->acl_get('m_approve', $forum_id)) ? true : false;

						$result_topic_id = $row['topic_id'];
						$view_topic_url_params = "f=$forum_id&amp;t=$result_topic_id" ;
						$view_topic_url = append_sid("{$this->phpbb_root_path}viewtopic.$this->php_ext", $view_topic_url_params);

				$unread_topic = (isset($topic_tracking_info[$forum_id][$row['topic_id']]) && $row['topic_last_post_time'] > $topic_tracking_info[$forum_id][$row['topic_id']]) ? true : false;
				$topic_deleted = $row['topic_visibility'] == ITEM_DELETED;
				$u_mcp_queue = ($topic_unapproved || $posts_unapproved) ? append_sid("{$this->phpbb_root_path}mcp.$this->php_exp", 'i=queue&amp;mode=' . (($topic_unapproved) ? 'approve_details' : 'unapproved_posts') . "&amp;t=$result_topic_id", true, $this->user->session_id) : '';
				$u_mcp_queue = (!$u_mcp_queue && $topic_deleted) ? append_sid("{$this->phpbb_root_path}mcp.$this->php_exp", "i=queue&amp;mode=deleted_topics&amp;t=$result_topic_id", true, $this->user->session_id) : '';

				$this->template->assign_block_vars('searchresults', array (
					'TOPIC_TITLE'		=> censor_text($row['topic_title']),
					'FORUM_TITLE'		=> $row['forum_name'],
					'TOPIC_AUTHOR_FULL'			=> get_username_string('full', $row['topic_poster'], $row['topic_first_poster_name'], $row['topic_first_poster_colour']),
					'FIRST_POST_TIME'			=> $this->user->format_date($row['topic_time']),
					'S_ROW_COUNT'		=> $row,
					'TOPIC_REPLIES'		=> $replies,
					'TOPIC_VIEWS'		=> $row['topic_views'],
					'LAST_POST_AUTHOR_FULL'		=> get_username_string('full', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),
					'LAST_POST_TIME'			=> $this->user->format_date($row['topic_last_post_time']),
					'ATTACH_ICON_IMG'		=> ($this->auth->acl_get('u_download') && $this->auth->acl_get('f_download', $forum_id) && $row['topic_attachment']) ? $this->user->img('icon_topic_attach', $this->user->lang['TOTAL_ATTACHMENTS']) : '',
					'S_UNREAD_TOPIC'		=> $unread_topic,
					'S_TOPIC_UNAPPROVED'	=> $topic_unapproved,
					'S_POSTS_UNAPPROVED'	=> $posts_unapproved,
					'TOPIC_IMG_STYLE'		=> $folder_img,
					'TOPIC_FOLDER_IMG'		=> $this->user->img($folder_img, $folder_alt),
					'TOPIC_FOLDER_IMG_ALT'	=> $this->user->lang[$folder_alt],

					'TOPIC_ICON_IMG'		=> (!empty($icons[$row['icon_id']])) ? $icons[$row['icon_id']]['img'] : '',
					'TOPIC_ICON_IMG_WIDTH'	=> (!empty($icons[$row['icon_id']])) ? $icons[$row['icon_id']]['width'] : '',
					'TOPIC_ICON_IMG_HEIGHT'	=> (!empty($icons[$row['icon_id']])) ? $icons[$row['icon_id']]['height'] : '',
					'UNAPPROVED_IMG'		=> ($topic_unapproved || $posts_unapproved) ? $this->user->img('icon_topic_unapproved', ($topic_unapproved) ? 'TOPIC_UNAPPROVED' : 'POSTS_UNAPPROVED') : '',
					'S_TOPIC_DELETED'		=> $topic_deleted,
					'S_TOPIC_REPORTED'		=> (!empty($row['topic_reported']) && $this->auth->acl_get('m_report', $forum_id)) ? true : false,
					'S_HAS_POLL'			=> ($row['poll_start']) ? true : false,

						'NEWEST_POST_IMG'	=> $this->user->img('icon_topic_newest', 'VIEW_NEWEST_POST'),
					'U_NEWEST_POST'			=> append_sid("{$this->phpbb_root_path}viewtopic.$this->php_ext", $view_topic_url_params . '&amp;view=unread') . '#unread',
					'U_VIEW_TOPIC'		=> $view_topic_url,
					'U_VIEW_FORUM'		=> append_sid("{$this->phpbb_root_path}viewforum.$this->php_ext", 'f=' . $row['forum_id']),
					'U_MCP_QUEUE'			=> $u_mcp_queue,
					'U_LAST_POST'			=> append_sid("{$this->phpbb_root_path}viewtopic.$this->php_ext", $view_topic_url_params . '&amp;p=' . $row['topic_last_post_id']) . '#p' . $row['topic_last_post_id'],
						));
				$this->pagination->generate_template_pagination($view_topic_url, 'searchresults.pagination', 'start', $replies + 1, $this->config['posts_per_page'], 1, true, true);
					//if ($topic_id && ($topic_id == $result_topic_id))
					//{
					//	 $template->assign_vars(array(
					//		  'SEARCH_TOPIC'		=> $topic_title,
					//		  'L_RETURN_TO_TOPIC'	=> $user->lang('RETURN_TO', $topic_title),
					//		  'U_SEARCH_TOPIC'	=> $view_topic_url
					//	 ));
					//}
					//$row++;
				}
				}
			//$pagination->generate_template_pagination($view_topic_url, 'pagination', 'start', $total_count + 1, $this->config['posts_per_page'], 1, true, true);
			//print_r($u_search);
				$this->pagination->generate_template_pagination($u_search, 'pagination', 'start', $total_count, $per_page, $start);

				}
				if ($forum_id)
				{
					$res_txt = sprintf($this->user->lang['LIVESEARCH_USERTOPIC_RESULT_IN_FORUM'], $username, $forum_name);
					if ($forum_has_subforums)
					{
							$res_txt .= $this->user->lang['LIVESEARCH_USERTOPIC_RESULT_IN_SUBFORUMS'];
					}
				}
				else
				{
					$res_txt = sprintf($this->user->lang['LIVESEARCH_USERTOPIC_RESULT'], $username);
				}
			$l_search_matches =  $this->user->lang('FOUND_SEARCH_MATCHES', $total_count) ;
			$this->template->assign_vars(array(
			'S_SHOW_TOPICS'		=> 1,
			'SEARCH_MATCHES'	=>  $total_count == 0 ? '' : $this->user->lang('FOUND_SEARCH_MATCHES', $total_count) ,
			'SEARCH_MATCHES_TXT'	=>	$res_txt,
			'PAGE_NUMBER'		=> $total_count == 0 ?  0 : $this->pagination->on_page($total_count, $this->config['posts_per_page'], $start),
			'TOTAL_MATCHES'		=> $total_count,
			'REPORTED_IMG'		=> $this->user->img('icon_topic_reported', 'TOPIC_REPORTED'),
			'UNAPPROVED_IMG'	=> $this->user->img('icon_topic_unapproved', 'TOPIC_UNAPPROVED'),
			'DELETED_IMG'			 => $this->user->img('icon_topic_deleted', 'TOPIC_DELETED'),
			'POLL_IMG'				 => $this->user->img('icon_topic_poll', 'TOPIC_POLL'),
			'LAST_POST_IMG'		=> $this->user->img('icon_topic_latest', 'VIEW_LATEST_POST'),

			));

		page_header($page_title);

		$this->template->set_filenames(array(
			'body' => $template_html));

		make_jumpbox(append_sid("{$this->phpbb_root_path}viewforum.$this->php_ext"));
		page_footer();
		return new Response($this->template->return_display('body'), 200);

	}

	private function build_subforums_search($forum_id)
	{
		if ($forum_id == 0)
		{
			return '';
		}
		$sql = "SELECT left_id, right_id " .
				" FROM " . FORUMS_TABLE .
				" WHERE forum_id = " . $forum_id ;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		$sql = "SELECT forum_id " .
				" FROM " . FORUMS_TABLE .
				" WHERE left_id >= " . $row['left_id'] .
				" AND right_id <= " .  $row['right_id'] .
				" ORDER BY  left_id" ;
		$result = $this->db->sql_query($sql);

		$subforums = ' AND t.forum_id IN (';
		while ($row = $this->db->sql_fetchrow($result))
		{
			$subforums .= ( $row['forum_id'] . ',');
		}
		$subforums = substr($subforums, 0, -1) . " )";
		return $subforums;
	}
    
    private function build_user_contact_by_name($user_contacts, $f_name, $f_value)
    {
        $this->user->add_lang('memberlist');
        foreach ($user_contacts as $contact)
        {
            if ($contact['field_name'] == str_replace('pf_', '', $f_name))
            {
                return $contact['field_name']  . '^' . $this->user->lang[$contact['field_contact_desc']] . '^' . sprintf($contact['field_contact_url'], $f_value);
            }
        }
        return '';
        
    }
    
    private function get_url_pm($seeking_user)
    {
        if( $this->user->data['user_id'] == ANONYMOUS)
        {
            return '';
        }
        
    	//$allow_pm = $this->config['allow_privmsg'] && $this->auth->acl_get('u_sendpm') && ($row['user_allow_pm'] || $this->auth->acl_gets('a_', 'm_') || $this->auth->acl_getf_global('m_')) ? 1 :0;
        $ids[] = $seeking_user['user_id'];
	    // Can this user receive a Private Message?
	    $can_receive_pm = (
		    // They must be a "normal" user
		    $seeking_user['user_type'] != USER_IGNORE &&

		    // They must not be deactivated by the administrator
		    ($seeking_user['user_type'] != USER_INACTIVE || $seeking_user['user_inactive_reason'] != INACTIVE_MANUAL) &&

		    // They must be able to read PMs
            $this->auth->acl_get_list($ids, 'u_readpm') &&

		    // They must not be permanently banned (don't need. we give only active users)
		    //!in_array($seeking_user['user_id'], $permanently_banned_users = phpbb_get_banned_user_ids(array_keys($user_cache), false)) &&

		    // They must allow users to contact via PM
		    (($this->auth->acl_gets('a_', 'm_') || $this->auth->acl_getf_global('m_')) || $seeking_user['allow_pm'])
	    );

	$u_pm = '';

	if ($this->config['allow_privmsg'] && $this->auth->acl_get('u_sendpm') && $can_receive_pm)
	{
		$u_pm = append_sid("{$this->phpbb_root_path}ucp.$this->php_ext", 'i=pm&amp;mode=compose&amp;u' . $seeking_user['user_id']);
	}
    return $u_pm;
    
    }
    
    private function get_url_email($seeking_user)
    {
        $seeking_user_id = $seeking_user['user_id'];
        $url = '';
 		if ( $this->user->data['user_id'] != ANONYMOUS && (!empty($seeking_user['user_allow_viewemail']) && $this->auth->acl_get('u_sendemail')) || $this->auth->acl_get('a_email'))
		{
			$url = ($this->config['board_email_form'] && $this->config['email_enable']) ? append_sid("{$this->phpbb_root_path}memberlist.$this->php_ext", "email&amp;u=$seeking_user_id"): (($this->config['board_hide_emails'] && !$this->auth->acl_get('a_email')) ? '' : 'mailto:' . $seeking_user['user_email']);
			//$url = ($         config['board_email_form'] && $        config['email_enable']) ? append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=email&amp;u=$poster_id") : (($config['board_hide_emails'] && !$auth->acl_get('a_email')) ? '' : 'mailto:' . $row['user_email']);
            //append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=email&amp;u=$poster_id") : (($config['board_hide_emails'] && !$auth->acl_get('a_email')) ? '' : 'mailto:' . $row['user_email']);
		}
		return $url;
   }
    
   private function get_url_jabber($seeking_user)
   {
        $seeking_user_id = $seeking_user['user_id'];
        $url = '';
        if (!$this->user->data['user_id'] != ANONYMOUS && $seeking_user['user_jabber'] && $this->auth->acl_get('u_sendim'))
        {
   			$url = append_sid("$this->phpbb_root_path}memberlist.$this->php_ext", "mode=contact&amp;action=jabber&amp;u=$seeking_user_id");
        }
        return $url;
   }
   
}
