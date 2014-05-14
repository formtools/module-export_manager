<?php

/**
 * This file defines all functions for managing export groups within the Export Manager module.
 *
 * @copyright Encore Web Studios 2008
 * @author Encore Web Studios <formtools@encorewebstudios.com>
 */


// -------------------------------------------------------------------------------------------------


/**
 * Returns all information about an export type group.
 *
 * @param integer $export_group_id
 */
function exp_get_export_group($export_group_id)
{
	global $g_table_prefix;

	$query = mysql_query("
	  SELECT *
    FROM   {$g_table_prefix}module_export_groups
    WHERE  export_group_id = $export_group_id
      ");

  $export_group_info = mysql_fetch_assoc($query);

  // get any custom list of clients, if this is a Private export type
  $query = mysql_query("
    SELECT account_id
    FROM   {$g_table_prefix}module_export_group_clients
    WHERE  export_group_id = $export_group_id
      ");

  $account_ids = array();
  while ($row = mysql_fetch_assoc($query))
  	$account_ids[] = $row["account_id"];

  $export_group_info["client_ids"] = $account_ids;

	return $export_group_info;
}


/**
 * Returns an array of all export type groups in the database.
 *
 * @return array
 */
function exp_get_export_groups()
{
	global $g_table_prefix;

	$query = mysql_query("
	  SELECT   *
    FROM     {$g_table_prefix}module_export_groups
    ORDER BY list_order
      ");

  $infohash = array();
	while ($field = mysql_fetch_assoc($query))
	{
		$export_group_id = $field["export_group_id"];
		$field["num_export_types"] = exp_get_num_export_types($export_group_id);
    $infohash[] = $field;
	}

  return $infohash;
}


/**
 * This is the main function used to figure out which export groups get displayed for a particular
 * client account. For the administrator account, just use exp_get_export_groups.
 *
 * @param integer $account_id
 * @return array an array of hashes
 */
function exp_get_client_export_groups($account_id)
{
	global $g_table_prefix;

  $query = mysql_query("
    SELECT export_group_id
    FROM   {$g_table_prefix}module_export_group_clients
    WHERE  account_id = $account_id
			");
  $private_export_group_ids = array();
  while ($row = mysql_fetch_assoc($query))
    $private_export_group_ids[] = $row["export_group_id"];


	$export_groups = exp_get_export_groups();

  $visible_export_groups = array();
	foreach ($export_groups as $group)
	{
		if ($group["access_type"] == "public")
		  $visible_export_groups[] = $group;
		else if ($group["access_type"] == "private")
		{
			if (in_array($group["export_group_id"], $private_export_group_ids))
			  $visible_export_groups[] = $group;
		}
	}

	return $visible_export_groups;
}


/**
 * Adds a new export type group to the database.
 *
 * @param array $info
 */
function exp_add_export_group($info)
{
	global $g_table_prefix, $L;

	$info = ft_sanitize($info);
	$group_name = $info["group_name"];
	$icon       = $info["icon"];
	$visibility = $info["visibility"];

	// get the next highest order count
	$query = mysql_query("SELECT count(*) as c FROM {$g_table_prefix}module_export_groups");
	$result = mysql_fetch_assoc($query);
	$order = $result["c"] + 1;

	// define the default options
	$access_type = "admin";
	$action = "new_window";
	$action_button_text = "{\$LANG.word_display}";

	mysql_query("
	  INSERT INTO {$g_table_prefix}module_export_groups (group_name, access_type, visibility,
	    icon, action, action_button_text, list_order)
	  VALUES ('$group_name', '$access_type', '$visibility', '$icon', '$action', '$action_button_text',
	    $order)
	    ");

	return array(true, $L["notify_export_group_added"]);
}


/**
 * Updates an export type group.
 *
 * @param array $info
 * @return array
 */
function exp_update_export_group($info)
{
	global $g_table_prefix, $L;

	$info = ft_sanitize($info);
	$export_group_id = $info["export_group_id"];
	$access_type  = $info["access_type"];
	$visibility   = $info["visibility"];
	$group_name   = $info["group_name"];
	$icon         = $info["icon"];
	$action       = $info["action"];
	$action_button_text = $info["action_button_text"];
	$popup_height = $info["popup_height"];
	$popup_width  = $info["popup_width"];
	$headers      = isset($info["headers"]) ? $info["headers"] : "";
	$smarty_template = $info["smarty_template"];
	$selected_client_ids = (isset($info["selected_client_ids"])) ? $info["selected_client_ids"] : array();

	mysql_query("
	  UPDATE {$g_table_prefix}module_export_groups
	  SET    visibility = '$visibility',
	         access_type = '$access_type',
	         group_name = '$group_name',
	         icon = '$icon',
	         action = '$action',
	         action_button_text = '$action_button_text',
	         popup_height = '$popup_height',
	         popup_width = '$popup_width',
	         headers = '$headers',
	         smarty_template = '$smarty_template'
	  WHERE  export_group_id = $export_group_id
	    ");

	// now update the list of clients that may have been manually assigned to this (Private) export group
	mysql_query("DELETE FROM {$g_table_prefix}module_export_group_clients WHERE export_group_id = $export_group_id");

	foreach ($selected_client_ids as $account_id)
	{
		mysql_query("
		  INSERT INTO {$g_table_prefix}module_export_group_clients (export_group_id, account_id)
		  VALUES ($export_group_id, $account_id)
		    ");
	}

	return array(true, $L["notify_export_group_updated"]);
}


/**
 * Deletes an export group. Important: note that any export types that were associated with this group are orphaned!
 *
 * TODO check this...!
 *
 * @param integer $export_group_id
 */
function exp_delete_export_group($export_group_id)
{
	global $g_table_prefix, $L;

  mysql_query("
    DELETE FROM {$g_table_prefix}module_export_groups
    WHERE export_group_id = $export_group_id
      ");

  mysql_query("
    UPDATE {$g_table_prefix}module_export_types
    SET    export_group_id = NULL
    WHERE  export_group_id = $export_group_id
      ");

  // now make sure there aren't any gaps in the
  exp_check_export_group_order();

  return array(true, $L["notify_export_group_deleted"]);
}


/**
 * This can be called after deleting an export group, or whenever is needed to ensure that the
 * order of the export groups are consistent, accurate & don't have any gaps.
 */
function exp_check_export_group_order()
{
  global $g_table_prefix;

  $query = mysql_query("
    SELECT export_group_id
    FROM   {$g_table_prefix}module_export_groups
    ORDER BY list_order ASC
      ");

  $ordered_groups = array();
  while ($row = mysql_fetch_assoc($query))
  	$ordered_groups[] = $row["export_group_id"];

  $order = 1;
  foreach ($ordered_groups as $export_group_id)
  {
  	mysql_query("
  	  UPDATE {$g_table_prefix}module_export_groups
  	  SET    list_order = $order
  	  WHERE  export_group_id = $export_group_id
  	    ");
  	$order++;
  }
}


/**
 * Called by the administrator on the Export Type Groups page. It reorders the export groups, which determines
 * the order in which they appear in the client and admin pages.
 *
 * @param array $info
 */
function exp_reorder_export_groups($info)
{
	global $g_table_prefix, $L;

	$new_order = array();

	// loop through $infohash and for each field_X_order values, log the new order
	while (list($key, $val) = each($info))
	{
		// find the export type group id
		preg_match("/^group_(\d+)_order$/", $key, $match);

		if (!empty($match[1]))
		{
			$export_group_id = $match[1];
			$new_order[$export_group_id] = $val;
		}
	}
	asort($new_order);

	$order = 1;
	while (list($key, $value) = each($new_order))
	{
		mysql_query("
			UPDATE {$g_table_prefix}module_export_groups
			SET    list_order = $order
			WHERE  export_group_id = $key
								");

		$order++;
	}

	return array(true, $L["notify_export_group_reordered"]);
}