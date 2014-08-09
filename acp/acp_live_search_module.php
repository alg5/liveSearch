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
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* @package acp
*/
class acp_live_search_module
{
	var $u_action;
	var $new_config = array();

	function main($id, $mode)
	{
		global $db, $user, $auth, $template;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;
        
		$this->tpl_name = 'acp_live_search';
		$this->page_title = 'ACP_LIVE_SEARCH_SETTINGS';
		$action	= request_var('action', '');
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
            $live_search_on_off_forum = request_var('live_search_on_off_forum', false);
            $live_search_on_off_topic = request_var('live_search_on_off_topic', false);
            $live_search_on_off_user = request_var('live_search_on_off_user', false);
            $live_search_show_in_new_window = request_var('live_search_show_in_new_window', 0);

            set_config('live_search_on_off_forum', $live_search_on_off_forum);
			set_config('live_search_on_off_topic', $live_search_on_off_topic);
			set_config('live_search_on_off_user', $live_search_on_off_user);
			set_config('live_search_show_in_new_window', $live_search_show_in_new_window);
			add_log('admin', 'LOG_CONFIG_' . strtoupper($mode));
            
            if($live_search_on_off_forum)
            {
                $live_search_min_num_symblols_forum= request_var('live_search_min_num_symblols_forum', 5);
                $live_search_max_items_to_show_forum= request_var('live_search_max_items_to_show_forum', 20);
			    set_config('live_search_min_num_symblols_forum', $live_search_min_num_symblols_forum);
			    set_config('live_search_max_items_to_show_forum', $live_search_max_items_to_show_forum);
            }
            if($live_search_on_off_topic)
            {
                $live_search_min_num_symblols_topic= request_var('live_search_min_num_symblols_topic', 5);
                $live_search_max_items_to_show_topic= request_var('live_search_max_items_to_show_topic', 20);
			    set_config('live_search_min_num_symblols_topic', $live_search_min_num_symblols_topic);
			    set_config('live_search_max_items_to_show_topic', $live_search_max_items_to_show_topic);
            }
            if($live_search_on_off_user)
            {
                $live_search_min_num_symblols_user= request_var('live_search_min_num_symblols_user', 5);
                $live_search_max_items_to_show_user= request_var('live_search_max_items_to_show_user', 20);
			    set_config('live_search_min_num_symblols_user', $live_search_min_num_symblols_user);
			    set_config('live_search_max_items_to_show_user', $live_search_max_items_to_show_user);
            }
            
            

			trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
		}

        $template->assign_vars(array(
			//'L_TITLE'			=> $user->lang[$display_vars['title']],
			//'L_TITLE_EXPLAIN'	=> $user->lang[$display_vars['title'] . '_EXPLAIN'],
			'L_ACP_LIVE_SEARCH_MOD_VER'	=> $user->lang['ACP_LIVE_SEARCH_MOD_VER'],
			'LIVE_SEARCH_MOD_VERSION'	=> isset($config['live_search']) ? $config['live_search'] : false,
			'CHECKED_FORUM'	=>  isset($config['live_search_on_off_forum']) & $config['live_search_on_off_forum'] ? 'checked' : '',
			'CHECKED_TOPIC'	=>  isset($config['live_search_on_off_topic']) & $config['live_search_on_off_topic'] ? 'checked' : '',
			'CHECKED_USER'	=>  isset($config['live_search_on_off_user']) & $config['live_search_on_off_user'] ? 'checked' : '',
			'LIVE_SEARCH_MIN_NUM_SYMBLOLS_FORUM'	=>  isset($config['live_search_min_num_symblols_forum']) ? $config['live_search_min_num_symblols_forum'] : 0,
			'LIVE_SEARCH_MAX_ITEMS_TO_SHOW_FORUM'	=>  isset($config['live_search_max_items_to_show_forum']) ? $config['live_search_max_items_to_show_forum'] : 0,
			'LIVE_SEARCH_MIN_NUM_SYMBLOLS_TOPIC'	=>  isset($config['live_search_min_num_symblols_topic']) ? $config['live_search_min_num_symblols_topic'] : 0,
			'LIVE_SEARCH_MAX_ITEMS_TO_SHOW_TOPIC'	=>  isset($config['live_search_max_items_to_show_topic']) ? $config['live_search_max_items_to_show_topic'] : 0,
			'LIVE_SEARCH_MIN_NUM_SYMBLOLS_USER'	=>  isset($config['live_search_min_num_symblols_user']) ? $config['live_search_min_num_symblols_user'] : 0,
			'LIVE_SEARCH_MAX_ITEMS_TO_SHOW_USER'	=>  isset($config['live_search_max_items_to_show_user']) ? $config['live_search_max_items_to_show_user'] : 0,
			'LIVE_SEARCH_SHOW_IN_NEW_WINDOW'	=>  isset($config['live_search_show_in_new_window']) ? $config['live_search_show_in_new_window'] : 0,

			'S_ERROR'			=> (sizeof($error)) ? true : false,
			'ERROR_MSG'			=> implode('<br />', $error),

			'U_ACTION'			=> $this->u_action)
		);

	}
}
