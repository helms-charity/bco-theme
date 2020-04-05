<?php
/*
Name: Simple Image Gallery
Description: Allows the author of a site to create image galleries
Version: 2.5.2
Author: Julian Castaneda (Square IT Solutions)
Author URI: http://www.squareitsol.com/
*/
define("GSG_VERSION", '2.5.2');

//includes
require_once GSPLUGINPATH.'squareit-gallery/inc/gsg_common.inc.php';
require_once GSG_INCPATH.'gsg_functions.inc.php';

if(!isset($_SESSION)){session_start();}

//load settings
squareit_gsg_load_settings();


require_once GSG_LANGPATH.$_SESSION['SQR_GSG']['language'].'.php';

# register plugin
register_plugin(
	GSG_PLUGINID,
	$i18n['SQR_GALLERY_PLUGIN_NAME'],
	GSG_VERSION,
	'Square IT Solutions',
	'http://www.squareitsol.com/',
	'Description:  '.$i18n['SQR_GALLERY_PLUGIN_DESC'],
	'plugins', //page type
	'squareit_setup_gallery'
);

//HOOKS
//add_action('plugins-sidebar','createSideMenu',array($thisfile,'Simple Image Gallery'));
add_action('index-pretemplate','squareit_gallery_include_check',array());
add_action(GSG_PLUGINID.'-sidebar','sqr_createSideMenu',array(GSG_PLUGINID, GSG_PLUGINID, $i18n['SQR_GALLERY_SIDEMENU_LIST']));
add_action(GSG_PLUGINID.'-sidebar','sqr_createSideMenu',array(GSG_PLUGINID,'squareit_admin_add_gallery',$i18n['SQR_GALLERY_SIDEMENU_ADD_GAL']));
add_action(GSG_PLUGINID.'-sidebar','sqr_createSideMenu',array(GSG_PLUGINID,'squareit_admin_settings',$i18n['SQR_GALLERY_SIDEMENU_SETTINGS']));
add_action('nav-tab','sqr_createNavTab',array(GSG_PLUGINID,$i18n['SQR_GALLERY_TAB_NAME']));
add_action(GSG_PLUGINID.'-sidebar-extra', 'squareit_gallery_new_version', array());

//filter hook that displays galleries
add_filter('content','squareit_display_gallery'); 

//variable to check if the gallary includes has been included or not
$sqr_gsg_inc_included = false;


/**
 * Lists all the available galleries
 */
function squareit_setup_gallery()
{
	global $i18n;
	global $SESSIONHASH;
	
	//gallery settings file
	$settings_file= GSG_FOLDERPATH.'squareit_gallery.xml';
	
	//check for tab loader plugin
	if (!file_exists(GSADMINPATH.'loadtab.php'))
	{
		squareit_msg_box($i18n['SQR_GALLERY_ADMIN_TAB_MISSING']);
	}
	
	squareit_health_check(GSG_FOLDERPATH);
	
	//display messages passed by url
	if(isset($_GET['msg']))
	{
		squareit_msg_box($_GET['msg']);
	}

	squareit_page_header($i18n['SQR_GALLERY_PLUGIN_NAME']);
	?>
	<p style="color:#8C8C8C">
		<?php echo $i18n['SQR_GALLERY_HELP_TEXT'];?>
	</p>
	
	<?php
	$pagesArray = squareit_get_all_galleries();
	$table = '';
	$counter = "0";
	if (count($pagesArray) != 0) 
	{ ?>
		<table class="edittable highlight paginate">
		<thead>
			<tr>
				<th><?php echo $i18n['SQR_GALLERY_NAME_LABEL'];?></th>
				<th><?php echo $i18n['SQR_GALLERY_LIST_HEAD_CODE']?></th>
				<th colspan="2">&nbsp; </th>
			</tr>
		</thead>
		<?php
		foreach ($pagesArray as $page) 
		{	
			$counter++;
							
			$table .= '<tr id="tr-'.$page['gallery_id'] .'" >';
			$table .= '<td><strong>'. cl($page['gallery_name']) .'</strong></td>';
			$table .= '<td>{squareit_gallery_'.$page['gallery_id'].'}</td>';
			$table .= '<td>[<a href="loadtab.php?id='.GSG_PLUGINID.'&item=squareit_admin_edit_gallery&gid='.$page['gallery_id'] .'">'.$i18n['SQR_GALLERY_LIST_EDIT'].'</a>]</td>';
			$table .= '<td class="delete"><a class="delconfirm" href="../'.GSG_RELFOLDERPATH.'process/deletegallery.php?ref='.GSG_PLUGINID.'&gid='.$page['gallery_id'].'&s='.$SESSIONHASH.'" title="'.$i18n['SQR_GALLERY_LIST_CONFIRM_DEL'].'?" >X</a></td></tr>';
						
		}
		echo $table;
		?>
		</table>
		<div id="page_counter" class="qc_pager"></div> 	
			<p><em><b><span id="pg_counter"><?php echo $counter; ?></span></b> <?php echo $i18n['TOTAL_PAGES']; ?></em></p>
		<script>
		$('body').bind("ajaxComplete", function(event, xhr, options){
			//TODO figure out best way to show a message stating that the gallery was deleted successfully.
			//console.log('myVar: ', $.httpData(xhr,options.dataType)); 
	 	});
	 	</script>
	<?php
	}else 
	{
		squareit_msg_box('<div style="font-size:14px">'.$i18n['SQR_GALLERY_LIST_EMPTY'].'</div>');
	}
}

/**
 * Get an array of all the galleries currently available
 *
 * @return array
 */
function squareit_get_all_galleries()
{
	$path = GSG_FOLDERPATH;
	//display all pages
	$filenames = getFiles($path);
	$count="0";
	$pagesArray = array();
	if (count($filenames) != 0) { 
		foreach ($filenames as $file) {
			if (isFile($file, $path, 'xml')) {
				$data = getXML($path . $file);
				$pagesArray[$count]['gallery_name'] = html_entity_decode($data->name, ENT_QUOTES, 'UTF-8');
				$pagesArray[$count]['gallery_id'] = $data->id;
				$count++;
			}
		}
	}
	
	return $pagesArray;
}

/**
 * Functionality to add/update a new gallery
 * 
 * @param boolean $editMode  - Parameter used to change to update
 */
function squareit_admin_add_gallery($editMode = false)
{
	global $i18n;
	global $SESSIONHASH;
	$path = GSDATAUPLOADPATH;
	
	$sqr_gallery_name = '';
	
	//display messages passed by url
	if(isset($_GET['msg']))
	{
		squareit_msg_box($_GET['msg']);
	}
	
	$count="0";
	$counter = "0";
	$totalsize = 0;
	$filesArray = array();
	$filenames = getFiles($path);
	//load all the images available
	if (count($filenames) != 0) {
		foreach ($filenames as $file) {
			if ($file == "." || $file == ".." || is_dir($path . $file) || $file == ".htaccess") {
				// not a upload file
			} else {
				$ext = substr($file, strrpos($file, '.') + 1);
				$extention = get_FileType($ext);
				if (strtolower($ext) == 'gif' || strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png')
				{
					$filesArray[$count]['name'] = $file;
					$filesArray[$count]['type'] = $extention;
					clearstatcache();
					$ss = @stat($path . $file);
					$filesArray[$count]['date'] = @date('M j, Y',$ss['ctime']);
					$filesArray[$count]['size'] = fSize($ss['size']);
					$totalsize = $totalsize + $ss['size'];
					$count++;
				}
			}
		}

		$filesSorted = subval_sort($filesArray,'name');
	}
	
	//if in edit mode, then get gallery
	$sqr_images = array();
	$sqr_captions = array();
	if ($editMode)
	{
		$gallery = squareit_get_gallery($_GET['gid']);
		$sqr_images = $gallery['images'];
		$sqr_gallery_name = $gallery['name'];
		squareit_page_header($i18n['SQR_GALLERY_EDIT_TITLE'].'('.$gallery['name'].')');
	}
	else
	{
		squareit_page_header($i18n['SQR_GALLERY_ADD_TITLE']);
	} 
	?>
	<p><?php echo $i18n['SQR_GALLERY_ADD_EDIT_INFO'];?></p>

	<!-- START metadata toggle -->
	<!--
	<div class="edit-nav" >
		<a href="#" id="metadata_toggle" accesskey="o" ><?php echo $i18n['SQR_GALLERY_ADD_EDIT_OPTIONS']; ?></a>
			<div class="clear" ></div>
	</div>
	 -->

	<?php if (!empty($filesSorted)) 
	{?>
	<form method="post" action="../<?php echo GSG_RELFOLDERPATH;?>process/processgallery.php?s=<?php echo $SESSIONHASH;?>">
	<?php 
	if ($editMode)
	{?>
	<input type="hidden" name="uid" value="<?php echo $_GET['gid'];?>" />
		<?php
	}
	?>
		<table class="cleantable">
			<tr>
				<td style="width:25%" ><b><?php echo $i18n['SQR_GALLERY_NAME_LABEL'];?></b></td>
				<td>
					<input type="text" id="sqr_gallery_name" name="sqr_gallery_name" class="text" style="width:250px;" value="<?php echo $sqr_gallery_name; ?>" />
				</td>
			<?php exec_action('gsg-add-gallery');  ?>	
			</tr>
			<tr><td colspan="2" style="background-color:#F0F0F0">
				<!-- START metadata toggle screen -->
				<!--
				<div style="display:none;" id="metadata_window" >
					<table>
						<tr>
							<td nowrap="nowrap" width="25%">
								<?php echo $i18n['SQR_GALLERY_ADD_EDIT_DISPLAY_CAPTION'];?>
							</td>
							<td width="25%">
								<input type="checkbox" name="sqr_gsg_display_caption" value="1" <?php echo (@$settings_vars['disable_caption'] ?  'checked="checked"' : '');?> />
							</td>
							<td nowrap="nowrap" width="25%">
								<?php echo $i18n['SQR_GALLERY_ADD_EDIT_DISPLAY_TITLE'];?>
							</td>
							<td width="25%">
								<input type="checkbox" name="sqr_gsg_display_title" value="1" <?php echo (@$settings_vars['disable_title'] ?  'checked="checked"' : '');?> />
							</td>
						</tr>
					</table>
				</div>
				-->
				<!-- END metadata toggle screen -->
			</td></tr>
			<tr><td colspan="2">
			<?php 
			if (count($filesSorted) != 0) {
			?>
			<table class="highlight" id="imageTable">
			<thead>
			<tr>
				<th><?php echo $i18n['SQR_GALLERY_LIST_HEAD_IMG'];?></th>
				<th><?php echo $i18n['SQR_GALLERY_LIST_HEAD_FILE'];?></th>
				<th style="text-align:center;"><?php echo $i18n['SQR_GALLERY_LIST_HEAD_SIZE'];?></th>
				<th style="text-align:center;"><?php echo $i18n['SQR_GALLERY_LIST_HEAD_DATE'];?></th>
				<th style="text-align:center;"><?php echo $i18n['SQR_GALLERY_LIST_HEAD_CAPTION'];?></th>
				<th> </th>

			</tr>
			</thead>
			<?php 
			$count = 0;
			foreach ($filesSorted as $upload) {
				$counter++;
				if ($upload['type'] == $i18n['IMAGES']) {
					$cclass = 'iimage';
				} else {
					$cclass = '';
				}
				echo '<tr class="All '.$upload['type'].' '.$cclass.'" >';
				echo '<td class="imgthumb" style="display: table-cell;">';
				if ($upload['type'] == $i18n['IMAGES']) {
					$gallery = 'rel="facybox"';
					$pathlink = 'image.php?i='.$upload['name'];
					if (file_exists('../data/thumbs/thumbsm.'.$upload['name'])) {
						echo '<a href="../data/uploads/'. $upload['name'] .'" title="'. $upload['name'] .'" rel="facybox" ><img src="../data/thumbs/thumbsm.'.$upload['name'].'" /></a>';
					} else {
						echo '<a href="../data/uploads/'. $upload['name'] .'" title="'. $upload['name'] .'" rel="facybox" ><img src="inc/thumb.php?src='. $upload['name'] .'&dest=thumbsm.'. $upload['name'] .'&x=65&f=1" /></a>';
					}
				} else {
					$gallery = '';
					$controlpanel = '';
					$pathlink = $path . $upload['name'];
				}
	
				echo '</td><td><a title="'.$i18n['VIEW_FILE'].': '. $upload['name'] .'?" href="'. $pathlink .'" class="primarylink">'.$upload['name'] .'</a></td>';
				echo '<td style="width:70px;text-align:center;" ><span><b>'. $upload['size'] .'</span></td>';
				echo '<td style="text-align:center;" ><span>'. $upload['date'] .'</span></td>';
				
				$exists = false;
				$caption = '';
				$disabled = 'disabled="disabled"';
				foreach ($sqr_images as $image)
				{
					if ($upload['name'] == $image['image'])
					{
						$exists = true;
						$disabled = '';
						$caption = $image['caption'];
					}
				}
				
				echo '<td ><input type="text" value="'.$caption.'" class="sqr_caption_dis" name="sqr_caption[]" id="sqr_'.$counter.'_caption" '.$disabled.' /></td>';
				$checked = '';
				
				if ($exists !== false)
				{
					$checked = 'checked="checked"';
				}
				echo '<td style="text-align:right"><input type="checkbox" name="sqr_image[]" id="sqr_'.$counter.'" class="sqr_chk" value="'.$upload['name'].'" '.$checked.'/></td>';
				//echo '<td ><input type="text" name="order" style="width:20px" /></td>';
				echo '</tr>';
				$count++;
			}
			echo '</table>';
			echo '<p><em><b>'. $counter .'</b> '.$i18n['TOTAL_FILES'].' ('. fSize($totalsize) .')</em></p>';
		} else {
			echo '<div id="imageTable"></div>';
		}
		?>
			</td></tr>
			
			<tr>
				<td>&nbsp;</td>
				<td>
					<input type="submit" name="submit" class="submit" value="<?php echo $i18n['SQR_GALLERY_ADD_EDIT_SUBMIT'];?>" />
					 <?php echo $i18n['SQR_GALLERY_OR'];?> <a href="loadtab.php?id=<?php echo GSG_PLUGINID;?>&item=<?php echo GSG_PLUGINID;?>"><?php echo $i18n['SQR_GALLERY_CANCEL'];?></a> </td>
				
			</tr>
		</table>
	</form>
	<script>
		jQuery(document).ready(function() { 
			jQuery('.sqr_chk').bind('click', function() {
				  var id = this.id;
				  var caption_id = id + "_caption";
				  if (jQuery('#'+caption_id).attr('disabled'))
				  {
				  	jQuery('#'+caption_id).removeAttr("disabled");
				  }
				  else
				  {
					  jQuery('#'+caption_id).attr('disabled', true);
					  jQuery('#'+caption_id).val('');
				  }
			});
		});
	 </script>
	<?php
	}
	else
	{
		squareit_msg_box($i18n['SQR_GALLERY_NO_IMAGES']);
	}

}

/**
 * Wrapper for edit/update functionality
 */
function squareit_admin_edit_gallery()
{
	squareit_admin_add_gallery(true);
}

/**
 * Settings screen
 */
function squareit_admin_settings()
{
	//TODO: setting to purge thumbs
	//TODO: setting to set the size of thumbs
	//TODO: missing validation
	global $i18n;
	
	$settings_vars = squareit_gsg_load_settings(true);
	
	if (isset($_POST['submit'])) {
		
		$xml = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><item></item>');
		$valid = true;
		
		
		if (isset($_POST['sqr_gsg_language']) && $_POST['sqr_gsg_language'] != '')
		{
			$settings_vars['language'] = $_POST['sqr_gsg_language'];
		}
		
		$settings_vars['jquery_disabled'] = (int)@$_POST['sqr_gsg_jquery'];
		$settings_vars['disable_plugins'] = (int)@$_POST['sqr_gsg_plugins'];
		$settings_vars['disable_gallery_name'] = (int)@$_POST['sqr_gsg_display_gallery_name'];
		$settings_vars['disable_caption'] = (int)@$_POST['sqr_gsg_display_caption'];
		
		
		//TODO make sure we sanitize the input (NO SPECIAL CHARS)
		if (isset($_POST['sqr_gsg_gallery_img_rel']))
		{
			$settings_vars['gallery_img_rel'] = $_POST['sqr_gsg_gallery_img_rel'];
		}
		
		if (isset($_POST['sqr_gsg_gallery_img_class']))
		{
			$settings_vars['gallery_img_class'] = $_POST['sqr_gsg_gallery_img_class'];
		}
		

		$settings= $xml->addChild('language');
		$settings->addCData($settings_vars['language']);
		
		$settings= $xml->addChild('jquery_disabled');
		$settings->addCData($settings_vars['jquery_disabled']);
		
		$settings= $xml->addChild('disable_gallery_name');
		$settings->addCData($settings_vars['disable_gallery_name']);
		
		$settings= $xml->addChild('disable_caption');
		$settings->addCData($settings_vars['disable_caption']);
		
		$settings= $xml->addChild('disable_plugins');
		$settings->addCData($settings_vars['disable_plugins']);
		
		$settings= $xml->addChild('gallery_img_class');
		$settings->addCData($settings_vars['gallery_img_class']);
		
		$settings= $xml->addChild('gallery_img_rel');
		$settings->addCData($settings_vars['gallery_img_rel']);
		
		exec_action('gsg-settings-process'); 
	
		if ($valid)
		{
			$response = $xml->asXML(GSG_FOLDERPATH."data/settings.xml");
			if ($response)
			{
				squareit_msg_box($i18n['SQR_SETTINGS_SAVE_SUCCESS']);
				$_SESSION['SQR_GSG'] = '';
				unset($_SESSION['SQR_GSG']);
			}
			else
			{
				squareit_msg_box($i18n['SQR_SETTINGS_SAVE_FAILED']);
			}
		}
		else
		{
			squareit_msg_box($i18n['SQR_SETTINGS_SAVE_VALIDATION_FAILED']);
		}
	}
	
	//Get all language files
	$filenames = getFiles(GSG_LANGPATH);
	$count=0;
	$langFilesArray = array();
	if (count($filenames) != 0) { 
		foreach ($filenames as $file) {
			if (isFile($file, GSG_LANGPATH, '.php')) {
				$langFilesArray[] = substr($file,0, -4);
				$count++;
			}
		}
	}

	squareit_page_header($i18n['SQR_GALLERY_SETTINGS_LABEL']);
	?>
	<form method="post" action="">
	<table class="cleantable">
		<tr>
			<td style="width:25%" nowrap="nowrap"><b><?php echo $i18n['SQR_GALLERY_SETTINGS_LANG'];?></b></td>
			<td>
				<select name="sqr_gsg_language">
					<?php foreach ($langFilesArray as $language) {
						$lang_sel = '';
						if  ($settings['language'] == $language)
						{
							$lang_sel = 'selected="selected"';
						}
						?>
						<option value="<?php echo $language;?>" <?php echo $lang_sel;?>><?php echo $language;?></option>
						<?php 
					}?>
				</select>
			</td>
		</tr>
		<tr>
			<td nowrap="nowrap"><b><?php echo $i18n['SQR_GALLERY_SETTINGS_DISPLAY_GALNAME'];?></b></td>
			<td>
				<input type="checkbox" name="sqr_gsg_display_gallery_name" value="1" <?php echo (@$settings_vars['disable_gallery_name'] ?  'checked="checked"' : '');?> />
			</td>
		</tr>
		
		<tr>
			<td nowrap="nowrap"><b><?php echo $i18n['SQR_GALLERY_SETTINGS_DISPLAY_CAPTION'];?></b></td>
			<td>
				<input type="checkbox" name="sqr_gsg_display_caption" value="1" <?php echo (@$settings_vars['disable_caption'] ?  'checked="checked"' : '');?> />
			</td>
		</tr>
		
		<tr>
			<td nowrap="nowrap"><b><?php echo $i18n['SQR_GALLERY_SETTINGS_PLUGINS'];?></b></td>
			<td>
				<input type="checkbox" name="sqr_gsg_plugins" value="1" <?php echo (@$settings_vars['disable_plugins'] ?  'checked="checked"' : '');?> />
			</td>
		</tr>
		
		<tr>
			<td nowrap="nowrap"><b><?php echo $i18n['SQR_GALLERY_SETTINGS_JQUERY'];?></b></td>
			<td>
				<input type="checkbox" name="sqr_gsg_jquery" value="1" <?php echo (@$settings_vars['jquery_disabled'] ?  'checked="checked"' : '');?> />
			</td>
		</tr>
		
		<tr>
			<td nowrap="nowrap"><b><?php echo $i18n['SQR_GALLERY_SETTINGS_RELTAG'];?></b></td>
			<td>
				<input type="text" name="sqr_gsg_gallery_img_rel" value="<?php echo @$settings_vars['gallery_img_rel'];?>" />
			</td>
		</tr>
		
		<tr>
			<td nowrap="nowrap"><b><?php echo $i18n['SQR_GALLERY_SETTINGS_CLASSTAG'];?></b></td>
			<td>
				<input type="text" name="sqr_gsg_gallery_img_class" value="<?php echo @$settings_vars['gallery_img_class'];?>" />
			</td>
		</tr>
		<?php 
		exec_action('gsg-settings-render'); 
		?>
		<tr>
				<td>&nbsp;</td>
				<td>
					<input type="submit" name="submit" class="submit" value="<?php echo $i18n['SQR_GALLERY_SETTINGS_SAVE'];?>" />
					 <?php echo $i18n['SQR_GALLERY_OR'];?> <a href="loadtab.php?id=<?php echo GSG_PLUGINID;?>&item=<?php echo GSG_PLUGINID;?>"><?php echo $i18n['SQR_GALLERY_CANCEL'];?></a> </td>
				
		</tr>
	</table>
	</form>
	<?php 
	
}

/**
 * Generates the actual gallery HTML
 * 
 * @param string $gid - Gallery ID
 */
function squareit_generate_gallery($gid)
{
	global $SITEURL, $i18n;
	$gal = '';
	$gallery = false;
	
	//get the gallery
	if (file_exists(GSG_FOLDERPATH.$gid.'.xml')) 
	{
		$gallery = squareit_get_gallery($gid);
		$sqr_images = $gallery['images'];
	}
	
	$gal .= '<!-- BEGIN Square IT\'s Simple Image Gallery [ID:'.$gid.'] -->';
		
	exec_action('gsg-pre-gallery'); 

	//for plugins use ---- to have the ability to override the output
	if ( !isset($_SESSION['SQR_GSG']['override_output']) || 
		(isset($_SESSION['SQR_GSG']['override_output']) && $_SESSION['SQR_GSG']['override_output']) 
		|| !isset($gallery['override_output']) || (isset($gallery['override_output']) && $gallery['override_output']) )
	{
		//make sure the gallery exists before displaying anything
		if ($gallery)
		{
			if(!$_SESSION['SQR_GSG']['disable_gallery_name'])
			{
				$gal .= '<h3>'.$gallery['name'].'</h3>';
			}
					
			$gal .= '<ul id="sqr_image_gallery_'.$gid.'" class="sqr_image_gallery">';
		
			//set the rel type
			$reltype = '';
			if (isset($_SESSION['SQR_GSG']['gallery_img_rel']))
			{
				$reltype = ' rel="'.$_SESSION['SQR_GSG']['gallery_img_rel'].'"';
			}
			
			$styleclass = ' class="gsgimage"';
			if (isset($_SESSION['SQR_GSG']['gallery_img_class']))
			{
				$styleclass = ' class="'.$_SESSION['SQR_GSG']['gallery_img_class'].'"';
			}

			$captionsAvailable = false;
			foreach ($sqr_images as $image)
			{
				if (isset($image['caption']) && trim($image['caption'])!='')
				{
					$captionsAvailable = true;
					break;
				}
			}
			
			foreach ($sqr_images as $image)
			{
				$pathlink = 'image.php?i='.$image['image'];
				
				$gal .= '<li><div class="sqr-thumb-cont"><span></span>';
				if (file_exists(GSG_THUMBSPATH.'thumbmed.'.$image['image'])) {
					$gal .= '<a href="'.$SITEURL.'data/uploads/'. $image['image'] .'" title="'. htmlspecialchars($image['caption']) .'" '.$reltype.$styleclass.'><img alt="'.htmlspecialchars($image['caption']).'" src="'.$SITEURL.GSG_RELFOLDERPATH.'thumbs/thumbmed.'. $image['image'].'" class="sqr_img" /></a>';
				} else {
					$gal .= '<a href="'.$SITEURL.'data/uploads/'.  $image['image'] .'" title="'. htmlspecialchars($image['caption'])  .'" '.$reltype.$styleclass.'><img alt="'.htmlspecialchars($image['caption']).'" src="'.$SITEURL.'admin/inc/thumb.php?src='.  $image['image'] .'&dest=../../'.GSG_RELFOLDERPATH.'thumbs/thumbmed.'.  $image['image'] .'&x=125&y=125&f=1" class="sqr_img" /></a>';
				}
				$gal .= '</div>';
				//caption
				if(!$_SESSION['SQR_GSG']['disable_caption'])
				{
					$strcamption = '&nbsp;';
					if (isset($image['caption']) && trim($image['caption'])!='')
					{
						$strcamption = $image['caption'];
					}

					if ($captionsAvailable)
					{
						$gal .= '<div class="sqr-thumb-caption">'. $strcamption  .'</div>';
					}
				}
				$gal .= '</li>';
			}
					
			$gal .= '</ul>
			<div class="sqr-clearer"></div>';
		}
		else 
		{
			//gallery not found.
			$gal .= "<div style='color:red'><strong>".$i18n['SQR_GALLERY_DISPLAY_INVALID'] .": $gid</strong> <br/> ".$i18n['SQR_GALLERY_DISPLAY_PLS_CHECK']."</div>";
		}
	
	}
	
	$gal = exec_filter('gsg-gallery-output', array($gal, $gallery));
	//if the return is an array make sure the first element is the CONTENT
	if (is_array($gal))
	{
		$gal = $gal[0];
	}
		
	exec_action('gsg-post-gallery');
		
	$gal .= '<!-- END Square IT\'s Simple Image Gallery [ID:'.$gid.'] -->';
			
	return $gal;
	
}

/**
 * Parses the content on a page and matches it to a gallery if one exists.
 * 
 * @param string $content - Content of Page
 * @return string;
 */
function squareit_display_gallery($content)
{
	$found = preg_match_all('/\{squareit_gallery_(\w+)\}/', $content, $match);
	
	for ($i=0; $i<=$found; $i++)
	{
		$gid = '';
		if (isset($match[1][$i]))
		{
			$gid = $match[1][$i];
		}
		else
		{
			return $content;
		}
		
		$gal = squareit_generate_gallery($gid);
		
		$content = preg_replace('/\{squareit_gallery_'.$gid.'\}/', $gal, $content);
	}
	
	return $content;
}

/**
 * Function that generates the HTML of a gallery
 * 
 * @param string $gid - Gallery Id
 * @param boolean $rtrn - If set to true it will return the html
 */
function squareit_gallery($gid, $rtrn = false)
{
	global $sqr_gsg_inc_included;
	
	if (!$sqr_gsg_inc_included)
	{
		squareit_gallery_includes();
	}
	
	$gal = squareit_generate_gallery($gid);

	if ($rtrn)
	{
		return $gal;
	}
	
	echo $gal;
}


/**
 * Returns an array with all images in the gallery
 * 
 * @return array
 */
function squareit_get_gallery($gid)
{
	//gallery settings file
	$settings_file= GSG_FOLDERPATH.$gid.'.xml';
	
	$galArray = array();
	if (file_exists($settings_file)) {
			$v = getXML($settings_file);
			$galArray = squareit_objectsintoarray($v);
			
			$indx = 0;
			$tempArrImages = array();
			foreach ($v->images->image as $image) {
				
				$tempArrImages[$indx]['image'] = (string)$image;
				$image = (array)$image;
				foreach ($image['@attributes'] as $attrname => $attrval) {
					$tempArrImages[$indx][$attrname]= (string)$attrval;
				}
				$indx++;
			}
			
			$galArray['images'] = $tempArrImages;
			
	}
	return $galArray;
}

/**
 * Checks to see if an image gallery exists in page and add includes
 */
function squareit_gallery_include_check()
{
	global $data_index;
	
	if (strpos($data_index->content, '{squareit_gallery_') === false)
	{
		return false;
	}
	
	add_action('theme-header','squareit_gallery_includes',array());
}


/**
 *  include the CSS/JS file that are needed for the image gallery
 */
function squareit_gallery_includes()
{
	global $SITEURL;
	global $sqr_gsg_inc_included;
	$sqr_gsg_settings = squareit_gsg_load_settings();
	$sqr_gsg_inc_included = true;
	
	//if jquery is disabled in settings then do not include it
	if (!$sqr_gsg_settings['jquery_disabled'])
	{?>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
		<script>
		//define no conflict jquery variable
		var $jSqr = jQuery.noConflict();
		</script>
		<?php 
	}?>
	<!--[if lt IE 8]>
	<style>
	.sqr-thumb-cont span {
		display: inline-block;
		height: 100%;
	}
	</style>
	<![endif]-->
	<link rel="stylesheet" type="text/css" href="<?php echo $SITEURL.GSG_RELFOLDERPATH;?>css/sqr_imgal.css" media="all">
	<?php 
	//action for the gsg head scripts
	exec_action('gsg-head'); 
	
}

/**
 * Checks to see if directory permission is set correctly
 */
if (!function_exists('squareit_health_check'))
{
	function squareit_health_check($dir_path)
	{
		$plugPerm = substr(base_convert(@fileperms($dir_path), 10, 8), 2);
		
		$plugFolder = str_replace('/', '', substr($dir_path, strlen(GSPLUGINPATH)));

		if ($plugPerm < 755 && !($plugPerm == '' || $plugPerm == 0))
		{
			$msg = $i18n['SQR_GALLERY_PERM_TO'].' <i><b>'.$plugFolder.'</b></i> '.$i18n['SQR_GALLERY_PERM_NOT_SET_MSG'].' <br/>';
			$msg .= '<b>'.$i18n['SQR_GALLERY_PERM_PATH'].':</b> '.$dir_path.'<br/>';
			$msg .= '<b>'.$i18n['SQR_GALLERY_PERM_CURRENT'].':</b> 0'.$plugPerm.'<br/>';
			$msg .= '<b>'.$i18n['SQR_GALLERY_PERM_RECOMMENDED'].':</b> 0755';
			
			squareit_msg_box($msg, 'error');
		} 
		else if($plugPerm == '' || $plugPerm == 0)
		{
			$msg = $i18n['SQR_GALLERY_PERM_NOT_EXISTS_MSG'].' <i><b>'.$plugFolder.'</b></i> '.$i18n['SQR_GALLERY_PERM_NOT_EXISTS_MSG_CONT'].'<br/>';
			$msg .= '<b>'.$i18n['SQR_GALLERY_PERM_PATH'].':</b> '.$dir_path.'<br/>';
			$msg .= '<b>'.$i18n['SQR_GALLERY_PERM_RECOMMENDED'].':</b> 0755';
			squareit_msg_box($msg, 'error');
		}
	}
}

/**
 * Function to check current version of the plugin
 *
 */
function squareit_gallery_new_version()
{
	$plugin_id = 85;

	//check to avoid calling the api at every page refresh
	if (!isset($_SESSION['SQR_GSG']['version_check']))
	{
		$apiback = file_get_contents('http://get-simple.info/api/extend/?id='.$plugin_id);
		if (!$apiback)
		{
			return false;
		}

		$response = json_decode($apiback);
		if ($response->status != 'successful') {
			return false;
		}

		$current_ver = $response->version;
		$_SESSION['SQR_GSG']['version_check'] = $current_ver;
	}
	else
	{
		$current_ver = $_SESSION['SQR_GSG']['version_check'];
	}
	
	
	$vCompRes = version_compare(GSG_VERSION, $current_ver);

	if ($vCompRes < 0)
	{
		?>

		<?php
	}
?>

	
	<?php
}


//include for plugins functionality
if (!$_SESSION['SQR_GSG']['disable_plugins'])
{
	require_once GSG_INCPATH.'gsg_plugins.inc.php';
}