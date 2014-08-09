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
* @ignore
*/
if (!defined('IN_PHPBB'))
{
    exit;
}

/**
* Event listener
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{

    public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\auth\auth $auth, \phpbb\template\template $template, \phpbb\user $user, $phpbb_root_path, $php_ext)
    {
     

    
        $this->template = $template;
        $this->user = $user;
		$this->auth = $auth;
		$this->db = $db;
		$this->config = $config;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
        
  
    }

	static public function getSubscribedEvents()
	{
		return array(
            'core.page_header_after'		                => 'page_header_after',
            'core.search_get_topic_data'		=> 'search_get_topic_data',
            'core.search_get_posts_data'		=> 'search_get_posts_data',
		);
	}
    
    public function page_header_after($event)
    {
        $this->user->add_lang_ext('alg/liveSearch', 'live_search');
        $on_off_forum = isset($this->config['live_search_on_off_forum']) ? (bool)$this->config['live_search_on_off_forum'] : false;
        $on_off_topic = isset($this->config['live_search_on_off_topic']) ? (bool)$this->config['live_search_on_off_topic'] : false;
        $on_off_user = isset($this->config['live_search_on_off_user']) ? (bool)$this->config['live_search_on_off_user'] : false;
        $this->template->assign_vars(array(
	        'U_FORUM_REDIRECT'		=> append_sid("{$this->phpbb_root_path}viewforum.$this->php_ext", ""),
	        'U_TOPIC_REDIRECT'		=> append_sid("{$this->phpbb_root_path}viewtopic.$this->php_ext", ""),
            'LIVE_SEARCH_ON_OFF_FORUM'	=>  $on_off_forum,
            'LIVE_SEARCH_ON_OFF_TOPIC'	=>  $on_off_topic,
            'LIVE_SEARCH_ON_OFF_USER'	=> $on_off_user,
            'S_LIVE_SEARCH'	=> $on_off_forum || $on_off_topic || $on_off_user,
            'MIN_CHARS_FORUM'	=>isset($this->config['live_search_min_num_symblols_forum']) ? $this->config['live_search_min_num_symblols_forum'] : 1,
            'MIN_CHARS_TOPIC'	=>isset($this->config['live_search_min_num_symblols_topic']) ? $this->config['live_search_min_num_symblols_topic'] : 1,
            'MIN_CHARS_USER'	=>isset($this->config['live_search_min_num_symblols_user']) ? $this->config['live_search_min_num_symblols_user'] : 1,
            'MAX_ITEMS_TO_SHOW_FORUM'	=>isset($this->config['live_search_max_items_to_show_forum']) ?$this->config['live_search_max_items_to_show_forum'] : 20,
            'MAX_ITEMS_TO_SHOW_TOPIC'	=>isset($this->config['live_search_max_items_to_show_topic']) ?$this->config['live_search_max_items_to_show_topic'] : 20,
            'MAX_ITEMS_TO_SHOW_USER'	=>isset($this->config['live_search_max_items_to_show_user']) ?$this->config['live_search_max_items_to_show_user'] : 20,
            'LIVE_SEARCH_SHOW_IN_NEW_WINDOW'	=>isset($this->config['live_search_show_in_new_window']) ?(bool)$this->config['live_search_show_in_new_window'] : false,

		    ));    
    }
  
    public function search_get_topic_data($event)
    {
        $ls = request_var('ls', 0);
        if ($ls == 0) return;
        $forum_id = request_var('forum_id', 0);
        $author_id = request_var('author_id', 0);
        $sql_where = $event['sql_where'];
        $sql_where .= ' AND t.topic_poster = ' . $author_id . $this->build_subforums_search($forum_id);
        $event['sql_where'] = $sql_where;
    }    
    public function search_get_posts_data($event)
    {
        $ls = request_var('ls', 0);
        if ($ls == 0) return;
    
        $forum_id = request_var('forum_id', 0);
        $topic_id = request_var('topic_id', 0);
        if ($forum_id == 0 && $topic_id == 0) return;
        
        
        $sql_array = $event['sql_array'];
        $where = $sql_array['WHERE'];
        if ($topic_id)
        {
            $where .= ' AND p.topic_id = ' . $topic_id;
           
        }
         $sql_array['WHERE'] = $where;
         
        $event['sql_array'] = $sql_array;
        // print_r($event['sql_array']['WHERE']);
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
        
        return ' AND (f.left_id  >= ' . $row['left_id'] . '  AND f.right_id <= ' .  $row['right_id'] . ' )';
    }

	
}
