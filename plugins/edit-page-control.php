<?php
/*
Plugin Name: Edit control funcitonality
Description: Alows link to edit page if logged in
Version: 0.1
Author: Dominion IT (Johannes Pretorius)
Author URI: http://www.dominion-it.co.za/
GS Version : 2.01
*/

# get correct id for plugin
$thisfile=basename(__FILE__, ".php");

# register plugin
register_plugin(
	$thisfile, 	# ID of plugin, should be filename minus php
	'Edit control funcitonality', 	# Title of plugin
	'0.1', 		# Version of plugin
	'Johannes Pretorius',	# Author of plugin
	'http://www.dominion-it.co.za/', 	# Author URL
	'Alows link to edit page if logged in', 	# Plugin Description
	'plugins', 	# Page type of plugin
	''  	# Function that displays content
);

# activate filter
add_filter('content','content_check'); 
//add_action('plugins-sidebar','createSideMenu',array($thisfile,'Edit control funcitonality'));

/*
  Filter Content for adsense markers (%ad_id%)
    the add of that id will be inserted in the markers section of the conent
*/
function content_check($contents){
  $tmpContent =  $contents;
  if (plugin_login_check() == TRUE) {
    $slug = return_page_slug();
    $tmpContent .= "<br/><p><a href='admin/edit.php?id=$slug'>Edit this Page</a></p>";
  }
    
  return $tmpContent;
}

function plugin_login_check() {
    include 'admin/inc/configuration.php';
    global $SALT;
	$saltCOOKIE = $cookie_name.$SALT;
	if(isset($_COOKIE[$saltCOOKIE])) {
        $userFile 		= getXML('data/other/user.xml');
        $fileUser = stripslashes($userFile->USR);
        $saltUSR = $fileUser.$SALT;    
      if ($_COOKIE[$saltCOOKIE]==sha1($saltUSR)) {
		return TRUE; // Cookie proves logged in status.
      } else {
       return FALSE; 
      }      
	} else { 
      return FALSE; 
    }
}


