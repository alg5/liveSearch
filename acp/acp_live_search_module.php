<?php
/**
*
* @author Alg
* @version 1.0.0.0
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace alg\liveSearch\acp;

/**
* @package acp
*/
class acp_live_search_module
{
	var $u_action;
	var $new_config = array();

	function main($id, $mode)
	{
		global $db, $user, $auth, $template, $request;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx, $phpbb_log;

		$this->tpl_name = 'acp_live_search';
		$this->page_title = 'ACP_LIVE_SEARCH_SETTINGS';
		$action	= $request->variable('action', '');
		$submit = (isset($_POST['submit'])) ? true : false;

		$form_key = 'acp_live_search';
		add_form_key($form_key);
		$error = array();

		// We validate the complete config if whished

		if ($submit && !check_form_key($form_key))
		{
			$error[] = $user->lang['FORM_INVALID'];
		}
		// Do not write values if there is an error
		if (sizeof($error))
		{
			$submit = false;
		}

		if ($submit)
		{
			$live_search_on_off_forum = $request->variable('live_search_on_off_forum', false);
			$live_search_on_off_topic = $request->variable('live_search_on_off_topic', false);
			$live_search_on_off_user = $request->variable('live_search_on_off_user', false);
			$live_search_on_off_similartopic = $request->variable('live_search_on_off_similartopic', false);
			$live_search_on_off_acp = $request->variable('live_search_on_off_acp', false);
			$live_search_on_off_mcp = $request->variable('live_search_on_off_mcp', false);
			$live_search_show_in_new_window = $request->variable('live_search_show_in_new_window', 0);
			$live_search_show_for_guest = $request->variable('live_search_show_for_guest', 1);
			$live_search_hide_after_select = $request->variable('live_search_hide_after_select', 1);
			$live_search_topic_link_type = $request->variable('live_search_topic_link_type', 1);
			$live_search_use_eye_button = $request->variable('live_search_use_eye_button', 1);
			$live_search_exclude_forums = $request->variable('live_search_exclude_forums', '');

			$config->set('live_search_on_off_forum', $live_search_on_off_forum);
			$config->set('live_search_on_off_topic', $live_search_on_off_topic);
			$config->set('live_search_on_off_user', $live_search_on_off_user);
			$config->set('live_search_on_off_similartopic', $live_search_on_off_similartopic);
			$config->set('live_search_on_off_acp', $live_search_on_off_acp);
			$config->set('live_search_on_off_mcp', $live_search_on_off_mcp);
			$config->set('live_search_show_in_new_window', $live_search_show_in_new_window);
			$config->set('live_search_show_for_guest', $live_search_show_for_guest);
			$config->set('live_search_hide_after_select', $live_search_hide_after_select);
			$config->set('live_search_topic_link_type', $live_search_topic_link_type);
			$config->set('live_search_use_eye_button', $live_search_use_eye_button);
			$config->set('live_search_exclude_forums', $live_search_exclude_forums);

			$phpbb_log->add('admin', $user->data['user_id'], $user->ip, 'LOG_CONFIG_' . strtoupper($mode));

			if ($live_search_on_off_forum)
			{
				$live_search_min_num_symblols_forum= $request->variable('live_search_min_num_symblols_forum', 5);
				$live_search_max_items_to_show_forum= $request->variable('live_search_max_items_to_show_forum', 20);
				$config->set('live_search_min_num_symblols_forum', $live_search_min_num_symblols_forum);
				$config->set('live_search_max_items_to_show_forum', $live_search_max_items_to_show_forum);
			}
			if ($live_search_on_off_topic)
			{
				$live_search_min_num_symblols_topic= $request->variable('live_search_min_num_symblols_topic', 5);
				$live_search_max_items_to_show_topic= $request->variable('live_search_max_items_to_show_topic', 20);
				$config->set('live_search_min_num_symblols_topic', $live_search_min_num_symblols_topic);
				$config->set('live_search_max_items_to_show_topic', $live_search_max_items_to_show_topic);
			}
			if ($live_search_on_off_user)
			{
				$live_search_min_num_symblols_user= $request->variable('live_search_min_num_symblols_user', 5);
				$live_search_max_items_to_show_user= $request->variable('live_search_max_items_to_show_user', 20);
				$config->set('live_search_min_num_symblols_user', $live_search_min_num_symblols_user);
				$config->set('live_search_max_items_to_show_user', $live_search_max_items_to_show_user);
			}

			if ($live_search_on_off_similartopic)
			{
				$live_search_min_num_symblols_similartopic= $request->variable('live_search_min_num_symblols_similartopic', 5);
				$live_search_max_items_to_show_similartopic= $request->variable('live_search_max_items_to_show_similartopic', 20);
				$config->set('live_search_min_num_symblols_similartopic', $live_search_min_num_symblols_similartopic);
				$config->set('live_search_max_items_to_show_similartopic', $live_search_max_items_to_show_similartopic);
			}
			if ($live_search_on_off_acp)
			{
				$live_search_min_num_symblols_acp_user= $request->variable('live_search_min_num_symblols_acp_user', 1);
				$live_search_min_num_symblols_acp_forum= $request->variable('live_search_min_num_symblols_acp_forum', 1);
				$live_search_min_num_symblols_acp_group= $request->variable('live_search_min_num_symblols_acp_group', 1);
				$live_search_max_items_to_show_acp= $request->variable('live_search_max_items_to_show_acp', 20);
				$config->set('live_search_min_num_symblols_acp_user', $live_search_min_num_symblols_acp_user);
				$config->set('live_search_min_num_symblols_acp_forum', $live_search_min_num_symblols_acp_forum);
				$config->set('live_search_min_num_symblols_acp_group', $live_search_min_num_symblols_acp_group);
				$config->set('live_search_max_items_to_show_acp', $live_search_max_items_to_show_acp);
			}
			if ($live_search_on_off_mcp)
			{
				$live_search_min_num_symblols_mcp_user= $request->variable('live_search_min_num_symblols_mcp_user', 1);
				$live_search_min_num_symblols_mcp_forum= $request->variable('live_search_min_num_symblols_mcp_forum', 1);
				$live_search_min_num_symblols_mcp_group= $request->variable('live_search_min_num_symblols_mcp_group', 1);
				$live_search_max_items_to_show_mcp= $request->variable('live_search_max_items_to_show_mcp', 20);
				$config->set('live_search_min_num_symblols_mcp_user', $live_search_min_num_symblols_mcp_user);
				$config->set('live_search_min_num_symblols_mcp_forum', $live_search_min_num_symblols_mcp_forum);
				$config->set('live_search_min_num_symblols_mcp_group', $live_search_min_num_symblols_mcp_group);
				$config->set('live_search_max_items_to_show_mcp', $live_search_max_items_to_show_mcp);
			}

			trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
		}

		$template->assign_vars(array(
			'CHECKED_FORUM'	=>  isset($config['live_search_on_off_forum']) & (bool) $config['live_search_on_off_forum'] ? 'checked' : '',
			'CHECKED_TOPIC'	=>  isset($config['live_search_on_off_topic']) & (bool) $config['live_search_on_off_topic'] ? 'checked' : '',
			'CHECKED_USER'	=>  isset($config['live_search_on_off_user']) & (bool) $config['live_search_on_off_user'] ? 'checked' : '',
			'CHECKED_SIMILARTOPIC'	=>  isset($config['live_search_on_off_similartopic']) & (bool) $config['live_search_on_off_similartopic'] ? 'checked' : '',
			'CHECKED_ACP'	=>  isset($config['live_search_on_off_acp']) & (bool) $config['live_search_on_off_acp'] ? 'checked' : '',
			'CHECKED_MCP'	=>  isset($config['live_search_on_off_mcp']) & (bool) $config['live_search_on_off_mcp'] ? 'checked' : '',

			'LIVE_SEARCH_MIN_NUM_SYMBLOLS_FORUM'	=>  isset($config['live_search_min_num_symblols_forum']) ? (int) $config['live_search_min_num_symblols_forum'] : 0,
			'LIVE_SEARCH_MAX_ITEMS_TO_SHOW_FORUM'	=>  isset($config['live_search_max_items_to_show_forum']) ? (int) $config['live_search_max_items_to_show_forum'] : 0,

			'LIVE_SEARCH_MIN_NUM_SYMBLOLS_TOPIC'	=>  isset($config['live_search_min_num_symblols_topic']) ? (int) $config['live_search_min_num_symblols_topic'] : 0,
			'LIVE_SEARCH_MAX_ITEMS_TO_SHOW_TOPIC'	=>  isset($config['live_search_max_items_to_show_topic']) ? (int) $config['live_search_max_items_to_show_topic'] : 0,

			'LIVE_SEARCH_MIN_NUM_SYMBLOLS_USER'	=>  isset($config['live_search_min_num_symblols_user']) ? (int) $config['live_search_min_num_symblols_user'] : 0,
			'LIVE_SEARCH_MAX_ITEMS_TO_SHOW_USER'	=>  isset($config['live_search_max_items_to_show_user']) ? (int) $config['live_search_max_items_to_show_user'] : 0,

			'LIVE_SEARCH_MIN_NUM_SYMBLOLS_SIMILARTOPIC'	=>  isset($config['live_search_min_num_symblols_similartopic']) ? (int) $config['live_search_min_num_symblols_similartopic'] : 0,
			'LIVE_SEARCH_MAX_ITEMS_TO_SHOW_SIMILARTOPIC'	=>  isset($config['live_search_max_items_to_show_similartopic']) ? (int) $config['live_search_max_items_to_show_similartopic'] : 0,

			'LIVE_SEARCH_MIN_NUM_SYMBLOLS_USER_ACP'		 =>  isset($config['live_search_min_num_symblols_acp_user']) ? (int) $config['live_search_min_num_symblols_acp_user'] : 0,
			'LIVE_SEARCH_MIN_NUM_SYMBLOLS_FORUM_ACP'	=>  isset($config['live_search_min_num_symblols_acp_forum']) ? (int) $config['live_search_min_num_symblols_acp_forum'] : 0,
			'LIVE_SEARCH_MIN_NUM_SYMBLOLS_GROUP_ACP'	=>  isset($config['live_search_min_num_symblols_acp_group']) ? (int) $config['live_search_min_num_symblols_acp_group'] : 0,
			'LIVE_SEARCH_MAX_ITEMS_TO_SHOW_ACP'					=>  isset($config['live_search_max_items_to_show_acp'])	? (int) $config['live_search_max_items_to_show_acp'] : 0,

			'LIVE_SEARCH_MIN_NUM_SYMBLOLS_USER_MCP'		 =>  isset($config['live_search_min_num_symblols_mcp_user']) ? (int) $config['live_search_min_num_symblols_mcp_user'] : 0,
			'LIVE_SEARCH_MIN_NUM_SYMBLOLS_FORUM_MCP'	=>  isset($config['live_search_min_num_symblols_mcp_forum']) ? (int) $config['live_search_min_num_symblols_mcp_forum'] : 0,
			'LIVE_SEARCH_MIN_NUM_SYMBLOLS_GROUP_MCP'	=>  isset($config['live_search_min_num_symblols_mcp_group']) ? (int) $config['live_search_min_num_symblols_mcp_group'] : 0,
			'LIVE_SEARCH_MAX_ITEMS_TO_SHOW_MCP'					=>  isset($config['live_search_max_items_to_show_mcp'])	? (int) $config['live_search_max_items_to_show_mcp'] : 0,

			'LIVE_SEARCH_SHOW_IN_NEW_WINDOW'	=>  isset($config['live_search_show_in_new_window']) ? (int) $config['live_search_show_in_new_window'] : 0,
			'LIVE_SEARCH_SHOW_FOR_GUEST'	=>  isset($config['live_search_show_for_guest']) ? (int) $config['live_search_show_for_guest'] : 1,
			'LIVE_SEARCH_HIDE_AFTER_SELECT'	=>  isset($config['live_search_hide_after_select']) ? (int) $config['live_search_hide_after_select'] : 1,
			'LIVE_SEARCH_TOPIC_LINK_TYPE'	=>  isset($config['live_search_topic_link_type']) ? (int) $config['live_search_topic_link_type'] : 1,
			'LIVE_SEARCH_USE_EYE_BUTTON'	=>  isset($config['live_search_use_eye_button']) ? (int) $config['live_search_use_eye_button'] : 1,
			'LIVE_SEARCH_EXCLUDE_FORUMS'	=>  isset($config['live_search_exclude_forums']) ? (int) $config['live_search_exclude_forums'] : '',

			'S_ERROR'			=> (sizeof($error)) ? true : false,
			'ERROR_MSG'			=> implode('<br />', $error),
			'U_ACTION'			=> $this->u_action,
				)
		);

	}
}
