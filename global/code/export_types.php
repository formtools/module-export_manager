<?php

/**
 * This file defines all functions relating to the Export Manager module' export types.
 *
 * @copyright Encore Web Studios 2008
 * @author Encore Web Studios <formtools@encorewebstudios.com>
 */


// -------------------------------------------------------------------------------------------------


/**
 * Deletes an export type.
 *
 * @param integer $export_type_id
 */
function exp_delete_export_type($export_type_id)
{
	global $g_table_prefix, $L;

	$export_type_info = exp_get_export_type($export_type_id);

  mysql_query("
    DELETE FROM {$g_table_prefix}module_export_types
    WHERE export_type_id = $export_type_id
      ");

  // now make sure there aren't any gaps in the
  exp_check_export_type_order($export_type_info["export_group_id"]);

  return array(true, $L["notify_export_type_deleted"]);
}


/**
 * Returns all information about a particular Export type.
 *
 * @param integer $export_type_id
 * @return array
 */
function exp_get_export_type($export_type_id)
{
	global $g_table_prefix;

	$query = mysql_query("
	  SELECT *, met.smarty_template as export_type_smarty_template
    FROM   {$g_table_prefix}module_export_types met, {$g_table_prefix}module_export_groups metg
    WHERE  met.export_group_id = metg.export_group_id AND
           met.export_type_id = $export_type_id
		    ");
	return mysql_fetch_assoc($query);
}


/**
 * Returns all available export types in the database.
 *
 * @param integer $export_group (optional)
 * @param boolean $only_return_visible (optional, defaulted to FALSE)
 * @return array
 */
function exp_get_export_types($export_group = "", $only_return_visible = false)
{
	global $g_table_prefix;

	$group_clause = (!empty($export_group)) ? "AND met.export_group_id = $export_group" : "";
  $visibility_clause = ($only_return_visible) ? "AND met.export_type_visibility = 'show'" : "";

	$query = mysql_query("
	  SELECT *, met.list_order as export_type_list_order, met.smarty_template as export_type_smarty_template
    FROM   {$g_table_prefix}module_export_types met, {$g_table_prefix}module_export_groups metg
    WHERE  met.export_group_id = metg.export_group_id
        $group_clause
        $visibility_clause
    ORDER BY met.list_order
      ");

  $infohash = array();
	while ($field = mysql_fetch_assoc($query))
    $infohash[] = $field;

  return $infohash;
}


/**
 * Returns all available export types in the database.
 *
 * @param integer $export_group
 * @return array
 */
function exp_get_num_export_types($export_group_id)
{
	global $g_table_prefix;

	$query = mysql_query("
	  SELECT count(*) as c
    FROM   {$g_table_prefix}module_export_types
    WHERE  export_group_id = $export_group_id
      ");

  $result = mysql_fetch_assoc($query);
  $num_export_types = $result["c"];

  return $num_export_types;
}


/**
 * Adds a new export type.
 *
 * @param array $info
 */
function exp_add_export_type($info)
{
	global $g_table_prefix, $L;

	$info = ft_sanitize($info);
	$export_type_name = $info["export_type_name"];
	$visibility = $info["visibility"];
  $filename  = $info["filename"];
  $export_group_id = $info["export_group_id"];
  $smarty_template = $info["smarty_template"];

	// get the next highest order count
	$query = mysql_query("SELECT count(*) as c FROM {$g_table_prefix}module_export_types WHERE export_group_id = $export_group_id");
	$result = mysql_fetch_assoc($query);
	$order = $result["c"] + 1;

	mysql_query("
	  INSERT INTO {$g_table_prefix}module_export_types (export_type_name, export_type_visibility, filename,
	      export_group_id, smarty_template, list_order)
	  VALUES ('$export_type_name', '$visibility', '$filename', $export_group_id, '$smarty_template', $order)
	    ");


	return array(true, $L["notify_export_type_added"]);
}


/**
 * Updates an export type.
 *
 * @param integer $export_type_id
 * @param array
 */
function exp_update_export_type($info)
{
	global $g_table_prefix, $L;

	$info = ft_sanitize($info);
	$export_type_id = $info["export_type_id"];
	$export_type_name = $info["export_type_name"];
	$visibility = $info["visibility"];
  $filename  = $info["filename"];
  $export_group_id = $info["export_group_id"];
  $smarty_template = $info["smarty_template"];

	mysql_query("
	  UPDATE {$g_table_prefix}module_export_types
	  SET    export_type_name = '$export_type_name',
	         export_type_visibility = '$visibility',
	         filename = '$filename',
	         export_group_id = $export_group_id,
	         smarty_template = '$smarty_template'
	  WHERE  export_type_id = $export_type_id
	    ");

	return array(true, $L["notify_export_type_updated"]);
}


/**
 * This can be called after deleting an export type, or whenever is needed to ensure that the
 * order of the export types are consistent, accurate & don't have any gaps.
 */
function exp_check_export_type_order($export_group_id)
{
  global $g_table_prefix;

  $query = mysql_query("
    SELECT export_type_id
    FROM   {$g_table_prefix}module_export_types
    WHERE  export_group_id = $export_group_id
    ORDER BY list_order
      ");

  $ordered_types = array();
  while ($row = mysql_fetch_assoc($query))
  	$ordered_types[] = $row["export_type_id"];

  $order = 1;
  foreach ($ordered_types as $export_type_id)
  {
  	mysql_query("
  	  UPDATE {$g_table_prefix}module_export_types
  	  SET    list_order = $order
  	  WHERE  export_type_id = $export_type_id
  	    ");
  	$order++;
  }
}


/**
 * Called by the administrator on the Export Types tab of the Edit Export Group page. It reorders the export
 * types within a particular export group.
 *
 * @param array $info
 */
function exp_reorder_export_types($info)
{
	global $g_table_prefix, $L;

	$export_group_id = $info["export_group_id"];

	$new_order = array();

	// loop through $infohash and for each field_X_order values, log the new order
	while (list($key, $val) = each($info))
	{
		// find the export type group id
		preg_match("/^export_type_(\d+)_order$/", $key, $match);

		if (!empty($match[1]))
		{
			$export_type_id = $match[1];
			$new_order[$export_type_id] = $val;
		}
	}
	asort($new_order);


	$order = 1;
	while (list($key, $value) = each($new_order))
	{
		mysql_query("
			UPDATE {$g_table_prefix}module_export_types
			SET    list_order = $order
			WHERE  export_type_id = $key AND
			       export_group_id = $export_group_id
								");

		$order++;
	}

	return array(true, $L["notify_export_types_reordered"]);
}