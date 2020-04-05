<?php

/**
 * Loads Settings file
 * 
 * @return SimpleXML
 */
function squareit_gsg_load_settings($reset=false)
{
	$sqr_gsg_settings = '';
	if (!isset($_SESSION['SQR_GSG']) || $reset)
	{
		if (file_exists(GSG_DATAPATH . 'settings.xml'))
		{
			$sqr_gsg_settings = getXML(GSG_DATAPATH . 'settings.xml');
			$_SESSION['SQR_GSG'] = squareit_objectsintoarray($sqr_gsg_settings);

			if (!$_SESSION['SQR_GSG']['disable_plugins'])
			{
				if (file_exists(GSG_DATAPATH . 'plugins.xml'))
				{
					$sqr_gsg_plug_stat = getXML(GSG_DATAPATH . 'plugins.xml');
					$_SESSION['SQR_GSG']['plugins'] = squareit_objectsintoarray($sqr_gsg_plug_stat);
				}
			}

			$_SESSION['SQR_GSG']['sess_time'] = time() + (60 * 60);
			$sqr_gsg_settings = $_SESSION['SQR_GSG'];
		}
		else
		{
			squareit_msg_box(GSG_PLUGINID . " - Problem Loading settings file");
			exit;
		}
	}
	else
	{
		//refresh session every hour
		if (time() > $_SESSION['SQR_GSG']['sess_time'])
		{
			squareit_gsg_load_settings(true);
		}

		$sqr_gsg_settings = $_SESSION['SQR_GSG'];
	}

	return $sqr_gsg_settings;
}

if (!function_exists('squareit_objectsintoarray'))
{

	function squareit_objectsintoarray($arrObjData, $arrSkipIndices = array())
	{
		$arrData = array();

		// if input is object, convert into array
		if (is_object($arrObjData))
		{
			$arrObjData = get_object_vars($arrObjData);
		}

		if (is_array($arrObjData))
		{
			foreach ($arrObjData as $index => $value)
			{
				if (is_object($value) || is_array($value))
				{
					$value = squareit_objectsintoarray($value, $arrSkipIndices); // recursive call
				}
				if (in_array($index, $arrSkipIndices))
				{
					continue;
				}
				$arrData[$index] = $value;
			}
		}
		return $arrData;
	}

}

/**
 *  Generates a message box
 *  - Prepare it so that it can be used across all square it plugins
 */
if (!function_exists('squareit_msg_box'))
{

	function squareit_msg_box($msg, $type='updated')
	{
		echo '<div class="' . $type . '">
				' . $msg . '
			 </div>';
	}

}

if (!function_exists('squareit_page_header'))
{
	function squareit_page_header($msg)
	{
		if ((double) GSVERSION > (double) '2.03')
		{?>
			<h3 class="floated"> <?php echo $msg; ?></h3>
			<?php
		}
		else
		{?>
			<label> <?php echo $msg; ?></label>
			<?php
		}
		?>
			<br/><br/>
		<?php
	}
}

//credit hook
add_action(GSG_PLUGINID . '-sidebar-extra', 'squareit_gallery_credit', array());

function squareit_gallery_credit()
{
?>
<?php
}

function squareit_gsg_security()
{
	global $SESSIONHASH;
	if (@$_REQUEST['s'] !== $SESSIONHASH)
	{
		die("INVALID REQUEST");
	}
}

