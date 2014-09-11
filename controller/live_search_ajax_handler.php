<?php
/**
*
* @author Alg
* @version 1.0.0	$
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace alg\liveSearch\controller;

/**
* @ignore
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

class live_search_ajax_handler
{
protected $thankers = array();
   public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\auth\auth $auth, \phpbb\template\template $template, \phpbb\user $user, \phpbb\cache\driver\driver_interface $cache, $phpbb_root_path, $php_ext, \phpbb\request\request_interface $request, $table_prefix, $phpbb_container)
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
		$this->return = array(); // save returned data in here
		$this->error = array(); // save errors in here

	}

	public function main($action, $forum, $user)
	{
		// Grab data
		$q = utf8_strtoupper(utf8_normalize_nfc(request_var('q', '',true)));
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
				$this->live_search_topic($action, $forum, $q);
			break;
			case 'user':
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
				" WHERE UPPER(f.forum_name) " . $this->db->sql_like_expression($this->db->get_any_char()  . $q . $this->db->get_any_char() ) .
				" ORDER BY f.forum_name";
		$result = $this->db->sql_query($sql);
		$arr_res = $arr_priority1 = $arr_priority2 = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			if ($phpbb_content_visibility->get_visibility_sql('topic', $row['forum_id'], 't.'))
			{
				$pos = strpos(utf8_strtoupper($row['forum_name']), $q);
				if ($pos !== false ) 
				{
					$row['pos'] = $pos;
					if($pos == 0)
						$arr_priority1[] = $row;
					else
						$arr_priority2[] = $row;
				}
			}
		}
		$this->db->sql_freeresult($result);
		
		$arr_res = array_merge((array)$arr_priority1, (array)$arr_priority2);
		$message = '';
		foreach ($arr_res as $forum_info)
		{
			$forum_id = $forum_info['forum_id'];
 			$key = htmlspecialchars_decode($forum_info['forum_name'] . ' (' . $forum_info['forum_parent_name'] . ')'  );
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
		"  AND UPPER(t.topic_title) " . $this->db->sql_like_expression($this->db->get_any_char() . $q . $this->db->get_any_char()) .
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
					$arr_priority1[] = $row;
				else
					$arr_priority2[] = $row;
			}
		}
		$this->db->sql_freeresult($result);
		
		$arr_res = array_merge((array)$arr_priority1, (array)$arr_priority2);
		$message = '';
		foreach ($arr_res as $topic_info)
		{
			$forum_id = $topic_info['forum_id'];
			$topic_id = ($topic_info['topic_status'] == 2) ? (int)$topic_info['topic_moved_id'] : (int)$topic_info['topic_id'];
			$topic_info['topic_title'] = str_replace('|', ' ', $topic_info['topic_title']);
 			$key = htmlspecialchars_decode($topic_info['topic_title'] . ' (' . $topic_info['forum_name'] . ')'  );
			$message .= $key . "|$topic_id|$forum_id\n";
		}
		$json_response = new \phpbb\json_response;
			$json_response->send($message);

	}
	
	private function live_search_user($action, $q)
	{
		$sql = "SELECT user_id, username, user_email " .
					" FROM " . USERS_TABLE .  
					" 	WHERE (user_type = " . USER_NORMAL . " OR user_type = " . USER_FOUNDER . ")" .
					" AND username_clean " . $this->db->sql_like_expression(utf8_clean_string($q) . $this->db->get_any_char());
					" ORDER BY username";

		$result = $this->db->sql_query($sql);
		//$user_list = array();
		$message = '';
		while ($row = $this->db->sql_fetchrow($result))
		{
			$user_id = (int)$row['user_id'];
			$user_email = $row['user_email'];
			{
				$message .= $row['username'] ."|$user_id|$user_email\n";
			}
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
        global $cache;
	    $icons = $cache->obtain_icons();
       
	    // define some vars for urls
	    // A single wildcard will make the search results look ugly
        $limit_days		= array(0 => $this->user->lang['ALL_RESULTS'], 1 => $this->user->lang['1_DAY'], 7 => $this->user->lang['7_DAYS'], 14 => $this->user->lang['2_WEEKS'], 30 => $this->user->lang['1_MONTH'], 90 => $this->user->lang['3_MONTHS'], 180 => $this->user->lang['6_MONTHS'], 365 => $this->user->lang['1_YEAR']);
        $sort_by_text	= array('a' => $this->user->lang['SORT_AUTHOR'], 't' => $this->user->lang['SORT_TIME'], 'f' => $this->user->lang['SORT_FORUM'], 'i' => $this->user->lang['SORT_TOPIC_TITLE'], 's' => $this->user->lang['SORT_POST_SUBJECT']);

$s_limit_days = $s_sort_key = $s_sort_dir = $u_sort_param = '';
gen_sort_selects($limit_days, $sort_by_text, $sort_days, $sort_key, $sort_dir, $s_limit_days, $s_sort_key, $s_sort_dir, $u_sort_param);

        $show_results	= 'topics';
        $keywords		= utf8_normalize_nfc(request_var('keywords', '', true));
	    $hilit = phpbb_clean_search_string(str_replace(array('+', '-', '|', '(', ')', '&quot;'), ' ', $keywords));
	    $hilit = str_replace(' ', '|', $hilit);
        $search_forum	= request_var('fid', array(0));
		$result_topic_id = 0;
	    $u_hilit = urlencode(htmlspecialchars_decode(str_replace('|', ' ', $hilit)));
	    $u_show_results = '&amp;sr=' . $show_results;
	    $u_search_forum = implode('&amp;fid%5B%5D=', $search_forum);
        //$u_search = append_sid("{$this->phpbb_root_path}viewforum.$this->php_ext", $u_sort_param . $u_show_results);
        ////$u_search .= ($search_id) ? '&amp;search_id=' . $search_id : '';
        //$u_search .= ($u_hilit) ? '&amp;keywords=' . urlencode(htmlspecialchars_decode($keywords)) : '';
        ////$u_search .= ($search_terms != 'all') ? '&amp;terms=' . $search_terms : '';
        ////$u_search .= ($topic_id) ? '&amp;t=' . $topic_id : '';
        ////$u_search .= ($author) ? '&amp;author=' . urlencode(htmlspecialchars_decode($author)) : '';
        //$u_search .= ($author_id) ? '&amp;author_id=' . $author_id : '';
        //$u_search .= ($u_search_forum) ? '&amp;fid%5B%5D=' . $u_search_forum : '';
        ////$u_search .= (!$search_child) ? '&amp;sc=0' : '';
        ////$u_search .= ($search_fields != 'all') ? '&amp;sf=' . $search_fields : '';
        ////$u_search .= ($return_chars != 300) ? '&amp;ch=' . $return_chars : '';
       // $u_search = './app.php/liveSearch/usertopic/' . $forum_id . '/' . $author_id;
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
        //$l_search_title = $this->user->lang['SEARCH_ACTIVE_TOPICS'];
        
        $sql = "SELECT count(t.topic_id) as total_count " .
                    " FROM " .TOPICS_TABLE . " t LEFT JOIN " . FORUMS_TABLE . " f ON (f.forum_id = t.forum_id)" .
                    " LEFT JOIN " . TOPICS_TRACK_TABLE . " tt ON (tt.user_id = " . $author_id .
                    " AND t.topic_id = tt.topic_id) " .
                    " LEFT JOIN " . FORUMS_TRACK_TABLE . " ft ON (ft.user_id = " . $author_id .
                    " AND ft.forum_id = f.forum_id) " .
                    " WHERE t.topic_status <> " . ITEM_MOVED . 
		            " AND t.topic_visibility = " . ITEM_APPROVED . 
                    " AND t.topic_poster = " . $author_id . $this->build_subforums_search($forum_id);
	    $result = $this->db->sql_query($sql);
        $total_count = (int) $this->db->sql_fetchfield('total_count');
        $this->db->sql_freeresult($result);
        
        if ($total_count)
        {
            $pagination = $this->phpbb_container->get('pagination');
        
            $sql = "SELECT t.*, f.forum_id, f.forum_name, tt.mark_time, ft.mark_time as f_mark_time" .
                        " FROM " .TOPICS_TABLE . " t LEFT JOIN " . FORUMS_TABLE . " f ON (f.forum_id = t.forum_id)" .
                        " LEFT JOIN " . TOPICS_TRACK_TABLE . " tt ON (tt.user_id = " . $author_id .
                        " AND t.topic_id = tt.topic_id) " .
                        " LEFT JOIN " . FORUMS_TRACK_TABLE . " ft ON (ft.user_id = " . $author_id .
                        " AND ft.forum_id = f.forum_id) " .
                        " WHERE t.topic_status <> " . ITEM_MOVED . 
		                " AND t.topic_visibility = " . ITEM_APPROVED . 
                        " AND t.topic_poster = " . $author_id . $this->build_subforums_search($forum_id);
                        " ORDER BY " . $sort_key . " " . $sort_dir;
					    $result = $this->db->sql_query_limit($sql, $per_page, $start);
            $row = 0;
			while ($row = $this->db->sql_fetchrow($result))
            {
                $folder_img = $folder_alt = $topic_type = '';
                $l_search_matches =  $this->user->lang('FOUND_SEARCH_MATCHES', $total_count) ;
		        $phpbb_content_visibility = $this->phpbb_container->get('content.visibility');
			    $replies = $phpbb_content_visibility->get_count('topic_posts', $row, $forum_id) - 1;
			    $result_topic_id = $row['topic_id'];
 			    $view_topic_url_params = "f=$forum_id&amp;t=$result_topic_id" . (($u_hilit) ? "&amp;hilit=$u_hilit" : '');
			    $view_topic_url = append_sid("{$this->phpbb_root_path}viewtopic.$this->php_ext", $view_topic_url_params);

 			//	if ($this->user->data['is_registered'] && $this->config['load_db_lastread'])
				//{
				//	$topic_tracking_info[$forum_id] = get_topic_tracking($forum_id, $forum['topic_list'], $forum['rowset'], array($forum_id => $forum['mark_time']));
				//}
				//else if ($this->config['load_anon_lastread'] || $this->user->data['is_registered'])
				//{
				//	$topic_tracking_info[$forum_id] = get_complete_topic_tracking($forum_id, $forum['topic_list']);

				//	if (!$this->user->data['is_registered'])
				//	{
				//		$this->user->data['user_lastmark'] = (isset($tracking_topics['l'])) ? (int) (base_convert($tracking_topics['l'], 36, 10) + $this->config['board_startdate']) : 0;
				//	}
				//}               
				topic_status($row, $replies, (isset($topic_tracking_info[$forum_id][$row['topic_id']]) && $row['topic_last_post_time'] > $topic_tracking_info[$forum_id][$row['topic_id']]) ? true : false, $folder_img, $folder_alt, $topic_type);
				$unread_topic = (isset($topic_tracking_info[$forum_id][$row['topic_id']]) && $row['topic_last_post_time'] > $topic_tracking_info[$forum_id][$row['topic_id']]) ? true : false;
				$topic_unapproved = (($row['topic_visibility'] == ITEM_UNAPPROVED || $row['topic_visibility'] == ITEM_REAPPROVE) && $this->auth->acl_get('m_approve', $forum_id)) ? true : false;
      			$posts_unapproved = ($row['topic_visibility'] == ITEM_APPROVED && $row['topic_posts_unapproved'] && $this->auth->acl_get('m_approve', $forum_id)) ? true : false;
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
				    'U_VIEW_FORUM'		=> append_sid("{$this->phpbb_root_path}viewforum.$this->php_ext", 'f=' . $forum_id),
					'U_MCP_QUEUE'			=> $u_mcp_queue,
					'U_LAST_POST'			=> append_sid("{$this->phpbb_root_path}viewtopic.$this->php_ext", $view_topic_url_params . '&amp;p=' . $row['topic_last_post_id']) . '#p' . $row['topic_last_post_id'],
                    ));
				$pagination->generate_template_pagination($view_topic_url, 'searchresults.pagination', 'start', $replies + 1, $this->config['posts_per_page'], 1, true, true);
                //if ($topic_id && ($topic_id == $result_topic_id))
                //{
                //    $template->assign_vars(array(
                //        'SEARCH_TOPIC'		=> $topic_title,
                //        'L_RETURN_TO_TOPIC'	=> $user->lang('RETURN_TO', $topic_title),
                //        'U_SEARCH_TOPIC'	=> $view_topic_url
                //    ));
                //}                
                $row++;            
           }
			//$pagination->generate_template_pagination($view_topic_url, 'pagination', 'start', $total_count + 1, $this->config['posts_per_page'], 1, true, true);
	       //print_r($u_search);
            $pagination->generate_template_pagination($u_search, 'pagination', 'start', $total_count, $per_page, $start);

            }
           //print_r($sql);
    		$this->template->assign_vars(array(
			'S_SHOW_TOPICS'		=> 1,
            //'SEARCH_TITLE'		=> $l_search_title,
            'SEARCH_MATCHES'	=> $l_search_matches,
			'PAGE_NUMBER'		=> $pagination->on_page($total_count, $this->config['posts_per_page'], $start),
			'TOTAL_MATCHES'		=> $total_count,
		    'REPORTED_IMG'		=> $this->user->img('icon_topic_reported', 'TOPIC_REPORTED'),
		    'UNAPPROVED_IMG'	=> $this->user->img('icon_topic_unapproved', 'TOPIC_UNAPPROVED'),
		    'DELETED_IMG'		    => $this->user->img('icon_topic_deleted', 'TOPIC_DELETED'),
		    'POLL_IMG'			    => $this->user->img('icon_topic_poll', 'TOPIC_POLL'),
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
		if ($forum_id == 0) return '';
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
	
}
