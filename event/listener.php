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

	public function __construct(\phpbb\config\config $config, \phpbb\template\template $template, \phpbb\user $user, $phpbb_root_path, $php_ext, \phpbb\auth\auth $auth)
	{
		$this->template = $template;
		$this->user = $user;
		$this->config = $config;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
		$this->auth = $auth;

	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.page_header_after'			=> 'page_header_after',
			'core.posting_modify_template_vars'		=> 'posting_modify_template_vars',
			'core.adm_page_header'		=> 'adm_page_header',
		);
	}

	public function adm_page_header($event)
	{
			$this->template->assign_vars(array(
				'U_USER_LS_ACP_PATH'				=> append_sid("{$this->phpbb_root_path}liveSearch/user/0/0"),
			));

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
		$this->user->add_lang_ext('alg/liveSearch', 'live_search');
		$on_off_forum = isset($this->config['live_search_on_off_forum']) ? (bool) $this->config['live_search_on_off_forum'] : false;
		$on_off_topic = isset($this->config['live_search_on_off_topic']) ? (bool) $this->config['live_search_on_off_topic'] : false;
		$on_off_user = isset($this->config['live_search_on_off_user']) ? (bool) $this->config['live_search_on_off_user'] : false;
		$live_search_show_for_guest = isset($this->config['live_search_show_for_guest']) ? (bool) $this->config['live_search_show_for_guest'] : true;
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
			'U_PROFILE_LS_PATH'			=>  append_sid("{$this->phpbb_root_path}memberlist.$this->php_ext", 'mode=viewprofile&amp;u='),
			'U_PM_LS_PATH'			=>   append_sid("{$this->phpbb_root_path}ucp.$this->php_ext", 'i=pm&amp;mode=compose&amp;u' ),
			'U_MAIL_LS_PATH'			=>  ($this->config['board_email_form'] && $this->config['email_enable']) ? append_sid("{$this->phpbb_root_path}memberlist.$this->php_ext", "email&amp;u="): (($this->config['board_hide_emails'] && !$this->auth->acl_get('a_email')) ? '' : 'mailto:' ),
			'U_JABBER_LS_PATH'			=>  append_sid("$this->phpbb_root_path}memberlist.$this->php_ext", "mode=contact&amp;action=jabber&amp;u="),

			'LIVE_SEARCH_ON_OFF_FORUM'	=>  $on_off_forum,
			'LIVE_SEARCH_ON_OFF_TOPIC'	=>  $on_off_topic,
			'LIVE_SEARCH_ON_OFF_USER'	=> $on_off_user,
			'S_LIVE_SEARCH'	=> $is_live_search ,
			'MIN_CHARS_FORUM'	=>isset($this->config['live_search_min_num_symblols_forum']) ? $this->config['live_search_min_num_symblols_forum'] : 1,
			'MIN_CHARS_TOPIC'	=>isset($this->config['live_search_min_num_symblols_topic']) ? $this->config['live_search_min_num_symblols_topic'] : 1,
			'MIN_CHARS_USER'	=>isset($this->config['live_search_min_num_symblols_user']) ? $this->config['live_search_min_num_symblols_user'] : 1,
			'MAX_ITEMS_TO_SHOW_FORUM'	=>isset($this->config['live_search_max_items_to_show_forum']) ?$this->config['live_search_max_items_to_show_forum'] : 20,
			'MAX_ITEMS_TO_SHOW_TOPIC'	=>isset($this->config['live_search_max_items_to_show_topic']) ?$this->config['live_search_max_items_to_show_topic'] : 20,
			'MAX_ITEMS_TO_SHOW_USER'	=>isset($this->config['live_search_max_items_to_show_user']) ?$this->config['live_search_max_items_to_show_user'] : 20,
			'LIVE_SEARCH_SHOW_IN_NEW_WINDOW'	=>isset($this->config['live_search_show_in_new_window']) ?(bool) $this->config['live_search_show_in_new_window'] : false,
			'LIVE_SEARCH_USE_EYE_BUTTON'	=>  isset($this->config['live_search_use_eye_button']) ? (bool) $this->config['live_search_use_eye_button'] : false,
			'LIVE_SEARCH_EYE_BUTTON_OPEN_T'	=>  $this->user->lang['LIVE_SEARCH_EYE_BUTTON_OPEN_T'],
			'LIVE_SEARCH_EYE_BUTTON_CLOSE_T'	=>  $this->user->lang['LIVE_SEARCH_EYE_BUTTON_CLOSE_T'],
			));
	}
}
