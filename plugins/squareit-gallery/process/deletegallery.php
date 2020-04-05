<?php
if(file_exists('../../../admin/')) {
	$path = '../../../admin/';
}
else if(file_exists('../../admin/')) {
	$path = '../../admin/';
}
else {
	die ('could not resolve admin path');
}

require_once ($path.'inc/common.php');

$pluginfiles = array();
require_once $path.'inc/plugin_functions.php';

if(!isset($_SESSION)){session_start();}

require_once '../inc/gsg_common.inc.php';
require_once GSG_INCPATH.'gsg_functions.inc.php'; //aux functions
require_once GSG_INCPATH.'gsg_plugins.inc.php'; //PLUGIN SUPPORT

squareit_gsg_security();

//load settings
squareit_gsg_load_settings();
require_once GSG_LANGPATH.$_SESSION['SQR_GSG']['language'].'.php'; //LANGUAGE SUPPORT

if (isset($_GET['gid'])) {
	if (file_exists("../".$_GET['gid'].'.xml')) {
		unlink("../".$_GET['gid'].'.xml');
		echo "succcess";
	}
	else
	{
		echo "Error: gallery does not exist";
	}
}
else
{
	echo "Error: gallery id was not sent as a parameter";
}
