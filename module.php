<?php

/*
 * Module file: Export Manager
 *
 * This module allows administrators to define their own custom export format types. For example,
 * XML or CSV (included by default). Basically, it just lets them define their own Smarty templates
 * for presenting the data in whatever form they wish.
 */

$MODULE["author"]          = "Encore Web Studios";
$MODULE["author_email"]    = "formtools@encorewebstudios.com";
$MODULE["author_link"]     = "http://www.encorewebstudios.com";
$MODULE["version"]         = "1.0.0-beta-20090102";
$MODULE["date"]            = "2009-01-02";
$MODULE["origin_language"] = "en_us";
$MODULE["supports_ft_versions"] = "2.0.0";

// define the module navigation - the keys are keys defined in the language file. This lets
// the navigation - like everything else - be customized to the users language. The paths are always built
// relative to the module's root, so help/index.php means: /[form tools root]/modules/export_manager/help/index.php
$MODULE["nav"] = array(
  "module_name"              => array('{$module_dir}/index.php', false),
  "word_settings"            => array('{$module_dir}/settings/', false),
  "word_help"                => array('{$module_dir}/help/', false)
    );