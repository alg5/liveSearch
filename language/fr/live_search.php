<?php
/**
*
* liveSearch [French]
* French translation by Galixte (http://www.galixte.com)
*
* @package liveSearch
* @copyright (c) 2014 alg
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* DO NOT CHANGE
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
//
// Some characters you may want to copy&paste:
// ’ « » “ ” …
//

$lang = array_merge($lang, array(
	'INCORRECT_SEARCH'			=> 'Paramètres de recherche incorrects',
	'LIVE_SEARCH_CAPTION'		=> 'Recherche rapide',
	'LIVE_SEARCH_FORUM'		=> 'Forums',
	'LIVE_SEARCH_FORUM_TXT'		=> 'Nom du forum…',
	'LIVE_SEARCH_FORUM_T'			=> 'Pour démarrer la recherche rapide veuillez saisir le nom du forum ou de la catégorie',
	'LIVE_SEARCH_GO_PROFILE'				=> 'Voir le profil',
	'LIVE_SEARCH_POSTS_BY_USER'		=> 'Messages de l’utilisateur',
	'LIVE_SEARCH_POSTS_BY_USER_BOARD'		=> '> dans tous les forums',
	'LIVE_SEARCH_POSTS_BY_USER_FORUM'		=> '>> dans ce forum',
	'LIVE_SEARCH_POSTS_BY_USER_TOPIC'		=> '>>> dans ce sujet',
	'LIVE_SEARCH_TOOLTIP_ALL'		=> 'Rechercher dans tous les forums',
	'LIVE_SEARCH_TOOLTIP_BY_FORUM'		=> 'Rechercher dans le forum',
	'LIVE_SEARCH_TOOLTIP_BY_TOPIC'		=> 'Rechercher dans le sujet',
	'LIVE_SEARCH_TOPIC'		=> 'Sujets',
	'LIVE_SEARCH_TOPICS_BY_USER'		=> 'Sujets de l’utilisateur',
	'LIVE_SEARCH_TOPICS_BY_USER_BOARD'		=> '> dans tous les forums',
	'LIVE_SEARCH_TOPICS_BY_USER_FORUM'		=> '>> dans ce forum',
	'LIVE_SEARCH_TXT'		=> 'Nom du sujet…',
	'LIVE_SEARCH_T'			=> 'Pour démarrer la recherche rapide veuillez saisir le nom du sujet',
	'LIVE_SEARCH_USER'		=> 'Utilisateurs',
	'LIVESEARCH_USER_TXT'	=> 'Nom de l’utilisateur…',
	'LIVESEARCH_USER_T'	=> 'Pour démarrer la recherche rapide veuillez saisir le nom de l’utilisateur',
	'LIVESEARCH_USERTOPIC_DISABLED'	=> 'La recherche rapide des sujets dans ce forum est actuellement désactivée par l’administrateur',
	'LIVESEARCH_USERTOPIC_RESULT'	=> 'Sujets de l’utilisateur « %1$s »',
	'LIVESEARCH_USERTOPIC_RESULT_IN_FORUM'	=> 'Sujets de l’utilisateur « %1$s » dans le forum « %2$s »',
	'LIVESEARCH_USERTOPIC_RESULT_IN_SUBFORUMS'	=> ' et ses sous-forums',
	'LIVESEARCH_USERPOST_DISABLED'	=> 'La recherche rapide des messages dans ce forum est actuellement désactivée par l’administrateur',
	'LIVESEARCH_USERPOST_RESULT'	=> 'Messages de l’utilisateur « %1$s »',
	'LIVESEARCH_USERPOST_RESULT_IN_FORUM'	=> 'Messages de l’utilisateur « %1$s » dans le forum « %2$s »',
	'LIVESEARCH_USERPOST_RESULT_IN_TOPIC'	=> 'Messages de l’utilisateur « %1$s » dans le sujet « %2$s » du forum « %3$s »',
	'LIVE_SEARCH_EYE_BUTTON_OPEN_T'	=> 'Afficher le panneau de la recherche',
	'LIVE_SEARCH_EYE_BUTTON_CLOSE_T'	=> 'Masquer le panneau de la recherche',
));
