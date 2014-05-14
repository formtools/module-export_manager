/**
 *
 */

// Our Export Manager namespace
if (typeof em == 'undefined')
  em = {};


/**
 * This function is called whenever the user clicks on one of the "Display" / "Generate" (or whatever they've been
 * renamed to) buttons in the Export Manager section at the bottom of the submission listing page. It performs the
 * appropriate action to display / generate the content
 *
 * @param integer export_group_id
 * @param string action "popup", "new_window", "file"
 */
em.export_submissions = function(export_group_id, action)
{
  var result_type;
  if ($("export_group_" + export_group_id + "_results"))
    result_type = $("export_group_" + export_group_id + "_results").value; // "all" / "selected"

  // if the user is only requesting to export the selected rows, check there's at least one selected
  if (result_type == "selected")
  {
    var num_selected = ms.update_display_row_count();

    if (num_selected == 0)
    {
      ft.display_message("ft_message", false, g.messages["validation_select_rows_to_export"]);
      return;
    }
  }

  switch (action)
  {
    case "popup":
      var height = em["export_group_id_" + export_group_id + "_height"];
      var width  = em["export_group_id_" + export_group_id + "_width"];

      var url = em.export_page + "?export_group_id=" + export_group_id + "&export_group_" + export_group_id + "_results=" + result_type;

      if ($("export_group_" + export_group_id + "_export_type"))
        url += "&export_type_id=" + $("export_group_" + export_group_id + "_export_type").value;

      window.open(url, "export_popup", "resizable=yes,scrollbars=yes,width=" + width + ",height=" + height);
      break;

    case "new_window":
      $("export_manager_form").target = "_blank";
      $("export_group_id").value = export_group_id;

      if ($("export_group_" + export_group_id + "_export_type"))
        $("export_type_id").value = $("export_group_" + export_group_id + "_export_type").value;

      $("export_manager_form").submit();
      break;

    case "file":
      var url = em.export_page + "?export_group_id=" + export_group_id +
        "&export_group_" + export_group_id + "_results=" + result_type + "&target_message_id=ft_message";

		 	new Ajax.Request(url, {
			  method: "post",
			  onSuccess: ft.response_handler,
			  onFailure: function() { alert("Couldn't load page: " + page_url); }
			});
			break;
  }
}
