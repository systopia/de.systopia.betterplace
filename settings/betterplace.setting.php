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

/*
* Settings metadata file
*/
return array(
  'betterplace_contact_failed_contribution_processing' => array(
    'group_name' => 'de.systopia.betterplace',
    'group' => 'de.systopia.betterplace',
    'name' => 'betterplace_contact_failed_contribution_processing',
    'type' => 'Integer',
    'quick_form_type' => 'Element',
    'html_type' => 'text', // TODO: contact reference?
    'title' => 'Contact to assign "Failed contribution processing" activity',
    'default' => NULL,
    'add' => '4.6',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'The ID of the contact to assign activities of type "Failed contribution processing".',
  ),
);
