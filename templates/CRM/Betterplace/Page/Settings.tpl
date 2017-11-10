{if !empty($profiles)}
  <table>
    <thead>
    <tr>
      <th>{ts}Profile name{/ts}</th>
      <th>{ts}Operations{/ts}</th>
    </tr>
    </thead>
    <tbody>
    {foreach from=$profiles item=profile}
      <tr>
        <td>{$profile}</td>
        <td><a href="/civicrm/admin/settings/betterplace/profile?name={$profile}" title="{ts 1=$profile}Edit profile %1{/ts}">{ts}Edit{/ts}</a></td>
      </tr>
    {/foreach}
    </tbody>
  </table>
{/if}
