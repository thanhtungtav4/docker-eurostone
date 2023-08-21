

<?php

use WPCCrawler\Objects\Filtering\Commands\Enums\CommandShortCodeName;
use WPCCrawler\Objects\ShortCodeButton;

$buttons = [
    ShortCodeButton::getShortCodeButton(
        CommandShortCodeName::REQUEST_URL,
        _wpcc('URL of the page being crawled')
    ),

    ShortCodeButton::getShortCodeButton(
        CommandShortCodeName::STATUS_CODE,
        sprintf(_wpcc('HTTP status code of the response of the crawl request. E.g. %s, etc.'), '400, 403, 200, 500')
    ),

    ShortCodeButton::getShortCodeButton(
        CommandShortCodeName::SITE_NAME,
        _wpcc('Name of the site, defined by you')
    ),

    ShortCodeButton::getShortCodeButton(
        CommandShortCodeName::SITE_ID,
        _wpcc('ID of the site')
    ),

    ShortCodeButton::getShortCodeButton(
        CommandShortCodeName::SITE_EDIT_URL,
        _wpcc('The URL that displays the site edit page')
    ),

    ShortCodeButton::getShortCodeButton(
        CommandShortCodeName::CURRENT_TIME,
        _wpcc('Current date and time')
    ),
];

?>

<?php echo $__env->make('form-items.combined.short-code-buttons-with-label', [
    'buttons' => $buttons,
    'noCustomShortCodes' => true,
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/form-items/combined/short-code-buttons-with-label-for-email-cmd.blade.php ENDPATH**/ ?>