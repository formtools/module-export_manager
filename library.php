<?php

/**
 * Export Manager code files.
 *
 * @copyright Encore Web Studios 2009
 * @author Encore Web Studios <formtools@encorewebstudios.com>
 */

// -------------------------------------------------------------------------------------------------

$folder = dirname(__FILE__);
require_once("$folder/global/code/export_groups.php");
require_once("$folder/global/code/export_types.php");
require_once("$folder/global/code/general.php");


/**
 * The Export Manager installation function. This is automatically called by the installation script if the
 * module is contained in the zipfile. Otherwise it's called when the user manually installs the module.
 */
function export_manager__install($module_id)
{
  global $g_table_prefix, $g_root_dir, $g_root_url, $LANG;

  $queries = array();
  $word_display = ft_sanitize($LANG["word_display"]);
  $queries[] = "
    CREATE TABLE {$g_table_prefix}module_export_groups (
      export_group_id smallint(5) unsigned NOT NULL auto_increment,
      group_name varchar(255) NOT NULL,
      access_type enum('admin','public','private') NOT NULL default 'public',
      form_view_mapping enum('all','except','only') NOT NULL default 'all',
      forms_and_views mediumtext NULL,
      visibility enum('show','hide') NOT NULL default 'show',
      icon varchar(100) NOT NULL,
      action enum('file','popup','new_window') NOT NULL default 'popup',
      action_button_text varchar(255) NOT NULL default '$word_display',
      popup_height varchar(5) default NULL,
      popup_width varchar(5) default NULL,
      headers text,
      smarty_template mediumtext NOT NULL,
      list_order tinyint(4) NOT NULL,
      PRIMARY KEY  (export_group_id)
    ) DEFAULT CHARSET=utf8
      ";

  $queries[] = "
    CREATE TABLE {$g_table_prefix}module_export_group_clients (
      export_group_id mediumint(8) unsigned NOT NULL,
      account_id mediumint(8) unsigned NOT NULL,
      PRIMARY KEY  (export_group_id, account_id)
    ) DEFAULT CHARSET=utf8
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
    ) DEFAULT CHARSET=utf8
      ";

  foreach ($queries as $query)
  {
    $result = mysql_query($query);
    if (!$result)
    {
      exp_remove_tables();
      return array(false, $LANG["export_manager"]["notify_installation_problem_c"] . " <b>" . mysql_error() . "</b>");
    }
  }

  // now populate the tables
  list($success, $message) = exp_insert_default_data();
  if (!$success)
  {
    exp_remove_tables();
    exp_clear_table_data();
    return array(false, $message);
  }

  ft_register_hook("template", "export_manager", "admin_submission_listings_bottom", "", "exp_display_export_options");
  ft_register_hook("template", "export_manager", "client_submission_listings_bottom", "", "exp_display_export_options");

  return array(true, "");
}


function export_manager__upgrade($old_version, $new_version)
{
  global $g_table_prefix;

  $old_version_info = ft_get_version_info($old_version);
  $new_version_info = ft_get_version_info($new_version);

  if ($old_version_info["release_date"] < 20090908)
  {
    @mysql_query("ALTER TABLE {$g_table_prefix}module_export_groups TYPE=MyISAM");
    @mysql_query("ALTER TABLE {$g_table_prefix}module_export_groups ENGINE=MyISAM");
    @mysql_query("ALTER TABLE {$g_table_prefix}module_export_group_clients TYPE=MyISAM");
    @mysql_query("ALTER TABLE {$g_table_prefix}module_export_group_clients ENGINE=MyISAM");
    @mysql_query("ALTER TABLE {$g_table_prefix}module_export_types TYPE=MyISAM");
    @mysql_query("ALTER TABLE {$g_table_prefix}module_export_types ENGINE=MyISAM");
  }

  if ($old_version_info["release_date"] < 20110525)
  {
    mysql_query("ALTER TABLE {$g_table_prefix}module_export_groups ADD form_view_mapping ENUM('all', 'except', 'only') NOT NULL DEFAULT all AFTER access_type");
    mysql_query("ALTER TABLE {$g_table_prefix}module_export_groups ADD forms_and_views MEDIUMTEXT NULL AFTER form_view_mapping");
  }
}


/**
 * The Export Manager uninstall script. This is called by Form Tools when the user explicitly chooses to
 * uninstall the module.
 */
function export_manager__uninstall($module_id)
{
  global $g_table_prefix;

  mysql_query("DROP TABLE {$g_table_prefix}module_export_groups");
  mysql_query("DROP TABLE {$g_table_prefix}module_export_group_clients");
  mysql_query("DROP TABLE {$g_table_prefix}module_export_types");

  return array(true, "");
}
