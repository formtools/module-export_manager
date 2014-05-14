{include file='modules_header.tpl'}

  <div class="title margin_bottom_large">{$L.phrase_add_export_group|upper}</div>

  <div class="margin_bottom_large">
    {$L.text_export_group_summary}
  </div>

  {include file='messages.tpl'}

  <form action="../" method="post" onsubmit="return rsv.validate(this, rules)">

    <table cellspacing="1" cellpadding="2" border="0" width="500">
    <tr>
      <td width="130" class="medium_grey">{$L.phrase_export_group_name}</td>
      <td>
        <input type="text" name="group_name" value="" style="width:200px" maxlength="50" />
      </td>
    </tr>
    <tr>
      <td class="medium_grey">{$L.word_visibility}</td>
      <td>
        <input type="radio" name="visibility" value="show" id="st1" checked />
          <label for="st1" class="green">{$LANG.word_show}</label>
        <input type="radio" name="visibility" value="hide" id="st2" />
          <label for="st2" class="red">{$LANG.word_hide}</label>
      </td>
    </tr>
    <tr>
      <td valign="top" class="medium_grey">{$L.word_icon}</td>
      <td>

        <span class="pad_right_large">
          <input type="radio" name="icon" id="icon_0" value="" checked />
          <label for="icon_0">{$L.word_none}</label>
        </span> |

        {foreach from=$icons item=icon name=i}
          {assign var=index value=$smarty.foreach.i.iteration}

          {if $index % 5 == 0}<br />{/if}

          <span class="pad_right_large">
            <input type="radio" name="icon" id="icon_{$index}" value="{$icon}" />
            <label for="icon_{$index}"><img src="{$g_root_url}/modules/export_manager/images/icons/{$icon}" /></label>
          </span> |
        {/foreach}

      </td>
    </tr>
    </table>

    <p>
      <input type="submit" name="add_export_group" value="{$L.phrase_add_export_group}" />
    </p>

  </form>

{include file='modules_footer.tpl'}