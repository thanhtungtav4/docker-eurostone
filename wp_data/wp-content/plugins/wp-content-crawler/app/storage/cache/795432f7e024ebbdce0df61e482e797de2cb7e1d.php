

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

<?php echo $__env->make('form-items.combined.short-code-buttons-with-label', [
    'buttons' => $buttons,
    'noCustomShortCodes' => true,
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/form-items/combined/short-code-buttons-with-label-for-template-cmd.blade.php ENDPATH**/ ?>