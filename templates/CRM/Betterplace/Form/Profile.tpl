{*-------------------------------------------------------+
| SYSTOPIA Betterplace Integration                       |
| Copyright (C) 2017 SYSTOPIA                            |
| Author: B. Endres (endres@systopia.de)                 |
|         J. Schuppe (schuppe@systopia.de)               |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+-------------------------------------------------------*}

<div>
  <div class="crm-section">
    <div class="label">{$form.name.label}</div>
    <div class="content">{$form.name.html}</div>
    <div class="clear"></div>
  </div>

  <div class="crm-section">
    <div class="label">{$form.selector.label}</div>
    <div class="content">{$form.selector.html}</div>
    <div class="clear"></div>
  </div>

  <div class="crm-section">
    <div class="label">{$form.financial_type_id.label}</div>
    <div class="content">{$form.financial_type_id.html}</div>
    <div class="clear"></div>
  </div>

  <div class="crm-section">
    <div class="label">{$form.campaign_id.label}</div>
    <div class="content">{$form.campaign_id.html}</div>
    <div class="clear"></div>
  </div>

  <div class="crm-section">
    <div class="label">{$form.pi_creditcard.label}</div>
    <div class="content">{$form.pi_creditcard.html}</div>
    <div class="clear"></div>
  </div>

  <div class="crm-section">
    <div class="label">{$form.pi_paypal.label}</div>
    <div class="content">{$form.pi_paypal.html}</div>
    <div class="clear"></div>
  </div>

  <div class="crm-section">
    <div class="label">{$form.pi_sepa.label}</div>
    <div class="content">{$form.pi_sepa.html}</div>
    <div class="clear"></div>
  </div>

  <div class="crm-section">
    <div class="label">{$form.groups.label}</div>
    <div class="content">{$form.groups.html}</div>
    <div class="clear"></div>
  </div>

</div>

{* FOOTER *}
<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
