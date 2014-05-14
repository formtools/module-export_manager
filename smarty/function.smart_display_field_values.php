<?php

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.smart_display_field_values
 * Type:     function
 * Name:     smart_display_field_values
 * Purpose:  Used throughout the export manager to display the values for dropdowns, multi-select dropdowns,
 *           radio buttons and checkboxes. This function is "smart" in that it caches the field option values
 *           in sessions for either the duration of sessions, or for the request, depending on the cache setting
 *           found in the modules' Settings page. This can *substantially* reduce load times for forms with
 *           large numbers of field options.
 *
 * -------------------------------------------------------------
 */
function smarty_function_smart_display_field_values($params, &$smarty)
{
	global $LANG, $g_multi_val_delimiter;

	if (empty($params["field_id"]))
  {
	  $smarty->trigger_error("assign: missing 'field_id' parameter. This is used to give the select field a name and id value.");
    return;
  }

  $_SESSION["ft"]["export_manager"]["cache"] = array();

	$field_id = $params["field_id"];
	$selected = $params["selected"];

	// determines if we should be displaying more that one value (e.g. multi-select fields)
	$multiple = isset($params["multiple"]) ? $params["multiple"] : false;
	$delimiter = isset($params["delimiter"]) ? $params["delimiter"] : $g_multi_val_delimiter;
  $escape    = isset($params["escape"]) ? $params["escape"] : ""; // options: csv

	// executive decision! If $selected is blank, output nothing. This is done because many fields have an empty
	// "Please Select" option. So instead of outputting "Please Select" for all blank fields, this outputs nothing
	// which is probably what most people intend
	if (empty($selected))
	  return;

	// check the location for temporary storage of field options has been specified in sessions
	if (!isset($_SESSION["ft"]))
	  $_SESSION["ft"] = array();
	  if (!isset($_SESSION["ft"]["export_manager"]))
	  $_SESSION["ft"]["export_manager"] = array();
	if (!isset($_SESSION["ft"]["export_manager"]["cache"]))
	  $_SESSION["ft"]["export_manager"]["cache"] = array();

	// if we haven't already loaded the field options for this field in sessions, do so!
	if (!isset($_SESSION["ft"]["export_manager"]["cache"][$field_id]))
	{
    $field_options = ft_get_field_options($field_id);
    $_SESSION["ft"]["export_manager"]["cache"][$field_id] = array();

		foreach ($field_options as $field_option)
		{
		  $option_value = $field_option["option_value"];
		  $option_name  = $field_option["option_name"];
		  $_SESSION["ft"]["export_manager"]["cache"][$field_id][$option_value] = $option_name;
		}
  }

  // now display the value(s)
  $display_str = "";
  if ($multiple)
  {
  	$values = explode($g_multi_val_delimiter, $selected);
  	$display_vals = array();
  	foreach ($values as $value)
  	  $display_vals[] = $_SESSION["ft"]["export_manager"]["cache"][$field_id][$value];

  	$display_str = implode($delimiter, $display_vals);
  }
  else
  	$display_str = $_SESSION["ft"]["export_manager"]["cache"][$field_id][$selected];

  // finally, if need be, escape the string
  if (!empty($escape))
  {
  	switch ($escape)
  	{
  	  case "csv":
        $display_str = preg_replace("/\"/", "\"\"", $display_str);

        // if it contains one or more commas, escape the whole thing in double quotes
   	    if (strstr($display_str, ","))
	        $display_str = "\"$display_str\"";
	      break;
  	}
  }

  echo $display_str;
}

