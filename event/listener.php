<?php
/**
 *
 * @package liveSearch
 * @copyright (c) 2014 alg 
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace alg\liveSearch\event;

/**
* Event listener
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
	public function __construct(\phpbb\config\config $config, \phpbb\template\template $template, \phpbb\user $user, $phpbb_root_path, $php_ext, \phpbb\auth\auth $auth, \phpbb\request\request_interface $request)
	{
		$this->template = $template;
		$this->user = $user;
		$this->config = $config;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
		$this->auth = $auth;
		$this->request = $request;

		if (!defined('TAB_FORUMS'))
		{
			define('TAB_FORUMS', 6);
		}
			if (!defined('TAB_USERGROUP'))
		{
			define('TAB_USERGROUP', 12);
		}

	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.page_header_after'			=> 'page_header_after',
			'core.posting_modify_template_vars'		=> 'posting_modify_template_vars',
			'core.adm_page_header'		=> 'adm_page_header',
			'core.acp_manage_forums_display_form'		=> 'acp_manage_forums_display_form',
			'core.modify_mcp_modules_display_option'		=> 'modify_mcp_modules_display_option',
		);
	}
	public function modify_mcp_modules_display_option($event)
	{
		$this->user->add_lang_ext('alg/liveSearch', 'live_search');
		$is_livesearch_mcp = isset($this->config['live_search_on_off_mcp']) & $this->config['live_search_on_off_mcp'] ? true : false;

		$this->template->assign_vars(array(
				'S_LIVESEARCH_MCP'	=>  $is_livesearch_mcp,
		));
		if(!$is_livesearch_mcp)
		{
			return;
		}
		$module = $event['module'];
		$mode = $event['mode'];
		$id = $event['id'];
		$module_name = $module->p_name;
		//print_r($module );
		//print_r($module->p_name );
		//print_r('$module_name = ' . $module_name . '; $mode=' . $mode . '; $id=' . $id);
		$mcp_action = '';
		switch($module_name)
		{
			case 'mcp_main':
				switch($mode)
				{
					case post_details:
						$this->template->assign_vars(array('MCP_POST_DETAILS'		 => true));
					break;
				}
			break;
		}
			$this->template->assign_vars(array(
				'LIVE_SEARCH_MIN_NUM_SYMBLOLS_USER_MCP'	=>  isset($this->config['live_search_min_num_symblols_mcp_user']) ? $this->config['live_search_min_num_symblols_mcp_user'] : 0,
				'LIVE_SEARCH_MIN_NUM_SYMBLOLS_FORUM_MCP'	=>  isset($this->config['live_search_min_num_symblols_mcp_forum']) ? $this->config['live_search_min_num_symblols_mcp_forum'] : 0,
				'LIVE_SEARCH_MIN_NUM_SYMBLOLS_GROUP_MCP'	=>  isset($this->config['live_search_min_num_symblols_mcp_group']) ? $this->config['live_search_min_num_symblols_mcp_group'] : 0,
				'LIVE_SEARCH_MAX_ITEMS_TO_SHOW_MCP'					=>  isset($this->config['live_search_max_items_to_show_mcp'])			? $this->config['live_search_max_items_to_show_mcp'] : 0,
			));
	}

	public function adm_page_header($event)
	{
		$this->user->add_lang_ext('alg/liveSearch', 'live_search');
		$is_livesearch_acp = isset($this->config['live_search_on_off_acp']) & $this->config['live_search_on_off_acp'] ? true : false;

		$this->template->assign_vars(array(
				'S_LIVESEARCH_ACP'	=>  $is_livesearch_acp,
		));
		if(!$is_livesearch_acp)
		{
			return;
		}
		$mode = utf8_normalize_nfc($this->request->variable('mode', '',true));
		$tab = utf8_normalize_nfc($this->request->variable('i', '',true));
		$action = utf8_normalize_nfc($this->request->variable('action', '',true));
		if(is_numeric ($tab))
		{
			switch ($tab)
			{
				case TAB_FORUMS:
					$tab = 'acp_forums';
					break;
				case TAB_USERGROUP:
					$tab = 'acp_users';
					break;
			}
		}
		$page_title = $event['page_title'];

			$this->template->assign_vars(array(
				'U_ADMIN_LIVESEARCH_PATH'				=> './../liveSearch/',
				'LIVE_SEARCH_MIN_NUM_SYMBLOLS_USER_ACP'	=>  isset($this->config['live_search_min_num_symblols_acp_user']) ? $this->config['live_search_min_num_symblols_acp_user'] : 0,
				'LIVE_SEARCH_MIN_NUM_SYMBLOLS_FORUM_ACP'	=>  isset($this->config['live_search_min_num_symblols_acp_forum']) ? $this->config['live_search_min_num_symblols_acp_forum'] : 0,
				'LIVE_SEARCH_MIN_NUM_SYMBLOLS_GROUP_ACP'	=>  isset($this->config['live_search_min_num_symblols_acp_group']) ? $this->config['live_search_min_num_symblols_acp_group'] : 0,
				'LIVE_SEARCH_MAX_ITEMS_TO_SHOW_ACP'					=>  isset($this->config['live_search_max_items_to_show_acp'])			? $this->config['live_search_max_items_to_show_acp'] : 0,
			));
				switch ($tab)
				{
					case 'users':
						switch ($mode)
						{
							case 'groups':
								$this->template->assign_vars(array('S_USER_GROUPS'		 => true));
								break;
						}
					break;
					case 'acp_users':
						switch ($mode)
						{
							case 'overview':
							case '':
								$this->template->assign_vars(array('S_FIND_USER_ACP'		 => true));
								break;
						}
					break;
					case 'acp_groups':
						switch ($mode)
						{
							case 'manage':
							$this->template->assign_vars(array('S_GROUP_MANAGE'		 => true));
								break;
							case 'position':
								$this->template->assign_vars(array('S_GROUP_POSITION'		 => true));
								break;
						}
					break;
					case 'acp_forums':
						switch ($mode)
						{
							case 'manage':
							case '':
								if($action == 'edit')
								{
									$this->template->assign_vars(array('S_FORUM_PARENT_MANAGE'		 => true));
								}
								else
								{
									$this->template->assign_vars(array('S_FORUM_MANAGE'		 => true));
								}
								break;
						}
					break;
					case 'acp_prune':
						switch ($mode)
						{
								case 'forums':
									$this->template->assign_vars(array('S_FORUM_PRUNE'		 => true));
									break;
								case 'users':
									$this->template->assign_vars(array('S_USER_PRUNE'		 => true));
									break;
						}
					break;
					case 'acp_logs':
						switch ($mode)
							{
								case 'mod':
									$this->template->assign_vars(array('S_FORUM_LOG'		 => true));
									break;
							}
					break;
					case 'acp_permissions':
						switch ($mode)
						{
								case 'setting_forum_local':
								case 'setting_mod_local':
								case 'setting_mod_global':
								case 'setting_admin_global':
									$this->template->assign_vars(array('S_FORUM_LOCAL'		 => true));
									break;
								case 'setting_forum_copy':
									$this->template->assign_vars(array('S_FORUM_PERMISSIONS_COPY'		 => true));
									break;
								case 'setting_user_local':
									$this->template->assign_vars(array('S_SETTING_USER_LOCAL'		 => true));
									break;
								case 'setting_group_local':
								case 'setting_group_global':
									$this->template->assign_vars(array('S_GROUP_LOCAL'		 => true));
									break;
								case 'setting_user_global':
									$this->template->assign_vars(array('S_SETTING_USER_GLOBAL'		 => true));
									break;
								case 'view_mod_local':
								case 'view_forum_local':
								//$this->template->assign_vars(array('S_FORUM_MULTIPLE'		 => true));  already exists
									break;
								case 'view_admin_global':
								case 'view_user_global':
								case 'view_mod_global':
									$this->template->assign_vars(array('S_ADMIN_GLOBAL'		 => true));
									break;
						}
						break;
					case 'acp_ban':
						switch ($mode)
						{
							case 'user':
								$this->template->assign_vars(array('S_USER_BAN'		 => true));
								break;
						}
						break;
					case 'acp_email':
						switch ($mode)
						{
								case 'email':
									$this->template->assign_vars(array('S_EMAIL'		 => true));
									break;
						}
						break;
				}
	}
	public function acp_manage_forums_display_form($event)
	{
		$action = $event['action'];
	}

	public function posting_modify_template_vars($event)
	{
		if($this->config['live_search_on_off_similartopic'] )
		{
			$mode = $event['mode'];
			$this->template->assign_vars(array(
				'S_SIMILARTOPIC_SHOW'		=> $mode == 'post' ,
				'MIN_CHARS_FORUM'	=>isset($this->config['live_search_min_num_symblols_forum']) ? $this->config['live_search_min_num_symblols_forum'] : 1,
				'MIN_CHARS_USER'	=>isset($this->config['live_search_min_num_symblols_user']) ? $this->config['live_search_min_num_symblols_user'] : 1,
				'MAX_ITEMS_TO_SHOW_FORUM'	=>isset($this->config['live_search_max_items_to_show_forum']) ?$this->config['live_search_max_items_to_show_forum'] : 20,
				'MAX_ITEMS_TO_SHOW_USER'	=>isset($this->config['live_search_max_items_to_show_user']) ?$this->config['live_search_max_items_to_show_user'] : 20,
			));
		}
	}

	public function page_header_after($event)
	{
		global $forum_id;
		$forum_exclude = $forum_id && isset($this->config['live_search_exclude_forums']) &&  strrpos($this->config['live_search_exclude_forums'], (string) $forum_id) !== false;
		$this->user->add_lang_ext('alg/liveSearch', 'live_search');
		$on_off_forum = isset($this->config['live_search_on_off_forum']) ? (bool) $this->config['live_search_on_off_forum'] : false;
		$on_off_topic = isset($this->config['live_search_on_off_topic']) ? (bool) $this->config['live_search_on_off_topic'] : false;
		$on_off_post = isset($this->config['live_search_on_off_post']) ? (bool) $this->config['live_search_on_off_post'] : false;
		$on_off_user = isset($this->config['live_search_on_off_user']) ? (bool) $this->config['live_search_on_off_user'] : false;
		$live_search_show_for_guest = isset($this->config['live_search_show_for_guest']) ? (bool) $this->config['live_search_show_for_guest'] : true;
		$live_search_hide_after_select = isset($this->config['live_search_hide_after_select']) ? (bool) $this->config['live_search_hide_after_select'] : true;
		$live_search_topic_link_type = isset($this->config['live_search_topic_link_type']) ? (bool) $this->config['live_search_topic_link_type'] : true;
		$is_live_search = $on_off_forum || $on_off_topic || $on_off_user;
		if (!$live_search_show_for_guest)
		{
			$is_live_search = $is_live_search && $this->user->data['is_registered'];
		}
		$this->template->assign_vars(array(
			'U_FORUM_LS_PATH'				=> append_sid("{$this->phpbb_root_path}liveSearch/forum/0/0/0"),
			'U_TOPIC_LS_PATH'				=> append_sid("{$this->phpbb_root_path}liveSearch/topic/0/0/0"),
			'U_SIMILARTOPIC_LS_PATH'				=> append_sid("{$this->phpbb_root_path}liveSearch/similartopic/0/0/0"),
			'U_USER_LS_PATH'				=> append_sid("{$this->phpbb_root_path}liveSearch/user/0/0/0"),
			'U_USERTOPIC_LS_PATH'				=> append_sid("{$this->phpbb_root_path}liveSearch/usertopic/"),
			'U_USERPOST_LS_PATH'				=> append_sid("{$this->phpbb_root_path}liveSearch/userpost/"),
			'U_USER_PM_LS_PATH'				=> append_sid("{$this->phpbb_root_path}liveSearch/userpm/0/0/0"),
			'U_MEMBERLIST_LS_PATH'			=> append_sid("{$this->phpbb_root_path}memberlist.$this->php_ext", "mode="),
			'U_UCP_LS_PATH'			=> append_sid("{$this->phpbb_root_path}ucp.$this->php_ext", "mode="),
			'U_FORUM_REDIRECT'		=> append_sid("{$this->phpbb_root_path}viewforum.$this->php_ext", ""),
			'U_TOPIC_REDIRECT'			=> append_sid("{$this->phpbb_root_path}viewtopic.$this->php_ext", ""),
			'U_PROFILE_LS_PATH'			=>  append_sid("{$this->phpbb_root_path}memberlist.$this->php_ext", "mode=viewprofile&amp;u="),
			'U_PM_LS_PATH'			=>	append_sid("{$this->phpbb_root_path}ucp.$this->php_ext", "i=pm&amp;mode=compose&amp;u="),
			'U_MAIL_LS_PATH'			=>  ($this->config['board_email_form'] && $this->config['email_enable']) ? append_sid("{$this->phpbb_root_path}memberlist.$this->php_ext", "email&amp;u="): (($this->config['board_hide_emails'] && !$this->auth->acl_get('a_email')) ? '' : 'mailto:' ),
			'U_JABBER_LS_PATH'			=>  append_sid("{$this->phpbb_root_path}memberlist.$this->php_ext", "mode=contact&amp;action=jabber&amp;u="),

			'LIVE_SEARCH_ON_OFF_FORUM'	=>  $on_off_forum,
			'LIVE_SEARCH_ON_OFF_TOPIC'	=>  $on_off_topic,
			'LIVE_SEARCH_ON_OFF_POST'	=>  $on_off_post,
			'LIVE_SEARCH_ON_OFF_USER'	=> $on_off_user,
			'S_FORUM_EXCLUDE'	=> (bool) $forum_exclude ,
			'S_LIVE_SEARCH'	=> $is_live_search ,
			'S_CANONICAL_TOPIC_TYPE'	=> $live_search_topic_link_type ,
			'MIN_CHARS_FORUM'	=>isset($this->config['live_search_min_num_symblols_forum']) ? $this->config['live_search_min_num_symblols_forum'] : 1,
			'MIN_CHARS_TOPIC'	=>isset($this->config['live_search_min_num_symblols_topic']) ? $this->config['live_search_min_num_symblols_topic'] : 1,
			'MIN_CHARS_USER'	=>isset($this->config['live_search_min_num_symblols_user']) ? $this->config['live_search_min_num_symblols_user'] : 1,
			'MAX_ITEMS_TO_SHOW_FORUM'	=>isset($this->config['live_search_max_items_to_show_forum']) ?$this->config['live_search_max_items_to_show_forum'] : 20,
			'MAX_ITEMS_TO_SHOW_TOPIC'	=>isset($this->config['live_search_max_items_to_show_topic']) ?$this->config['live_search_max_items_to_show_topic'] : 20,
			'MAX_ITEMS_TO_SHOW_USER'	=>isset($this->config['live_search_max_items_to_show_user']) ?$this->config['live_search_max_items_to_show_user'] : 20,
			'LIVE_SEARCH_SHOW_IN_NEW_WINDOW'	=>isset($this->config['live_search_show_in_new_window']) ?(bool) $this->config['live_search_show_in_new_window'] : false,
			'LIVE_SEARCH_HIDE_AFTER_SELECT'	=>$live_search_hide_after_select,
			'LIVE_SEARCH_USE_EYE_BUTTON'	=>  isset($this->config['live_search_use_eye_button']) ? (bool) $this->config['live_search_use_eye_button'] : false,
			'LIVE_SEARCH_EYE_BUTTON_OPEN_T'	=>  $this->user->lang['LIVE_SEARCH_EYE_BUTTON_OPEN_T'],
			'LIVE_SEARCH_EYE_BUTTON_CLOSE_T'	=>  $this->user->lang['LIVE_SEARCH_EYE_BUTTON_CLOSE_T'],
			));
	}
}
