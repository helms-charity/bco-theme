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


//handle post request to save gallery
if (isset($_POST['submit'])) {
	
	$xml = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><item></item>');

	$valid = true;
	if (trim($_POST['sqr_gallery_name']) == '')
	{
		header('location: '.$path.'loadtab.php?id=squareit-gallery&item=squareit_admin_add_gallery&msg='.$i18n['SQR_GALLERY_VAL_NO_NAME']);
		$valid = false;
	}

	$gallery = $xml->addChild('name');
	$sqr_gallery_name = $_POST['sqr_gallery_name'];
	$gallery->addCData($sqr_gallery_name);

	if (isset($_POST['uid']) && $_POST['uid'] != '')
	{
		$uid = $_POST['uid'];
	}
	else {
		$uid = uniqid(); //generate a unique id for new poll
	}
	
	$gallery = $xml->addChild('id');
	$gallery->addCData($uid);
	
	exec_action('gsg-add-gallery-process'); 

	$gallery = $xml->addChild('images');
	if (isset($_POST['sqr_image']) && is_array($_POST['sqr_image']))
	{
		$captions = array();
		if (isset($_POST['sqr_caption']) && is_array($_POST['sqr_caption']))
		{
			$captions = array_reverse($_POST['sqr_caption']);
		}
		
		foreach ($_POST['sqr_image'] as $img) {
			$image = $gallery->addChild('image');
			$image->addAttribute('caption', array_pop($captions));
			$image->addCData($img);
		}
	}

	if ($valid)
	{
		$response = $xml->asXML("../".$uid.".xml");
		if ($response)
		{
			header('location: '.$path.'loadtab.php?id=squareit-gallery&item=squareit-gallery&msg='.$i18n['SQR_GALLERY_SUCCESS_MSG']);
		}
		else
		{
			die($i18n['SQR_GALLERY_FAILED_SAVE_MSG'].': '.$uid.'.xml');
		}
	}
}