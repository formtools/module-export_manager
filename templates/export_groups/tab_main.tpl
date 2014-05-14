
  {include file='messages.tpl'}

  <form action="{$samepage}" method="post" onsubmit="return rsv.validate(this, rules)">
    <input type="hidden" name="export_group_id" value="{$export_group_info.export_group_id}" />

	  <table cellspacing="1" cellpadding="2" border="0" width="100%">
	  <tr>
	    <td width="130" class="medium_grey">{$L.phrase_export_group_name}</td>
	    <td>
	      <input type="text" name="group_name" value="{$export_group_info.group_name|escape}" style="width:300px" maxlength="255" />
	    </td>
	  </tr>
	  <tr>
	    <td class="medium_grey">{$L.word_visibility}</td>
	    <td>
	      <input type="radio" name="visibility" value="show" id="st1" {if $export_group_info.visibility == "show"}checked{/if} />
	        <label for="st1" class="green">{$LANG.word_show}</label>
	      <input type="radio" name="visibility" value="hide" id="st2" {if $export_group_info.visibility == "hide"}checked{/if} />
	        <label for="st2" class="red">{$LANG.word_hide}</label>
	    </td>
	  </tr>
    <tr>
      <td class="medium_grey" valign="top">{$LANG.phrase_access_type}</td>
      <td>

        <table cellspacing="1" cellpadding="0" >
        <tr>
          <td>
		        <input type="radio" name="access_type" id="at1" value="admin" {if $export_group_info.access_type == 'admin'}checked{/if}
		          onfocus="page_ns.toggle_form_type(this.value)" />
		          <label for="at1">{$LANG.phrase_admin_only}</label>
		      </td>
		    </tr>
		    <tr>
		      <td>
		        <input type="radio" name="access_type" id="at2" value="public" {if $export_group_info.access_type == 'public'}checked{/if}
		          onfocus="page_ns.toggle_form_type(this.value)" />
		          <label for="at2">{$LANG.word_public} <span class="light_grey">(all clients have access)</span></label>
		      </td>
		    </tr>
		    <tr>
		      <td>
		        <input type="radio" name="access_type" id="at3" value="private" {if $export_group_info.access_type == 'private'}checked{/if}
		          onfocus="page_ns.toggle_form_type(this.value)" />
		          <label for="at3">{$LANG.word_private} <span class="light_grey">(only specific clients have access)</span></label>
		      </td>
		    </tr>
		    </table>

				<div id="custom_clients" {if $export_group_info.access_type != 'private'}style="display:none"{/if} class="margin_top">
	        <table cellpadding="0" cellspacing="0" class="subpanel">
	        <tr>
	          <td class="medium_grey">{$LANG.phrase_available_clients}</td>
	          <td></td>
	          <td class="medium_grey">{$LANG.phrase_selected_clients}</td>
	        </tr>
	        <tr>
	          <td>
	            {clients_dropdown name_id="available_client_ids[]" multiple="true" multiple_action="hide"
	              clients=$export_group_info.client_ids size="4" style="width: 140px"}
	          </td>
	          <td align="center" valign="middle" width="100">
	            <input type="button" value="{$LANG.word_add_uc_rightarrow}"
	              onclick="ft.move_options(this.form['available_client_ids[]'], this.form['selected_client_ids[]']);" /><br />
	            <input type="button" value="{$LANG.word_remove_uc_leftarrow}"
	              onclick="ft.move_options(this.form['selected_client_ids[]'], this.form['available_client_ids[]']);" />
	          </td>
	          <td>
	            {clients_dropdown name_id="selected_client_ids[]" multiple="true" multiple_action="show"
	              clients=$export_group_info.client_ids size="4" style="width: 140px"}
	          </td>
	        </tr>
	        </table>
	      </div>

      </td>
    </tr>
	  <tr>
	    <td valign="top" class="medium_grey">{$L.word_icon}</td>
	    <td>

	      <span class="pad_right_large">
	        <input type="radio" name="icon" id="icon_0" value="" {if $export_group_info.icon == ""}checked{/if}/>
	        <label for="icon_0">{$L.word_none}</label>
	      </span> |

	      {foreach from=$icons item=icon name=i}
	        {assign var=index value=$smarty.foreach.i.iteration}

	        {if $index % 5 == 0}<br />{/if}

		      <span class="pad_right_large">
		        <input type="radio" name="icon" id="icon_{$index}" value="{$icon}" {if $export_group_info.icon == $icon}checked{/if} />
		        <label for="icon_{$index}"><img src="{$g_root_url}/modules/export_manager/images/icons/{$icon}" /></label>
		      </span> |
        {/foreach}

	    </td>
	  </tr>
	  <tr>
	    <td valign="top" class="medium_grey">{$L.word_action}</td>
	    <td>
	      <div>
	        <input type="radio" name="action" value="file" id="action1" {if $export_group_info.action == "file"}checked{/if}
	          onclick="page_ns.change_action_type(this.value)" />
	          <label for="action1">{$L.phrase_generate_file}</label>
	      </div>
	      <div>
	        <input type="radio" name="action" value="new_window" id="action2" {if $export_group_info.action == "new_window"}checked{/if}
	          onclick="page_ns.change_action_type(this.value)" />
	          <label for="action2">{$L.phrase_open_in_new_window}</label>
	      </div>
	      <div>
	        <input type="radio" name="action" value="popup" id="action3" {if $export_group_info.action == "popup"}checked{/if}
	          onclick="page_ns.change_action_type(this.value)" />
	          <label for="action3">{$L.phrase_display_popup}</label>

            <table cellspacing="0" cellpadding="1" style="margin-left: 40px;">
            <tr>
              <td class="pad_right medium_grey">&#8212; Height:</td>
              <td><input type="text" name="popup_height" value="{$export_group_info.popup_height}" style="width:50px" />px</td>
            </tr>
            <tr>
              <td class="pad_right medium_grey">&#8212; Width:</td>
              <td><input type="text" name="popup_width" value="{$export_group_info.popup_width}" style="width:50px" />px</td>
            </tr>
            </table>
        </div>
	    </td>
	  </tr>
	  <tr>
	    <td class="medium_grey">{$L.phrase_action_button_text}</td>
	    <td>
	      <input type="text" name="action_button_text" maxlength="100" style="width:300px" value="{$export_group_info.action_button_text|escape}" />
	    </td>
	  </tr>
	  <tr>
	    <td valign="top" class="medium_grey">{$L.word_headers}</td>
	    <td>
        
        <div style="border: 1px solid #666666; padding: 3px">
          <textarea style="width:100%; height: 80px;" name="headers" id="headers"
           {if $export_group_info.action == "file"}disabled{/if}>{$export_group_info.headers}</textarea>
        </div>
        
        <script type="text/javascript">
        var html_editor = new CodeMirror.fromTextArea("headers", {literal}{{/literal}
        parserfile: ["parsexml.js"],
        path: "{$g_root_url}/global/codemirror/js/",
        stylesheet: "{$g_root_url}/global/codemirror/css/xmlcolors.css"
        {literal}});{/literal}
        </script>

      </td>
    </tr>
	  <tr>
	    <td valign="top" class="medium_grey">{$L.phrase_smarty_template}</td>
	    <td>
        <div style="border: 1px solid #666666; padding: 3px">
          <textarea style="width:100%; height: 220px;" name="smarty_template" id="smarty_template">{$export_group_info.smarty_template}</textarea>
        </div>

        <script type="text/javascript">
        var html_editor = new CodeMirror.fromTextArea("smarty_template", {literal}{{/literal}
        parserfile: ["parsexml.js"],
        path: "{$g_root_url}/global/codemirror/js/",
        stylesheet: "{$g_root_url}/global/codemirror/css/xmlcolors.css"
        {literal}});{/literal}
        </script>
      </td>
    </tr>
	  </table>

	  <p>
	    <input type="submit" name="update_export_group" value="{$LANG.word_update|upper}" />
	  </p>

  </form>