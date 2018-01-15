<?php
/*------------------------------------------------------------+
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
+-------------------------------------------------------------*/

function civicrm_api3_b_p_donation_revocation($params) {
  if (defined('BETTERPLACE_API_LOGGING') && BETTERPLACE_API_LOGGING) {
    CRM_Core_Error::debug_log_message('BPDonation.revocation: ' . json_encode($params));
  }

  $contribution = civicrm_api3('Contribution', 'get', array(
    'id' => $params['foreign_id'],
    'trxn_id' => $params['donation_id'],
  ));
  if ($contribution['count'] <= 0) {
    return civicrm_api3_create_error('The contribution could not be found.');
  }
  $contribution_data = array(
    'id' => $contribution['id'],
    'contribution_status_id' => 'Refunded',
  );
  if (isset($params['revoked_at'])) {
    if (!is_numeric($params['revoked_at'])) {
      return civicrm_api3_create_error('Parameter "revoked_at" must not be empty.');
    }
    // Convert UTC timestamp to local time.
    $revoked_at_utc = date('YmdHis', $params['revoked_at']);
    $revoked_at_date = date_create($revoked_at_utc, new DateTimeZone('UTC'));
    $params['confirmed_at'] = $revoked_at_date
      ->setTimezone(new DateTimeZone(date_default_timezone_get()))
      ->getTimestamp();
    $contribution_data['cancel_date'] = date('YmdHis', $params['revoked_at']);
  }
  else {
    // Set to now, when not given.
    $contribution_data['cancel_date'] = date('YmdHis');
  }
  $contribution = civicrm_api3('Contribution', 'create', $contribution_data);

  return civicrm_api3_create_success($contribution, $params);
}

function _civicrm_api3_b_p_donation_revocation_spec(&$params) {
  $params['foreign_id'] = array(
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
  $params['revoked_at'] = array(
    'name'         => 'revoked_at',
    'title'        => 'Revoked at',
    'type'         => CRM_Utils_Type::T_INT,
    'api.required' => 0,
    'description'  => 'A timestamp when the donation was cancelled.',
  );
}
