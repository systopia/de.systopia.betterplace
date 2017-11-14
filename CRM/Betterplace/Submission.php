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

class CRM_Betterplace_Submission {

  /**
   * The default ID of the "Work" location type.
   */
  const LOCATION_TYPE_ID_WORK = 2;

  /**
   * The option value name of the group type for mailing lists.
   */
  const GROUP_TYPE_MAILING_LIST = 'Mailing List';

  /**
   * Retrieves the contact matching the given contact data or creates a new
   * contact.
   *
   * @param string $contact_type
   *   The contact type to look for/to create.
   * @param array $contact_data
   *   Data to use for contact lookup/to create a contact with.
   *
   * @return int | NULL
   *   The ID of the matching/created contact, or NULL if no matching contact
   *   was found and no new contact could be created.
   * @throws API_Exception
   *   When invalid data was given.
   */
  public static function getContact($contact_type, $contact_data) {
    // If no parameters are given, do nothing.
    if (empty($contact_data)) {
      return NULL;
    }

    // Prepare values: country.
    if (!empty($contact_data['country'])) {
      if (is_numeric($contact_data['country'])) {
        // If a country ID is given, update the parameters.
        $contact_data['country_id'] = $contact_data['country'];
        unset($contact_data['country']);
      }
      else {
        // Look up the country depending on the given ISO code.
        $country = civicrm_api3('Country', 'get', array('iso_code' => $contact_data['country']));
        if (!empty($country['id'])) {
          $contact_data['country_id'] = $country['id'];
          unset($contact_data['country']);
        }
        else {
          throw new API_Exception("Unknown country '{$contact_data['country']}'", 1);
        }
      }
    }

    // Pass to XCM.
    $contact_data['contact_type'] = $contact_type;
    $contact = civicrm_api3('Contact', 'getorcreate', $contact_data);
    if (empty($contact['id'])) {
      return NULL;
    }

    return $contact['id'];
  }

  /**
   * Share an organisation's work address, unless the contact already has one
   *
   * @param $contact_id
   *   The ID of the contact to share the organisation address with.
   * @param $organisation_id
   *   The ID of the organisation whose address to share with the contact.
   * @param $location_type_id
   *   The ID of the location type to use for address lookup.
   *
   * @return boolean
   *   Whether the organisation address has been shared with the contact.
   */
  public static function shareWorkAddress($contact_id, $organisation_id, $location_type_id = self::LOCATION_TYPE_ID_WORK) {
    if (empty($organisation_id)) {
      // Only if organisation exists.
      return FALSE;
    }

    // Check whether organisation has a WORK address.
    $existing_org_addresses = civicrm_api3('Address', 'get', array(
      'contact_id'       => $organisation_id,
      'location_type_id' => $location_type_id));
    if ($existing_org_addresses['count'] <= 0) {
      // Organisation does not have a WORK address.
      return FALSE;
    }

    // Check whether contact already has a WORK address.
    $existing_contact_addresses = civicrm_api3('Address', 'get', array(
      'contact_id'       => $contact_id,
      'location_type_id' => $location_type_id));
    if ($existing_contact_addresses['count'] > 0) {
      // Contact already has a WORK address.
      return FALSE;
    }

    // Create a shared address.
    $address = reset($existing_org_addresses['values']);
    $address['contact_id'] = $contact_id;
    $address['master_id']  = $address['id'];
    unset($address['id']);
    civicrm_api3('Address', 'create', $address);
    return TRUE;
  }

}
