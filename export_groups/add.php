<?php

require("../../../global/library.php");
ft_init_module_page();
require_once("../library.php");

$page_vars = array();
$page_vars["icons"] = exp_get_export_icons();
$page_vars["head_title"] = "{$L["module_name"]} - {$L["phrase_add_export_group"]}";
$page_vars["head_js"] = "
var rules = [];
rules.push(\"required,group_name,{$L["validation_no_export_group_name"]}\");
";

ft_display_module_page("templates/export_groups/add.tpl", $page_vars);
