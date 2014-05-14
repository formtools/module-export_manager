<?php

if (isset($request["update_export_group"]))
  list ($g_success, $g_message) = exp_update_export_group($request);

$export_group = exp_get_export_group($export_group_id);
$page_vars = array();
$page_vars["export_group_info"] = $export_group;
$page_vars["page"] = "main";
$page_vars["tabs"] = $tabs;
$page_vars["icons"] = exp_get_export_icons();
$page_vars["head_title"] = "{$L["module_name"]} - {$L["phrase_edit_export_group"]}";
$page_vars["head_js"] = "
  var page_ns = {};
  page_ns.toggle_form_type = function(form_type)
  {
    switch (form_type)
    {
      case \"admin\":
	      $(\"custom_clients\").hide();
	      break;
      case \"public\":
	      $(\"custom_clients\").hide();
	      break;
      case \"private\":
	      $(\"custom_clients\").show();
	      break;
	  }
  }

  page_ns.change_action_type = function(action_type)
  {
    if (action_type == \"file\")
      $(\"headers\").disabled = true;
    else
      $(\"headers\").disabled = false;
  }

  var rules = [];
  rules.push(\"required,group_name,Please enter the export group name.\");
  rules.push(\"if:action=popup,required,popup_height,Please enter the popup height.\");
  rules.push(\"if:action=popup,required,popup_width,Please enter the popup width.\");

  // select all clients on form submit (to pass to server)
  rsv.onCompleteHandler = function() { ft.select_all($(\"selected_client_ids[]\")); }
";

ft_display_module_page("templates/export_groups/edit.tpl", $page_vars);