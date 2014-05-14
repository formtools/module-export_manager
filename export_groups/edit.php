<?php

require("../../../global/library.php");
ft_init_module_page();
require_once("../library.php");

$request = array_merge($_POST, $_GET);

$page            = ft_load_module_field("export_manager", "page", "export_manager_tab", "main");
$export_group_id = ft_load_module_field("export_manager", "export_group_id", "export_manager_export_group_id", "export_group_id");


if (isset($request["add_export_type"]))
  list ($g_success, $g_message) = exp_add_export_type($request);


// define the Image Manager tabs
$tabs = array(
  "main" => array(
      "tab_label" => "Main",
      "tab_link" => "{$_SERVER["PHP_SELF"]}?page=main&export_group_id=$export_group_id"
        ),
  "export_types" => array(
      "tab_label" => "Export Types",
      "tab_link" => "{$_SERVER["PHP_SELF"]}?page=export_types&export_group_id=$export_group_id",
      "pages" => array("export_types", "add_export_type", "edit_export_type")
        )
    );


// load the appropriate code pages
switch ($page)
{
	case "main":
		require("page_main.php");
		break;
	case "export_types":
		require("page_export_types.php");
		break;
	case "add_export_type":
		require("page_add_export_type.php");
    break;
	case "edit_export_type":
		require("page_edit_export_type.php");
    break;

	default:
		require("page_main.php");
		break;
}
