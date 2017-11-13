<a href="/civicrm/admin/settings/betterplace/profile?new=1" title="{ts}New profile{/ts}" class="button">
  <span><i class="crm-i fa-plus-circle"></i> {ts}New profile{/ts}</span>
</a>
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
        <td><a href="/civicrm/admin/settings/betterplace/profile?name={$profile}" title="{ts 1=$profile}Edit profile %1{/ts}" class="action-item crm-hover-button">{ts}Edit{/ts}</a></td>
      </tr>
    {/foreach}
    </tbody>
  </table>
{/if}
