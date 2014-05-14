<?php

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.smart_display_field
 * Type:     function
 * Name:     smart_display_field
 * Purpose:  This function functionally does the same as the Core dispay_custom_field Smarty function,
 *           except that it's designed to display the field values in bulk. To speed things up, it caches
 *           as much info as it can, to reduce DB queries etc.
 * -------------------------------------------------------------
 */
function smarty_function_smart_display_field($params, &$smarty)
{
  global $LANG, $g_multi_val_delimiter, $g_root_url, $g_root_dir;

  if (empty($params["form_id"]))
  {
    $smarty->trigger_error("assign: missing 'form_id' parameter.");
    return;
  }
  if (empty($params["view_id"]))
  {
    $smarty->trigger_error("assign: missing 'view_id' parameter.");
    return;
  }
  if (empty($params["submission_id"]))
  {
    $smarty->trigger_error("assign: missing 'submission_id' parameter.");
    return;
  }
  if (empty($params["field_info"]))
  {
    $smarty->trigger_error("assign: missing 'field_info' parameter.");
    return;
  }
  if (empty($params["field_types"]))
  {
    $smarty->trigger_error("assign: missing 'field_types' parameter.");
    return;
  }

  $form_id       = $params["form_id"];
  $view_id       = $params["view_id"];
  $submission_id = $params["submission_id"];
  $field_info    = $params["field_info"];
  $field_types   = $params["field_types"];
  $settings      = $params["settings"];
  $value         = $params["value"];

  // loop through the field types and store the one we're interested in in $field_type_info
  $field_type_info = array();
  foreach ($field_types as $curr_field_type)
  {
    if ($field_info["field_type_id"] == $curr_field_type["field_type_id"])
    {
      $field_type_info = $curr_field_type;
      break;
    }
  }

  $markup_with_placeholders = trim($field_type_info["view_field_smarty_markup"]);
  $field_settings = $field_info["settings"];

  if (empty($markup_with_placeholders))
  {
    if ($field_info["col_name"] == "submission_id")
      echo $submission_id;
    else
      echo $value;
  }
  else
  {
    // now construct all available placeholders
    $placeholders = array(
      "FORM_ID"       => $form_id,
      "VIEW_ID"       => $view_id,
      "SUBMISSION_ID" => $submission_id,
      "FIELD_ID"      => $field_info["field_id"],
      "NAME"          => $field_info["field_name"],
      "COLNAME"       => $field_info["col_name"],
      "VALUE"         => $value,
      "SETTINGS"      => $settings,
      "CONTEXTPAGE"   => "export",
      "g_root_url"    => $g_root_url,
      "g_root_dir"    => $g_root_dir,
      "g_multi_val_delimiter" => $g_multi_val_delimiter
    );

    // add in all field type settings and their replacements
    foreach ($field_type_info["settings"] as $setting_info)
    {
      $curr_setting_id         = $setting_info["setting_id"];
      $curr_setting_field_type = $setting_info["field_type"];
      $value                   = $setting_info["default_value"];
      $identifier              = $setting_info["field_setting_identifier"];

      if (isset($field_settings) && !empty($field_settings))
      {
        while (list($setting_id, $setting_value) = each($field_settings))
        {
          if ($setting_id == $curr_setting_id)
          {
            $value = $setting_value;
            break;
          }
        }
        reset($field_settings);
      }

      // if this setting type is a dropdown list and $value is non-empty, get the option list
      if ($curr_setting_field_type == "option_list_or_form_field" && !empty($value))
      {
        if (preg_match("/form_field:/", $value))
        {
          $value = ft_get_mapped_form_field_data($value);
        }
        else
        {
          $value = ft_get_option_list($value);
        }
      }
      $placeholders[$identifier] = $value;
    }

    $value = ft_eval_smarty_string($markup_with_placeholders, $placeholders);

    // additional code for CSV encoding
    if (isset($params["escape"]))
    {
      if ($params["escape"] == "csv")
      {
        $value = preg_replace("/\"/", "\"\"", $value);
        if (strstr($value, ","))
          $value = "\"$value\"";
      }
      if ($params["escape"] == "excel")
      {
        $value = preg_replace("/(\n\r|\n)/", "<br style=\"mso-data-placement:same-cell;\" />", $value);
      }
    }

    echo $value;
  }
}

