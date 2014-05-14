<?php

require("../../../global/library.php");
ft_init_module_page();
require_once("../library.php");

$request = array_merge($_POST, $_GET);

if (isset($request["update"]))
  list ($g_success, $g_message) = exp_update_settings($request);

$module_settings = ft_get_module_settings();

// ------------------------------------------------------------------------------------------------

$page_vars = array();
$page_vars["head_title"] = "{$L["module_name"]} - {$LANG["word_settings"]}";
$page_vars["module_settings"] = $module_settings;
$page_vars["allow_url_fopen"] = (ini_get("allow_url_fopen") == "1");

ft_display_module_page("templates/settings/index.tpl", $page_vars);