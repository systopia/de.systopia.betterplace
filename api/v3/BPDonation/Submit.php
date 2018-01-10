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

function civicrm_api3_b_p_donation_submit($params) {
  try {
    // Move data parameters to top-level.
    if (!empty($params['data'])) {
      if (($data = json_decode($params['data'])) !== NULL) {
        $params['data'] = (array) $data;
      }
      $params += $params['data'];
      unset($params['data']);
    }

    // Prepare parameter mapping for address.
    foreach (array(
               'street' => 'street_address',
               'zip' => 'postal_code',
               'city' => 'city',
               'country' => 'country',
             ) as $address_param => $address_component) {
      if (!empty($params[$address_param])) {
        $params[$address_component] = $params[$address_param];
        unset($params[$address_param]);
      }
    }

    // Prepare parameter mapping for organisation.
    if (!empty($params['company_name'])) {
      $params['organization_name'] = $params['company_name'];
      unset($params['company_name']);
    }

    // Remove parameter "id".
    if (!empty($params['id'])) {
      unset($params['id']);
    }

    // Call API action when not "submit" or "new_donation".
    if (!empty($params['type']) && $params['type'] != 'submit' && $params['type'] != 'new_donation') {
      $type = $params['type'];
      unset($params['type']);
      return civicrm_api3('BPDonation', $type, $params);
    }

    // Check for required parameters. We can not use the API specification for
    // required, as that is different per API action.
    $required_submit_params = array(
      'form_id',
      'email',
      'amount_in_cents',
      'payment_method',
    );
    foreach ($required_submit_params as $required_submit_param) {
      if (empty($params[$required_submit_param])) {
        throw new CiviCRM_API3_Exception('Parameter ' . $required_submit_param . ' is required.', 'mandatory_missing');
      }
    }

    // Get the profile defined for the given form ID, or the default profile if
    // none matches.
    $profile = CRM_Betterplace_Profile::getProfileForForm($params['form_id']);

    // Exclude address for now when retrieving/creating the individual contact
    // and an organisation is given, as we are checking organisation address
    // first and share it with the individual.
    if (!empty($params['organization_name'])) {
      $submitted_address = array();
      foreach (array(
                 'street_address',
                 'postal_code',
                 'city',
                 'country'
               ) as $address_component) {
        if (!empty($params[$address_component])) {
          $submitted_address[$address_component] = $params[$address_component];
          unset($params[$address_component]);
        }
      }
    }

    // Get the ID of the contact matching the given contact data, or create a
    // new contact if none exists for the given contact data.
    if (!$contact_id = CRM_Betterplace_Submission::getContact('Individual', $params)) {
      throw new CiviCRM_API3_Exception('Individual contact could not be found or created.', 'invalid_format');
    }

    // Organisation lookup.
    if (!empty($params['organization_name'])) {
      if (!empty($submitted_address)) {
        $params += $submitted_address;
      }
      if (!$organisation_id = CRM_Betterplace_Submission::getContact('Organization', $params)) {
        throw new CiviCRM_API3_Exception('Organisation contact could not be found or created.', 'invalid_format');
      }
    }
    $address_shared = isset($organisation_id) && CRM_Betterplace_Submission::shareWorkAddress($contact_id, $organisation_id, $profile->getAttribute('location_type_id'));

    // Address is not shared, use submitted address.
    if (!$address_shared && !empty($submitted_address)) {
      $submitted_address['contact_id'] = $contact_id;
      $submitted_address['location_type_id'] = $profile->getAttribute('location_type_id');
      civicrm_api3('Address', 'create', $submitted_address);
    }

    // Get the payment instrument defined within the profile, or return an error
    // if none matches (i.e. an unknown payment method was submitted).
    if (!$payment_instrument_id = $profile->getAttribute('pi_' . $params['payment_method'])) {
      throw new CiviCRM_API3_Exception('Payment method could not be matched to existing payment instrument.', 'invalid_format');
    }

    // Create contribution.
    $contribution_data = array(
      'financial_type_id' => $profile->getAttribute('financial_type_id'),
      'contact_id' => $contact_id,
      'payment_instrument_id' => $payment_instrument_id,
      'total_amount' => $params['amount_in_cents'] / 100,
      'contribution_status_id' => 'Completed',
    );
    if (!empty($params['donation_id'])) {
      $contribution_data['trxn_id'] = $params['donation_id'];
    }
    if (isset($params['confirmed_at'])) {
      if (!is_numeric($params['confirmed_at'])) {
        throw new CiviCRM_API3_Exception('Parameter "confirmed_at" must not be empty.', 'mandatory_missing');
      }
      // Convert UTC timestamp to local time.
      $confirmed_at_utc = date('YmdHis', $params['confirmed_at']);
      $confirmed_at_date = date_create($confirmed_at_utc, new DateTimeZone('UTC'));
      $params['confirmed_at'] = $confirmed_at_date
        ->setTimezone(new DateTimeZone(date_default_timezone_get()))
        ->getTimestamp();
      $contribution_data['receive_date'] = date('YmdHis', $params['confirmed_at']);
    }
    // Add campaign relationship if defined in the profile.
    if (!empty($campaign_id = $profile->getAttribute('campaign_id'))) {
      $contribution_data['campaign_id'] = $campaign_id;
    }
    $contribution = civicrm_api3('Contribution', 'create', $contribution_data);

    // If requested, add contact to the groups defined in the profile.
    if (!empty($params['newsletter']) && !empty($groups = $profile->getAttribute('groups'))) {
      foreach ($groups as $group_id) {
        civicrm_api3('GroupContact', 'create', array(
          'group_id' => $group_id,
          'contact_id' => $contact_id,
        ));
      }
    }

    return civicrm_api3_create_success($contribution, $params, NULL, NULL, $foo = NULL, array(
      'foreign_id' => $contribution['id'],
    ));
  }
  catch (CiviCRM_API3_Exception $exception) {
    CRM_Core_Error::debug_log_message('BPDonation:submit:Exception caught: ' . $exception->getMessage());

    $extraParams = $exception->getExtraParams();

    // Rollback current base transaction in order to not rollback the creation
    // of the activity.
    \Civi\Core\Transaction\Manager::singleton()->getBaseFrame()->forceRollback();
    try {
      // Create an activity of type "Failed contribution processing" and assign
      // it to the contact defined in configuration with fallback to the
      // currently logged in contact.
      $assignee_id = CRM_Core_BAO_Setting::getItem(
        'de.systopia.betterplace',
        'betterplace_contact_failed_contribution_processing'
      );
      $activity_data = array(
        'assignee_id'        => $assignee_id ?: CRM_Core_Session::singleton()->getLoggedInContactID(),
        'activity_type_id'   => CRM_Core_OptionGroup::getValue('activity_type', 'betterplace_failed_contribution_processing', 'name'),
        'subject'            => 'Failed betterplace.org Direkt contribution processing',
        'activity_date_time' => date('YmdHis'),
        'source_contact_id'  => CRM_Core_Session::singleton()->getLoggedInContactID(),
        'status_id'          => CRM_Core_OptionGroup::getValue('activity_status', 'Scheduled', 'name'),
        'target_id'          => $contact_id,
        'details'            => json_encode($params),
      );
      $activity = civicrm_api3('Activity', 'create', $activity_data);
      $extraParams['additional_notices'][] = $activity;
      if (!isset($contact_id)) {
        $extraParams['additional_notices'][] = 'No contact ID is configured for assigning an activity of the type "Failed contribution processing". The activity has not been assigned to a contact.';
      }
    }
    catch (Exception $activity_exception) {
      $extraParams['additional_notices'][] = 'Failed creating an activity of the type "Failed contribution processing".';
    }

    return civicrm_api3_create_error($exception->getMessage(), $extraParams);
  }
}

function _civicrm_api3_b_p_donation_submit_spec(&$params) {
  $params['type'] = array(
    'name' => 'type',
    'title' => 'API action',
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 1,
    'api.default' => 'submit',
    'description' => 'The API action.',
  );
  $params['data'] = array(
    'name' => 'data',
    'title' => 'API call data',
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 0,
    'description' => 'An array of data for the API action.',
  );
  $params['foreign_id'] = array(
    'name' => 'foreign_id',
    'title' => 'CiviCRM contribution ID',
    'type' => CRM_Utils_Type::T_INT,
    'api.required' => 0,
    'description' => 'ID of the CiviCRM Contribution entity',
  );
  $params['form_id'] = array(
    'name'         => 'form_id',
    'title'        => 'betterplace.org Direkt form ID',
    'type'         => CRM_Utils_Type::T_STRING,
    'api.required' => 0,
    'description'  => 'ID of the betterplace.org Direkt form.',
  );
  $params['first_name'] = array(
    'name'         => 'first_name',
    'title'        => 'First Name',
    'type'         => CRM_Utils_Type::T_STRING,
    'api.required' => 0,
    'description'  => 'The contact\'s first name.',
  );
  $params['last_name'] = array(
    'name'         => 'last_name',
    'title'        => 'Last Name',
    'type'         => CRM_Utils_Type::T_STRING,
    'api.required' => 0,
    'description'  => 'The contact\'s last name.',
  );
  $params['email'] = array(
    'name'         => 'email',
    'title'        => 'Email',
    'type'         => CRM_Utils_Type::T_STRING,
    'api.required' => 0,
    'description'  => 'The contact\'s email.',
  );
  $params['street'] = array(
    'name'         => 'street',
    'title'        => 'Street address',
    'type'         => CRM_Utils_Type::T_STRING,
    'api.required' => 0,
    'description'  => 'The contact\'s street_address.',
  );
  $params['zip'] = array(
    'name'         => 'zip',
    'title'        => 'Postal / ZIP code',
    'type'         => CRM_Utils_Type::T_STRING,
    'api.required' => 0,
    'description'  => 'The contact\'s postal_code.',
  );
  $params['city'] = array(
    'name'         => 'city',
    'title'        => 'City',
    'type'         => CRM_Utils_Type::T_STRING,
    'api.required' => 0,
    'description'  => 'The contact\'s city.',
  );
  $params['country'] = array(
    'name'         => 'country',
    'title'        => 'Country',
    'type'         => CRM_Utils_Type::T_STRING,
    'api.required' => 0,
    'description'  => 'The contact\'s country.',
  );
  $params['company_name'] = array(
    'name'         => 'company_name',
    'title'        => 'Company name',
    'type'         => CRM_Utils_Type::T_STRING,
    'api.required' => 0,
    'description'  => 'The organisation\'s name.',
  );
  $params['donation_id'] = array(
    'name'         => 'donation_id',
    'title'        => 'Donation ID',
    'type'         => CRM_Utils_Type::T_STRING,
    'api.required' => 0,
    'description'  => 'The donation ID.',
  );
  $params['confirmed_at'] = array(
    'name'         => 'confirmed_at',
    'title'        => 'Confirmed at',
    'type'         => CRM_Utils_Type::T_INT,
    'api.required' => 0,
    'description'  => 'A timestamp when the donation was issued.',
  );
  $params['revoked_at'] = array(
    'name'         => 'revoked_at',
    'title'        => 'Revoked at',
    'type'         => CRM_Utils_Type::T_INT,
    'api.required' => 0,
    'description'  => 'A timestamp when the donation was cancelled.',
  );
  $params['amount_in_cents'] = array(
    'name'         => 'amount_in_cents',
    'title'        => 'Amount (in cents)',
    'type'         => CRM_Utils_Type::T_INT,
    'api.required' => 0,
    'description'  => 'The donation amount in Euro cents.',
  );
  $params['payment_method'] = array(
    'name'         => 'payment_method',
    'title'        => 'Payment method',
    'type'         => CRM_Utils_Type::T_STRING,
    'api.required' => 0,
    'description'  => 'The payment method used for the donation.',
  );
  $params['campaign'] = array(
    'name'         => 'campaign',
    'title'        => 'Campaign',
    'type'         => CRM_Utils_Type::T_STRING,
    'api.required' => 0,
    'description'  => 'Betterplace campaign.',
  );
  $params['newsletter'] = array(
    'name'         => 'newsletter',
    'title'        => 'Newsletter',
    'type'         => CRM_Utils_Type::T_BOOLEAN,
    'api.required' => 0,
    'description'  => 'Whether to subscribe the contact to the newsletter group defined in the profile.',
  );
  $params['webhook_id'] = array(
    'name'         => 'webhook_id',
    'title'        => 'Webhook ID',
    'type'         => CRM_Utils_Type::T_STRING,
    'api.required' => 0,
    'description'  => 'Webhook ID.',
  );
}
