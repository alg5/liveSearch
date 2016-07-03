<?php
/**
*
 * @package livesearch
 * @copyright (c) 2014 Alg
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace alg\liveSearch\controller;

class liveSearch_ajax_handler
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\cache\service */
	protected $cache;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var string PHP extension */
	protected $php_ext;

	/** @var \phpbb\request\request_interface */
	protected $request;

	/** @var string PHP extension */
	protected $phpbb_container;

	/** @var \phpbb\pagination */
	protected $pagination;

	/** @var \phpbb\content_visibility */
	protected $content_visibility;

	/** @var \phpbb\profilefields\manager */
	protected $profilefields_manager;

	/** @var \phpbb\event\dispatcher_interface */
	protected $dispatcher;

	/** @var array */
	protected $thankers = array();

	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\auth\auth $auth, \phpbb\template\template $template, \phpbb\user $user, \phpbb\cache\service $cache, $phpbb_root_path, $php_ext, \phpbb\request\request_interface $request, $phpbb_container, \phpbb\pagination $pagination, \phpbb\content_visibility $content_visibility, \phpbb\profilefields\manager $profilefields_manager, \phpbb\event\dispatcher_interface $dispatcher, $groups_table)
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
		//$this->table_prefix = $table_prefix;
		$this->profilefields_manager = $profilefields_manager;
		$this->dispatcher = $dispatcher;
		$this->groups_table = $groups_table;

		$this->return = array(); // save returned data in here
		$this->error = array(); // save errors in here

	}

	public function main($action, $forum, $topic, $user)
	{
		// Grab data
		$q = utf8_strtoupper(utf8_normalize_nfc($this->request->variable('q', '',true)));

		$this->user->add_lang_ext('alg/liveSearch', 'live_search');

		switch ($action)
		{
			case 'forum':
				$this->live_search_forum($action, $forum, $q);
			break;
			case 'topic':
			case 'similartopic':
				$this->live_search_topic($action, $topic, $q);
			break;
			case 'user':
			case 'userpm':
				$this->live_search_user($action, $q);
			break;
			case 'usertopic':
				$this->live_search_usertopic( $forum, $topic, $user);
			break;
			case 'userpost':
				$this->live_search_userpost( $forum, $topic, $user);
			break;
			case 'group':
				$this->live_search_group($action, $q);

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
					if ($pos == 0)
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
			$key = $forum_info['forum_name'] ;
			if ($forum_info['forum_parent_name'] )
			{
				$key .= ' (' . $forum_info['forum_parent_name'] . ')'  ;
			}
			$message .=  $key . "|$forum_id\n";
		}
		$json_response = new \phpbb\json_response;
			$json_response->send($message);
	}

	private function live_search_topic($action, $forum_id, $q)
	{
		$ex_fid_ary = array();
		$ex_fid_ary = array_keys($this->auth->acl_getf('!f_read', true));

		if ($this->config['live_search_exclude_forums'])
		{
				$exclude_forums = explode(',', $this->config['live_search_exclude_forums']);
				if (sizeof($exclude_forums))
				{
					$ex_fid_ary = array_merge($ex_fid_ary, $exclude_forums);
					$ex_fid_ary = array_unique($ex_fid_ary);
			}
		}
		$where = "t.topic_status <> " . ITEM_MOVED .
						" AND t.topic_visibility = " . ITEM_APPROVED .
						"  AND UPPER(t.topic_title) " . $this->db->sql_like_expression($this->db->get_any_char() .  $this->db->sql_escape($q) . $this->db->get_any_char());
		if (sizeof($ex_fid_ary))
		{
			$where .= " AND " . $this->db->sql_in_set('f.forum_id', $ex_fid_ary, true);
		}

		$sql_array = array(
		'SELECT'	=> 't.topic_id, t.topic_title, t.topic_status, t.topic_moved_id, t.forum_id, f.forum_name',
		'FROM'		=> array(TOPICS_TABLE => 't'),
		'LEFT_JOIN'	=> array(
			array(
				'FROM'	=> array(FORUMS_TABLE => 'f'),
				'ON'	=> 'f.forum_id = t.forum_id',
			),

			),
		'WHERE'		=> $where ,
		'ORDER_BY'	=> 'topic_title',
	);
	/**
	* Event to modify the SQL query before the topics data is retrieved
	*
	* @event alg.livesearch.sql_livesearch_topics
	* @var	array	sql_array		The SQL array
	* @since 3.0.2
	*/
	$vars = array('sql_array');
	extract($this->dispatcher->trigger_event('alg.livesearch.sql_livesearch_topics', compact($vars)));
	$sql = $this->db->sql_build_query('SELECT', $sql_array);
	$result = $this->db->sql_query($sql);
	$rowset = array();
	while ($row = $this->db->sql_fetchrow($result))
	{
			$topic_id = (int) $row['topic_id'];
			$rowset[$topic_id] = $row;
	}
	/**
	* Modify the rowset data
	*
	* @event alg.livesearch.topics_modify_rowset
	* @var	array	rowset		Array with topics results data
	* @since 3.0.2
	*/
	$vars = array(
		'rowset',
	);
	extract($this->dispatcher->trigger_event('alg.livesearch.topics_modify_rowset', compact($vars)));

	$topic_list = array();
	$arr_res = $arr_priority1 = $arr_priority2 = array();
	foreach ($rowset as $key => $row)
	{
		if (isset($row['topic_title']) && strlen($row['topic_title']) >0)
		{
			$pos = strpos(utf8_strtoupper($row['topic_title']), $q);
			if ($pos !== false && $this->auth->acl_get('f_read', $row['forum_id']) )
			{
				$row['pos'] = $pos;
				if ($pos == 0)
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
	foreach ($arr_res as $topic_info)
	{
		$forum_id = $topic_info['forum_id'];
		$topic_id = ($topic_info['topic_status'] == 2) ? (int) $topic_info['topic_moved_id'] : (int) $topic_info['topic_id'];
		$topic_info['topic_title'] = str_replace('|', ' ', $topic_info['topic_title']);
		$key = censor_text($topic_info['topic_title']	);
		$forum_name =  ' (' . $topic_info['forum_name'] . ')'  ;
		$message .= $key . "|$topic_id|$forum_id|$forum_name\n";
	}
	$json_response = new \phpbb\json_response;
	$json_response->send($message);

	}
	private function live_search_group($action, $q)
	{
		$sql = "SELECT group_id, group_name, group_type  FROM " . $this->groups_table .
					" ORDER BY group_type DESC, group_name ASC";
		$result = $this->db->sql_query($sql);
		$message='';
		while ($row = $this->db->sql_fetchrow($result))
		{
			$key = $row['group_type'] == GROUP_SPECIAL ?  $this->user->lang['G_' . $row['group_name']] : $row['group_name']	;
			if (strpos(utf8_strtoupper($key), $q) == 0)
			{
				$group_id=$row['group_id'];
				$message .= $key . "|$group_id\n";
			}
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
		}

		$message = '';
		foreach ($user_info as $row)
		{
			$user_id = (int) $row['user_id'];
			$message .= $row['username'] . '|' . $user_id;
			if ($this->user->data['user_id']!= ANONYMOUS)
			{
				//add user profile
				$url = append_sid("{$this->phpbb_root_path}memberlist.$this->php_ext", 'mode=viewprofile&amp;u=' . $user_id);
				$message .= '|profile^' . $this->user->lang['LIVE_SEARCH_GO_PROFILE'] . '^' . $url;
			}

			$url = $this->get_url_pm($row);
			if ($url)
			{
				$message .= '|pm^' . $this->user->lang['SEND_PRIVATE_MESSAGE'] . '^' . $url ;
			}
			$url = $this->get_url_email($row);
			if ($url)
			{
				$message .= '|email^' . $this->user->lang['SEND_EMAIL'] . '^' . $url ;
			}
			$url = $this->get_url_jabber($row);
			if ($url)
			{
				$message .= '|jabber^' . $this->user->lang['JABBER'] . '^' . $url ;
			}

			foreach ($row as $f_name => $f_value)
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

	private function live_search_usertopic($forum, $topic, $user)
	{
		include_once($this->phpbb_root_path . 'includes/functions_display.' . $this->php_ext);
		$this->user->add_lang(array( 'search'));
		$forum_id = $forum;
		$topic_id = $topic;
		$author_id = $user;
		$user_id = $this->user->data['user_id'];

		// Grab icons
		$icons = $this->cache->obtain_icons();

		$show_results	= 'topics';
		$u_search = append_sid("{$this->phpbb_root_path}liveSearch/usertopic/$forum_id/$topic_id/$author_id");

		// Define initial vars
		$page_title = $this->user->lang['SEARCH'];
		$template_html = 'live_search_results.html';
		$start = $this->request->variable('start', 0);
		$per_page = $this->config['posts_per_page'];
		$default_key = 't.topic_last_post_time';
		$sort_key = $this->request->variable('sk', $default_key);
		$sort_dir = $this->request->variable('sd', 'desc');

		// clear arrays
		$id_ary = array();
		$author_id_ary[] = $author_id;

		// Which forums should not be searched? Author searches are also carried out in unindexed forums
		$ex_fid_ary = array();
		$ex_fid_ary = array_keys($this->auth->acl_getf('!f_read', true));

		if ($this->config['live_search_exclude_forums'])
		{
				$exclude_forums = explode(',', $this->config['live_search_exclude_forums']);
				if (sizeof($exclude_forums))
				{
					$ex_fid_ary = array_merge($ex_fid_ary, $exclude_forums);
					$ex_fid_ary = array_unique($ex_fid_ary);
			}
		}
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

		$sql = "SELECT count(t.topic_id) as total_count, u.username" .
					" FROM " .TOPICS_TABLE . " t LEFT JOIN " . FORUMS_TABLE . " f ON (f.forum_id = t.forum_id)" .
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
		if ($forum_id)
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
				$where =	  ' topic_status <> ' . ITEM_MOVED  . '  AND t.topic_visibility = ' .  ITEM_APPROVED  . '  AND t.topic_poster = ' . $author_id  ;
				if (sizeof($ex_fid_ary))
				{
					$where .= ' AND ' . $this->db->sql_in_set('f.forum_id', $ex_fid_ary, true);
				}
				if ($forum_id)
				{
					$where .= $this->build_subforums_search($forum_id) ;
				}
				$sql_array = array(
				'SELECT'	=> 't.*, u.user_id, u.username, u.user_colour, f.forum_id, f.forum_name, tt.mark_time, ft.mark_time as f_mark_time',
				'FROM'		=> array(TOPICS_TABLE => 't'),
				'LEFT_JOIN'	=> array(
					array(
						'FROM'	=> array(FORUMS_TABLE => 'f'),
						'ON'	=> 'f.forum_id = t.forum_id',
					),
					array(
						'FROM'	=> array(TOPICS_TRACK_TABLE => 'tt'),
						'ON'	=> 'tt.user_id = ' . $user_id .  ' AND t.topic_id = tt.topic_id' ,
					),
					array(
						'FROM'	=> array(FORUMS_TRACK_TABLE => 'ft'),
						'ON'	=> 'ft.user_id = ' . $user_id .  ' AND  ft.forum_id = f.forum_id' ,
					),
						array(
								'FROM'	=> array(USERS_TABLE => 'u'),
								'ON'	=> 't.topic_last_poster_id = u.user_id',
						),
					),
				'WHERE'		=> $where ,
				'ORDER_BY'	=> 't.topic_last_post_time DESC',
			);
			$total_match_count =$total_count;
			// Set limit for the $total_match_count to reduce server load
			$total_matches_limit = 1000;
			if ($total_match_count)
			{
				// Limit the number to $total_matches_limit for pre-made searches
				if ($total_match_count > $total_matches_limit)
				{
					$found_more_search_matches = true;
					$total_match_count = $total_matches_limit;
				}
			}
			/**
			* Event to modify the SQL query before the topics data is retrieved
			*
			* @event alg.livesearch.sql_livesearch_usertopics
			* @var	array	sql_array		The SQL array
			* @var	int	 total_match_count	The total number of search matches
			* @since 1.0.0
			* @changed 3.0.2 Added total_match_count
			*/
			$vars = array('sql_array', 'total_match_count');
			extract($this->dispatcher->trigger_event('alg.livesearch.sql_livesearch_usertopics', compact($vars)));
			$result = $this->db->sql_query_limit($this->db->sql_build_query('SELECT', $sql_array),  $per_page, $start);
			$row_count = 0;
			$rowset = array();
			while ($row = $this->db->sql_fetchrow($result))
			{
					$topic_id = (int) $row['topic_id'];
					$rowset[$topic_id] = $row;
			}

			/**
			* Modify the rowset data
			*
			* @event alg.livesearch.usertopics_modify_rowset
			* @var	array	rowset					Array with topics results data
			* @var	int 	total_match_count					Array with topics results data
			* @since 3.0.2
			*/
			$vars = array(
				'rowset',
				'total_match_count',
			);
			extract($this->dispatcher->trigger_event('alg.livesearch.usertopics_modify_rowset', compact($vars)));
			foreach ($rowset as $key => $row)
			{
				$ls_forum_id = (int) $row['forum_id'];
				$ls_topic_id = (int) $row['topic_id'];
				if ($this->auth->acl_get('f_read',$ls_forum_id))
				{
					$row_count++;
					// Get topic tracking info
					if ($this->user->data['is_registered'] && $this->config['load_db_lastread'] && !$this->config['ls_topics_cache'])	//todo ls_topics_cache
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
					$unread_topic = (isset($topic_tracking_info[$row['topic_id']]) && $row['topic_last_post_time'] > $topic_tracking_info[$row['topic_id']]) ? true : false;

					topic_status($row, $replies, $unread_topic, $folder_img, $folder_alt, $topic_type);
					$topic_unapproved = ($row['topic_visibility'] == ITEM_UNAPPROVED && $this->auth->acl_get('m_approve', $ls_forum_id)) ? true : false;
					$posts_unapproved = ($row['topic_visibility'] == ITEM_APPROVED && $row['topic_posts_unapproved'] && $this->auth->acl_get('m_approve', $ls_forum_id)) ? true : false;

					$result_forum_id = $row['forum_id'];
					$result_topic_id = $row['topic_id'];
						$live_search_topic_link_type = isset($this->config['live_search_topic_link_type']) ? (bool) $this->config['live_search_topic_link_type'] : true;

					$view_topic_url_params = $live_search_topic_link_type ?  "f=$result_forum_id&amp;t=$result_topic_id" :  "t=$result_topic_id";
					$view_topic_url = append_sid("{$this->phpbb_root_path}viewtopic.$this->php_ext", $view_topic_url_params);
					$unread_topic = (isset($topic_tracking_info[$forum_id][$row['topic_id']]) && $row['topic_last_post_time'] > $topic_tracking_info[$forum_id][$row['topic_id']]) ? true : false;
					$topic_deleted = $row['topic_visibility'] == ITEM_DELETED;
					$u_mcp_queue = ($topic_unapproved || $posts_unapproved) ? append_sid("{$this->phpbb_root_path}mcp.$this->php_exp", 'i=queue&amp;mode=' . (($topic_unapproved) ? 'approve_details' : 'unapproved_posts') . "&amp;t=$result_topic_id", true, $this->user->session_id) : '';
					$u_mcp_queue = (!$u_mcp_queue && $topic_deleted) ? append_sid("{$this->phpbb_root_path}mcp.$this->php_exp", "i=queue&amp;mode=deleted_topics&amp;t=$result_topic_id", true, $this->user->session_id) : '';

					$tpl_ary = array(
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
						'U_VIEW_POST'		=> (!empty($row['post_id'])) ?  append_sid("{$this->phpbb_root_path}viewtopic.$this->php_ext", "f=" + $row['forum_id'] + "&amp;t=" . $row['topic_id'] . '&amp;p=' . $row['post_id'] ) . '#p' . $row['post_id'] : '',
						'U_MCP_QUEUE'			=> $u_mcp_queue,
						'U_LAST_POST'			=> append_sid("{$this->phpbb_root_path}viewtopic.$this->php_ext", $view_topic_url_params . '&amp;p=' . $row['topic_last_post_id']) . '#p' . $row['topic_last_post_id'],
					);
					/**
					* Modify the topic data before it is assigned to the template
					*
					* @event alg.livesearch.modify_tpl_ary_livesearch_usertopics
					* @var	array	row			Array with topic data
					* @var	array	tpl_ary		Template block array with topic data
					* @since 1.0.0
					*/
					$vars = array('row', 'tpl_ary');
					extract($this->dispatcher->trigger_event('alg.livesearch.modify_tpl_ary_livesearch_usertopics', compact($vars)));

					$this->template->assign_block_vars('livesearchresults', $tpl_ary);
					$this->pagination->generate_template_pagination($view_topic_url, 'livesearchresults.pagination', 'start', $replies + 1, $this->config['posts_per_page'], 1, true, true);
				}
			}
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
				$tpl_ary = array(
				'S_SHOW_TOPICS'		=> 1,
				'SEARCH_MATCHES'	=>  $total_count == 0 ? '' : $this->user->lang('FOUND_SEARCH_MATCHES', $total_count) ,
				'SEARCH_MATCHES_TXT'	=>	$res_txt,
				'PAGE_NUMBER'		=> $total_count == 0 ?  0 : $this->pagination->on_page($total_count, $this->config['topics_per_page'], $start),
				'TOTAL_MATCHES'		=> $total_count,
				'REPORTED_IMG'		=> $this->user->img('icon_topic_reported', 'TOPIC_REPORTED'),
				'UNAPPROVED_IMG'	=> $this->user->img('icon_topic_unapproved', 'TOPIC_UNAPPROVED'),
				'DELETED_IMG'			 => $this->user->img('icon_topic_deleted', 'TOPIC_DELETED'),
				'POLL_IMG'				 => $this->user->img('icon_topic_poll', 'TOPIC_POLL'),
				'LAST_POST_IMG'		=> $this->user->img('icon_topic_latest', 'VIEW_LATEST_POST'),
			);

		/**
		* Modify the topic matches data before it is assigned to the template
		*
		* @event alg.livesearch.modify_tpl_ary_livesearch_usertopics_matches
		* @var	array	tpl_ary		Template block array with topic data
		* @var	int start		Template block array with topic data
		* @var	int total_count		Template block array with topic data
		* @since 3.0.2
		*/
		$vars = array( 'tpl_ary', 'start', 'total_count');
		extract($this->dispatcher->trigger_event('alg.livesearch.modify_tpl_ary_livesearch_usertopics_matches', compact($vars)));
		$this->template->assign_vars($tpl_ary);

		page_header($page_title);

		$this->template->set_filenames(array(
			'body' => $template_html));

		make_jumpbox(append_sid("{$this->phpbb_root_path}viewforum.$this->php_ext"));
		page_footer();
		return new Response($this->template->return_display('body'), 200);

	}

	private function live_search_userpost($forum, $topic, $user)
	{
		include_once($this->phpbb_root_path . 'includes/functions_display.' . $this->php_ext);
		include_once($this->phpbb_root_path . 'includes/functions_posting.' . $this->php_ext);

		$this->user->add_lang(array( 'search'));
		$forum_id = $forum;
		$topic_id = $topic;
		$author_id = $user;
		$user_id = $this->user->data['user_id'];

		// Grab icons
		$icons = $this->cache->obtain_icons();
		// define some vars for urls
		// A single wildcard will make the search results look ugly
		$limit_days		= array(0 => $this->user->lang['ALL_RESULTS'], 1 => $this->user->lang['1_DAY'], 7 => $this->user->lang['7_DAYS'], 14 => $this->user->lang['2_WEEKS'], 30 => $this->user->lang['1_MONTH'], 90 => $this->user->lang['3_MONTHS'], 180 => $this->user->lang['6_MONTHS'], 365 => $this->user->lang['1_YEAR']);
		$sort_by_text	= array('a' => $this->user->lang['SORT_AUTHOR'], 't' => $this->user->lang['SORT_TIME'], 'f' => $this->user->lang['SORT_FORUM'], 'i' => $this->user->lang['SORT_TOPIC_TITLE'], 's' => $this->user->lang['SORT_POST_SUBJECT']);

		$s_limit_days = $s_sort_key = $s_sort_dir = $u_sort_param = '';
		gen_sort_selects($limit_days, $sort_by_text, $sort_days, $sort_key, $sort_dir, $s_limit_days, $s_sort_key, $s_sort_dir, $u_sort_param);

		$show_results	= 'posts';
		$u_search = append_sid("{$this->phpbb_root_path}liveSearch/userpost/$forum_id/$topic_id/$author_id");

		// Define initial vars
		$page_title = $this->user->lang['SEARCH'];
		$template_html = 'live_search_results.html';
		$start = $this->request->variable('start', 0);
		$per_page = $this->config['posts_per_page'];
		$default_key = 't.topic_last_post_time';
		$sort_key = $this->request->variable('sk', $default_key);
		$sort_dir = $this->request->variable('sd', 'desc');

		// clear arrays
		$id_ary = array();
		$author_id_ary[] = $author_id;

		// Which forums should not be searched? Author searches are also carried out in unindexed forums
		$ex_fid_ary = array();
		$ex_fid_ary = array_keys($this->auth->acl_getf('!f_read', true));

		if ($this->config['live_search_exclude_forums'])
		{
			$exclude_forums = explode(',', $this->config['live_search_exclude_forums']);
			if (sizeof($exclude_forums))
			{
					$ex_fid_ary = array_merge($ex_fid_ary, $exclude_forums);
					$ex_fid_ary = array_unique($ex_fid_ary);
			}
		}
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
		$sql = "SELECT count(t.topic_id) as total_count, t.topic_title, u.username" .
					" FROM " . POSTS_TABLE . " p LEFT JOIN  " .TOPICS_TABLE . " t ON (p.topic_id = t.topic_id) ".
					" LEFT JOIN " . USERS_TABLE . " u ON p.poster_id = u.user_id" .
					" WHERE  t.topic_status <> " . ITEM_MOVED .
					" AND t.topic_visibility = " . ITEM_APPROVED .
					" AND p.post_visibility = " . ITEM_APPROVED .
					" AND p.poster_id = " . $author_id ;
		if (sizeof($ex_fid_ary))
		{
			$sql .= " AND " . $this->db->sql_in_set('p.forum_id', $ex_fid_ary, true);
		}
		if ($forum_id)
		{
			$sql .= $this->build_subforums_search($forum_id) ;
		}
		if ($topic_id)
		{
			$sql .= " AND p.topic_id = " .  $topic_id;
		}

		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$total_count = (int) $row['total_count'];
		$username = $row['username'];
		$this->db->sql_freeresult($result);
		$forum_name = '';
		$topic_name = '';
		$forum_has_subforums = false;
		if ($topic_id)
		{
			$topic_name = censor_text($row['topic_title']);
		}
		if ($forum_id)
		{
			$sql = 	" SELECT forum_name, left_id, right_id FROM " . FORUMS_TABLE .  " WHERE forum_id=" . $forum_id;
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$forum_name = $row['forum_name'] ;
			$forum_has_subforums = ($row['right_id'] - $row['left_id'] > 1) ? true : false ;
			$this->db->sql_freeresult($result);
		}

		if ($total_count)
		{//1
				$where =	  ' topic_status <> ' . ITEM_MOVED  . '  AND t.topic_visibility = ' .  ITEM_APPROVED  . '  AND p.poster_id = ' . $author_id  ;
				if (sizeof($ex_fid_ary))
				{
					$where .= ' AND ' . $this->db->sql_in_set('f.forum_id', $ex_fid_ary, true);
				}
				if ($forum_id)
				{
					$where .= $this->build_subforums_search($forum_id) ;
				}
				if ($topic_id)
				{
					$where .= ' AND t.topic_id = ' . $topic_id ;
				}
				$sql_array = array(
				'SELECT'	=> 'p.*, f.forum_id, f.forum_name, t.*, u.username, u.username_clean, u.user_sig, u.user_sig_bbcode_uid, u.user_colour ',
				'FROM'		=> array(POSTS_TABLE => 'p'),
				'LEFT_JOIN'	=> array(
					array(
						'FROM'	=> array(TOPICS_TABLE => 't'),
						'ON'	=> 'p.topic_id = t.topic_id',
					),
					array(
						'FROM'	=> array(FORUMS_TABLE => 'f'),
						'ON'	=> 'f.forum_id = p.forum_id',
					),

						array(
								'FROM'	=> array(USERS_TABLE => 'u'),
								'ON'	=> 'p.poster_id = u.user_id',
						),
					),
				'WHERE'		=> $where ,
				'ORDER_BY'	=> ' p.post_time DESC  ',
			);
			$total_match_count =$total_count;
			// Set limit for the $total_match_count to reduce server load
			$total_matches_limit = 1000;
			if ($total_match_count)
			{
				// Limit the number to $total_matches_limit for pre-made searches
				if ($total_match_count > $total_matches_limit)
				{
					$found_more_search_matches = true;
					$total_match_count = $total_matches_limit;
				}
			}
			/**
			* Event to modify the SQL query before the topics data is retrieved
			*
			* @event alg.livesearch.sql_livesearch_userposts
			* @var	array	sql_array		The SQL array
			* @var	int 	total_match_count		The total number of search matches
			* @since 1.0.0
			* @changed 3.0.2 Added total_match_count
			*/
			$vars = array('sql_array', 'total_match_count');
			extract($this->dispatcher->trigger_event('alg.livesearch.sql_livesearch_userposts', compact($vars)));

			$result = $this->db->sql_query_limit($this->db->sql_build_query('SELECT', $sql_array),  $per_page, $start);

				$row_count = 0;
			$rowset = array();
			while ($row = $this->db->sql_fetchrow($result))
			{
				$rowset[] = $row;
			}
		/**
		* Modify the rowset of posts data
		*
		* @event alg.livesearch.userposts_modify_rowset
		* @var	array	rowset					Array with the search results data
		* @var	int 	total_match_count		The total number of search matches
		* @since 3.0.1
		* @changed 3.0.2 Added total_match_count
		*/
			$vars = array(
			'rowset',
			'total_match_count',
		);
		extract($this->dispatcher->trigger_event('alg.livesearch.userposts_modify_rowset', compact($vars)));
			//while ($row = $this->db->sql_fetchrow($result))
			foreach ($rowset as $row)
			{//2
				$ls_forum_id = (int) $row['forum_id'];
				$ls_topic_id = (int) $row['topic_id'];
				$rowset[$ls_topic_id] = $row;
				if ($this->auth->acl_get('f_read',$ls_forum_id))
				{//3
					$row_count++;

					$replies = $this->content_visibility->get_count('topic_posts', $row, $ls_forum_id) - 1;

					$result_topic_id = $row['topic_id'];
					$view_topic_url_params = "f=$forum_id&amp;t=$result_topic_id" ;
					$view_topic_url = append_sid("{$this->phpbb_root_path}viewtopic.$this->php_ext", $view_topic_url_params);
					$parse_flags = ($row['bbcode_bitfield'] ? OPTION_FLAG_BBCODE : 0) | OPTION_FLAG_SMILIES;
					$row['post_text'] = generate_text_for_display($row['post_text'], $row['bbcode_uid'], $row['bbcode_bitfield'], $parse_flags, false);

						$tpl_ary = array(
						'POST_AUTHOR_FULL'		=> get_username_string('full', $row['poster_id'], $row['username'], $row['user_colour'], $row['post_username']),
						'POST_AUTHOR_COLOUR'	=> get_username_string('colour', $row['poster_id'], $row['username'], $row['user_colour'], $row['post_username']),
						'POST_AUTHOR'			=> get_username_string('username', $row['poster_id'], $row['username'], $row['user_colour'], $row['post_username']),
						'U_POST_AUTHOR'			=> get_username_string('profile', $row['poster_id'], $row['username'], $row['user_colour'], $row['post_username']),
						'U_VIEW_POST'		=> (!empty($row['post_id'])) ?  append_sid("{$this->phpbb_root_path}viewtopic.$this->php_ext", "f=" + $row['forum_id'] + "&amp;t=" . $row['topic_id'] . '&amp;p=' . $row['post_id'] ) . '#p' . $row['post_id'] : '',

						'POST_SUBJECT'		=> $row['post_subject'],
						'POST_DATE'			=> (!empty($row['post_time'])) ? $this->user->format_date($row['post_time']) : '',
						'MESSAGE'			=> $row['post_text'],
						'TOPIC_TITLE'		=> censor_text($row['topic_title']),
						'FORUM_TITLE'		=> $row['forum_name'],
						'FIRST_POST_TIME'			=> $this->user->format_date($row['topic_time']),
						'S_ROW_COUNT'		=> $row,
						'TOPIC_REPLIES'		=> $replies,
						'TOPIC_VIEWS'		=> $row['topic_views'],
						'LAST_POST_AUTHOR_FULL'		=> get_username_string('full', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),
						'LAST_POST_TIME'			=> $this->user->format_date($row['topic_last_post_time']),
						'U_VIEW_TOPIC'		=> $view_topic_url,
						'U_VIEW_FORUM'		=> append_sid("{$this->phpbb_root_path}viewforum.$this->php_ext", 'f=' . $row['forum_id']),
//						'U_LAST_POST'			=> append_sid("{$this->phpbb_root_path}viewtopic.$this->php_ext", $view_topic_url_params . '&amp;p=' . $row['topic_last_post_id']) . '#p' . $row['topic_last_post_id'],
					);
					/**
					* Modify the topic data before it is assigned to the template
					*
					* @event alg.livesearch.modify_tpl_ary_livesearch_userposts
					* @var	array	row			Array with topic data
					* @var	array	tpl_ary		Template block array with topic data
					* @since 1.0.0
					*/
					$vars = array('row', 'tpl_ary');
				extract($this->dispatcher->trigger_event('alg.livesearch.modify_tpl_ary_livesearch_userposts', compact($vars)));

				$this->template->assign_block_vars('livesearchresults', $tpl_ary);
				}//3
			}//2 while
				$this->pagination->generate_template_pagination($u_search, 'pagination', 'start', $total_count, $per_page, $start);
		}//1
		if ($topic_id)
		{
			$res_txt =  sprintf($this->user->lang['LIVESEARCH_USERPOST_RESULT_IN_TOPIC'], $username, $topic_name, $forum_name);
		}
		else
		{
			if ($forum_id)
			{
				$res_txt = sprintf($this->user->lang['LIVESEARCH_USERPOST_RESULT_IN_FORUM'], $username, $forum_name);
				if ($forum_has_subforums)
				{
				$res_txt .= $this->user->lang['LIVESEARCH_USERTOPIC_RESULT_IN_SUBFORUMS'];
				}
			}
			else
			{
				$res_txt = sprintf($this->user->lang['LIVESEARCH_USERPOST_RESULT'], $username);
			}
		}
		$l_search_matches =  $this->user->lang('FOUND_SEARCH_MATCHES', $total_count) ;
		//$this->template->assign_vars(array(

		//));
		$tpl_ary = array(
			'S_SHOW_TOPICS'		=> 0,
			'SEARCH_MATCHES'	=>  $total_count == 0 ? '' : $this->user->lang('FOUND_SEARCH_MATCHES', $total_count) ,
			'SEARCH_MATCHES_TXT'	=>	$res_txt,
			'PAGE_NUMBER'		=> $total_count == 0 ?  0 : $this->pagination->on_page($total_count, $this->config['posts_per_page'], $start),
			'TOTAL_MATCHES'		=> $total_count,
			'REPORTED_IMG'		=> $this->user->img('icon_topic_reported', 'TOPIC_REPORTED'),
			'UNAPPROVED_IMG'	=> $this->user->img('icon_topic_unapproved', 'TOPIC_UNAPPROVED'),
			'DELETED_IMG'			 => $this->user->img('icon_topic_deleted', 'TOPIC_DELETED'),
			'POLL_IMG'				 => $this->user->img('icon_topic_poll', 'TOPIC_POLL'),
			'LAST_POST_IMG'		=> $this->user->img('icon_topic_latest', 'VIEW_LATEST_POST'),
		);
		/**
		* Modify the topic matches data before it is assigned to the template
		*
		* @event alg.livesearch.modify_tpl_ary_livesearch_userposts_matches
		* @var	array	tpl_ary		Template block array with topic data
		* @var	int total_count		The total number of search matches
		* @since 3.0.2
		*/
		$vars = array('tpl_ary', 'total_count');
		extract($this->dispatcher->trigger_event('alg.livesearch.modify_tpl_ary_livesearch_userposts_matches', compact($vars)));
		$this->template->assign_vars($tpl_ary);
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
				$desc = isset($this->user->lang[$contact['field_contact_desc']]) ? $this->user->lang[$contact['field_contact_desc']] : $contact['field_contact_desc'];
				return $contact['field_name']  . '^' . $desc . '^' . sprintf($contact['field_contact_url'], $f_value);
			}
		}
		return '';
	}

	private function get_url_pm($seeking_user)
	{
		if ($this->user->data['user_id'] == ANONYMOUS)
		{
			return '';
		}

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
			(($this->auth->acl_gets('a_', 'm_') || $this->auth->acl_getf_global('m_')) || $seeking_user['user_allow_pm'])
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
		if ($this->user->data['user_id'] != ANONYMOUS && (!empty($seeking_user['user_allow_viewemail']) && $this->auth->acl_get('u_sendemail')) || $this->auth->acl_get('a_email'))
		{
			$url = ($this->config['board_email_form'] && $this->config['email_enable']) ? append_sid("{$this->phpbb_root_path}memberlist.$this->php_ext", "email&amp;u=$seeking_user_id"): (($this->config['board_hide_emails'] && !$this->auth->acl_get('a_email')) ? '' : 'mailto:' . $seeking_user['user_email']);
		}
		return $url;
	}

	private function get_url_jabber($seeking_user)
	{
		$seeking_user_id = $seeking_user['user_id'];
		$url = '';
		if (!$this->user->data['user_id'] != ANONYMOUS && $seeking_user['user_jabber'] && $this->auth->acl_get('u_sendim'))
		{
			$url = append_sid("{$this->phpbb_root_path}memberlist.$this->php_ext", "mode=contact&amp;action=jabber&amp;u=$seeking_user_id");
		}
		return $url;
	}
}
