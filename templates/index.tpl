{include file='modules_header.tpl'}

  <table cellpadding="0" cellspacing="0">
  <tr>
    <td width="45"><img src="images/icon_export.gif" width="34" height="34" /></td>
    <td class="title">{$L.module_name|upper}</td>
  </tr>
  </table>

  {include file='messages.tpl'}

  <div class="margin_bottom_large">
    {$L.text_export_manager_intro}
  </div>

  {if $export_groups|@count == 0}

		<div class="notify yellow_bg" class="margin_bottom_large">
			<div style="padding:8px">
				{$L.notify_no_export_groups}
		  </div>
	  </div>

	{else}

    <form action="{$samepage}" method="post">

		  <table class="list_table" style="width:100%" cellpadding="1" cellspacing="1">
		  <tr style="height: 20px;">
		    <th width="40"><input type="submit" name="reorder_export_groups" value="{$LANG.word_order|escape}" /></th>
		    <th>{$L.phrase_export_group}</th>
		    <th width="60">{$L.word_icon}</th>
		    <th>{$L.word_visibility}</th>
		    <th>{$L.phrase_num_export_types}</th>
		    <th width="60">{$LANG.word_edit|upper}</th>
		    <th width="60" class="del">{$LANG.word_delete|upper}</th>
		  </tr>

	    {foreach from=$export_groups item=group name=row}
	      {assign var='index' value=$smarty.foreach.row.index}
	      {assign var='count' value=$smarty.foreach.row.iteration}
	      {assign var='export_group_id' value=$group.export_group_id}

	 	    <tr>
	 	      <td align="center"><input type="text" name="group_{$export_group_id}_order" value="{$group.list_order}" style="width: 30px" /></td>
	 	    	<td class="pad_left_small">{eval var=$group.group_name}</td>
	 	    	<td align="center">{if $group.icon}<img src="images/icons/{$group.icon}" />{/if}</td>
	 	    	<td align="center">
            {if $group.visibility == "show"}
              <span class="green">{$LANG.word_show}</span>
            {else}
              <span class="red">{$LANG.word_hide}</span>
            {/if}
          </td>
	 	    	<td align="center"><a href="export_groups/edit.php?page=export_types&export_group_id={$export_group_id}">{$group.num_export_types}</a></td>
					<td align="center"><a href="export_groups/edit.php?page=main&export_group_id={$export_group_id}">{$LANG.word_edit|upper}</a></td>
					<td class="del"><a href="#" onclick="return page.delete_export_group({$export_group_id})">{$LANG.word_delete|upper}</a></td>
		    </tr>

	    {/foreach}
		  </table>

    </form>

  {/if}

  <form action="export_groups/add.php" method="post">
		<p>
		  <input type="submit" value="{$L.phrase_add_export_group|upper}" />
		</p>
  </form>

{include file='modules_footer.tpl'}