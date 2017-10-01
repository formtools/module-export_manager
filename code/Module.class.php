<?php


namespace FormTools\Modules\ExportManager;

use FormTools\Core;
use FormTools\Hooks;
use FormTools\Module as FormToolsModule;


class Module extends FormToolsModule
{
    protected $moduleName = "Export Manager";
    protected $moduleDesc = "";
    protected $author = "Ben Keen";
    protected $authorEmail = "ben.keen@gmail.com";
    protected $authorLink = "http://formtools.org";
    protected $version = "3.0.0";
    protected $date = "2017-09-30";
    protected $originLanguage = "en_us";
    protected $jsFiles = array(
    );

    protected $cssFiles = array("css/styles.css");

    protected $nav = array(
        "module_name"              => array("index.php", false),
        "word_settings"            => array("settings.php", true),
        "phrase_reset_defaults"    => array("reset.php", true),
        "word_help"                => array("help.php", true)
    );

    public function install($module_id)
    {
        $db = Core::$db;
        $LANG = Core::$L;

        $queries = array();
        $word_display = addcslashes($LANG["word_display"], "''");
        $queries[] = "
            CREATE TABLE {PREFIX}module_export_groups (
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
            CREATE TABLE {PREFIX}module_export_group_clients (
            export_group_id mediumint(8) unsigned NOT NULL,
            account_id mediumint(8) unsigned NOT NULL,
            PRIMARY KEY  (export_group_id, account_id)
            ) DEFAULT CHARSET=utf8
        ";

        $queries[] = "
            CREATE TABLE {PREFIX}module_export_types (
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

        //$db->beginTransaction();

        foreach ($queries as $query) {
            $db->query($query);
            $db->execute();

            //exp_remove_tables();
        }


        //return array(false, $LANG["export_manager"]["notify_installation_problem_c"] . " <b>" . mysql_error() . "</b>");


        // now populate the tables
        list ($success, $message) = exp_insert_default_data();
        if (!$success) {
            exp_remove_tables();
            exp_clear_table_data();
            return array(false, $message);
        }

        Hooks::registerHook("template", "export_manager", "admin_submission_listings_bottom", "", "exp_display_export_options");
        Hooks::registerHook("template", "export_manager", "client_submission_listings_bottom", "", "exp_display_export_options");

        return array(true, "");
    }

    public function uninstall($module_id)
    {
        $db = Core::$db;

        $db->query("DROP TABLE {PREFIX}module_export_groups");
        $db->execute();

        $db->query("DROP TABLE {PREFIX}module_export_group_clients");
        $db->execute();

        $db->query("DROP TABLE {PREFIX}module_export_types");
        $db->execute();

        return array(true, "");
    }
}
