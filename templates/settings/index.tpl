{include file='modules_header.tpl'}

	<div class="title">{$L.word_settings|upper}</div>

    {include file='messages.tpl'}

    <form action="{$same_page}" method="post">

	  <table cellpadding="0" cellspacing="1" class="list_table" width="100%">
	  <tr>
	    <td width="170" class="nowrap pad_left pad_right_large">Generated files folder path</td>
	    <td>

	      <table cellpadding="0" cellspacing="0" width="100%">
	      <tr>
	        <td><input type="text" name="file_upload_dir" id="file_upload_dir" value="{$module_settings.file_upload_dir}" style="width: 98%" /></td>
	        <td width="150">
	          <input type="button" value="{$LANG.phrase_test_folder_permissions}" onclick="ft.test_folder_permissions($('file_upload_dir').value, 'permissions_result')" style="width: 150px;" />
	        </td>
	      </tr>
	      </table>

	      <div id="permissions_result"></div>

	    </td>
	  </tr>
	  <tr>
	    <td class="pad_left">Generated files folder URL</td>
	    <td>

	      <table cellpadding="0" cellspacing="0" width="100%">
	      <tr>
	        <td><input type="text" name="file_upload_url" id="file_upload_url" value="{$module_settings.file_upload_url}" style="width: 98%" /></td>

	        {if $allow_url_fopen}
	          <td width='150'><input type="button" value="{$LANG.phrase_confirm_folder_url_match}" onclick="ft.test_folder_url_match($('file_upload_dir').value, $('file_upload_url').value, 'folder_match_message_id')" style="width: 150px;" /></td>
	        {/if}

	      </tr>
	      </table>

	      <div id="folder_match_message_id"></div>

	    </td>
	  </tr>
    </table>

    <p>
      <input type="submit" name="update" value="{$LANG.word_update}" />
    </p>

  </form>

{include file='modules_footer.tpl'}