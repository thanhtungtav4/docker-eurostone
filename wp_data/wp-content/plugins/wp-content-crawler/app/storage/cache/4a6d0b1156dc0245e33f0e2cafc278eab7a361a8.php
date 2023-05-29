<?php
    $testUrlCategorySelector = sprintf('#%s', \WPCCrawler\Objects\Settings\Enums\SettingKey::TEST_URL_CATEGORY);
?>

<div class="wcc-settings-title">
    <h3><?php echo e(_wpcc('Category Page Settings')); ?></h3>
    <span><?php echo e(_wpcc("A category page is a page where URLs of the posts exist. For example, a page listing many news in a news
    site, a page listing many hotels in a booking site or a page showing many products in an e-commerce site can be
    considered as category pages. Here, you can define URLs of the categories of target site and CSS selectors that find
    post URLs so that the plugin can find and save posts automatically.")); ?></span>
</div>


<?php echo $__env->make('partials.tab-section-navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<table class="wcc-settings">
    
    <?php echo $__env->make('form-items.combined.multiple-category-map-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::CATEGORY_MAP,
        'title' =>  _wpcc('Category URLs'),
        'info'  =>  sprintf(_wpcc('Enter URLs of target site\'s categories. A category is a page in which post
            URLs exist. Also, define into which category of your site the posts crawled from a category URL
            should be saved. You can write full URLs, starting with "http". E.g. %1$s. You can also write the
            URLs relative to the main site URL you defined under Main tab. E.g. %2$s. Category URLs should be
            added once, no duplicates allowed. <b>Note that</b> changing the values of this setting will clear
            the post URLs waiting to be saved.'),
            '<span class="highlight url">http://site.com/category/art</span>',
            '<span class="highlight url">/category/art</span>'
        ),
        'placeholder'   =>  _wpcc('Category URL from the target site...'),
        'categories'    =>  $categories,
        'data'          =>  [
            'urlSelector'       =>  "input",
            'closest_inside'    =>  true,
            'testType'          =>  \WPCCrawler\Test\Test::$TEST_TYPE_HREF,
        ],
        'formItemTdId'  => 'category-map',
        'markRequired'  => true,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'          => \WPCCrawler\Objects\Settings\Enums\SettingKey::CATEGORY_ADD_CATEGORY_URLS_WITH_SELECTOR,
        'title'         => _wpcc("Add category URLs automatically?"),
        'info'          => _wpcc('Category URLs you want to enter into Category URLs setting can be automatically
            retrieved from the target site. If you want to fill category URLs using CSS selectors, check this.'),
        'dependants'    => '[".auto-fill-category-map"]'
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.input-with-label', [
        'name'          => \WPCCrawler\Objects\Settings\Enums\SettingKey::CATEGORY_LIST_PAGE_URL,
        'title'         => _wpcc('URL of a page containing category URLs'),
        'info'          => _wpcc('The URL to get category links from. The page should include a container having category URLs.
            This will be used to automatically insert category URLs for category map.'),
        'type'          => 'url',
        'placeholder'   => _wpcc("URL of a page in which category URLs of target site exist..."),
        'class'         => 'auto-fill-category-map',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-selector-with-attribute', [
        'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::CATEGORY_LIST_URL_SELECTORS,
        'title'         =>  _wpcc('Category URL Selectors'),
        'info'          =>  _wpcc('CSS selectors for category links. This is used to get category URLs automatically for category map.
            Gets "href" attributes of "a" tags. E.g. <span class="highlight selector">.top-level-navigation ul > li > a</span>.
            Before using the insert button, make sure you filled the category list page URL.'),
        'addon'         =>  'dashicons dashicons-plus',
        'addonTitle'    =>  _wpcc('Find and add category URLs'),
        'addonClasses'  =>  'wcc-category-map',
        'urlSelector'   =>  sprintf("#%s", \WPCCrawler\Objects\Settings\Enums\SettingKey::CATEGORY_LIST_PAGE_URL),
        'data'          =>  [
            'targetTag'              =>  'a',
            'selectorFinderBehavior' =>  \WPCCrawler\Objects\Enums\SelectorFinderBehavior::SIMILAR,
        ],
        'inputClass'    => 'css-selector',
        'showDevTools'  => true,
        'defaultAttr'   => 'href',
        'class'         => 'auto-fill-category-map',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.input-with-label', [
        'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::TEST_URL_CATEGORY,
        'title'         =>  _wpcc('Test Category URL'),
        'info'          =>  _wpcc('A full category URL to be used to perform the tests for category page CSS selectors.'),
        'type'          => 'url',
        'placeholder'   => _wpcc('A category URL that will be used for tests...'),
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-selector-with-attribute', [
        'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::CATEGORY_POST_LINK_SELECTORS,
        'title'         =>  _wpcc('Category Post URL Selectors'),
        'info'          =>  _wpcc('CSS selectors for the post URLs in category pages. Gets "href" attributes of "a" tags.
            E.g. <span class="highlight selector">article.post > h2 > a</span>. When testing, make sure you
            filled the category test URL. If you give more than one selector, each selector will be used
            to get URLs and the results will be combined.'),
        'urlSelector'   =>  $testUrlCategorySelector,
        'data'          =>  [
            'targetTag'              =>  'a',
            'selectorFinderBehavior' =>  \WPCCrawler\Objects\Enums\SelectorFinderBehavior::SIMILAR,
        ],
        'inputClass'    => 'css-selector',
        'showDevTools'  => true,
        'defaultAttr'   => 'href',
        'optionsBox'    => true,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::CATEGORY_COLLECT_IN_REVERSE_ORDER,
        'title' =>  _wpcc('Collect URLs in reverse order?'),
        'info'  =>  _wpcc('When you check this, the URLs found by URL selectors will be ordered in reverse before
                they are saved into the database. Therefore, the posts will be saved in reverse order for
                each category page.')
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('partials.table-section-title', ['title' => _wpcc("Next Page")], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-selector-with-attribute', [
        'name'          => \WPCCrawler\Objects\Settings\Enums\SettingKey::CATEGORY_NEXT_PAGE_SELECTORS,
        'title'         => _wpcc('Category Next Page URL Selectors'),
        'info'          => _wpcc('CSS selectors for next page URL in a category page. Gets "href" attributes of "a" tags.
            E.g. <span class="highlight selector">.pagination > a.next</span>. When testing, make sure you
            filled the category test URL. If you give more than one selector, the first
            match will be used.'),
        'urlSelector'   =>  $testUrlCategorySelector,
        'data' =>  [
            'targetTag'              => 'a',
            'targetCssSelectors'     => ['link[rel="next"]'],
            'selectorFinderBehavior' => \WPCCrawler\Objects\Enums\SelectorFinderBehavior::UNIQUE,
        ],
        'defaultAttr'   => 'href',
        'optionsBox'    => true

    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('partials.table-section-title', ['title' => _wpcc("Featured Images")], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::CATEGORY_POST_SAVE_THUMBNAILS,
        'class'         =>  'label-thumbnail',
        'title'         =>  _wpcc('Save featured images?'),
        'info'          =>  _wpcc('If there are featured images for each post on category page and you want to
            save the featured images for the posts, check this.'),
        'dependants'    => '[
            "#category-post-thumbnail-selectors",
            "#category-thumbnail-test-url",
            "#category-thumbnail-find-replace",
            "#category-post-link-is-before-thumbnail"
        ]',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-selector-with-attribute', [
        'name'          => \WPCCrawler\Objects\Settings\Enums\SettingKey::CATEGORY_POST_THUMBNAIL_SELECTORS,
        'class'         =>  'label-thumbnail',
        'title'         => _wpcc('Featured Image Selectors'),
        'info'          => _wpcc('CSS selectors for post featured images in a category page. Gets "src" attributes of "img" tags.
            E.g. <span class="highlight selector">.post-item > img</span>. When testing, make sure you
            filled the category test URL. If you give more than one selector, the first match will be used.'),
        'urlSelector'   =>  $testUrlCategorySelector,
        'data'          =>  [
            'targetTag'              =>  'img',
            'selectorFinderBehavior' =>  \WPCCrawler\Objects\Enums\SelectorFinderBehavior::SIMILAR,
        ],
        'inputClass'    => 'css-selector',
        'showDevTools'  => true,
        'defaultAttr'   => 'src',
        'id'            => 'category-post-thumbnail-selectors',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.input-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::TEST_FIND_REPLACE_THUMBNAIL_URL_CAT,
        'class' =>  'label-thumbnail',
        'title' =>  _wpcc('Test Featured Image URL'),
        'info'  =>  _wpcc('A full image URL to be used to perform tests for the find-replace settings
            for featured image URL.'),
        'id'    => 'category-thumbnail-test-url',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-find-replace-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::CATEGORY_FIND_REPLACE_THUMBNAIL_URL,
        'class' =>  'label-thumbnail',
        'title' => _wpcc("Find and replace in featured image URL"),
        'info'  => _wpcc('If you want some things to be replaced with some other things in <b>URL of the
            featured image</b>, this is the place. The replacement will be done before saving the image.'),
        'data'  =>  [
            'subjectSelector'   =>  sprintf("#%s", \WPCCrawler\Objects\Settings\Enums\SettingKey::TEST_FIND_REPLACE_THUMBNAIL_URL_CAT),
        ],
        'id'    => 'category-thumbnail-find-replace'
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::CATEGORY_POST_IS_LINK_BEFORE_THUMBNAIL,
        'class' =>  'label-thumbnail',
        'title' =>  _wpcc('Post links come before featured images?'),
        'info'  =>  _wpcc("If the links for the posts in the category page come before the featured images,
            considering the position of the featured image and link in the HTML of the page, check this."),
        'id'    =>  'category-post-link-is-before-thumbnail',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('site-settings.partial.html-manipulation-inputs', [
        "keyTestUrl" => \WPCCrawler\Objects\Settings\Enums\SettingKey::TEST_URL_CATEGORY,
        "keyFactory" => \WPCCrawler\Objects\Settings\Factory\HtmlManip\CategoryHtmlManipKeyFactory::getInstance(),
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('partials.table-section-title', ['title' => _wpcc("Unnecessary Elements")], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-selector', [
        'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::CATEGORY_UNNECESSARY_ELEMENT_SELECTORS,
        'title'         =>  _wpcc('Unnecessary Element Selectors'),
        'info'          =>  _wpcc('CSS selectors for unwanted elements in the category page. Specified elements will be
            removed from the HTML of the page. Content extraction will be done after the page is cleared
            from unnecessary elements.'),
        'urlSelector'   => $testUrlCategorySelector,
        'inputClass'    => 'css-selector',
        'showDevTools'  => true,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('partials.table-section-title', ['title' => _wpcc("Filters")], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.filter-with-label', [
        'name'        => \WPCCrawler\Objects\Settings\Enums\SettingKey::CATEGORY_REQUEST_FILTERS,
        'title'       => _wpcc('Category request filters'),
        'info'        => _wpcc('Define filters that are related to the requests made to crawl category pages.') . _wpcc_filter(true),
        'eventGroup'  => \WPCCrawler\Objects\Events\Enums\EventGroupKey::CATEGORY_REQUEST,
        'filterClass' => 'request-filter',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.filter-with-label', [
        'name'        => \WPCCrawler\Objects\Settings\Enums\SettingKey::CATEGORY_PAGE_FILTERS,
        'title'       => _wpcc('Category page filters'),
        'info'        => _wpcc('Define filters that will be applied in the category pages.') . _wpcc_filter(true),
        'eventGroup'  => \WPCCrawler\Objects\Events\Enums\EventGroupKey::CATEGORY_PAGE,
        'filterClass' => 'page-filter',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.filter-with-label', [
        'name'       =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::CATEGORY_DATA_FILTERS,
        'title'      =>  _wpcc('Category data filters'),
        'info'       =>  _wpcc('Define filters that will be applied to the category data.') . _wpcc_filter(true),
        'eventGroup' =>  \WPCCrawler\Objects\Events\Enums\EventGroupKey::CATEGORY_DATA
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('partials.table-section-title', ['title' => _wpcc("Notifications")], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-selector-with-attribute', [
        'name'          => \WPCCrawler\Objects\Settings\Enums\SettingKey::CATEGORY_NOTIFY_EMPTY_VALUE_SELECTORS,
        'title'         => _wpcc('CSS selectors for empty value notification'),
        'info'          => _wpcc('Write CSS selectors and their attributes you want to retrieve. If the retrieved value
            is empty, you will be notified via email. These CSS selectors will be tried to be retrieved after all
            replacements are applied.'),
        'urlSelector'   =>  $testUrlCategorySelector,
        'defaultAttr'   => 'text',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php

    /** @var int $postId */
    /** @var array $settings */
    /**
     * Fires before closing table tag in category tab of site settings page.
     *
     * @param array $settings   Existing settings and their values saved by user before
     * @param int $postId       ID of the site
     * @since 1.6.3
     */
    do_action('wpcc/view/site-settings/tab/category', $settings, $postId);

    ?>

</table>
<?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/site-settings/tab-category.blade.php ENDPATH**/ ?>