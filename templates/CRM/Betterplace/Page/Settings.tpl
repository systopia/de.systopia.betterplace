{*------------------------------------------------------------+
| SYSTOPIA betterplace.org Spendenformular Direkt Integration |
| Copyright (C) 2017 SYSTOPIA                                 |
| Author: B. Endres (endres@systopia.de)                      |
|         J. Schuppe (schuppe@systopia.de)                    |
+-------------------------------------------------------------+
| This program is released as free software under the         |
| Affero GPL license. You can redistribute it and/or          |
| modify it under the terms of this license which you         |
| can read by viewing the included agpl.txt or online         |
| at www.gnu.org/licenses/agpl.html. Removal of this          |
| copyright header is strictly prohibited without             |
| written permission from the original author(s).             |
+-------------------------------------------------------------*}

<a href="{crmURL p="civicrm/admin/settings/betterplace/profile" q="op=create"}" title="{ts domain="de.systopia.betterplace"}New profile{/ts}" class="button">
  <span><i class="crm-i fa-plus-circle"></i> {ts domain="de.systopia.betterplace"}New profile{/ts}</span>
</a>
{if !empty($profiles)}
  <table>
    <thead>
    <tr>
      <th>{ts domain="de.systopia.betterplace"}Profile name{/ts}</th>
      <th>{ts domain="de.systopia.betterplace"}Properties{/ts}</th>
      <th>{ts domain="de.systopia.betterplace"}Operations{/ts}</th>
    </tr>
    </thead>
    <tbody>
    {foreach from=$profiles item=profile}
      {assign var="profile_name" value=$profile.name}
      <tr>
        <td>{$profile.name}</td>
        <td>
          <div><strong>{ts domain="de.systopia.betterplace"}Selector{/ts}:</strong> {$profile.selector}</div>
          {* TODO: More properties *}
        </td>
        <td>
          <a href="{crmURL p="civicrm/admin/settings/betterplace/profile" q="op=edit&name=$profile_name"}" title="{ts domain="de.systopia.betterplace" 1=$profile.name}Edit profile %1{/ts}" class="action-item crm-hover-button">{ts domain="de.systopia.betterplace"}Edit{/ts}</a>
          {if $profile_name == 'default'}
            <a href="{crmURL p="civicrm/admin/settings/betterplace/profile" q="op=delete&name=$profile_name"}" title="{ts domain="de.systopia.betterplace" 1=$profile.name}Reset profile %1{/ts}" class="action-item crm-hover-button">{ts domain="de.systopia.betterplace"}Reset{/ts}</a>
          {else}
            <a href="{crmURL p="civicrm/admin/settings/betterplace/profile" q="op=delete&name=$profile_name"}" title="{ts domain="de.systopia.betterplace" 1=$profile.name}Delete profile %1{/ts}" class="action-item crm-hover-button">{ts domain="de.systopia.betterplace"}Delete{/ts}</a>
          {/if}

        </td>
      </tr>
    {/foreach}
    </tbody>
  </table>
{/if}
