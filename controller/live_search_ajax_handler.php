<?php
/**
*
* @author Alg
* @version $Id: toplist.php,v 135 2012-10-10 10:02:51 Палыч $
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
   public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\auth\auth $auth, \phpbb\template\template $template, \phpbb\user $user, \phpbb\cache\driver\driver_interface $cache, $phpbb_root_path, $php_ext, \phpbb\request\request_interface $request, $table_prefix)
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
        $this->return = array(); // save returned data in here
        $this->error = array(); // save errors in here

    }

	public function main($action, $forum, $user)
	{
		// Grab data
        $q = utf8_strtoupper(utf8_normalize_nfc(request_var('q', '',true)));
        if(!$q)
        {
	        exit();
        }	 
		$this->user->add_lang_ext('alg/live_search', 'live_search');

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
           
            default:
                $this->error[] = array('error' => $this->user->lang['INCORRECT_SEARCH']);
            

        }

	}
    
    private function live_search_forum($action, $forum_id, $q)
    {
    
        $sql = "SELECT  f.forum_id, f.forum_name, pf.forum_name as forum_parent_name  " .
                " FROM " . FORUMS_TABLE . " f LEFT JOIN " . FORUMS_TABLE . " pf on f.parent_id = pf.forum_id " .
                " WHERE  UPPER(f.forum_name) " . $this->db->sql_like_expression($this->db->get_any_char()  . $q . $this->db->get_any_char() ) .
                " ORDER BY f.forum_name";
        $result = $this->db->sql_query($sql);
        $arr_res = $arr_priority1 = $arr_priority2 = array();
		while ($row = $this->db->sql_fetchrow($result))
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
		$this->db->sql_freeresult($result);
        
        $arr_res = array_merge((array)$arr_priority1, (array)$arr_priority2);
        $message = '';
        foreach ($arr_res as $forum_info)
        {
            $forum_id = $forum_info['forum_id'];
 			$key = htmlspecialchars_decode($forum_info['forum_name'] . '(' . $forum_info['forum_parent_name'] . ')'  );
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
        " WHERE t.topic_status <> 2 " .
        "  AND UPPER(t.topic_title) " . $this->db->sql_like_expression($this->db->get_any_char() . $q . $this->db->get_any_char()) .
        " AND t.topic_approved = 1 ". $this->build_subforums_search($forum_id) . 
		" ORDER BY topic_title";
        $result = $this->db->sql_query($sql);
		$topic_list = array();
        
        $arr_res = $arr_priority1 = $arr_priority2 = array();
		while ($row = $this->db->sql_fetchrow($result))
        {
            $pos = strpos(utf8_strtoupper($row['topic_title']), $q);
            if ($pos !== false && $this->auth->acl_get('f_read', $row['forum_id'])) 
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
 			$key = htmlspecialchars_decode($topic_info['topic_title'] . '(' . $topic_info['forum_name'] . ')'  );
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
    
        $subforums = 'AND t.forum_id IN (';
        while ($row = $this->db->sql_fetchrow($result))
        {
            $subforums .= ( $row['forum_id'] . ',');
        }
        $subforums = substr($subforums, 0, -1) . " )"; 
        return $subforums;
    }
    
}
