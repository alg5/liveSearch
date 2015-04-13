<?php
/** 
*
* liveSearch [English]
*
* @package liveSearch
* @copyright (c) 2014 alg
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'INCORRECT_SEARCH'			=> 'Incorrect search parameters',
	'LIVE_SEARCH_CAPTION'		=> 'Quick search',
	'LIVE_SEARCH_FORUM'		=> 'Forums',
	'LIVE_SEARCH_FORUM_TXT'		=> 'Forum name…',
	'LIVE_SEARCH_FORUM_T'			=> 'For quick search start typing the name of the forum/category',
	'LIVE_SEARCH_GO_PROFILE'				=> 'Go to profile',
	'LIVE_SEARCH_POSTS_BY_USER'		=> 'User posts',
	'LIVE_SEARCH_POSTS_BY_USER_BOARD'		=> '>In the board',
	'LIVE_SEARCH_POSTS_BY_USER_FORUM'		=> '>>In the forum',
	'LIVE_SEARCH_POSTS_BY_USER_TOPIC'		=> '>>>In the topic',
	'LIVE_SEARCH_TOOLTIP_ALL'		=> 'Search in all the forums of the board',
	'LIVE_SEARCH_TOOLTIP_BY_FORUM'		=> 'Search in a forum ',
	'LIVE_SEARCH_TOOLTIP_BY_TOPIC'		=> 'Search in a topic ',
	'LIVE_SEARCH_TOPIC'		=> 'Topics',
	'LIVE_SEARCH_TOPICS_BY_USER'		=> 'User’s topic',
	'LIVE_SEARCH_TOPICS_BY_USER_BOARD'		=> '>In the board',
	'LIVE_SEARCH_TOPICS_BY_USER_FORUM'		=> '>>In the forum',
	'LIVE_SEARCH_TXT'		=> 'Topic name…',
	'LIVE_SEARCH_T'			=> 'For quick search start typing the name of the topic',
	'LIVE_SEARCH_USER'		=> 'Users',
	'LIVESEARCH_USER_TXT'	=> 'Name…',
	'LIVESEARCH_USER_T'	=> 'For quick search start typing the username',
	'LIVESEARCH_USERTOPIC_DISABLED'	=> 'Quick search for topics in the current forum is turned off by the administrator',
	'LIVESEARCH_USERTOPIC_RESULT'	=> 'Topics of the user %1$s',
	'LIVESEARCH_USERTOPIC_RESULT_IN_FORUM'	=> 'Topics of the user %1$s in the forum %2$s',
	'LIVESEARCH_USERTOPIC_RESULT_IN_SUBFORUMS'	=> ' and its subforums',
	'LIVESEARCH_USERPOST_DISABLED'	=> 'Quick search for posts in the current forum is turned off by the administrator',
	'LIVESEARCH_USERPOST_RESULT'	=> 'Posts of the user %1$s',
	'LIVESEARCH_USERPOST_RESULT_IN_FORUM'	=> 'Posts of the user %1$s in the forum %2$s',
	'LIVESEARCH_USERPOST_RESULT_IN_TOPIC'	=> 'Posts of the user %1$s in the topic %2$s of the forum  %3$s',
	'LIVE_SEARCH_EYE_BUTTON_OPEN_T'	=> 'Show search panel',
	'LIVE_SEARCH_EYE_BUTTON_CLOSE_T'	=> 'Hide search panel',
));
