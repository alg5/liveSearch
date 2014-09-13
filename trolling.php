<?php

define('IN_PHPBB', true);
$phpbb_root_path = './';
$phpEx = substr(strrchr (__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_user.' .$phpEx);
include($phpbb_root_path . 'includes/bbcode.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl ($user->data);
$user->setup('mods/trolling');

page_header('privet');
//page_header($user->lang['PARTNERS']);

$template->set_filenames(array('body' => 'trolling_body.html',));


//make_jumpbox(append_sid("{$phpbb_root_path}viewforum.$phpEx"));
page_footer();
?>