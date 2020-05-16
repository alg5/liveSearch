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
	'ACP_LIVE_SEARCH'		=> 'Recherche rapide',
	'ACP_LIVE_SEARCH_MOD_VER'				=> 'Version de l’extension : ',
	'ACP_LIVE_SEARCH_SETTINGS'				=> 'Paramètres',
	'ACP_LIVE_SEARCH_SETTINGS_FORUMS'				=> 'Recherche rapide de forums',
	'ACP_LIVE_SEARCH_SETTINGS_TOPICS'				=> 'Recherche rapide de sujets',
	'ACP_LIVE_SEARCH_SETTINGS_SIMILARTOPICS'				=> 'Recherche rapide de sujets similaires',
	//'ACP_LIVE_SEARCH_SETTINGS_TOPICS_EXPLAIN'				=> 'Быстрый(живой) поиск по названию темы, если включено, поиск осуществляется либо по всей конференции с главной страницы, либо по конкретному форуму и всем содержашимся в нём подфорумам <br /><strong>Важно: Для корректной работы настроек расширения требуется MySQL версии 4.1 или выше!</strong><br />',
	'ACP_LIVE_SEARCH_SETTINGS_TOPICS_EXPLAIN'				=>'La recherche est effectuée dans tout le forum ou dans un forum en particulier ainsi que dans ses sous-forums.<br /><strong>Important : L’extension requiert au minimum la version 4.1 de MySQL ou une version plus récente pour fonctionner !</strong>',
	'ACP_LIVE_SEARCH_SETTINGS_USERS'				=> 'Recherche rapide d’utilisateurs',

	'LIVE_SEARCH_MIN_NUM_SYMBLOLS'				=> 'Nombre de caractères minimum pour la recherche rapide',
	'LIVE_SEARCH_MIN_NUM_SYMBLOLS_EXPLAIN'				=> 'La recherche débute dès que le nombre de caractères minimum a été saisi.<br />Saisir une valeur comprise entre 1 et 5.',
	'LIVE_SEARCH_MAX_ITEMS_TO_SHOW'				=> 'Nombre de résultats',
	'LIVE_SEARCH_MAX_ITEMS_TO_SHOW_EXPLAIN'				=> 'Nombre de résultats maximum affichés dans la liste déroulante.<br />Saisir une valeur comprise entre 1 et 50. La valeur recommandée est de 20.',
	'LIVE_SEARCH_EXCLUDE_FORUMS'		=> 'Forums exclus de la recherche rapide',
	'LIVE_SEARCH_EXCLUDE_FORUMS_EXPLAIN'		=> 'Saisir les ID des forums à exclure séparés par une virgule.',

	'LIVE_SEARCH_FORUM_ON'				=> '<strong>Activer la recherche rapide de forums</strong>',
	'LIVE_SEARCH_TOPIC_ON'				=> '<strong>Activer la recherche rapide de sujets</strong>',
	'LIVE_SEARCH_USER_ON'				=> '<strong>Activer la recherche rapide d’utilisateurs</strong>',
	'LIVE_SEARCH_SIMILARTOPIC_ON'				=> '<strong>Activer la recherche rapide de sujets similaires</strong>',
	'LIVE_SEARCH_SHOW_IN_NEW_WINDOW'				=> 'Afficher les résultats dans une nouvelle fenêtre',
	'LIVE_SEARCH_SHOW_FOR_GUEST'				=> 'Afficher aux invités',
	'LIVE_SEARCH_USE_EYE_BUTTON'				=> 'Utilisez le bouton « oeil » pour masquer temporairement le panneau de la recherche',
));
