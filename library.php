<?php

/**
 * Export Manager code files.
 *
 * @copyright Encore Web Studios 2008
 * @author Encore Web Studios <formtools@encorewebstudios.com>
 */

// -------------------------------------------------------------------------------------------------

$folder = dirname(__FILE__);
require_once("$folder/global/code/export_groups.php");
require_once("$folder/global/code/export_types.php");
require_once("$folder/global/code/general.php");


/**
 * The Export Manager installation function. This is automatically called by the installation script if the
 * module is contained in the zipfile. Otherwise it's called explicitly when the user manually installs the
 * module.
 */
function export_manager__install($module_id)
{
  global $g_table_prefix, $g_root_dir, $g_root_url;

  $queries = array();
  $queries[] = "
		CREATE TABLE {$g_table_prefix}module_export_groups (
		  export_group_id smallint(5) unsigned NOT NULL auto_increment,
		  group_name varchar(255) NOT NULL,
		  access_type enum('admin','public','private') NOT NULL default 'public',
		  visibility enum('show','hide') NOT NULL default 'show',
		  icon varchar(100) NOT NULL,
		  action enum('file','popup','new_window') NOT NULL default 'popup',
		  action_button_text varchar(255) NOT NULL default '{\$LANG.word_display}',
		  popup_height varchar(5) default NULL,
		  popup_width varchar(5) default NULL,
		  headers text,
		  smarty_template mediumtext NOT NULL,
		  list_order tinyint(4) NOT NULL,
		  PRIMARY KEY  (export_group_id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8
		  ";

  $queries[] = "
		INSERT INTO {$g_table_prefix}module_export_groups (export_group_id, group_name, access_type, visibility, icon, action, action_button_text, popup_height, popup_width, headers, smarty_template, list_order) VALUES
		(1, '{\$LANG.export_manager.phrase_html_printer_friendly}', 'public', 'show', 'printer.gif', 'popup', '{\$LANG.word_display}', '600', '800', '', '<html>\r\n<head>\r\n  <title>{\$export_group_name}</title>\r\n\r\n  {* escape the CSS so it doesn''t confuse Smarty *}\r\n  {literal}\r\n  <style type=\"text/css\">\r\n  body { margin: 0px; }\r\n  table, td, tr, div, span { font-family: verdana; font-size: 8pt; }\r\n  #nav_row { background-color: #efefef; padding: 10px; }\r\n  #export_group_name { color: #336699; font-weight:bold }\r\n  .print_table { border: 1px solid #dddddd; }\r\n  .print_table th { border: 1px solid #cccccc; background-color: #efefef; text-align: left; }\r\n  .print_table td { border: 1px solid #cccccc; }\r\n  .page_break { page-break-after: always; }\r\n  </style>\r\n\r\n  <style type=\"text/css\" media=\"print\">\r\n  .no_print { display: none }\r\n  </style>\r\n  {/literal}\r\n\r\n</head>\r\n<body>\r\n\r\n<div id=\"nav_row\" class=\"no_print\">\r\n\r\n  <span style=\"float:right\">\r\n    {* if there''s more than one export type in this group, display the types in a dropdown *}\r\n    {if \$export_types|@count > 1}\r\n      <select name=\"export_type_id\" onchange=\"window.location=''{\$same_page}?export_group_id={\$export_group_id}&export_group_{\$export_group_id}_results={\$export_group_results}&export_type_id='' + this.value\">\r\n      {foreach from=\$export_types item=export_type}\r\n        <option value=\"{\$export_type.export_type_id}\" {if \$export_type.export_type_id == \$export_type_id}selected{/if}>{eval var=\$export_type.export_type_name}</option>\r\n      {/foreach}\r\n      </select>\r\n    {/if}\r\n    <input type=\"button\" onclick=\"window.close()\" value=
		\"{\$LANG.word_close|upper}\" />\r\n    <input type=\"button\" onclick=\"window.print()\" value=\"{\$LANG.word_print|upper}\" />\r\n  </span>\r\n\r\n  <span id=\"export_group_name\">{eval var=\$export_group_name}</span>\r\n</div>\r\n\r\n<div style=\"padding: 15px\">\r\n  {\$export_type_smarty_template}\r\n</div>\r\n\r\n</body>\r\n</html>', 1),
		(2, '{\$LANG.export_manager.word_excel}', 'public', 'show', 'xls.gif', 'new_window', '{\$LANG.export_manager.word_generate}', '', '', 'Pragma: public\nCache-Control: max-age=0\nContent-Type: application/vnd.ms-excel; charset=utf-8\nContent-Disposition: attachment; filename={\$filename}', '<html>\r\n<head>\r\n</head>\r\n<body>\r\n\r\n{\$export_type_smarty_template}\r\n\r\n</body>\r\n</html>', 2),
		(3, '{\$LANG.export_manager.word_xml}', 'public', 'hide', 'xml.jpg', 'file', '{\$LANG.export_manager.word_generate}', '', '', '', '<?xml version=\"1.0\" encoding=\"utf-8\" ?>\r\n\r\n{\$export_type_smarty_template}', 4),
		(4, '{\$LANG.export_manager.word_csv}', 'public', 'hide', 'csv.gif', 'new_window', '{\$LANG.export_manager.word_generate}', '', '', 'Content-type: application/xml; charset=\"octet-stream\"\r\nContent-Disposition: attachment; filename={\$filename}', '{\$export_type_smarty_template}', 3)
		  ";

  $queries[] = "
		CREATE TABLE {$g_table_prefix}module_export_group_clients (
		  export_group_id mediumint(8) unsigned NOT NULL,
		  account_id mediumint(8) unsigned NOT NULL,
		  PRIMARY KEY  (export_group_id, account_id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8
		  ";

  $queries[] = "
	  CREATE TABLE {$g_table_prefix}module_export_types (
		  export_type_id mediumint(8) unsigned NOT NULL auto_increment,
		  export_type_name varchar(255) NOT NULL,
		  export_type_visibility enum('show','hide') NOT NULL default 'show',
		  filename varchar(255) NOT NULL,
		  export_group_id smallint(6) default NULL,
		  smarty_template text NOT NULL,
		  list_order tinyint(3) unsigned NOT NULL,
		  PRIMARY KEY (export_type_id)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8
		  ";

  $queries[] = "
		INSERT INTO {$g_table_prefix}module_export_types (export_type_id, export_type_name, export_type_visibility, filename, export_group_id, smarty_template, list_order) VALUES
		(1, '{\$LANG.export_manager.phrase_one_by_one}', 'show', 'submissions-{\$M}.{\$j}.html', 1, '<h1>{\$form_name}</h1>\r\n\r\n{foreach from=\$submissions item=submission name=row}      \r\n<table cellpadding=\"2\" cellspacing=\"0\" width=\"100%\" class=\"print_table\">\r\n  {foreach from=\$display_fields item=field name=col_row}\r\n    {assign var=field_id value=\$field.field_id}\r\n    {assign var=field_type value=\$field.field_info.field_type}\r\n    {assign var=col_name value=\$field.col_name}\r\n    {assign var=value value=\$submission.\$col_name}\r\n      <tr>\r\n        <th width=\"140\">{\$field.field_title}</th>\r\n        <td>\r\n\r\n            {if \$field_type == \"select\" || \$field_type == \"radio-buttons\"}\r\n	          {foreach from=\$field.field_info.options key=k3 item=option}\r\n  	              {if \$option.option_value == \$value}\r\n	                  {\$option.option_name}\r\n	              {/if}\r\n	          {/foreach}\r\n            {elseif \$field_type == \"checkboxes\" || \$field_type == \"multi-select\"}\r\n	        {display_multi_select_field_values options=\$field.field_info.options values=\$value}\r\n            {elseif \$field_type == \"system\"}\r\n                {if \$col_name == \"submission_id\"}\r\n	            {\$submission.submission_id}\r\n                {elseif \$col_name == \"submission_date\"}    \r\n                    {\$submission.submission_date|custom_format_date:\$timezone_offset:\$date_format}\r\n                {elseif \$col_name == \"last_modified_date\"}\r\n                    {\$search_row.last_modified_date|custom_format_date:\$timezone_offset:\$date_format}\r\n                {elseif \$col_name == \"ip_address\"}\r\n                    {\$submission.ip_address}\r\n                {/if}\r\n            {else}\r\n                {\$value}\r\n            {/if}\r\n            </td>\r\n          </tr>\r\n       {/foreach}\r\n    </table>\r\n\r\n  <br />\r\n {/foreach}\r\n\r\n</table>', 2),
		(2, '{\$LANG.export_manager.phrase_table_format}', 'show', 'submissions-{\$M}.{\$j}.html', 1, '<h1>{\$form_name}</h1>\r\n\r\n<table cellpadding=\"2\" cellspacing=\"0\" width=\"100%\" class=\"print_table\">\r\n{* display the column headings *}\r\n<tr>\r\n    {foreach from=\$display_fields item=column name=row}\r\n        <th>{\$column.field_title}</th>\r\n    {/foreach}\r\n</tr>\r\n\r\n{* loop through all submissions in this current result set, and display each item in a manner appropriate to \r\n    the field type *}\r\n{foreach from=\$submissions item=submission name=row}\r\n    <tr>\r\n        {foreach from=\$display_fields item=field name=col_row}\r\n            {assign var=field_id value=\$field.field_id}\r\n            {assign var=field_type value=\$field.field_info.field_type}\r\n            {assign var=col_name value=\$field.col_name}\r\n	    {assign var=value value=\$submission.\$col_name}\r\n\r\n            <td>\r\n            {if \$field_type == \"select\" || \$field_type == \"radio-buttons\"}\r\n	          {foreach from=\$field.field_info.options key=k3 item=option}\r\n  	              {if \$option.option_value == \$value}\r\n	                  {\$option.option_name}\r\n	              {/if}\r\n	          {/foreach}\r\n            {elseif \$field_type == \"checkboxes\" || \$field_type == \"multi-select\"}\r\n	        {display_multi_select_field_values options=\$field.field_info.options values=\$value}\r\n            {elseif \$field_type == \"system\"}\r\n                {if \$col_name == \"submission_id\"}\r\n	            {\$submission.submission_id}\r\n                {elseif \$col_name == \"submission_date\"}    \r\n                    {\$submission.submission_date|custom_format_date:\$timezone_offset:\$date_format}\r\n                {elseif \$col_name == \"last_modified_date\"}\r\n                    {\$search_row.last_modified_date|custom_format_date:\$timezone_offset:\$date_format}\r\n                {elseif \$col_name == \"ip_address\"}\r\n                    {\$submission.ip_address}\r\n                {/if}\r\n            {else}\r\n                {\$value}\r\n            {/if}\r\n            </td>\r\n\r\n       {/foreach}\r\n    </tr>\r\n\r\n {/foreach}\r\n\r\n</table>', 1),
		(3, '{\$LANG.export_manager.phrase_one_submission_per_page}', 'show', 'submissions-{\$M}.{\$j}.html', 1, '<h1>{\$form_name}</h1>\r\n\r\n{foreach from=\$submissions item=submission name=row}      \r\n<table cellpadding=\"2\" cellspacing=\"0\" width=\"100%\" class=\"print_table\">\r\n  {foreach from=\$display_fields item=field name=col_row}\r\n    {assign var=field_id value=\$field.field_id}\r\n    {assign var=field_type value=\$field.field_info.field_type}\r\n    {assign var=col_name value=\$field.col_name}\r\n    {assign var=value value=\$submission.\$col_name}\r\n      <tr>\r\n        <th width=\"140\">{\$field.field_title}</th>\r\n        <td>\r\n\r\n            {if \$field_type == \"select\" || \$field_type == \"radio-buttons\"}\r\n	          {foreach from=\$field.field_info.options key=k3 item=option}\r\n  	              {if \$option.option_value == \$value}\r\n	                  {\$option.option_name}\r\n	              {/if}\r\n	          {/foreach}\r\n            {elseif \$field_type == \"checkboxes\" || \$field_type == \"multi-select\"}\r\n	        {display_multi_select_field_values options=\$field.field_info.options values=\$value}\r\n            {elseif \$field_type == \"system\"}\r\n                {if \$col_name == \"submission_id\"}\r\n	            {\$submission.submission_id}\r\n                {elseif \$col_name == \"submission_date\"}    \r\n                    {\$submission.submission_date|custom_format_date:\$timezone_offset:\$date_format}\r\n                {elseif \$col_name == \"last_modified_date\"}\r\n                    {\$search_row.last_modified_date|custom_format_date:\$timezone_offset:\$date_format}\r\n                {elseif \$col_name == \"ip_address\"}\r\n                    {\$submission.ip_address}\r\n                {/if}\r\n            {else}\r\n                {\$value}\r\n            {/if}\r\n            </td>\r\n          </tr>\r\n       {/foreach}\r\n    </table>\r\n\r\n    {if !\$smarty.foreach.row.last}\r\n      <br />\r\n      <div class=\"no_print\"><i>- {\$LANG.phrase_new_page} -</i></div>\r\n      <br class=\"page_break\" />\r\n    {/if}\r\n\r\n {/foreach}\r\n\r\n', 3),
		(4, '{\$LANG.export_manager.phrase_table_format}', 'show', 'submissions-{\$M}.{\$j}.xls', 2, '<h1>{\$form_name}</h1>\r\n\r\n<table cellpadding=\"2\" cellspacing=\"0\" width=\"100%\" class=\"print_table\">\r\n{* display the column headings *}\r\n<tr>\r\n    {foreach from=\$display_fields item=column name=row}\r\n        <th>{\$column.field_title}</th>\r\n    {/foreach}\r\n</tr>\r\n\r\n{* loop through all submissions in this current result set, and display each item in a manner appropriate to \r\n    the field type *}\r\n{foreach from=\$submissions item=submission name=row}\r\n    <tr>\r\n        {foreach from=\$display_fields item=field name=col_row}\r\n            {assign var=field_id value=\$field.field_id}\r\n            {assign var=field_type value=\$field.field_info.field_type}\r\n            {assign var=col_name value=\$field.col_name}\r\n	    {assign var=value value=\$submission.\$col_name}\r\n\r\n            <td>\r\n            {if \$field_type == \"select\" || \$field_type == \"radio-buttons\"}\r\n	          {foreach from=\$field.field_info.options key=k3 item=option}\r\n  	              {if \$option.option_value == \$value}\r\n	                  {\$option.option_name}\r\n	              {/if}\r\n	          {/foreach}\r\n            {elseif \$field_type == \"checkboxes\" || \$field_type == \"multi-select\"}\r\n	        {display_multi_select_field_values options=\$field.field_info.options values=\$value}\r\n            {elseif \$field_type == \"system\"}\r\n                {if \$col_name == \"submission_id\"}\r\n	            {\$submission.submission_id}\r\n                {elseif \$col_name == \"submission_date\"}    \r\n                    {\$submission.submission_date|custom_format_date:\$timezone_offset:\$date_format}\r\n                {elseif \$col_name == \"last_modified_date\"}\r\n                    {\$search_row.last_modified_date|custom_format_date:\$timezone_offset:\$date_format}\r\n                {elseif \$col_name == \"ip_address\"}\r\n                    {\$submission.ip_address}\r\n                {/if}\r\n            {else}\r\n                {\$value}\r\n            {/if}\r\n            </td>\r\n\r\n       {/foreach}\r\n    </tr>\r\n\r\n {/foreach}\r\n\r\n</table>', 1),
		(5, 'All submissions', 'show', 'form{\$form_id}_{\$datetime}.csv', 4, '{strip}\r\n  {* display the column headings *}\r\n  {foreach from=\$display_fields item=column name=row}\r\n    {\$column.field_title|escape:''csv''}{if !\$smarty.foreach.row.last},{/if}\r\n  {/foreach}\r\n{/strip}\r\n{* display the each submission row *}{foreach from=\$submissions item=submission name=row}{strip}\r\n    {foreach from=\$display_fields item=field name=col_row}\r\n        {assign var=field_id value=\$field.field_id}\r\n        {assign var=field_type value=\$field.field_info.field_type}\r\n        {assign var=col_name value=\$field.col_name}\r\n        {assign var=value value=\$submission.\$col_name}\r\n\r\n        {if \$field_type == \"select\" || \$field_type == \"radio-buttons\"}\r\n            {foreach from=\$field.field_info.options key=k3 item=option}\r\n                {if \$option.option_value == \$value}\r\n                    {\$option.option_name|escape:''csv''}\r\n                {/if}\r\n            {/foreach}\r\n        {elseif \$field_type == \"checkboxes\" || \$field_type == \"multi-select\"}\r\n            {display_multi_select_field_values options=\$field.field_info.options values=\$value}\r\n        {elseif \$field_type == \"system\"}\r\n            {if \$col_name == \"submission_id\"}\r\n	        {\$submission.submission_id}\r\n            {elseif \$col_name == \"submission_date\"}    \r\n                {\$submission.submission_date|custom_format_date:\$timezone_offset:\$date_format|escape:''csv''}\r\n            {elseif \$col_name == \"last_modified_date|escape:''csv''\"}\r\n                {\$search_row.last_modified_date|custom_format_date:\$timezone_offset:\$date_format}\r\n            {elseif \$col_name == \"ip_address\"}\r\n                {\$submission.ip_address|escape:''csv''}\r\n            {/if}\r\n        {else}\r\n            {\$value|escape:''csv''}\r\n        {/if}\r\n\r\n        {* if this wasn''t the last row, output a comma *}\r\n        {if !\$smarty.foreach.col_row.last},{/if}\r\n\r\n    {/foreach}\r\n\r\n    {/strip}{if !\$smarty.foreach.row.last}!!??\r\n{/if}{/foreach}', 1),
		(6, 'All Submissions', 'show', 'form{\$form_id}_{\$datetime}.xml', 3, '<export>\r\n  <export_datetime>{\$datetime}</export_datetime>\r\n  <export_unixtime>{\$U}</export_unixtime>\r\n  <form_info>\r\n    <form_id>{\$form_id}</form_id>\r\n    <form_name><![CDATA[{\$form_name}]]></form_name>\r\n    <form_url>{\$form_url}</form_url>\r\n  </form_info>\r\n  <view_info>\r\n    <view_id>{\$view_id}</view_id>\r\n    <view_name><![CDATA[{\$view_name}]]></view_name>\r\n  </view_info>\r\n\r\n  <submissions>\r\n    {foreach from=\$submissions item=submission name=row}      \r\n      <submission>\r\n        {foreach from=\$display_fields item=field name=col_row}\r\n          {assign var=col_name value=\$field.col_name}\r\n          <{\$col_name}><![CDATA[{\$submission.\$col_name}]]></{\$col_name}>\r\n        {/foreach}\r\n      </submission>\r\n    {/foreach}\r\n\r\n  </submissions>\r\n</export>', 1)
		  ";

  $queries[] = "INSERT INTO {$g_table_prefix}settings (setting_name, setting_value, module) VALUES ('file_upload_dir', '{$g_root_dir}/upload', 'export_manager')";
  $queries[] = "INSERT INTO {$g_table_prefix}settings (setting_name, setting_value, module) VALUES ('file_upload_url', '{$g_root_url}/upload', 'export_manager')";

  foreach ($queries as $query)
  	$result = mysql_query($query);

  return array(true, "");
}


/**
 * The Export Manager uninstall script. This is called by Form Tools when the user explicitly chooses to
 * uninstall the module.
 */
function export_manager__uninstall($module_id)
{
	global $g_table_prefix;

	// our create table query
	mysql_query("DROP TABLE {$g_table_prefix}module_export_groups");
	mysql_query("DROP TABLE {$g_table_prefix}module_export_group_clients");
	mysql_query("DROP TABLE {$g_table_prefix}module_export_types");

	return array(true, "");
}
