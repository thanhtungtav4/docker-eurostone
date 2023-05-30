{{-- Short code buttons with label for "template" command. For variables that can be used, see the actual view
    itself. --}}

<?php

use WPCCrawler\Objects\Filtering\Commands\Enums\CommandShortCodeName;
use WPCCrawler\Objects\ShortCodeButton;

$buttons = [
    ShortCodeButton::getShortCodeButton(
        CommandShortCodeName::ITEM,
        _wpcc('Value of the current item')
    ),
];

?>

@include('form-items.combined.short-code-buttons-with-label', [
    'buttons' => $buttons,
    'noCustomShortCodes' => true,
])