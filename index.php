<?php

require_once("../../global/library.php");
ft_init_module_page();
$request = array_merge($_POST, $_GET);

if (isset($request["add_export_group"]))
  list ($g_success, $g_message) = exp_add_export_group($request);
if (isset($request["delete"]))
  list ($g_success, $g_message) = exp_delete_export_group($request["delete"]);
if (isset($request["reorder_export_groups"]))
  list ($g_success, $g_message) = exp_reorder_export_groups($request);

$export_groups = exp_get_export_groups();

// ------------------------------------------------------------------------------------------------

$page_vars = array();
$page_vars["export_groups"] = $export_groups;
$page_vars["head_js"] = "
var page = {};
page.delete_export_group = function(group_id)
{
  var answer = confirm(\"{$L["confirm_delete_export_group"]}\");

  if (answer)
    window.location = \"index.php?delete=\" + group_id;

  return false;
}
";

ft_display_module_page("templates/index.tpl", $page_vars);