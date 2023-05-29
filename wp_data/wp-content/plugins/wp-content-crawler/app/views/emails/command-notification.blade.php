{{-- The default notification message template that is intended for use via "send notification" command --}}
<?php

use WPCCrawler\Objects\Filtering\Commands\Enums\CommandShortCodeName;

// Get the short codes with brackets to create a template. The short codes will be replaced before sending the
// notification.
$siteName   = '['. CommandShortCodeName::SITE_NAME     . ']';
$requestUrl = '['. CommandShortCodeName::REQUEST_URL   . ']';
$statusCode = '['. CommandShortCodeName::STATUS_CODE   . ']';
$editUrl    = '['. CommandShortCodeName::SITE_EDIT_URL . ']';
$now        = '['. CommandShortCodeName::CURRENT_TIME  . ']';

// Get extra information
$adminUrl   = admin_url();
?>

{{ _wpcc("This is a notification sent by a notification command that is defined in your site. Below, you can find
certain details about the site and the request that triggered this notification.") }}

<br><br>
<table>
    <tr>
        <td>{{ _wpcc("Site name") }}</td>
        <td><b>{{ $siteName }}</b></td>
    </tr>
    <tr>
        <td>{{ _wpcc("Status code") }}</td>
        <td><b>{{ $statusCode }}</b></td>
    </tr>
    <tr>
        <td>{{ _wpcc("Request URL") }}</td>
        <td><b><a href="{!! $requestUrl !!}">{!! $requestUrl !!}</a></b></td>
    </tr>
    <tr>
        <td>{{ _wpcc("Date and time") }}</td>
        <td><b>{{ $now }}</b></td>
    </tr>
    <tr>
        <td>{{ _wpcc("Edit site") }}</td>
        <td><b><a href="{!! $editUrl !!}">{!! $editUrl !!}</a></b></td>
    </tr>
    <tr>
        <td>{{ _wpcc("Admin dashboard") }}</td>
        <td><b><a href="{!! $adminUrl !!}">{!! $adminUrl !!}</a></b></td>
    </tr>
</table>

<br>
---<br>
{{ _wpcc('WP Content Crawler') }}
