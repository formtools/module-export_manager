{*
  This template generates the HTML for the various export options at the foot of the submission
  listing page, for both the administrator and clients.

  1. popup
  2. new window (form: target="_blank")
  3. generate file
*}

  <script type="text/javascript" src="{$modules_dir}/export_manager/global/scripts/export_manager.js"></script>
  <script type="text/javascript">
  if (typeof em == 'undefined')
    em = {literal}{}{/literal};

  em.export_page = "{$modules_dir}/export_manager/export.php";
  g.messages["validation_select_rows_to_export"] = "{$LANG.export_manager.validation_select_rows_to_export}";
  </script>

  {if $export_groups|@count > 0}
    <hr size="1" align="left" style="width: 100%;" />

    <form action="{$modules_dir}/export_manager/export.php" id="export_manager_form" method="post">
      <input type="hidden" name="export_group_id" id="export_group_id" value="" />
      <input type="hidden" name="export_type_id" id="export_type_id" value="" />

		  <table cellpadding="0" cellpadding="1">
		  {foreach from=$export_groups item=export_group name=row}
        {assign var=export_group_id value=$export_group.export_group_id}
			  <tr>
			    <td class="pad_right_large"><img src="{$export_icon_folder}/{$export_group.icon}"/></td>
			    <td class="pad_right_large">{eval var=$export_group.group_name}</td>
			    <td>
			      {assign var=var_name value="export_group_`$export_group_id`_results"}
			      <select name="export_group_{$export_group_id}_results" id="export_group_{$export_group_id}_results">
			        <option value="all"      {if $page_vars.$var_name == "all"}selected{/if}>{$LANG.word_all}</option>
			        <option value="selected" {if $page_vars.$var_name == "selected"}selected{/if}>{$LANG.word_selected}</option>
			      </select>
			    </td>
			    <td>

            {* if this is a popup type, store the size of the popup in JS for use by the em.export function *}
            {if $export_group.action == "popup"}
						  <script type="text/javascript">
						  em.export_group_id_{$export_group_id}_height = {$export_group.popup_height};
						  em.export_group_id_{$export_group_id}_width  = {$export_group.popup_width};
						  </script>
            {/if}

			      <table cellspacing="0" cellpadding="0">
			      <tr>
			        {* if there's more than 1 export type in this export group, display the list. Otherwise, don't show
		             the single item: in that case it's implicit that that's what the user wants to see *}
		          {if $export_group.export_types|@count > 1}
		            {assign var=var_name value="export_group_`$export_group_id`_export_type"}
		            <td>
		              <select name="export_group_{$export_group_id}_export_type" id="export_group_{$export_group_id}_export_type">
		              {foreach from=$export_group.export_types item=export_type name=row}
		                <option value="{$export_type.export_type_id}" {if $page_vars.$var_name == $export_type.export_type_id}selected{/if}>{eval var=$export_type.export_type_name}</option>
		              {/foreach}
		              </select>
		            </td>
		          {/if}
		          <td>
					      <input type="button" name="export_group_{$export_group_id}" value="{eval var=$export_group.action_button_text}"
					        onclick="em.export_submissions({$export_group_id}, '{$export_group.action}')" />
					    </td>
					  </tr>
					  </table>

			    </td>
			  </tr>
		  {/foreach}
		  </table>

    </form>

  {/if}
