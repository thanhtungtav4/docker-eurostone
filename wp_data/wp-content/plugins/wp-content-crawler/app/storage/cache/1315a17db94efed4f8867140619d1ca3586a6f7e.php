<?php

/** @var array $dataVariableValue Value of 'data' key for find-replace settings */
$dataVariableValue = [
    'subjectSelector'   => sprintf('#%s', \WPCCrawler\Objects\Settings\Enums\SettingKey::TEST_FIND_REPLACE),
    'testType'          =>  \WPCCrawler\Test\Test::$TEST_TYPE_FIND_REPLACE,
];

/**
 * @param string $shortCodeName Name of the short code
 * @param string $transElement Singular element name
 * @param string $transElements Plural element name
 * @return string Information as a translated string
 * @since 1.8.0
 */
function _wpcc_trans_html_element_short_code($shortCodeName, $transElement, $transElements) {
    return sprintf(
        _wpcc('WordPress does not allow %2$s elements for security reasons. If you want to show %3$s in
                the post template, you can check this. When you check this, the %2$s elements in the short code data
                will be converted to %1$s short code that shows the %3$s in the front end. <b>Use this with
                caution since unknown %3$s can cause security vulnerabilities.</b> The short code will output the
                HTML element <b>only for the domains defined in the general settings</b>.'),
        '<b>[' . $shortCodeName . ']</b>',
        $transElement,
        $transElements
    );
}

?>

<div class="wcc-settings-title">
    <h3><?php echo e(_wpcc('Template Settings')); ?></h3>
    <span><?php echo e(_wpcc('Set templates for the post, find and replace things...')); ?></span>
</div>


<?php echo $__env->make('partials.tab-section-navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<table class="wcc-settings">
    
    <?php echo $__env->make('form-items.combined.template-editor-with-label', [
        'name'      => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_TEMPLATE_MAIN,
        'title'     => _wpcc('Main Post Template'),
        'info'      => _wpcc('Main template used for the posts. The buttons above the editor holds short codes which
            are used to place certain elements into the post page. You can hover over the buttons
            to see what they are used to show in post page, and <b>click them to copy the code</b>. After
            copying, just place the short codes into anywhere you want in the editor. <b>You must
            fill the template.<b>'),
        'buttons'   => $buttonsMain
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.textarea-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_TEMPLATE_TITLE,
        'title' => _wpcc('Post Title Template'),
        'info'  => _wpcc('Template for post title. You can also use custom short codes. If you leave this empty,
                original post title found by CSS selectors will be used.'),
        'buttons'   => $buttonsTitle,
        'rows'      => 3,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.textarea-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_TEMPLATE_EXCERPT,
        'title' => _wpcc('Post Excerpt Template'),
        'info'  => _wpcc('Template for post excerpt. You can also use custom short codes. If you leave this empty,
                original post excerpt found by CSS selectors will be used.'),
        'buttons'   => $buttonsExcerpt,
        'rows'      => 3,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.template-editor-with-label', [
        'name'      => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_TEMPLATE_LIST_ITEM,
        'title'     => _wpcc('List Item Template'),
        'info'      => _wpcc('This template is used for the list. If you set the post list type and wrote some selectors
            for the list items, then the list items will be crawled. Here, you can set a template
            to be used for <b>each</b> list item. You can include the entire list in main post
            template. <b>You must fill the template if you expect a list from the target page.</b>'),
        'buttons'   => $buttonsList
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.template-editor-with-label', [
        'name'      => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_TEMPLATE_GALLERY_ITEM,
        'title'     => _wpcc('Gallery Item Template'),
        'info'      => _wpcc('This template is used for the gallery. If you activated saving images as gallery
            and wrote some selectors for the image URLs, then the gallery items will be crawled.
            Here, you can set a template to be used for <b>each</b> gallery image. You can
            include the entire gallery in main post template. <b>You must fill the template if
            you expect a gallery from the target page.</b>'),
        'buttons'   => $buttonsGallery
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('partials.table-section-title', ['title' => _wpcc("Quick Fixes")], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_REMOVE_LINKS_FROM_SHORT_CODES,
        'title' =>  _wpcc('Remove links from short codes?'),
        'info'  =>  sprintf(_wpcc('If you want to remove links from all of the short code data, check this.
                Checking this box is almost the same as adding <b>%1$s</b> regex for find and <b>%2$s</b> for
                replace option for each find and replace option in this tab. This option will not touch custom
                links inside the templates.'),
                esc_html(trim(\WPCCrawler\Objects\Crawling\Preparers\BotConvenienceFindReplacePreparer::REMOVE_LINKS_FIND, '/')),
                esc_html(\WPCCrawler\Objects\Crawling\Preparers\BotConvenienceFindReplacePreparer::REMOVE_LINKS_REPLACE)
        )
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_CONVERT_IFRAMES_TO_SHORT_CODE,
        'title' => _wpcc('Convert iframe elements to short code'),
        'info'  => _wpcc_trans_html_element_short_code(
            \WPCCrawler\Objects\GlobalShortCodes\GlobalShortCodeService::getShortCodeTagName(\WPCCrawler\Objects\GlobalShortCodes\ShortCodes\IFrameGlobalShortCode::class),
            _wpcc('iframe'),
            _wpcc('iframes')
        )
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_CONVERT_SCRIPTS_TO_SHORT_CODE,
        'title' => _wpcc('Convert script elements to short code'),
        'info'  => _wpcc_trans_html_element_short_code(
            \WPCCrawler\Objects\GlobalShortCodes\GlobalShortCodeService::getShortCodeTagName(\WPCCrawler\Objects\GlobalShortCodes\ShortCodes\ScriptGlobalShortCode::class),
            _wpcc('script'),
            _wpcc('scripts')
        )
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_REMOVE_EMPTY_HTML_TAGS,
        'title' => _wpcc('Remove empty HTML elements and comments'),
        'info'  => sprintf(
            _wpcc('Check this if HTML elements that do not have any content and comments in the HTML code should all be
            removed from all parts of the post. This <b>does not remove</b> the elements that should not have any
            content by default, such as %1$s elements.'),
            '<span class="highlight selector">img</span>'
        ),
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_REMOVE_SCRIPTS,
        'title' => _wpcc('Remove scripts'),
        'info'  => sprintf(
            _wpcc('Check this if you want to remove scripts from all parts of the post. This removes %1$s
            elements as well as HTML attributes that can store JavaScript code, such as %2$s.'),
            '<span class="highlight selector">script</span>',
            '<span class="highlight attribute">onclick</span>'
        ),
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('partials.table-section-title', ['title' => _wpcc("Unnecessary Elements")], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-selector', [
        'name'          => \WPCCrawler\Objects\Settings\Enums\SettingKey::TEMPLATE_UNNECESSARY_ELEMENT_SELECTORS,
        'title'         =>  _wpcc('Template Unnecessary Element Selectors'),
        'info'          =>  _wpcc('CSS selectors for unwanted elements in the template. Specified elements will be
            removed from the HTML of the template. The removal will be done after the shortcodes are replaced.
            Find-and-replaces will be done after the template is cleared from unnecessary elements. <b>This
            will use test post URL on Post tab to conduct the tests.</b>'),
        'urlSelector'   =>  sprintf('#%s', \WPCCrawler\Objects\Settings\Enums\SettingKey::TEST_URL_POST),
        'inputClass'    => 'css-selector',
        'showDevTools'  => true,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('partials.table-section-title', ['title' => _wpcc("Manipulate HTML")], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.textarea-with-label', [
        'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::TEST_FIND_REPLACE,
        'title'         =>  _wpcc('Find and Replace Test Code'),
        'info'          =>  _wpcc('A piece of code to be used when testing find-and-replace settings below.'),
        'placeholder'   =>  _wpcc('The code which will be used to test find-and-replace settings'),
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-find-replace-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_FIND_REPLACE_TEMPLATE,
        'title' => _wpcc("Find and replace in post's content"),
        'info'  => _wpcc('If you want some things to be replaced with some other things in <b>HTML of post\'s content</b>,
            this is the place. The replacement will be done after the final post template is ready.'),
        'data'  => $dataVariableValue,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-find-replace-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_FIND_REPLACE_CUSTOM_SHORTCODES,
        'title' => _wpcc("Find and replace in custom short code contents"),
        'info'  => _wpcc('If you want some things to be replaced with some other things in <b>each custom short
            code\'s content</b>, this is the place. The replacement will be done after the final post template
            is ready.'),
        'data'  => $dataVariableValue,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-find-replace-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_FIND_REPLACE_TITLE,
        'title' => _wpcc("Find and replace in post's title"),
        'info'  => _wpcc('If you want some things to be replaced with some other things in <b>post\'s title</b>,
            this is the place.'),
        'data'  => $dataVariableValue,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-find-replace-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_FIND_REPLACE_EXCERPT,
        'title' => _wpcc("Find and replace in post's excerpt"),
        'info'  => _wpcc('If you want some things to be replaced with some other things in <b>post\'s excerpt</b>,
            this is the place.'),
        'data'  => $dataVariableValue,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-find-replace-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_FIND_REPLACE_TAGS,
        'title' => _wpcc("Find and replace in post's each tag"),
        'info'  => _wpcc('If you want some things to be replaced with some other things in <b>post\'s each tag</b>,
            this is the place.'),
        'data'  => $dataVariableValue,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-find-replace-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_FIND_REPLACE_META_KEYWORDS,
        'title' => _wpcc("Find and replace in meta keywords"),
        'info'  => _wpcc('If you want some things to be replaced with some other things in <b>post\'s meta keywords</b>,
            this is the place.'),
        'data'  => $dataVariableValue,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-find-replace-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_FIND_REPLACE_META_DESCRIPTION,
        'title' => _wpcc("Find and replace in meta description"),
        'info'  => _wpcc('If you want some things to be replaced with some other things in <b>post\'s meta description</b>,
            this is the place.'),
        'data'  => $dataVariableValue,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php

    /** @var int $postId */
    /**
     * Fires before closing table tag in templates tab of site settings page.
     *
     * @param array $settings   Existing settings and their values saved by user before
     * @param int $postId       ID of the site
     * @since 1.6.3
     */
    do_action('wpcc/view/site-settings/tab/templates', $settings, $postId);

    ?>

</table>
<?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/site-settings/tab-templates.blade.php ENDPATH**/ ?>