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

use CRM_Betterplace_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Betterplace_Form_Profile extends CRM_Core_Form {

  public function buildQuickForm() {

    // get the profile
    $profile_name = CRM_Utils_Request::retrieve('name', 'String', $this);
    // TODO: if not exists -> create?
    if (empty($profile_name)) {
      $profile_name = 'default';
    }
    $this->profile = CRM_Betterplace_Profile::getProfile($profile_name);

    // add form elements
    $this->add(
      'text', // field type
      'selector', // field name
      E::ts('Form IDs'), // field label
      array(),
      TRUE // is required
    );

    $this->add(
      'select', // field type
      'financial_type_id', // field name
      E::ts('Financial Type'), // field label
      $this->getFinancialTypes(), // list of options
      TRUE // is required
    );

    $this->add(
      'select', // field type
      'campaign_id', // field name
      E::ts('Campaign'), // field label
      $this->getCampaigns(), // list of options
      TRUE // is required
    );

    $this->add(
      'select', // field type
      'pi_creditcard', // field name
      E::ts('Record CreditCard as'), // field label
      $this->getPaymentInstruments(), // list of options
      TRUE // is required
    );

    $this->add(
      'select', // field type
      'pi_paypal', // field name
      E::ts('Record PayPal as'), // field label
      $this->getPaymentInstruments(), // list of options
      TRUE // is required
    );

    $this->add(
      'select', // field type
      'pi_sepa', // field name
      E::ts('Record SEPA direct debit as'), // field label
      $this->getPaymentInstruments(), // list of options
      TRUE // is required
    );

    $this->add(
      'select', // field type
      'groups', // field name
      E::ts('Sign up for groups'), // field label
      $this->getGroups(), // list of options
      TRUE, // is required
      array('class' => 'crm-select2 huge', 'multiple' => 'multiple')
    );


    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => E::ts('Save'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    parent::buildQuickForm();
  }



  /**
   * set the default (=current) values in the form
   */
  public function setDefaultValues() {
    // TODO: take from $this->profile
  }


  public function postProcess() {
    $values = $this->exportValues();
    // TODO STORE
    error_log("VALUES " . json_encode($values) );
    parent::postProcess();
  }


  /**
   * TODO
   */
  public function getFinancialTypes() {
    $financial_types = array();
    $query = civicrm_api3('FinancialType', 'get', array(
      'is_active'    => 1,
      'option.limit' => 0,
      'return'       => 'id,name'
    ));
    foreach ($query['values'] as $type) {
      $financial_types[$type['id']] = $type['name'];
    }
    return $financial_types;
  }

  /**
   * TODO
   */
  public function getCampaigns() {
    $campaigns = array('' => E::ts("no campaign"));
    $query = civicrm_api3('Campaign', 'get', array(
      'is_active'    => 1,
      'option.limit' => 0,
      'return'       => 'id,title'
    ));
    foreach ($query['values'] as $campaign) {
      $campaigns[$campaign['id']] = $campaign['title'];
    }
    return $campaigns;
  }

  /**
   * TODO
   */
  public function getPaymentInstruments() {
    // TODO: cache
    $pis = array();
    $query = civicrm_api3('OptionValue', 'get', array(
      'option_group_id' => 'payment_instrument',
      'TODO_is_active'  => 1,
      'option.limit'    => 0,
      'return'          => 'value,label'
    ));
    foreach ($query['values'] as $campaign) {
      $pis[$campaign['value']] = $campaign['label'];
    }
    return $pis;


    return array(1 => 'Donation');
  }

  /**
   * TODO
   */
  public function getGroups() {
    $groups = array();
    $query = civicrm_api3('Group', 'get', array(
      'TOOD_is_active' => 1,
      'TOOD_type_mail' => 1,
      'option.limit'   => 0,
      'return'         => 'id,name'
    ));
    foreach ($query['values'] as $group) {
      $groups[$group['id']] = $group['name'];
    }
    return $groups;
  }


}
