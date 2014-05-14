<?php

/**
 * This file defines all general functions relating to the Export Manager module.
 *
 * @copyright Encore Web Studios 2008
 * @author Encore Web Studios <formtools@encorewebstudios.com>
 */


// -------------------------------------------------------------------------------------------------


/**
 * Returns a list of all export icons, found in the /modules/export_manager/images/icons/ folder.
 *
 * return array an array of image filenames.
 */
function exp_get_export_icons()
{
	global $g_root_dir;

	$icon_folder = "$g_root_dir/modules/export_manager/images/icons/";

	// store all the icon filenames in an array
	$filenames = array();
	if ($handle = opendir($icon_folder))
	{
		while (false !== ($file = readdir($handle)))
		{
			$extension = ft_get_filename_extension($file, true);

			if ($extension == "jpg" || $extension == "gif" || $extension == "bmp" || $extension == "png")
			  $filenames[] = $file;
		}
	}

	return $filenames;
}


/**
 * Called on the Settings page. Updates the generated file folder information.
 *
 * @param array $info
 * @return array [0] T/F [1] Error / notification message
 */
function exp_update_settings($info)
{
	global $g_table_prefix, $L;

	$old_settings = ft_get_module_settings();

  $settings = array();
  $settings["file_upload_dir"] = $info["file_upload_dir"];
  $settings["file_upload_url"] = $info["file_upload_url"];

  ft_set_module_settings($settings);

  return array(true, $L["notify_settings_updated"]);
}


/**
 * Used in generating the filenames; this builds most of the placeholder values (the date-oriented ones)
 * to which the form and export-specific placeholders are added.
 *
 * @return array the placeholder array
 */
function exp_get_export_filename_placeholder_hash()
{
 	$offset = ft_get_current_timezone_offset();
	$date_str = ft_get_date($offset, ft_get_current_datetime(), "Y|y|F|M|m|n|d|D|j|g|h|H|i|s|U|a");
	list($Y, $y, $F, $M, $m, $n, $d, $D, $j, $g, $h, $H, $i, $s, $U, $a) = explode("|", $date_str);

	$placeholders = array(
	  "datetime" => "$Y-$m-$d.$H-$i-$s",
	  "date" => "$Y-$m-$d",
	  "time" => "$H-$i-$s",
	  "Y" => $Y,
	  "y" => $y,
	  "F" => $F,
	  "M" => $M,
	  "n" => $n,
	  "d" => $d,
	  "D" => $D,
	  "j" => $j,
	  "g" => $g,
	  "h" => $h,
	  "H" => $H,
	  "s" => $s,
	  "U" => $U,
	  "a" => $a
	);

	return $placeholders;
}
