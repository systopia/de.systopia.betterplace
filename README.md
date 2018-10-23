# betterplace.org Spendenformular Direkt

Extension to connect to the betterplace.org Direkt donation page via the webhook
api.

* [About betterplace.org Direkt](https://www.spendenformular-direkt.org/)
* [Webhook api documentation](https://betterplace.github.io/xform/webhooks)

The extension is licensed under
[AGPL-3.0](https://github.com/systopia/de.systopia.betterplace/blob/master/LICENSE.txt).

## Requirements

* PHP v5.4+
* CiviCRM 4.6/4.7/5.x

## Usage

### Configure www.spendenformular-direkt.org

- Go to your client administration
- Configure the "API Callback URL"
  at `https://www.spendenformular-direkt./backoffice/clients/YOUR_CLIENT_ID/endpoint/edit`
- Change the "Versandart" to "Als JSON-URL-Parameter" so the CiviCRM API can
  work with the callback data.
- Add the URL using this schema:
  `https://DOMAIN/sites/all/modules/civicrm/extern/rest.php?entity=BPDonation&action=submit&api_key=APIKEY&key=KEY`

| Url part| Description                                                                                         |
|---------|-----------------------------------------------------------------------------------------------------|
| DOMAIN  | Your CiviCRM domain                                                                                 |
| APIKEY  | The CiviCRM API key of the contact you wish to use for performing API calls.                        |
| KEY     | The CiviCRM site key as defined as the constant CIVICRM_SITE_KEY in your civicrm.settings.php file. |

### Configure CiviCRM

- Go to the Administration console `/civicrm/admin`
- Open "betterplace.org Direkt API Configuration" at
  `/civicrm/admin/settings/betterplace`

#### Configure a contact for administrative activities

Open "Configure extension settings" at
`/civicrm/admin/settings/betterplace/settings` and provide the CiviCRM ID of the
contact that will receive the activities.

#### Configure profiles

Open "Configure profiles" at `/civicrm/admin/settings/betterplace/profiles`.

The *default* profile is used whenever the plugin cannot match the
betterplace.org Direkt form id from any other profile. Therefore the default
profile will be used for all newly created betterplace.org Direkt forms.

| Label                       | Description                                                                                                                                                             |
|-----------------------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Profile name                | Internal name, used inside the extension.                                                                                                                               |
| Form IDs                    | betterplace.org Direkt form IDs. Separate multiple IDs by commas. Example: `e1e36e0a-d706-45ce-96b8-8948bee03efe`                                                       |
| Location type               | Specify how the address data sent by the form should be categorised in CiviCRM. The list is based on your CiviCRM configuration.                                        |
| Campaign                    | (Optional) Specify if a donation should be attributed to a CiviCRM Campaign. CiviCRM will ignore all attribution that was given inside the betterplace.org Direkt form. |
| Record CreditCard as        | Specifiy the payment method for donations via credit card.                                                                                                              |
| Record PayPal as            | Specifiy the payment method for donations via PayPal.                                                                                                                   |
| Record SEPA direct debit as | Specifiy the payment method for donations via SEPA direct debit.                                                                                                        |
| Sign up for groups          | Whenever the donor checked the newsletter checkbox, the contact will be assigned to the groups listed here.                                                             |


## Known Issues

* At the moment, only regular donations are supported, recurring donations are
  not yet implemented.
