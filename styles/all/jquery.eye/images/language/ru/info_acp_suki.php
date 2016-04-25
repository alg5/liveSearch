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
	'ACP_ADDTIME'				=> 'Дополнительное время',
	'ACP_ANSWERS'				=> 'Ответы ',
	'ACP_COMMANDGAME'				=> 'Командные игры ',
 	'ACP_COMMANDGAME_GAMES'					=> 'Игры ',
	'ACP_CAPTAINS'				=> 'Капитаны ',
	'ACP_GAME_ARCHIVE'			=> 'Архив',
	'ACP_LEADERS'				=> 'Ведущие ',
	'ACP_QUESTIONS'				=> 'Вопросы ',
	'ACP_TEAMS'					=> 'Команды ',
 	'ACP_SUKI'		=> 'СУКИ',
	'ACP_SUKI_SETTINGS'				=> 'УСТАНОВКИ ',
  //'ACP_LIVE_SEARCH_MOD_VER'				=> 'Версия МОДа: ',
	'ADD_NEW_CAPTAIN'			=> 'Добавить капитанa',
	'ADD_NEW_CAPTAINS'			=> 'Добавить капитанов',
	'BLITZ_CAPTION'				=> 'Блиц',
	'BLITZ_DURATION'			=> 'Продолжительность блица',
	'BLITZ_DURATION_COMMENT'	=> 'кликнуть на текстбокс (имеет смысл только при выбранном блице)',
	'CAPTAIN'					=> 'Капитан',
	'CAPTAIN_PAGE'				=> 'На этой странице можно назначить/сменить/удалить капитанов команды',
	'CAPTAINS'					=> 'Капитаны',
	'CAPTAIN_1'					=> 'Капитан 1',
	'CAPTAIN_2'					=> 'Капитан 2',
	'CAPTAINS_ADD'				=> 'Капитаны добавлены',
	'CAPTAIN_DELETED'			=> 'Капитан удалён',
	'CURRENT_GAME_CAPTION'		=> 'Текущая игра',
	'CHOOSE_FORUM'				=> 'Выберите форум из списка',
	'DELETE_ARCHIVE'				=> 'Форум удаляется только из списка архивных категорий, чтобы удалить сам форум, воспользуйтесь вкладкой ФОРУМЫ',
 	'DELETE_CAPTAIN'			=> 'Удалить капитана',
	'DELETE_CAPTAIN_TOOLTIP'	=> 'Снять статус капитана( перевести в обычные игроки)',
	'DELETE_PARENT'				=> 'Форум удаляется только из списка игровых категорий, чтобы удалить сам форум, воспользуйтесь вкладкой ФОРУМЫ',
	'DELETE_GAME'				=> 'Игру можно удалить только до её начала.  Будет удалён игровой форум со всеми подфорумами и созданными для данной игры группами',
 	'DELETE_GAME_ARCHIVE'				=> 'Удалить игру.  Будет удалён игровой форум со всеми подфорумами и созданными для данной игры группами',
  	'DESC_NEW_GAME'				=> 'Описание игры',
	'DESC_NEW_GAME_VISTA'		=> 'Здесь поддерживаются ббкоды, смайлы и т.д',
  	'DESC_NEW_TEAM'				=> 'Девиз/описание команды',
	'FORUM_AFTER_COMMENT'				=> 'Если выбрано, внутри игрового форума будет создан подфорум с именем по умолчанию или с введённым. Подфорум будет виден всем зрителям, а, по окончании игры, и всем участникам (рекомендуется)',
	'FORUM_AFTER_DEFAULT_NAME'	=> 'Зрительный зал',
	'FORUM_AFTER_TITLE'			=> 'Создать послетурнирый форум',
	'FORUM_ARCHIVE_CAT'			=> 'Архивный раздел/категория',
	'FORUM_ARCHIVE_NEW'			=> 'Установить новый архивный раздел/категорию',
	'FORUM_BEFORE_COMMENT'			=> 'Если выбрано, внутри игрового форума будет создан подфорум с именем по умолчанию или с введённым. Подфорум будет виден всем участникам игры',
	'FORUM_BEFORE_DEFAULT_NAME'	=> 'Подготовка к турниру',
	'FORUM_BEFORE_TITLE'		=> 'Создать предтурнирый форум',
	'FORUM_BLITZ_TITLE'			=> 'Создать блиц форум',
	'FORUM_BLITZ_COMMENT'				=> 'Если выбрано, для каждой команды будет создан подфорум с именем: имя команды - блиц ',
	'FORUM_QUESTION_COMMENT'			=> 'Если выбрано, внутри игрового форума будет создан подфорум, видимый только администратору и/или ведущей группе. ',
	'FORUM_QUESTION_DEFAULT_NAME'=> 'Служебное помещение',
	'FORUM_QUESTION_TITLE'		=> 'Создать форум для вопросов',
	'GAME_BLITZ_DURATION'			=> 'Продолжительность блица',
	'GAME_BLITZ_DURATION_COMMENT'	=> 'кликнуть на текстбокс (имеет смысл только при выбранном блице)',
	'GAME_CREATED'				=> 'Игра  создана успешно.',
	'GAME_DUTCH_TREAT'			=> 'Игра вскладчину',
 	'GAME_DUTCH_TREAT_COMMENT'	=> 'Отметьте, если каждая команда приносит свой вопрос(свои вопросы) на игру',
	'GAMER_ACCESS'				=> 'Доступ игрока',
	'GAMER_COMMENT'				=> 'Рекомендация: выберите или создайте роль с запретом игроку удалять и изменять свои посты в игровом форуме',
    'GAMES_ACTIVE'	        => 'Активные игры',
    'GAMES_ARCHIVE'	        => 'Архивные игры',
	'GAME_DURATION'				=> 'Продолжительность игры',
    'GAME_MOVED_TO_ACTIVE'	        => 'Игра %s переведена в архив',
	'GAME_OPTIONS'				=> 'Опции игры',
	'GAME_POSSIBLE_LAST_START_TIME'	=> 'Последнее возможное время начала игры ',
	'GAME_START_COMMENT'		=> 'кликнуть на текстбокс',
	'GAME_START_TIME'			=> 'Время начала игры',
	'GAME_TIME_COMMENT'		=> 'Время начала игры будет соответствовать установкам Вашей конференции',
	'GAME_TIME_SETTING'			=> 'Установки времени',
	'IS_LEADERGROUP_SHOW'		=> 'Показывать членов ведущей группы',
	'LEADER_GROUP'				=> 'Ведущая группа',
	'LEADER_GROUP_ACCESS'		=> 'Доступ ведущей группе',
	'LEADER_GROUP_ACCESS_COMMENT'=> 'Ведушая группа получает выбранный вами доступ к игровым форумам и выбранные права модератора ( или предложенные по умолчанию).',
	'LEADER_GROUP_COMMENT'		=> 'Выберите ведуших игры(для модерирования всех игровых форумов)',
	'LEADER_GROUP_TITLE'		=> 'Название ведущих:',
	'LEADER_GROUP_TITLE_SINGULAR'=> 'в ед. ч.',
	'LEADER_GROUP_TITLE_PLURAL'	=> 'во мн. ч.',
	'LEADER_GROUP_TITLE_SINGULAR_DEFAULT'=> 'Ведущий',
	'LEADER_GROUP_TITLE_PLURAL_DEFAULT'	=> 'Ведущие',
    'MOVE_TO_ACTIVE'	        => 'В архив',
	'NAME_GAME'					=> 'Название игры',
	'NAME_GAME_COMMENT'			=> 'Создание игрового форума',
	'NEW_GAME_CAPTION'			=> 'Новая игра',
	'NEW_TEAM_CAPTION'			=> 'Добавить новую команду',
//    'NOTIFICATION_COMMANDGAME'					=> ' Игра <b>%1$s</b> команда <b>%2$s</b>',
	'NOTIFICATION_TYPE_COMMANDGAME'					=> 'Информация о турнире',
	'NO_FORUM_PARENT_MESSAGE'	=> 'На вашей конференции не указано ни одного турнирного раздела/категории. Вы должны указать такой раздел,  выбрав из имеющихся. Как правило, достаточно одного раздела на всю конференцию. Если такой раздел отсутствует, создайте его вручную и вернитесь на эту страницу',
    'NO_GAMES_ARCHIVE'	        => 'В выбраном архиве нет игр',
    'NO_GAMES_ACTIVE'	        => 'Нет активных игр на конференции',
	'NO_SELECTED_GAME_MESSAGE'	=> 'Игра не выбрана',
	'NO_TEAMS'					=> 'Для выбранной игры не создано ни одной команды. Создайте команду на вкладке Команды и вернитесь на эту страницу',
	'EDIT_GAME'					=> 'Игру можно редактировать только до её начала. Изменить время начала игры возможно, если нет команд, назначивших более ранне время, чем изменённое',
	'PARENT_CAT_ADDED'			=> 'Турнирная категория/раздел добавлена',
	'PARENT_CAT_DELETED'		=> 'Турнирная категория/раздел удалена',
	'REDIRECT_TO_CAPTAINS'		=> 'Теперь вы можете %sназначить капитанов%s для этой команды.',
	'REDIRECT_TO_TEAM'			=> 'Теперь вы можете %sсоздавать команды%s для этой игры.',
	'ROLE_COMMENT'				=> 'Для доступа к игровым форумам используется механизм групп и ролей. Выберете подходящую роль для каждой группы участников игры или оставьте выбор по умолчанию. Если нужной вам роли нет, создайте её',
	'ROLE_GAMER_ACCESS'			=> 'Доступ игрока',
	'ROLE_SETTING'				=> 'Установки доступа',
 	'SET_CURRENT_GAME'	=> 'Установить статус текущей игры',
	'TEAM_CREATED'				=> 'Команда создана успешно.',
	'TEAM_NAME'					=> 'Команда',
	'TEAM_BLITZ_GAME'			=> 'Начало блица',
	'TEAM_START_GAME'			=> 'Начало игры',
	'TEAM_MEMBERS'				=> 'Состав команды',
	'TEAM_NAME'					=> 'Команда',
	'TEAM_NAME_COMMENT'			=> 'Введите название команды',
	'TEAM_PAGE'					=> 'На этой странице можно добавить новую команду или изменить/удалить существующую',
	'TEAMS_TOGETHER'			=> 'Команды начинают одновременно',
	'TEAMS_TOGETHER_COMMENT'	=> 'Если не выбрано, каждая команда может выбирать себе время начала игры в пределах установленных выше времени начала и продолжительности',
));
