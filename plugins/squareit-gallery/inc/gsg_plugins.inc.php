<?php
$gsg_plugins = array();  // used for option names
$gsg_pluginfiles = '';
$gsg_plugin_info = array();

add_action(GSG_PLUGINID.'-sidebar','sqr_createSideMenu',array(GSG_PLUGINID,'squareit_gsg_plugins',$i18n['SQR_GALLERY_SIDEMENU_PLUGINS']));


// include any plugins, depending on where the referring file that calls it we need to 
// set the correct paths. 

if (file_exists(GSG_PLUGINPATH)){
	$gsg_pluginfiles = getFiles(GSG_PLUGINPATH);
} 

foreach ($gsg_pluginfiles as $gsgfile) 
{
	$gsg_pathExt = pathinfo($gsgfile,PATHINFO_EXTENSION );
	$gsg_pathName= pathinfo($gsgfile,PATHINFO_FILENAME );
	
	if ($gsg_pathExt=="php" && isset($_SESSION['SQR_GSG']['plugins'][$gsg_pathName])||
	(isset($_GET['item']) && $_GET['item'] == 'squareit_gsg_plugins') && $gsg_pathExt=="php")
	{
		require_once(GSG_PLUGINPATH . $gsgfile);
	}
}

function gsg_register_plugin($id, $name, $ver=null, $auth=null, $auth_url=null, $desc=null, $type=null, $loaddata=null) {
	global $gsg_plugin_info;
	
	$gsg_plugin_info[$id] = array(
		  'name' => $name,
		  'version' => $ver,
		  'author' => $auth,
		  'author_url' => $auth_url,
		  'description' => $desc,
		  'page_type' => $type,
		  'load_data' => $loaddata
	);
	
}

/**
 * Interface that lists all the available plugins
 */
function squareit_gsg_plugins()
{
	global $gsg_plugin_info;
	global $i18n;
	
	$html = '<h3>'.$i18n['SQR_PLUGINS_TITLE'].'</h3>';
	
	if(isset($_GET['gsgplugaction']) && isset($_GET['gsgplugid']))
	{
		$doact = cl($_GET['gsgplugaction']);
		$plugid = cl($_GET['gsgplugid']);
		$update = false;	
		$xml = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><item></item>');
		if ($doact == 'enable' && $plugid != '')
		{
			if(!isset($_SESSION['SQR_GSG']['plugins'][$plugid]))
			{
				foreach($_SESSION['SQR_GSG']['plugins'] as $pid => $plugin)
				{
					$plugins= $xml->addChild($pid);
					$plugins->addCData(1);
				}
					
				$plugins= $xml->addChild($plugid);
				$plugins->addCData(1);
				$update = true;
				
			}
		}
		else if ($doact == 'disable' && $plugid != '')
		{
			foreach($_SESSION['SQR_GSG']['plugins'] as $pid => $plugin)
			{
				if ($plugid != $pid)
				{
					$plugins= $xml->addChild($pid);
					$plugins->addCData(1);
				}
			}
			$update = true;
		}
		else
		{
		  //invalid
		}
		
		if($update)
		{
			$response = $xml->asXML(GSG_DATAPATH."plugins.xml");
			if ($response)
			{
				squareit_msg_box($i18n['SQR_SETTINGS_SAVE_SUCCESS']);
				$_SESSION['SQR_GSG'] = '';
				unset($_SESSION['SQR_GSG']);
				squareit_gsg_load_settings();
			}
			else
			{
				squareit_msg_box($i18n['SQR_SETTINGS_SAVE_FAILED']);
			}
		}
	}
	
	$html .= '<table class="edittable highlight paginate">';
	$counter = 0;
	foreach ($gsg_plugin_info as $id => $plugin)
	{
		$html .= '<tr id="tr-'.$counter.'" >';
		$html .= '<td width="25%" ><b>'.$plugin['name'] .'</b></td>';
		$html .= '<td><span>'.$plugin['description'] .'<br />';
		$html .= $i18n['PLUGIN_VER'] .' '. $plugin['version'].' &nbsp;|&nbsp; By <a href="'.$plugin['author_url'].'" target="_blank">'.$plugin['author'].'</a></span></td>';
		
		
		if (isset($_SESSION['SQR_GSG']['plugins'][$id]))
		{
			$html .= '<td nowrap><span style="color:green">'.$i18n['SQR_PLUGINS_ENABLE'].'</span> - [<a href="loadtab.php?id=squareit-gallery&item=squareit_gsg_plugins&gsgplugaction=disable&gsgplugid='.$id.'">'.$i18n['SQR_PLUGINS_DISABLE'].'</a>]</td>';
		}
		else
		{
			$html .= '<td nowrap><span style="color:grey">'.$i18n['SQR_PLUGINS_DISABLE'].'</span> - [<a href="loadtab.php?id=squareit-gallery&item=squareit_gsg_plugins&gsgplugaction=enable&gsgplugid='.$id.'">'.$i18n['SQR_PLUGINS_ENABLE'].'</a>]</td>';
		}
		
		$html .= "</tr>\n";
		$counter++;	
	}
	$html .= '</table>';
	
	echo $html;
}
