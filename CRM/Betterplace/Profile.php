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
 * Profiles define how incoming submissions from
 *  a donation page are processed in CiviCRM
 */
class CRM_Betterplace_Profile {

  /** caches the profile objects */
  protected static $_profiles = NULL;

  protected $name = NULL;
  protected $data = NULL;

  public function __construct($name, $data) {
    $this->name = $name;
    $this->data = $data;
  }


  /**
   * TODO
   */
  public function matches($form_id) {
    $selector = $this->getAttribute('selector');
    $form_ids = explode(',', $selector);
    return in_array($form_id, $form_ids);
  }

  /**
   * TODO
   */
  public function getAttribute($attribute_name) {
    if (isset($this->data[$attribute_name])) {
      return $this->data[$attribute_name];
    } else {
      return NULL;
    }
  }

  /**
   * TODO
   */
  public function setAttribute($attribute_name, $value) {
    // TODO: check if attribute wanted, value acceptable
    $this->data[$attribute_name] = $value;
  }


  /**
   * Verify if given profile is valid, (also wrt the other profiles)
   * @throws Exception
   */
  public function verifyProfile() {
    // TODO: check
    //  data of this profile consistent?
    //  conflicts with other profiles?
  }

  /**
   * Verify if given profile is valid, (also wrt the other profiles)
   */
  public function saveProfile() {
    $this->verifyProfile();
    self::storeProfiles();
  }




  /**
   * this is the "factory default" profile
   */
  public static function createDefaultProfile() {
    return new CRM_Betterplace_Profile('default', array(
      'selector'          => '',
      'financial_type_id' => 1, // "Donation"
      'campaign_id'       => '',
      'pi_creditcard'     => 1, // "Credit Card"
      'pi_sepa'           => 5, // "EFT"
      'pi_paypal'         => 3, // "Debit"
      'groups'            => '',
    ));
  }

  /**
   * retrieves the rigth profile for the given
   *  form ID. Returns the default profile if none
   *  found.
   */
  public static function getProfileForForm($form_id) {
    $profiles = self::getProfiles();
    foreach ($profiles as $profile) {
      if ($profile->matches($form_id)) {
        return $profile;
      }
    }

    // no profile found?
    return $profiles['default'];
  }

  /**
   * get the profile with the given name
   */
  public static function getProfile($name) {
    $profiles = self::getProfiles();
    if (isset($profiles[$name])) {
      return $profiles[$name];
    } else {
      return NULL;
    }
  }

  /**
   * Get the (raw) list of all profiles
   */
  public static function getProfiles() {
    if (self::$_profiles === NULL) {
      self::$_profiles = array();
      $profiles_data = CRM_Core_BAO_Setting::getItem('de.systopia.betterplace', 'betterplace_profiles');
      foreach ($profiles_data as $profile_name => $profile_data) {
        self::$_profiles[$profile_name] = new CRM_Betterplace_Profile($profile_name, $profile_data);
      }
    }

    // make sure the default profile is there
    if (!isset(self::$_profiles['default'])) {
      self::$_profiles['default'] = self::createDefaultProfile();
      self::storeProfiles();
    }

    return self::$_profiles;
  }


  /**
   * Set the (raw) list of all profiles
   */
  public static function storeProfiles() {
    $profile_data = array();
    foreach (self::$_profiles as $profile_name => $profile) {
      $profile_data[$profile_name] = $profile->profile;
    }
    CRM_Core_BAO_Setting::setItem($profile_data, 'de.systopia.betterplace', 'betterplace_profiles');
  }
}