<?php


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
