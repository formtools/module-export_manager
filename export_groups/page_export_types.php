<?php

if (isset($request["delete"]))
  list ($g_success, $g_message) = exp_delete_export_type($request["delete"]);

if (isset($request["reorder_export_types"]))
  list ($g_success, $g_message) = exp_reorder_export_types($request);

$export_types = exp_get_export_types($export_group_id);

$page_vars = array();
$page_vars["export_group_info"] = exp_get_export_group($export_group_id);
$page_vars["export_types"] = $export_types;
$page_vars["head_title"] = "{$L["module_name"]} - {$L["phrase_edit_export_group"]}";
$page_vars["page"] = "export_types";
$page_vars["tabs"] = $tabs;
$page_vars["icons"] = exp_get_export_icons();
$page_vars["head_js"] = "
var page_ns = {};
page_ns.delete_export_type = function(export_type_id)
{
  var answer = confirm(\"{$L["confirm_delete_export_type"]}\");

  if (answer)
    window.location = \"edit.php?delete=\" + export_type_id;

  return false;
}
";

ft_display_module_page("templates/export_groups/edit.tpl", $page_vars);