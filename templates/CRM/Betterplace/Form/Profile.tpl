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

<div class="crm-block crm-form-block">

  {if $op == 'create' or $op == 'edit'}
    <table class="form-layout-compressed">

      <tr class="crm-section">
        <td class="label">{$form.name.label}</td>
        <td class="content">{$form.name.html}</td>
      </tr>

      <tr class="crm-section">
        <td class="label">{$form.selector.label}</td>
        <td class="content">{$form.selector.html}</td>
      </tr>

      <tr class="crm-section">
        <td class="label">{$form.location_type_id.label}</td>
        <td class="content">{$form.location_type_id.html}</td>
      </tr>

      <tr class="crm-section">
        <td class="label">{$form.financial_type_id.label}</td>
        <td class="content">{$form.financial_type_id.html}</td>
      </tr>

      <tr class="crm-section">
        <td class="label">{$form.campaign_id.label}</td>
        <td class="content">{$form.campaign_id.html}</td>
      </tr>

      <tr class="crm-section">
        <td class="label">{$form.pi_creditcard.label}</td>
        <td class="content">{$form.pi_creditcard.html}</td>
      </tr>

      <tr class="crm-section">
        <td class="label">{$form.pi_paypal.label}</td>
        <td class="content">{$form.pi_paypal.html}</td>
      </tr>

      <tr class="crm-section">
        <td class="label">{$form.pi_sepa.label}</td>
        <td class="content">{$form.pi_sepa.html}</td>
      </tr>

      <tr class="crm-section">
        <td class="label">{$form.groups.label}</td>
        <td class="content">{$form.groups.html}</td>
      </tr>

    </table>
  {elseif $op == 'delete'}
    {if $profile_name}
      {if $profile_name == 'default'}
        <div class="status">{ts domain="de.systopia.betterplace" 1=$profile_name}Are you sure you want to reset the default profile?{/ts}</div>
      {else}
        <div class="status">{ts domain="de.systopia.betterplace" 1=$profile_name}Are you sure you want to delete the profile <em>%1</em>?{/ts}</div>
      {/if}
    {else}
      <div class="crm-error">{ts domain="de.systopia.betterplace"}Profile name not given or invalid.{/ts}</div>
    {/if}
  {/if}

  {* FOOTER *}
  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
  </div>

</div>
