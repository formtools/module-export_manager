<?php

/**
 * Export.php
 *
 * This file does the actual generation of the content for view / display by the user. It calls the
 * export.tpl found in the /modules/export_manager/templates folder.
 */

require("../../global/session_start.php");
ft_include_module("export_manager");

$request = array_merge($_POST, $_GET);

// passed in explicitly via POST or GET
$export_group_id = (isset($request["export_group_id"])) ? $request["export_group_id"] : "";
$export_type_id  = (isset($request["export_type_id"])) ? $request["export_type_id"] : "";
$results         = (isset($request["export_group_{$export_group_id}_results"])) ? $request["export_group_{$export_group_id}_results"] : "all";

// drawn from sessions
$form_id       = isset($_SESSION["ft"]["curr_form_id"]) ? $_SESSION["ft"]["curr_form_id"] : "";
$view_id       = $_SESSION["ft"]["form_{$form_id}_view_id"];
$order         = $_SESSION["ft"]["current_search"]["order"];
$search_fields = $_SESSION["ft"]["current_search"]["search_fields"];

// if any of the required fields weren't entered, just output a simple blank message
if (empty($form_id) || empty($view_id) || empty($order) || empty($search_fields) || empty($export_group_id))
{
	echo $LANG["export_manager"]["notify_export_incomplete_fields"];
	exit;
}

// if the user only wants to display the currently selected rows, limit the query to those submission IDs
$submission_ids = array();
if ($results == "selected")
  $submission_ids = $_SESSION["ft"]["form_{$form_id}_selected_submissions"];

// perform the almighty search query
$results_info = ft_search_submissions($form_id, $view_id, "all", 1, $order, "all", $search_fields, $submission_ids);

$form_info    = ft_get_form($form_id);
$view_info    = ft_get_view($view_id);
$display_fields = ft_get_submission_field_info($view_info["fields"], true);


// first, build the list of information we're going to send to the export type smarty template
$placeholders = exp_get_export_filename_placeholder_hash();
$placeholders["export_group_id"] = $export_group_id;
$placeholders["export_type_id"] = $export_type_id;
$placeholders["export_group_results"] = $results;
$placeholders["same_page"] = $_SERVER["PHP_SELF"];
$placeholders["display_fields"] = $display_fields;
$placeholders["submissions"]    = $results_info["search_rows"];
$placeholders["num_results"]    = $results_info["search_num_results"];
$placeholders["view_num_results"] = $results_info["view_num_results"];
$placeholders["form_info"] = $form_info;
$placeholders["view_info"] = $view_info;
$placeholders["date_format"] = $_SESSION["ft"]["account"]["date_format"];
$placeholders["timezone_offset"] = $_SESSION["ft"]["account"]["timezone_offset"];

// pull out a few things into top level placeholders for easy use
$placeholders["form_name"] = $form_info["form_name"];
$placeholders["form_id"]   = $form_id;
$placeholders["form_url"]  = $form_info["form_url"];
$placeholders["view_name"] = $view_info["view_name"];
$placeholders["view_id"]   = $view_id;


// if the export type isn't available, the export group probably only contains a single export type. Get it!
$export_group_info = exp_get_export_group($export_group_id);
$export_types = exp_get_export_types($export_group_id);

if (empty($export_type_id))
	$export_type_info = $export_types[0];
else
	$export_type_info = exp_get_export_type($export_type_id);


$placeholders["export_group_name"] = ft_create_slug(ft_eval_smarty_string($export_group_info["group_name"]));
$placeholders["export_group_type"] = ft_create_slug(ft_eval_smarty_string($export_type_info["export_type_name"]));
$placeholders["filename"] = ft_eval_smarty_string($export_type_info["filename"], $placeholders);

$template = $export_type_info["export_type_smarty_template"];
$placeholders["export_type_name"] = $export_type_info["export_type_name"];
$export_type_smarty_template = ft_eval_smarty_string($template, $placeholders);

// next, add the placeholders needed for the export group smarty template
$template = $export_group_info["smarty_template"];
$placeholders["export_group_name"] = ft_eval_smarty_string($export_group_info["group_name"]);
$placeholders["export_types"] = $export_types;
$placeholders["export_type_smarty_template"] = $export_type_smarty_template;
$page = ft_eval_smarty_string($template, $placeholders);


if ($export_group_info["action"] == "new_window" || $export_group_info["action"] == "popup")
{
	// if required, send the HTTP headers
	if (!empty($export_group_info["headers"]))
	{
	  $headers = preg_replace("/\r\n|\r/", "\n", $export_group_info["headers"]);
	  $header_lines = explode("\n", $headers);

	  foreach ($header_lines as $header)
	  	header(ft_eval_smarty_string($header, $placeholders));
	}

	echo $page;
}

// create a file on the server
else
{
	$settings = ft_get_settings("", "export_manager");
	$file_upload_dir = $settings["file_upload_dir"];
	$file_upload_url = $settings["file_upload_url"];

  $file = "$file_upload_dir/{$placeholders["filename"]}";
  if ($handle = fopen($file, "w"))
  {
	  fwrite($handle, $page);
	  fclose($handle);

	  $message = "The file has been generated. <a href='$file_upload_url/{$placeholders["filename"]}' target='_blank'>Click here</a> to view the file.";
	  echo "{ success: true, message: \"$message\", target_message_id: \"ft_message\" }";
	  exit;
  }
}

