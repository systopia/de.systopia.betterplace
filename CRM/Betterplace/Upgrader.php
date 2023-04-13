<?php
use CRM_Betterplace_ExtensionUtil as E;

/**
 * Collection of upgrade steps.
 */
class CRM_Betterplace_Upgrader extends CRM_Extension_Upgrader_Base {

  // By convention, functions that look like "function upgrade_NNNN()" are
  // upgrade tasks. They are executed in order (like Drupal's hook_update_N).

  /**
   * Convert serialized settings from objects to arrays.
   *
   * @link https://civicrm.org/advisory/civi-sa-2019-21-poi-saved-search-and-report-instance-apis
   */
  public function upgrade_5011() {
    // Do not use CRM_Core_BAO::getItem() or Civi::settings()->get().
    // Extract and unserialize directly from the database.
    $betterplace_profiles_query = CRM_Core_DAO::executeQuery("
        SELECT `value`
          FROM `civicrm_setting`
        WHERE `name` = 'betterplace_profiles';");
    if ($betterplace_profiles_query->fetch()) {
      $profiles = unserialize($betterplace_profiles_query->value);
      Civi::settings()->set('betterplace_profiles', (array) $profiles);
    }

    return TRUE;
  }

}
