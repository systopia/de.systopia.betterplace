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

use CRM_Betterplace_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Betterplace_Form_Profile extends CRM_Core_Form {

  /**
   * @var CRM_Betterplace_Profile $profile
   *
   * The profile object the form is acting on.
   */
  protected $profile;

  /**
   * @var string
   *
   * The operation to perform within the form.
   */
  protected $_op;

  /**
   * @var array
   *
   * A static cache of retrieved payment instruments found within
   * self::getPaymentInstruments().
   */
  protected static $_paymentInstruments = NULL;

  /**
   * Builds the form structure.
   */
  public function buildQuickForm() {
    // "Create" is the default operation.
    if (!$this->_op = CRM_Utils_Request::retrieve('op', 'String', $this)) {
      $this->_op = 'create';
    }

    // Verify that profile with the given name exists.
    $profile_name = CRM_Utils_Request::retrieve('name', 'String', $this);
    if (!$this->profile = CRM_Betterplace_Profile::getProfile($profile_name)) {
      $profile_name = NULL;
    }

    // Assign template variables.
    $this->assign('op', $this->_op);
    $this->assign('profile_name', $profile_name);

    // Set redirect destination.
    $this->controller->_destination = CRM_Utils_System::url('civicrm/admin/settings/betterplace', 'reset=1');

    switch ($this->_op) {
      case 'delete':
        if ($profile_name) {
          CRM_Utils_System::setTitle(E::ts('Delete betterplace.org Direkt API profile <em>%1</em>', array(1 => $profile_name)));
          $this->addButtons(array(
            array(
              'type' => 'submit',
              'name' => ($profile_name == 'default' ? E::ts('Reset') : E::ts('Delete')),
              'isDefault' => TRUE,
            ),
          ));
        }
        parent::buildQuickForm();
        return;
      case 'edit':
        // When editing without a valid profile name, edit the default profile.
        if (!$profile_name) {
          $profile_name = 'default';
          $this->profile = CRM_Betterplace_Profile::getProfile($profile_name);
        }
        CRM_Utils_System::setTitle(E::ts('Edit betterplace.org Direkt API profile <em>%1</em>', array(1 => $this->profile->getName())));
        break;
      case 'create':
        // Load factory default profile values.
        $this->profile = CRM_Betterplace_Profile::createDefaultProfile($profile_name);
        CRM_Utils_System::setTitle(E::ts('New betterplace.org Direkt API profile'));
        break;
    }

    // Add form elements.
    $is_default = $profile_name == 'default';
    $this->add(
      ($is_default ? 'static' : 'text'),
      'name',
      E::ts('Profile name'),
      array(),
      !$is_default
    );

    $this->add(
      'text', // field type
      'selector', // field name
      E::ts('Form IDs'), // field label
      array(),
      TRUE // is required
    );

    $this->add(
      'select',
      'location_type_id',
      E::ts('Location type'),
      $this->getLocationTypes(),
      TRUE
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
      FALSE // is not required
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
      FALSE, // is not required
      array('class' => 'crm-select2 huge', 'multiple' => 'multiple')
    );

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => E::ts('Save'),
        'isDefault' => TRUE,
      ),
    ));

    // Export form elements.
    parent::buildQuickForm();
  }

  /**
   * @inheritdoc
   */
  public function addRules() {
    $this->addFormRule(array('CRM_Betterplace_Form_Profile', 'validateProfileForm'));
  }

  /**
   * Validates the profile form.
   *
   * @param array $values
   *   The submitted form values, keyed by form element name.
   *
   * @return bool | array
   *   TRUE when the form was successfully validated, or an array of error
   *   messages, keyed by form element name.
   */
  public static function validateProfileForm($values) {
    $errors = array();

    // Restrict profile names to alphanumeric characters and the underscore.
    if (isset($values['name']) && preg_match("/[^A-Za-z0-9\_]/", $values['name'])) {
      $errors['name'] = E::ts('Only alphanumeric characters and the underscore (_) are allowed for profile names.');
    }

    return empty($errors) ? TRUE : $errors;
  }

  /**
   * Set the default values (i.e. the profile's current data) in the form.
   */
  public function setDefaultValues() {
    $defaults = parent::setDefaultValues();
    if (in_array($this->_op, array('create', 'edit'))) {
      $defaults['name'] = $this->profile->getName();
      foreach ($this->profile->getData() as $element_name => $value) {
        $defaults[$element_name] = $value;
      }
    }
    return $defaults;
  }

  /**
   * Store the values submitted with the form in the profile.
   */
  public function postProcess() {
    $values = $this->exportValues();
    if (in_array($this->_op, array('create', 'edit'))) {
      if (empty($values['name'])) {
        $values['name'] = 'default';
      }
      $this->profile->setName($values['name']);
      foreach ($this->profile->getData() as $element_name => $value) {
        if (isset($values[$element_name])) {
          $this->profile->setAttribute($element_name, $values[$element_name]);
        }
      }
      $this->profile->saveProfile();
    }
    elseif ($this->_op == 'delete') {
      $this->profile->deleteProfile();
    }
    parent::postProcess();
  }

  /**
   * Retrieves location types present within the system as options for select
   * form elements.
   */
  public function getLocationTypes() {
    $location_types = array();
    $query = civicrm_api3('LocationType', 'get', array(
      'is_active' => 1,
    ));
    foreach ($query['values'] as $type) {
      $location_types[$type['id']] = $type['name'];
    }

    return $location_types;
  }

  /**
   * Retrieves financial types present within the system as options for select
   * form elements.
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
   * Retrieves campaigns present within the system as options for select form
   * elements.
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
   * Retrieves payment instruments present within the system as options for
   * select form elements.
   */
  public function getPaymentInstruments() {
    if (!isset(self::$_paymentInstruments)) {
      self::$_paymentInstruments = array();
      $query = civicrm_api3('OptionValue', 'get', array(
        'option_group_id' => 'payment_instrument',
        'TODO_is_active'  => 1,
        'option.limit'    => 0,
        'return'          => 'value,label'
      ));
      foreach ($query['values'] as $payment_instrument) {
        self::$_paymentInstruments[$payment_instrument['value']] = $payment_instrument['label'];
      }
    }
    return self::$_paymentInstruments;
  }

  /**
   * Retrieves active groups used as mailing lists within the system as options
   * for select form elements.
   */
  public function getGroups() {
    $groups = array();
    $group_types = civicrm_api3('OptionValue', 'get', array(
      'option_group_id' => 'group_type',
      'name' => CRM_Betterplace_Submission::GROUP_TYPE_MAILING_LIST,
    ));
    if ($group_types['count'] > 0) {
      $group_type = reset($group_types['values']);
      $query = civicrm_api3('Group', 'get', array(
        'is_active' => 1,
        'group_type' => array('LIKE' => '%' . CRM_Utils_Array::implodePadded($group_type['value']) . '%'),
        'option.limit'   => 0,
        'return'         => 'id,name'
      ));
      foreach ($query['values'] as $group) {
        $groups[$group['id']] = $group['name'];
      }
    }
    else {
      $groups[''] = E::ts('No mailing lists available');
    }
    return $groups;
  }

}
