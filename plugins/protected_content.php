<?php
/*
Plugin Name: Protected Content
Description: This plugin provides password protection for content on a named page and any child pages.
Version: 0.7
Author: Dunmail
Author URI: http://www.stanegatesolutions.com/getsimple/protected_content.html
*/

// get correct id for plugin
$thisfile = basename(__FILE__, '.php'); // This gets the correct ID for the plugin.

// register plugin
register_plugin(
	$thisfile,	// ID of plugin, should be filename minus php
	'Protected Content', // Title of plugin
	'0.7',	// Version of plugin
	'Dunmail',	// Author of plugin
	'http://www.stanegatesolutions.com/getsimple/protected_content.html',	// Author URL
	'This plugin provides password protection for content on a named page and any child pages.',	// Plugin Description
	'settings',	// Page type of plugin
	'protectedContent_administration'	// Function that displays content
);

# hooks
add_action('index-pretemplate','protectedContent_challenge'); 
add_filter('content','protectedContent_filter');
add_action('settings-sidebar', 'createSideMenu', array($thisfile, 'Protected Content'));

# definitions
define('GSDATAPROTECTEDCONTENTPATH', GSDATAOTHERPATH.'protected_content/');

#functions
/* 
	protectedContent_administration
	entry point to plugin administration pages
*/
function protectedContent_administration(){	
  // Save configuration
  $updated = false;
  if (isset($_POST['realm']))
  {
	  $realm=$_POST['realm'];
	  protectedContent::get_configXml()->realm  = $realm;
	  $updated = true;  
  }

  if (isset($_POST['protected_page']))
  {
	  $protected_page=$_POST['protected_page'];
	  protectedContent::get_configXml()->protected_page  = $protected_page;
	  $updated = true;  
  }
  
  if (isset($_POST['message']))
  {
	  $message=$_POST['message'];
	  protectedContent::get_configXml()->message  = $message;
	  $updated = true;  
  }
  
  if (isset($_POST['username']))
  {
	  $username=$_POST['username'];
	  protectedContent::get_configXml()->user[0]->username  = $username;
	  $updated = true;  
  }
  
  if (isset($_POST['password']))
  {
	  $password=$_POST['password'];
	  protectedContent::get_configXml()->user[0]->password  = $password;
	  $updated = true;  
  }
  
  if ($updated)
  {
	  protectedContent::get_configXml()->asXML(GSDATAPROTECTEDCONTENTPATH.'config.xml');
	  echo '<div class="updated" style="display: block;">Your changes have been saved.</div>';
  }
  
  //show configuration
  protectedContent_showConfiguration();
}

/* 
	protectedContent_showConfiguration
	shows configuration page for plugin
*/
function protectedContent_showConfiguration(){
	echo '<h3>Protected Content Configuration</h3>';	
	echo '<form action="load.php?id=protected_content" method="post" >';
	echo '<table class="formtable"><tbody>';
	echo '<tr>';	
	/* realm */
	echo '<td><b>Description:</b><br /><input id="realm" class="text short" type="text" value="'.protectedContent::get_configXml()->realm.'" name="realm" /></td>';
	echo '</tr><tr>';
	/* protected page */
	$protected_page = (string)protectedContent::get_configXml()->protected_page;
	echo '<td><b>Protected page:</b><br/><select name="protected_page">';
	$pages = get_pages();
	foreach ($pages as $url => $title)
	{
		if ($url == $protected_page)
		{
			echo '<option value="'.$url.'" selected="true">'.$title.'</option>';			
		}
		else
		{
			echo '<option value="'.$url.'">'.$title.'</option>';
		}
	}
	echo '</select></td>';
	echo '</tr><tr>';
	/* message */
	echo '<td><b>Message:</b><br /><input id="message" class="text short" type="text" value="'.protectedContent::get_configXml()->message.'"  name="message" /></td>';
	echo '</tr><tr>';
	/* users */
		echo '<td><b>Username:</b><br /><input class="text short" type="text" value="'.protectedContent::get_configXml()->user[0]->username.'"  name="username" /></td>';
	echo '</tr><tr>';
		echo '<td><b>Password:</b><br /><input class="text short" type="text" value="'.protectedContent::get_configXml()->user[0]->password.'"  name="password" /></td>';
	echo '</tr>';
	echo '</tbody></table>';
	/* submit */
	echo '<p id="submit_line">
				<span><input type="submit" value="Save Configuration" name="submitted" class="submit"></span>&nbsp;&nbsp;
				or&nbsp;&nbsp;
				<a title="Cancel" href="plugins.php" class="cancel">Cancel</a></p>';
	echo '</form>';	
}

/* 
	protectedContent_challenge
	Requests authentication for protected pages 
*/
function protectedContent_challenge() 
{
	  	//find the parent of the current page
	    $active_parent=get_active_parent();
		
		//check if this page is protected
		$protected_content = protectedContent::is_protected($active_parent);
		
		//if content is protected, challenge user via HTTP Basic authentication
		if ($protected_content==true)
		{   	
			  $authenticated = false;
			  if (!isset($_SERVER['PHP_AUTH_USER']))
			  {
				  header('WWW-Authenticate: Basic realm="'.protectedContent::get_configXml()->realm.'"');
				  header('HTTP/1.0 401 Unauthorized');
			  } 
			  else 
			  {	
				  $authenticated = protectedContent::authenticate($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
				  if ($authenticated != true)
				  {
						header('WWW-Authenticate: Basic realm="'.protectedContent::get_configXml()->realm.'"');
						header('HTTP/1.0 401 Unauthorized');
				  }
			 }	
		}
}

/* 
	protectedContent_filter
	Removes content for unauthenticated users viewing protected pages
*/
function protectedContent_filter($content) 
{
		//find the parent of the current page
	  $active_parent=get_active_parent();
		
		//decide if this content is protected
		$protected_content = false;
		$protected_content = protectedContent::is_protected($active_parent);
		
		//decide if content should be shown, using HTTP Basic authentication
		$show_content = false;
		if ($protected_content==true)
		{
			$authenticated = false;
			if (isset($_SERVER['PHP_AUTH_USER']))
			{
				$authenticated = protectedContent::authenticate($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
			}
			
			if ($authenticated != true)
			{
			 	  $content = '<div id="unauthenticated">'.protectedContent::get_configXml()->message.'</div>';
			}		
			//TODO: If possible, append code to provide logout function. NOt sure if this is possible using Basic HTTP
		}

		return $content;		
}

/*
	get_active_parent
	find the parent of the current page (code derived from child_menu plugin: http://get-simple.info/extend/plugin/child-menu/40/)
*/
function get_active_parent()
{
	  
	  $active_parent=return_parent();
	  if (strlen($active_parent)==0)
	  {
		  $active_parent=return_page_slug();
	  }	
	  return $active_parent;
}

/*
	get_pages
	returns array of page urls and titles (code derived from child_menu plugin: http://get-simple.info/extend/plugin/child-menu/40/)
*/
function get_pages(){
	$dir_handle = @opendir(GSDATAPAGESPATH) or exit('Unable to open ...getsimple/data/pages folder');
	$filenames = array();
	while ($filename = readdir($dir_handle))
	{
		$filenames[] = $filename;
	}
	
	$pages = array();
	if (count($filenames) != 0)
	{
		sort($filenames);
		foreach ($filenames as $file) 
		{
			if (!($file == '.' || $file == '..' || is_dir(GSDATAPAGESPATH.$file) || $file == '.htaccess'))
			{
				$thisfile = file_get_contents(GSDATAPAGESPATH.$file);
				$XMLdata = simplexml_load_string($thisfile);
				$url = (string)$XMLdata->url; 
				$title = (string)$XMLdata->title;
				//$title = substr($file, 0, strrpos($file, '.'));
				$pages[$url] = $title;
			}
		}
	}
	return $pages;
}

/*
	Methods to support authentication and content protection
*/
class protectedContent {
	static $_config;
	static public function get_configXml()
	{
		if (!self::$_config)
		{
			if (!file_exists(GSDATAPROTECTEDCONTENTPATH)) {
    			mkdir(GSDATAPROTECTEDCONTENTPATH);
			    $ht = fopen(GSDATAPROTECTEDCONTENTPATH.'.htaccess', "w");
			    fwrite($ht, "Allow from all");
			    fclose ($ht);
			}
			
			if (file_exists(GSDATAPROTECTEDCONTENTPATH.'config.xml'))
			{
				$xmlstr = file_get_contents(GSDATAPROTECTEDCONTENTPATH.'config.xml');
				self::$_config = new SimpleXMLElement($xmlstr); 	
			}
			else
			{								
				$xmlstr = '<document>
<realm>Protected Content</realm>
<protected_page>protected</protected_page>
<message>Access to this content is restricted. Contact an administrator for further information.</message>
<user>
	<username>siteuser</username>
	<password>'.rand(100, 999).rand(100, 999).rand(100, 999).'</password>
</user>
</document>';
				self::$_config = new SimpleXMLElement($xmlstr); 	
				self::$_config->asXML(GSDATAPROTECTEDCONTENTPATH.'config.xml');
			}

	
		}
		return self::$_config;	
	}
	static public function is_protected($page)
	{
		$config = self::get_configXml();
		return ($page==(string)$config->protected_page);
	}
	static public function authenticate($un, $pw)
	{	
		$config = self::get_configXml();
		foreach($config->user as $user)
		{
			if ($user->username == $un && $user->password == $pw)
			{
				return true;	
			}
		}
		return false;
	}
}
?>