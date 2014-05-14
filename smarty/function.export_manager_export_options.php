<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.export_manager_export_options
 * Type:     function
 * Name:     export_manager_export_options
 * Purpose:  this function is called from the admin and client pages at the foot of the submission listing page.
 *           It generates the HTML to display all the export functionality, based on the current user account.
 *
 *           *** The HTML is found in the export_manage/templates/export_options_html.tpl template ***
 * -------------------------------------------------------------
 */
function smarty_function_export_manager_export_options($params, &$smarty)
{
	global $LANG, $g_smarty, $g_root_url, $request;

	if (empty($params["account_type"]))
  {
	  $smarty->trigger_error("assign: missing 'account_type' parameter. This should contain 'admin' or 'client' depending on what page is calling it.");
    return;
  }
	if (empty($params["account_id"]))
  {
	  $smarty->trigger_error("assign: missing 'account_id' parameter.");
    return;
  }

  $export_groups = array();
  if ($params["account_type"] == "admin")
    $export_groups = exp_get_export_groups();
  else
    $export_groups = exp_get_client_export_groups($params["account_id"]);

  // add the export group types
  $export_info = array();
  foreach ($export_groups as $export_group)
  {
  	// get all VISIBLE export types
  	$export_types = exp_get_export_types($export_group["export_group_id"], true);

  	// don't show the group if (a) there are no export types, and/or (b) it's set to hidden
  	if (empty($export_types) || $export_group["visibility"] == "hide")
  	  continue;

  	$export_group["export_types"] = $export_types;
    $export_info[] = $export_group;
  }

  // now for the fun stuff! We loop through all export groups and log all the settings for
  // each of the fields, based on incoming POST values
  $page_vars = array();
  foreach ($export_info as $export_group)
  {
  	$export_group_id = $export_group["export_group_id"];
  	$page_vars["export_group_{$export_group_id}_results"] = ft_load_module_field("export_manager", "export_group_{$export_group_id}_results", "export_group_{$export_group_id}_results");
  	$page_vars["export_group_{$export_group_id}_export_type"] = ft_load_module_field("export_manager", "export_group_{$export_group_id}_export_type", "export_group_{$export_group_id}_export_type");
  }

  // now pass the information to the Smarty template to display
  $g_smarty->assign("export_groups", $export_info);
  $g_smarty->assign("page_vars", $page_vars);
  $g_smarty->assign("export_icon_folder", "$g_root_url/modules/export_manager/images/icons");

	// now process the template and return the HTML
	return $smarty->fetch("../../modules/export_manager/templates/export_options_html.tpl");
}

