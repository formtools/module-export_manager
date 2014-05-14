  {include file='messages.tpl'}


  {if $export_types|@count == 0}

		<div class="notify yellow_bg" class="margin_bottom_large">
			<div style="padding:8px">
				{$L.notify_no_export_types}
		  </div>
	  </div>

  {else}

	  <form action="{$same_page}" method="post">
	    <input type="hidden" name="export_group_id" value="{$export_group_info.export_group_id}" />

		  <table class="list_table" style="width:100%" cellpadding="1" cellspacing="1">
		  <tr style="height: 20px;">
			  <th width="40"><input type="submit" name="reorder_export_types" value="{$LANG.word_order|escape}" /></th>
			  <th width="30" class="nowrap pad_left pad_right">{$LANG.word_id|upper}</th>
		    <th>{$L.phrase_export_type}</th>
		    <th>{$L.word_visibility}</th>
		    <th width="60">{$LANG.word_edit|upper}</th>
		    <th width="60" class="del">{$LANG.word_delete|upper}</th>
		  </tr>

	    {foreach from=$export_types item=export_type name=row}
	      {assign var='index' value=$smarty.foreach.row.index}
	      {assign var='count' value=$smarty.foreach.row.iteration}
	      {assign var='export_type_id' value=$export_type.export_type_id}

	 	    <tr>
		      <td align="center"><input type="text" name="export_type_{$export_type_id}_order" value="{$export_type.export_type_list_order}" style="width: 30px" /></td>
		      <td align="center" class="medium_grey">{$export_type_id}</td>
	 	    	<td class="pad_left_small">{eval var=$export_type.export_type_name}</td>
	 	    	<td align="center">
	 	    	  {if $export_type.export_type_visibility == "show"}
              <span class="green">{$LANG.word_show}</span>
            {else}
              <span class="red">{$LANG.word_hide}</span>
            {/if}
	 	    	</td>
					<td align="center"><a href="edit.php?page=edit_export_type&export_type_id={$export_type_id}">{$LANG.word_edit|upper}</a></td>
					<td class="del"><a href="#" onclick="return page_ns.delete_export_type({$export_type_id})">{$LANG.word_delete|upper}</a></td>
		    </tr>
	    {/foreach}
		  </table>

	  </form>

  {/if}

  <form action="{$same_page}" method="post">
    <input type="hidden" name="page" value="add_export_type" />

	  <p>
	    <input type="submit" value="{$L.phrase_add_export_type|upper}" />
	  </p>
	</form>