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
			$live_search_show_in_new_window = $request->variable('live_search_show_in_new_window', 0);
			$live_search_show_for_guest = $request->variable('live_search_show_for_guest', 1);
			$live_search_use_eye_button = $request->variable('live_search_use_eye_button', 1);
			$live_search_exclude_forums = $request->variable('live_search_exclude_forums', '');

			$config->set('live_search_on_off_forum', $live_search_on_off_forum);
			$config->set('live_search_on_off_topic', $live_search_on_off_topic);
			$config->set('live_search_on_off_user', $live_search_on_off_user);
			$config->set('live_search_on_off_similartopic', $live_search_on_off_similartopic);
			$config->set('live_search_show_in_new_window', $live_search_show_in_new_window);
			$config->set('live_search_show_for_guest', $live_search_show_for_guest);
			$config->set('live_search_use_eye_button', $live_search_use_eye_button);
			$config->set('live_search_exclude_forums', $live_search_exclude_forums);

			$phpbb_log->add('admin', $user->data['user_id'], $user->ip, 'LOG_CONFIG_' . strtoupper($mode));

			if($live_search_on_off_forum)
			{
				$live_search_min_num_symblols_forum= $request->variable('live_search_min_num_symblols_forum', 5);
				$live_search_max_items_to_show_forum= $request->variable('live_search_max_items_to_show_forum', 20);
				$config->set('live_search_min_num_symblols_forum', $live_search_min_num_symblols_forum);
				$config->set('live_search_max_items_to_show_forum', $live_search_max_items_to_show_forum);
			}
			if($live_search_on_off_topic)
			{
				$live_search_min_num_symblols_topic= $request->variable('live_search_min_num_symblols_topic', 5);
				$live_search_max_items_to_show_topic= $request->variable('live_search_max_items_to_show_topic', 20);
				$config->set('live_search_min_num_symblols_topic', $live_search_min_num_symblols_topic);
				$config->set('live_search_max_items_to_show_topic', $live_search_max_items_to_show_topic);
			}
			if($live_search_on_off_user)
			{
				$live_search_min_num_symblols_user= $request->variable('live_search_min_num_symblols_user', 5);
				$live_search_max_items_to_show_user= $request->variable('live_search_max_items_to_show_user', 20);
				$config->set('live_search_min_num_symblols_user', $live_search_min_num_symblols_user);
				$config->set('live_search_max_items_to_show_user', $live_search_max_items_to_show_user);
			}

			if($live_search_on_off_similartopic)
			{
				$live_search_min_num_symblols_similartopic= $request->variable('live_search_min_num_symblols_similartopic', 5);
				$live_search_max_items_to_show_similartopic= $request->variable('live_search_max_items_to_show_similartopic', 20);
				$config->set('live_search_min_num_symblols_similartopic', $live_search_min_num_symblols_similartopic);
				$config->set('live_search_max_items_to_show_similartopic', $live_search_max_items_to_show_similartopic);
			}

			trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
		}

		$template->assign_vars(array(
			'CHECKED_FORUM'	=>  isset($config['live_search_on_off_forum']) & $config['live_search_on_off_forum'] ? 'checked' : '',
			'CHECKED_TOPIC'	=>  isset($config['live_search_on_off_topic']) & $config['live_search_on_off_topic'] ? 'checked' : '',
			'CHECKED_USER'	=>  isset($config['live_search_on_off_user']) & $config['live_search_on_off_user'] ? 'checked' : '',
			'CHECKED_SIMILARTOPIC'	=>  isset($config['live_search_on_off_similartopic']) & $config['live_search_on_off_similartopic'] ? 'checked' : '',
			'LIVE_SEARCH_MIN_NUM_SYMBLOLS_FORUM'	=>  isset($config['live_search_min_num_symblols_forum']) ? $config['live_search_min_num_symblols_forum'] : 0,
			'LIVE_SEARCH_MAX_ITEMS_TO_SHOW_FORUM'	=>  isset($config['live_search_max_items_to_show_forum']) ? $config['live_search_max_items_to_show_forum'] : 0,
			'LIVE_SEARCH_MIN_NUM_SYMBLOLS_TOPIC'	=>  isset($config['live_search_min_num_symblols_topic']) ? $config['live_search_min_num_symblols_topic'] : 0,
			'LIVE_SEARCH_MAX_ITEMS_TO_SHOW_TOPIC'	=>  isset($config['live_search_max_items_to_show_topic']) ? $config['live_search_max_items_to_show_topic'] : 0,
			'LIVE_SEARCH_MIN_NUM_SYMBLOLS_USER'	=>  isset($config['live_search_min_num_symblols_user']) ? $config['live_search_min_num_symblols_user'] : 0,
			'LIVE_SEARCH_MAX_ITEMS_TO_SHOW_USER'	=>  isset($config['live_search_max_items_to_show_user']) ? $config['live_search_max_items_to_show_user'] : 0,
			'LIVE_SEARCH_MIN_NUM_SYMBLOLS_SIMILARTOPIC'	=>  isset($config['live_search_min_num_symblols_similartopic']) ? $config['live_search_min_num_symblols_similartopic'] : 0,
			'LIVE_SEARCH_MAX_ITEMS_TO_SHOW_SIMILARTOPIC'	=>  isset($config['live_search_max_items_to_show_similartopic']) ? $config['live_search_max_items_to_show_similartopic'] : 0,
			'LIVE_SEARCH_SHOW_IN_NEW_WINDOW'	=>  isset($config['live_search_show_in_new_window']) ? $config['live_search_show_in_new_window'] : 0,
			'LIVE_SEARCH_SHOW_FOR_GUEST'	=>  isset($config['live_search_show_for_guest']) ? $config['live_search_show_for_guest'] : 1,
			'LIVE_SEARCH_USE_EYE_BUTTON'	=>  isset($config['live_search_use_eye_button']) ? $config['live_search_use_eye_button'] : 1,
			'LIVE_SEARCH_EXCLUDE_FORUMS'	=>  isset($config['live_search_exclude_forums']) ? $config['live_search_exclude_forums'] : '',

			'S_ERROR'			=> (sizeof($error)) ? true : false,
			'ERROR_MSG'			=> implode('<br />', $error),

			'U_ACTION'			=> $this->u_action)
		);

	}
}
