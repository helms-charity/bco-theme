<?php
/****************************************************
*
* @File: 			us_US.php
* @Package:			GetSimple Image Gallery Plugin
* @Subject:			US English language file
* @Date:			13 April 2010
* @Revision:		4 Feb 2011
* @Version:			GetSimple 2.0
* @Status:			Beta
* @Traductors:			Julian Castaneda	
*
*****************************************************/

/**MAIN**/
$i18n['SQR_GALLERY_PLUGIN_NAME'] = 'Simple Image Gallery';
$i18n['SQR_GALLERY_PLUGIN_DESC'] = 'Allows the author of a site to create image galleries';
$i18n['SQR_GALLERY_TAB_NAME'] = 'Image Gallery';
$i18n['SQR_GALLERY_ADMIN_TAB_MISSING'] = 'Admin Tab Loader plugin missing!<br/> In order for this plugin to work as intended you need to install the "Admin Tab Loader" plugin that came bundled with this plugin';
$i18n['SQR_GALLERY_HELP_TEXT'] = 'To display a gallery in a page, just copy the code given (ie. <i>{squareit_gallery_4bb104e22f23c}</i>) and paste it into the content. You can put multiple galleries in the same page.';
$i18n['SQR_GALLERY_NAME_LABEL'] = 'Gallery Name';
$i18n['SQR_GALLERY_LIST_HEAD_CODE'] = 'Code';
$i18n['SQR_GALLERY_LIST_EMPTY'] = 'You don\'t have any galleries yet! <br/>Create a <a href="loadtab.php?id='.GSG_PLUGINID.'&item=squareit_admin_add_gallery">new gallery</a> now. It\'s Simple!';
$i18n['SQR_GALLERY_LIST_EDIT'] = 'edit';
$i18n['SQR_GALLERY_LIST_CONFIRM_DEL'] = 'Are you sure you want to delete this gallery';
$i18n['SQR_GALLERY_SIDEMENU_LIST'] = $i18n['SQR_GALLERY_PLUGIN_NAME'];
$i18n['SQR_GALLERY_SIDEMENU_ADD_GAL'] = 'Add New Gallery';
$i18n['SQR_GALLERY_SIDEMENU_SETTINGS'] = 'Settings';
$i18n['SQR_GALLERY_SIDEMENU_PLUGINS'] = 'Plugins';


/*** FRONTEND DISPLAY ***/
$i18n['SQR_GALLERY_DISPLAY_INVALID'] = 'Invalid Gallery ID';
$i18n['SQR_GALLERY_DISPLAY_PLS_CHECK'] = 'Please check this gallery exists.';

/*** ADD/EDIT SECTION ***/
$i18n['SQR_GALLERY_EDIT_TITLE'] = 'Edit Gallery';
$i18n['SQR_GALLERY_ADD_TITLE'] = 'Create New Gallery';
$i18n['SQR_GALLERY_ADD_EDIT_INFO'] = 'Select the images you want to display in your image gallery. (You need to have uploaded your images via the Files tab first.) Thumbnails are made automatically.';
$i18n['SQR_GALLERY_LIST_HEAD_IMG'] = 'Image';
$i18n['SQR_GALLERY_LIST_HEAD_FILE'] = 'File Name';
$i18n['SQR_GALLERY_LIST_HEAD_SIZE'] = 'Size';
$i18n['SQR_GALLERY_LIST_HEAD_DATE'] = 'Date Added';
$i18n['SQR_GALLERY_LIST_HEAD_CAPTION'] = 'Caption';
$i18n['SQR_GALLERY_NO_IMAGES'] = 'No images available.<br/> Click <a href="upload.php">here</a> to upload some images.';
$i18n['SQR_GALLERY_ADD_EDIT_SUBMIT'] = "Save Gallery";
$i18n['SQR_GALLERY_ADD_EDIT_OPTIONS'] = "Gallery Options";
$i18n['SQR_GALLERY_ADD_EDIT_DISPLAY_TITLE'] = "Turn off gallery title display?";
$i18n['SQR_GALLERY_ADD_EDIT_DISPLAY_CAPTION'] = "Turn off caption display?";
$i18n['SQR_GALLERY_OR'] = "or";
$i18n['SQR_GALLERY_CANCEL'] = "Cancel";

$i18n['SQR_GALLERY_VAL_NO_NAME'] = 'Please enter a Gallery Name!';
$i18n['SQR_GALLERY_SUCCESS_MSG'] = 'Image Gallery Saved Successfully!';
$i18n['SQR_GALLERY_FAILED_SAVE_MSG'] = 'ERROR: Unable to save';

/*** HEALTH CHECK ***/
$i18n['SQR_GALLERY_PERM_TO'] = 'Permissions to';	
$i18n['SQR_GALLERY_PERM_NOT_SET_MSG'] = 'might not be set correctly';

$i18n['SQR_GALLERY_PERM_NOT_EXISTS_MSG'] = 'Plugin folder does not exist. Please make sure you uploaded';
$i18n['SQR_GALLERY_PERM_NOT_EXISTS_MSG_CONT'] = 'to the plugins folder.';

$i18n['SQR_GALLERY_PERM_NOT_PATH'] = 'Path';
$i18n['SQR_GALLERY_PERM_CURRENT'] = 'Current Permissions';
$i18n['SQR_GALLERY_PERM_RECOMMENDED'] = 'Recommended Permissions';

/** SETTINGS PAGE ***/
$i18n['SQR_GALLERY_SETTINGS_LABEL'] = "Settings";
$i18n['SQR_GALLERY_SETTINGS_LANG'] = "Plugin Language";
$i18n['SQR_GALLERY_SETTINGS_JQUERY'] = "Disable jQuery Include";
$i18n['SQR_GALLERY_SETTINGS_DISPLAY_GALNAME'] = "GLOBAL - Disable Gallery Name Display";
$i18n['SQR_GALLERY_SETTINGS_DISPLAY_CAPTION'] = "GLOBAL - Disable Image Caption Display";
$i18n['SQR_GALLERY_SETTINGS_SAVE'] = "Save Settings";
$i18n['SQR_GALLERY_SETTINGS_LOAD_ERR'] = "Unable to load settings file.";
$i18n['SQR_GALLERY_SETTINGS_PLUGINS'] = "Disable Plugin System";
$i18n['SQR_GALLERY_SETTINGS_RELTAG'] = "Global Images REL Tag";
$i18n['SQR_GALLERY_SETTINGS_CLASSTAG'] = "Global Images Class";

$i18n['SQR_SETTINGS_SAVE_SUCCESS'] = 'Your settings were saved successfully!';
$i18n['SQR_SETTINGS_SAVE_FAILED'] = 'Something went wrong while trying to save your settings. Please try again!';
$i18n['SQR_SETTINGS_SAVE_VALIDATION_FAILED'] = '';

/** PLUGINS PAGE **/
$i18n['SQR_PLUGINS_TITLE'] = $i18n['PLUGINS_MANAGEMENT'];
$i18n['SQR_PLUGINS_ENABLE'] = "Enable";
$i18n['SQR_PLUGINS_DISABLE'] = "Disable";

