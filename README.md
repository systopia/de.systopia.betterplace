# de.systopia.betterplace

![Screenshot](/images/screenshot.png)

Extension to connect to the betterplace.org Direkt donation page via the webhook api.

* [About betterplace.org Direkt](https://www.spendenformular-direkt.org/)
* [Webhook api documentation](https://betterplace.github.io/xform/webhooks)

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v5.4+
* CiviCRM (*FIXME: Version number*)

## Installation (Web UI)

This extension has not yet been published for installation via the web UI.

## Installation (CLI, Zip)

Sysadmins and developers may download the `.zip` file for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
cd <extension-dir>
cv dl de.systopia.betterplace@https://github.com/FIXME/de.systopia.betterplace/archive/master.zip
```

## Installation (CLI, Git)

Sysadmins and developers may clone the [Git](https://en.wikipedia.org/wiki/Git) repo for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
git clone https://github.com/FIXME/de.systopia.betterplace.git
cv en betterplace
```

## Usage

### Configure www.spendenformular-direkt.org

- Go to your client administration
- Configure the "API Callback URL"
  at `https://www.spendenformular-direkt./backoffice/clients/YOUR_CLIENT_ID/endpoint/edit`
- Change the "Versandart" to "Als JSON-URL-Parameter" so the CiviCRM API can
work with the callback data.
- Add the URL using this schema
`https://DOMAIN/sites/all/modules/civicrm/extern/rest.php?entity=BPDonation&action=submit&api_key=APIKEY&key=KEY`

| Url Part| Description |
|-------|-------------|
| DOMAIN | Your CiviCRM Domain |
| APIKEY | The CiviCRM API Key of the contact you wish to use for performing API calls. |
| KEY | The CiviCRM site key as defined as the constant CIVICRM_SITE_KEY in your civicrm.settings.php file. |


### Configure CiviCRM

- Go to the Admin panel `/civicrm/admin`
- Open "betterplace.org Direkt API Configuration"
  at `/civicrm/admin/settings/betterplace`

#### Configure a contact for administration-activities

Open "Configure extention settings"
at `/civicrm/admin/settings/betterplace/settings`

**Form:**

Provide the CiviCRM-ID of the contact that will receive the activities

#### Configure "profiles"

Open "Configure profiles" at `/civicrm/admin/settings/betterplace/profiles`

The "default" profile is used whenever the plugin cannot match the
betterplace.org Direkt form id from any other profile.
Therefore the default profile will be used for all newly created
betterplace.org Direkt forms.

**Form:**

| Label | Description |
|-------|-------------|
| Profile name | Internal name, used inside the extension. |
| Form IDs | betterplace.org Direkt form IDs. Separate multiple IDs by commas. Example: `e1e36e0a-d706-45ce-96b8-8948bee03efe` |
| Location type | Specify how the address data sent by the form should be categorised in CiviCRM. List is based on your CiviCRM configuration. |
| Campaign | (Optional) Specify if a donation should be attributed to a CiviCRM Campaign. CiviCRM will ignore all attribution that was given inside the betterplace.org Direkt form. |
| Record CreditCard as | Specifiy the payment method for donations via credit card. |
| Record PayPal as | Specifiy the payment method for donations via PayPal. |
| Record SEPA direct debit as | Specifiy the payment method for donations via SEPA direct debit. |
| Sign up for groups | Whenever the donor checked the newsletter checkbox, the contact will be assigned to the groups listed here. |

## Known Issues

(* FIXME *)
* ATM only regular donations are supported, recurring donations are still TODO
