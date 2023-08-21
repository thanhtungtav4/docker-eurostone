<div class="description">
    <?php echo sprintf(
        _wpcc('You can add calculation options for the current value. If the item is a number, the formula you enter
        will be applied to the number. Write %1$s for the current value. For example, if the current value is %2$s and
        you want to multiply it by %3$s, then you can write %4$s. You can use parenthesis to group the expressions. For
        example, %5$s will result in %6$s. Please make sure your mathematical expressions work by using the test button
        next to each option. Note that options created in the find-replace and general tabs will be applied first. If you enter more
        than one formula, a random one will be used. Operators you can use: %7$s'),
            '<b>X</b>',
            '<b>50</b>',
            '<b>2</b>',
            '<b>X * 2</b>',
            '<b>((5 * 7 + 1) / 4^2 - 2) * 10 / X</b>',
            '<b>0.05</b>',
            '<b>+, -, *, /, ^</b>'
        ); ?>


    <?php echo sprintf(
        _wpcc('In case of treating the item as JSON, do not use X in the formula. Use <b>[%1$s]</b> short code with a dot key to get the values from JSON.'),
        \WPCCrawler\Objects\Enums\ShortCodeName::WCC_ITEM
    ); ?>

</div>

<table class="wcc-settings">
    
    <?php echo $__env->make('form-items.combined.select-with-label', [
        'name'    => \WPCCrawler\Objects\Settings\Enums\SettingKey::OPTIONS_BOX_DECIMAL_SEPARATOR_AFTER,
        'title'   =>  _wpcc('Decimal separator for result'),
        'info'    =>  _wpcc('Define the decimal separator for the number that will be shown in your site.'),
        'options' => \WPCCrawler\Utils::getDecimalSeparatorOptionsForSelect(),
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::OPTIONS_BOX_USE_THOUSANDS_SEPARATOR,
        'title' =>  _wpcc('Use thousands separator in the result?'),
        'info'  =>  _wpcc('Check this if you want to use thousands separator in the result.')
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::OPTIONS_BOX_REMOVE_IF_NOT_NUMERIC,
        'title' =>  _wpcc('Remove item if it is not numeric?'),
        'info'  =>  _wpcc('Check this if you want to remove the item when its value is not numeric.')
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.input-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::OPTIONS_BOX_PRECISION,
        'title' =>  _wpcc('Precision'),
        'info'  =>  _wpcc('How many digits at max there can be after the decimal separator.'),
        'value' => 0,
        'type'  => 'number'
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-text-with-label', [
        'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::OPTIONS_BOX_FORMULAS,
        'title'         =>  _wpcc('Formulas'),
        'info'          =>  _wpcc('Enter the formulas. If you enter more than one, a random one will be used.'),
        'inputKey'      =>  'formula',
        'placeholder'   =>  _wpcc('Formula'),
        'addon'         =>  'dashicons dashicons-search',
        'data'          =>  [
            'testType'  =>  \WPCCrawler\Test\Test::$TEST_TYPE_CALCULATION,
            'extra'     =>  $dataExtra
        ],
        'test'          =>  true,
        'addonClasses'  => 'wcc-test-calculation',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

</table><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/form-items/options-box/tabs/tab-calculations.blade.php ENDPATH**/ ?>