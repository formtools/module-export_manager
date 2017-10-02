<?php

require_once("../../global/library.php");

use FormTools\Modules;

$module = Modules::initModulePage("admin");
$L = $module->getLangString();

$success = true;
$message = "";
if (isset($request["reset"])) {
    //list($success, $message) = exp_insert_default_data();
}

$page_vars = array(
    "head_title" => "{$L["module_name"]} - {$L["phrase_reset_defaults"]}"
);

$module->displayPage("templates/reset.tpl", $page_vars);
