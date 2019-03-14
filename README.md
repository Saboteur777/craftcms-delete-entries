# Delete Entries plugin for Craft

This plugin is a fork of the [Guest Entries](https://github.com/craftcms/guest-entries) plugin by Pixel & Tonic. It allows you to delete entries from the front-end of your website. Any PR is welcome, as this is my first venture to the land of Craft plugins.

## Requirements

This plugin requires Craft CMS 3.0.0-beta.22 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Add this repository to your `composer.json`:

        "repositories": [
            { "type": "vcs", "url": "https://github.com/Saboteur777/craftcms-delete-entries" }
        ],

3. Then tell Composer to load the plugin:

        composer require saboteur777/delete-entries:dev-master

4. In the Control Panel, go to Settings → Plugins and click the “Install” button for Delete Entries.

## Settings

> This plugin is **not intended to be used without further modifications**.

I use it to delete an entry which was submitted with Guest Entries and the user was notified by e-mail ([Notifications](https://github.com/Rias500/craft-notifications)). In the notification e-mail the user gets a token and a link with the token appended to it (e.g. token is `abc`, then the url will be example.com/entry-slug?tokenUrl=abc. `ReservationToken` is one field of the submitted entry, generated on page load, before submission).


## Usage

Your entry template can look something like this:

```twig
    {% set urlToken = craft.app.request.getQueryParam('urlToken') ?? null %}

    <form id="delete-reservation-form" method="post" enctype="multipart/form-data">
        {{ csrfInput() }}
        <input id="formAction" type="hidden" name="action" value="delete-entries/delete">
        <input type="hidden" name="entryId" value="{{ entry.id }}">
        <input type="hidden" name="sectionId" value="{{ entry.section.id }}">
        <input type="hidden" name="entryToken" value="{{ urlToken }}">
        {{ redirectInput('/') }}

        <button value="delete" onclick="submitForm('delete-reservation-form')">
            Cancel reservation
        </button>
    </form>

    <script>
        // https://craftcms.stackexchange.com/questions/28605/delete-entry-from-front-end#answer-28815
        function submitForm(targetID) {
            if (targetID) {
                let targetElement = document.getElementById(targetID);
                if (targetElement) {
                    targetElement.submit();
                }
            }
        }
    </script>
```

You will need to adjust the hidden `sectionId` input to point to the section you would like to delete guest entries from. This is a "safeguard" to prevent accidental post deletion after copy-pasting the code.
If you have a `redirect` hidden input, the user will get redirected to it upon successfully deleting the entry.

### Entries

The plugin will emit the following two events: `beforeDeleteEntry`, `afterDeleteEntry`.
