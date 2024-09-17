<?php
	define('IN_PHPBB', true);
	//$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
	$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '/home/sineokur/public_html/test/';
	$phpEx = substr(strrchr(__FILE__, '.'), 1);
	include($phpbb_root_path . 'common.' . $phpEx);
	include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
	include($phpbb_root_path . 'includes/functions_user.'.$phpEx);
	// Start session management
	$user->session_begin();
	$auth->acl($user->data);
	//$user->setup('mods/template');
	echo "{";
	echo '"userId":"'.$user->data['user_id'].'",';
	echo '"userName":"'.$user->data['username'].'",';
	echo '"email":"'.$user->data['user_email'].'",';
	echo '"ip":"'.$user->data['user_ip'].'"';
	echo "}";
?>