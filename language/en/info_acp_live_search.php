<?php
/** 
*
* liveSearch [Russian]
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
	'ACP_LIVE_SEARCH'		=> 'Quick search',
	'ACP_LIVE_SEARCH_SETTINGS'				=> 'Quick search settings',
	'ACP_LIVE_SEARCH_SETTINGS_FORUMS'				=> 'Quick search of forums',
	'ACP_LIVE_SEARCH_SETTINGS_TOPICS'				=> 'Quick search of topics',
	'ACP_LIVE_SEARCH_SETTINGS_SIMILARTOPICS'				=> 'Quick search of similar topics when creating a new topic',
	//'ACP_LIVE_SEARCH_SETTINGS_TOPICS_EXPLAIN'				=> 'Быстрый(живой) поиск по названию темы, если включено, поиск осуществляется либо по всей конференции с главной страницы, либо по конкретному форуму и всем содержашимся в нём подфорумам <br /><strong>Важно: Для корректной работы настроек расширения требуется MySQL версии 4.1 или выше!</strong><br />',
	'ACP_LIVE_SEARCH_SETTINGS_TOPICS_EXPLAIN'				=>'<strong>Important: The extension requires MySQL version 4.1 or higher!</strong><br />',
	'ACP_LIVE_SEARCH_SETTINGS_USERS'				=> 'Quick search of users',

	'LIVE_SEARCH_MIN_NUM_SYMBLOLS'				=> 'Min number of characters for search',
	'LIVE_SEARCH_MIN_NUM_SYMBLOLS_EXPLAIN'				=> 'The search starts after the min number of characters are entered.',
	'LIVE_SEARCH_MAX_ITEMS_TO_SHOW'				=> 'Number of results',
	'LIVE_SEARCH_MAX_ITEMS_TO_SHOW_EXPLAIN'				=> 'Limits the result number that is shown in the dropdown list. The recommended value is 20.',
	'LIVE_SEARCH_EXCLUDE_FORUMS'		=> 'Forums that are excluded from the live search',
	'LIVE_SEARCH_EXCLUDE_FORUMS_EXPLAIN'		=> 'List of ID forums separated by a comma.',

	'LIVE_SEARCH_FORUM_ON'				=> '<strong>Turn on the quick search of forums</strong>',
	'LIVE_SEARCH_TOPIC_ON'				=> '<strong>Turn on the quick search of topics</strong>',
	'LIVE_SEARCH_USER_ON'				=> '<strong>Turn on the quick search of users</strong>',
	'LIVE_SEARCH_SIMILARTOPIC_ON'				=> '<strong>Turn on the quick search of similar topics</strong>',
	'LIVE_SEARCH_SHOW_IN_NEW_WINDOW'				=> 'Show results in a new window',
	'LIVE_SEARCH_SHOW_FOR_GUEST'				=> 'Show for guests',
	'LIVE_SEARCH_USE_EYE_BUTTON'				=> 'Use "eye" button to temporary hide the search panel',
	//*******version 2.0.*******
	'LIVE_SEARCH_TOPIC_LINK_TYPE'				=> 'Displays a link to the topic in the canonical view ',
	'LIVE_SEARCH_TOPIC_LINK_TYPE_EXPLAIN'		=> 'canonical view of a link: "forum/viewtopic.php?f=N1&t=N2", non-canonical(SEO-compatible) view:"forum/viewtopic.php?t=N2"',
	'LIVE_SEARCH_ACP_ON'				=> '<strong> Turn on the quick search in ACP</strong>',
	'ACP_LIVE_SEARCH_SETTINGS_ACP'				=> 'Quick search of forums, groups, users in ACP',
	'LIVE_SEARCH_MIN_NUM_SYMBLOLS_USERS'				=> 'Min number of characters for users search',
	'LIVE_SEARCH_MIN_NUM_SYMBLOLS_GROUPS'				=> 'Min number of characters for groups search',
	'LIVE_SEARCH_MIN_NUM_SYMBLOLS_FORUMS'				=> 'Min number of characters for forums search',
));
