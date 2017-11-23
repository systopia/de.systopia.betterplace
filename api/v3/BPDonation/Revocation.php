<?php
/*-------------------------------------------------------+
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
+--------------------------------------------------------*/

function civicrm_api3_b_p_donation_revocation($params) {
  $contribution = civicrm_api3('Contribution', 'get', array(
    'id' => $params['contribution_id'],
    'trxn_id' => $params['donation_id'],
  ));
  if ($contribution['count'] <= 0) {
    return civicrm_api3_create_error('The contribution could not be found.');
  }
  $contribution_data = array(
    'id' => $contribution['id'],
    'contribution_status_id' => 'Refunded',
    'trxn_id' => $params['donation_id'],
  );
  if (isset($params['time'])) {
    if (!is_numeric($params['time'])) {
      return civicrm_api3_create_error('Parameter "revoked_at" must not be empty.');
    }
    $contribution_data['cancel_date'] = date('YmdHis', $params['time']);
  }
  $contribution = civicrm_api3('Contribution', 'create', $contribution_data);

  return civicrm_api3_create_success($contribution, $params);
}

function _civicrm_api3_b_p_donation_revocation_spec(&$params) {
  $params['contribution_id'] = array(
    'name' => 'foreign_id',
    'title' => 'CiviCRM contribution ID',
    'type' => CRM_Utils_Type::T_INT,
    'api.required' => 1,
    'description' => 'ID of the CiviCRM Contribution entity',
  );
  $params['form_id'] = array(
    'name'         => 'form_id',
    'title'        => 'betterplace.org Direkt form ID',
    'type'         => CRM_Utils_Type::T_STRING,
    'api.required' => 0,
    'description'  => 'ID of the betterplace.org Direkt form.',
  );
  $params['donation_id'] = array(
    'name'         => 'donation_id',
    'title'        => 'Donation ID',
    'type'         => CRM_Utils_Type::T_STRING,
    'api.required' => 1,
    'description'  => 'The donation ID.',
  );
  $params['time'] = array(
    'name'         => 'revoked_at',
    'title'        => 'Revoked at',
    'type'         => CRM_Utils_Type::T_INT,
    'api.required' => 0,
    'description'  => 'A timestamp when the donation was cancelled.',
  );
}
