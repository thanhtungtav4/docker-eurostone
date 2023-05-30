<div class="wcc-settings-title">
    <h3><?php echo e(_wpcc('Post Page Settings')); ?></h3>
    <span><?php echo e(_wpcc("A post page is a page that contains the data that can be used to create posts in your site. For
    example, an article page of a blog, a product page of an e-commerce site or a hotel's page in a booking site can be
    considered as post pages. Here, you can configure many settings to define what information should be saved from
    target post pages.")); ?></span>
</div>


<?php echo $__env->make('partials.tab-section-navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php

// URL selector for all inputs that require a $urlSelector parameter.
$urlSelector = sprintf('#%s', \WPCCrawler\Objects\Settings\Enums\SettingKey::TEST_URL_POST);
$testFindReplaceFirstLoadSelector = sprintf('#%s', \WPCCrawler\Objects\Settings\Enums\SettingKey::TEST_FIND_REPLACE_FIRST_LOAD)

?>

<table class="wcc-settings">
    
    <?php echo $__env->make('form-items.combined.input-with-label', [
        'name'          => \WPCCrawler\Objects\Settings\Enums\SettingKey::TEST_URL_POST,
        'title'         => _wpcc('Test Post URL'),
        'info'          => _wpcc('A full post URL to be used to perform the tests for post page CSS selectors.'),
        'type'          => 'url',
        'placeholder'   => _wpcc('A post URL that will be used for tests...'),
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-selector-with-attribute', [
        'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_TITLE_SELECTORS,
        'title'         =>  _wpcc('Post Title Selectors'),
        'info'          =>  _wpcc('CSS selectors for post title. E.g. <span class="highlight selector">h1</span>. This
            gets text of the specified element. If you give more than one selector, the first match will
            be used.'),
        'urlSelector'   =>  $urlSelector,
        'data'          =>  [
            'targetCssSelectors'     => ['h1'],
            'selectorFinderBehavior' => \WPCCrawler\Objects\Enums\SelectorFinderBehavior::UNIQUE,
        ],
        'inputClass'    => 'css-selector',
        'showDevTools'  => true,
        'defaultAttr'   => 'text',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-selector-with-attribute', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_EXCERPT_SELECTORS,
        'title' =>  _wpcc('Post Excerpt Selectors'),
        'info'  =>  _wpcc('CSS selectors for the post excerpt, if exists. E.g. <span class="highlight selector">p.excerpt</span>.
            This gets html of the specified element. If you give more than one selector, the first match will
            be used.'),
        'urlSelector'   =>  $urlSelector,
        'inputClass'    => 'css-selector',
        'showDevTools'  => true,
        'defaultAttr'   => 'html',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-selector-with-attribute', [
        'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_CONTENT_SELECTORS,
        'title'         =>  _wpcc('Post Content Selectors'),
        'info'          =>  _wpcc('CSS selectors for the post content. This gets HTML of specified element. E.g.
            <span class="highlight selector">.post-content > p</span>. If you give more than one selector,
            each match will be crawled and the results will be merged.'),
        'urlSelector'   =>  $urlSelector,
        'data'          =>  [
            'selectorFinderBehavior' => \WPCCrawler\Objects\Enums\SelectorFinderBehavior::CONTAINS,
        ],
        'inputClass'    => 'css-selector',
        'showDevTools'  => true,
        'defaultAttr'   => 'html',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-selector-with-attribute', [
        'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_TAG_SELECTORS,
        'title'         =>  _wpcc('Post Tag Selectors'),
        'info'          =>  _wpcc('CSS selectors for post tags. By default, this gets "text" of the specified
            elements. You can also use any attribute of the elements. If you give more than one selector,
            the results will be combined to create post tags.'),
        'urlSelector'   =>  $urlSelector,
        'data'          =>  [
            'selectorFinderBehavior' => \WPCCrawler\Objects\Enums\SelectorFinderBehavior::SIMILAR,
        ],
        'defaultAttr'   => 'text',
        'optionsBox'    => true,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-selector-with-attribute', [
        'name'          => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_SLUG_SELECTORS,
        'title'         => _wpcc('Post Slug (Permalink) Selectors'),
        'info'          => _wpcc('CSS selectors for post slug. The slug is the post name in the URL of the saved post.
                            If the slug is not unique, a unique slug will be generated from the found slug. If a slug
                            is not found, the slug will automatically be generated from the post title.')
                            . ' ' . _wpcc_trans_multiple_selectors_first_match(),
        'optionsBox'    => true,
        'urlSelector'   => $urlSelector,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('partials.table-section-title', ['title' => _wpcc("Category")], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-selector-with-attribute', [
        'name'          => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_CATEGORY_NAME_SELECTORS,
        'title'         => _wpcc('Category Name Selectors'),
        'info'          => _wpcc("CSS selectors for category names. Found names will be used to assign the post's
            categories. If a category with a found name does not exist, it will be created. This gets text of the found
            element by default."),
        'optionsBox'    => true,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_CATEGORY_ADD_ALL_FOUND_CATEGORY_NAMES,
        'title' => _wpcc('Add all found category names?'),
        'info'  => _wpcc("Check this if you want to add all categories found by category name selectors. Otherwise,
            when there are multiple selectors, only the results of the first match will be used."),
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-text-with-label', [
        'name'          => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_CATEGORY_NAME_SEPARATORS,
        'title'         => _wpcc('Category Name Separators'),
        'info'          => _wpcc("Set separators for category names. For example, if a category name selector finds
            'shoes, women, casual', when you add ',' as separator, there will be three categories as
            'shoes', 'women', and 'casual'. Otherwise, the category name will be 'shoes, women, casual'. If you add
            more than one separator, all will be applied."),
        'placeholder'   => _wpcc('Separator...')
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_CATEGORY_ADD_HIERARCHICAL,
        'title' => _wpcc('Add as subcategories?'),
        'info'  => _wpcc("When you check this, if there are more than one category name found by a single selector input,
            each category name that comes after a category name in the found category names will be considered as the
            previous category name's child category. This option applies to a single selector input. When there are
            multiple selector inputs, their results will <b>not</b> be combined to create a subcategory hierarchy. As an
            example, let's say a selector found three category names as 'shoes', 'women', and 'casual'. When you check
            this option, the post will be in 'shoes > women > casual' category. However, if these three categories
            are found by three different selectors, the post will have 'shoes', 'women', and 'casual' categories
            separately, not hierarchically. If you do not want to create a subcategory hierarchy, do not check this.
            When you check this, all categories will be added under the category defined in the
            category URLs."),
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_CATEGORY_DO_NOT_ADD_CATEGORY_IN_MAP,
        'title' => _wpcc('Do not add the category defined in the category URLs?'),
        'info'  => _wpcc("Check this if you do not want the post to have the category defined in
            the category URLs. This option will be applied only if at least one category is found
            by category name selectors."),
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $postDetailSettingsViews; ?>


    
    <?php echo $__env->make('partials.table-section-title', ['title' => _wpcc("Date")], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-selector-with-attribute', [
        'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_DATE_SELECTORS,
        'title'         =>  _wpcc('Post Date Selectors'),
        'info'          =>  sprintf(_wpcc('CSS selectors for post date.
            E.g. <span class="highlight selector">[itemprop="datePublished"]</span>. If you give more than one
            selector, then the first match will be used. Found date will be parsed by %1$s function. So, if
            the date found by the selectors cannot be parsed properly, you need to use find-and-replace options
            to change the date into a suitable format. Generally, sites show the date via meta tags in a format
            like %2$s. This format will be parsed without any issues.'),
            '<a target="_blank" href="http://php.net/manual/en/function.strtotime.php">strtotime</a>',
            '<b>2017-02-27T05:00:17-05:00</b>'
        ),
        'urlSelector'   =>  $urlSelector,
        'data'          =>  [
            'targetCssSelectors' =>  [
                'meta[itemprop="datePublished"]',
                'meta[itemprop="dateCreated"]',
                'time.published',
                'time.entry-date'
            ],
        ],
        'defaultAttr'   => 'content',
        'optionsBox'    => true,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.input-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::TEST_FIND_REPLACE_DATE,
        'title' =>  _wpcc('Test Date'),
        'info'  =>  _wpcc('A date to be used to perform tests for the find-replace settings for dates.')
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-find-replace-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_FIND_REPLACE_DATE,
        'title' => _wpcc("Find and replace in dates"),
        'info'  => _wpcc('If you want some things to be replaced with some other things in <b>found post dates</b>,
            this is the place. The replacement will be done before parsing the date.'),
        'data'  =>  [
            'subjectSelector' => sprintf('#%s', \WPCCrawler\Objects\Settings\Enums\SettingKey::TEST_FIND_REPLACE_DATE),
        ],
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.input-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_DATE_ADD_MINUTES,
        'title' =>  _wpcc('Minutes that should be added to the final date'),
        'info'  =>  sprintf(_wpcc('How many minutes should be added to the final date of the post. If the final date
            becomes greater than now, the post will be scheduled. If you write a negative number, it will be
            subtracted from the date. Write comma-separated numbers to randomize. You can write the same number
            multiple times to increase its chance to be selected. <b>This setting will be applied even if you do
            not supply any date selectors.</b> Example values: <b>%1$s</b> or <b>%2$s</b>'),
                "10",
                "10, -10, 25, 25, 25"
        )
   ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('partials.table-section-title', ['title' => _wpcc("Meta")], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_META_KEYWORDS,
        'class'         =>  'label-meta',
        'title'         =>  _wpcc('Save meta keywords?'),
        'info'          =>  _wpcc('Check this if you want to save meta keywords of the target post.'),
        'dependants'    => '["#meta-keywords-as-tags"]'
   ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_META_KEYWORDS_AS_TAGS,
        'class' =>  'label-meta',
        'title' =>  _wpcc('Add meta keywords as tags?'),
        'info'  =>  _wpcc('Check this if you want each meta keyword should be added as tag to the crawled post.'),
        'id'    => 'meta-keywords-as-tags',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_META_DESCRIPTION,
        'class' =>  'label-meta',
        'title' =>  _wpcc('Save meta description?'),
        'info'  =>  _wpcc('Check this if you want to save meta description of the target post.')
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('partials.table-section-title', ['title' => _wpcc("Featured Image")], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_SAVE_THUMBNAILS_IF_NOT_EXIST,
        'class'         =>  'label-thumbnail',
        'title'         =>  _wpcc('Save featured image, if it is not found in category page?'),
        'info'          =>  _wpcc('If you want to save an image from post page as featured image, when there is no
            featured image found in category page, check this.'),
        'dependants'    => '[
            "#post-thumbnail-selectors",
            "#post-thumbnail-test-url",
            "#post-thumbnail-find-replace"
        ]',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-selector-with-attribute', [
        'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_THUMBNAIL_SELECTORS,
        'class'         =>  'label-thumbnail',
        'title'         =>  _wpcc('Featured Image Selectors'),
        'info'          =>  _wpcc('CSS selectors for featured image <b>in HTML of the post page</b>. This gets the "src"
            attribute of <b>the first found element</b>. If you give more than one selector, the first match will
            be used. E.g. <span class="highlight selector">img.featured</span>'),
        'urlSelector'   =>  $urlSelector,
        'data'          =>  [
            'targetTag'     =>  'img',
        ],
        'inputClass'    => 'css-selector',
        'showDevTools'  => true,
        'defaultAttr'   => 'src',
        'optionsBox'    => true,
        'id'            => 'post-thumbnail-selectors',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.input-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::TEST_FIND_REPLACE_THUMBNAIL_URL,
        'class' =>  'label-thumbnail',
        'title' =>  _wpcc('Test Featured Image URL'),
        'info'  =>  _wpcc('A full image URL to be used to perform tests for the find-replace settings
            for featured image URL.'),
        'id'    => 'post-thumbnail-test-url',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-find-replace-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_FIND_REPLACE_THUMBNAIL_URL,
        'class' =>  'label-thumbnail',
        'title' => _wpcc("Find and replace in featured image URL"),
        'info'  => _wpcc('If you want some things to be replaced with some other things in <b>URL of the
            featured image</b>, this is the place. The replacement will be done before saving the image.'),
        'data'  =>  [
            'subjectSelector'   =>  sprintf('#%s', \WPCCrawler\Objects\Settings\Enums\SettingKey::TEST_FIND_REPLACE_THUMBNAIL_URL),
        ],
        'id'    => 'post-thumbnail-find-replace',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('partials.table-section-title', ['title' => _wpcc("Images")], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_SAVE_ALL_IMAGES_IN_CONTENT,
        'class' =>  'label-save-images',
        'title' =>  _wpcc('Save all images in the post content?'),
        'info'  =>  sprintf(_wpcc('If you want all the images in the post content to be saved as media and included in
            the post from your server, check this. <b>This is the same as checking "save images as media" and
            writing %1$s to the image selectors. </b>'),
                '<b><span class="highlight selector">img</span></b>'
            ) . " " . _wpcc_trans_save_image_note()
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_SAVE_IMAGES_AS_MEDIA,
        'class' =>  'label-save-images',
        'title' =>  _wpcc('Save images as media?'),
        'info'  =>  _wpcc('If you want the images in the post content to be saved as media and included in
            the post from your server, check this.') . " " .  _wpcc_trans_save_image_note(),
        'dependants'    => '[
            "#post-save-images-as-gallery",
            "#post-image-selectors",
            "#post-image-add-link"
        ]'
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <tr id="post-save-images-as-gallery">
        <td colspan="2">

            
            <table class="wcc-settings">
                
                <?php echo $__env->make('form-items.combined.checkbox-with-label', [
                    'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_SAVE_IMAGES_AS_GALLERY,
                    'class' =>  'label-gallery',
                    'title' =>  _wpcc('Save images as gallery?'),
                    'info'  =>  _wpcc('If you want to save specific images as a gallery, check this.'),
                    'dependants'    => '[
                        "#post-gallery-image-selectors",
                        "#post-gallery-save-as-woocommerce-gallery"
                    ]'
                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                
                <?php echo $__env->make('form-items.combined.multiple-selector', [
                    'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_GALLERY_IMAGE_SELECTORS,
                    'class'         =>  'label-gallery',
                    'title'         =>  _wpcc('Gallery Image URL Selectors'),
                    'info'          =>  _wpcc('CSS selectors for <b>image URLs in the HTML of the page</b>. This gets the
                        "src" attribute of specified element by default. If you give more than one selector, each
                        match will be used when saving images and creating the gallery. Note that these elements
                        will be removed from the HTML after URL is acquired from them.'),
                    'urlSelector'   =>  $urlSelector,
                    'data'          =>  [
                        'testType'               =>  \WPCCrawler\Test\Test::$TEST_TYPE_SELECTOR_ATTRIBUTE,
                        'attr'                   =>  'src',
                        'targetTag'              =>  'img',
                        'selectorFinderBehavior' => \WPCCrawler\Objects\Enums\SelectorFinderBehavior::SIMILAR
                    ],
                    'addonClasses'  => 'wcc-test-selector',
                    'optionsBox'    => true,
                    'id'            => 'post-gallery-image-selectors',
                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                
                <?php if(class_exists("WooCommerce")): ?>
                    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
                        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_SAVE_IMAGES_AS_WOOCOMMERCE_GALLERY,
                        'class' =>  'label-gallery',
                        'title' =>  _wpcc('Save images as WooCommerce product gallery?'),
                        'info'  =>  _wpcc("If you set post type as WooCommerce product and you want to save
                            the gallery as the product's gallery, check this."),
                        'id'    =>  'post-gallery-save-as-woocommerce-gallery',
                    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php endif; ?>
            </table>
        </td>

    </tr>

    
    <?php echo $__env->make('form-items.combined.multiple-selector', [
        'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_IMAGE_SELECTORS,
        'class'         =>  'label-save-images',
        'title'         =>  _wpcc('Image URL Selectors'),
        'info'          =>  _wpcc('CSS selectors for images <b>in the post content</b>. This gets the "src" attribute of
            specified element. If you give more than one selector, each match will be used when saving
            images. E.g. <b><span class="highlight selector">img</span> will save all images in the post content.</b>'),
        'urlSelector'   =>  $urlSelector,
        'data'          =>  [
            'testType'      =>  \WPCCrawler\Test\Test::$TEST_TYPE_SELECTOR_ATTRIBUTE,
            'targetTag'     =>  'img',
            'attr'          =>  'src',
        ],
        'inputClass'    => 'css-selector',
        'showDevTools'  => true,
        'addonClasses'  => 'wcc-test-selector',
        'optionsBox'    => true,
        'id'            => 'post-image-selectors',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.input-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::TEST_FIND_REPLACE_IMAGE_URLS,
        'class' =>  'label-save-images',
        'title' =>  _wpcc('Test Image URL'),
        'info'  =>  _wpcc('A full image URL to be used to perform tests for the find-replace settings for image URLs.'),
        'id' => 'post-image-test-url',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-find-replace-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_FIND_REPLACE_IMAGE_URLS,
        'class' =>  'label-save-images',
        'title' => _wpcc("Find and replace in image URLs"),
        'info'  => _wpcc('If you want some things to be replaced with some other things in <b>URLs of the
            found images</b>, this is the place. The replacement will be done before saving the image.'),
        'data'  =>  [
            'subjectSelector'   =>  sprintf('#%s', \WPCCrawler\Objects\Settings\Enums\SettingKey::TEST_FIND_REPLACE_IMAGE_URLS),
        ],
        'id'    => 'post-image-find-replace',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('partials.table-section-title', ['title' => _wpcc("Custom Short Codes")], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <tr>
        <td>
            <?php echo $__env->make('form-items/label', [
                'for'   =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_CUSTOM_CONTENT_SHORTCODE_SELECTORS,
                'title' =>  _wpcc('Custom Content Selectors for Shortcodes'),
                'info'  =>  _wpcc('CSS selectors for HTML elements whose contents can be used in post template
                    by defined shortcode. If more than one element is found, their content will be merged. If
                    you do not want them merged, check the "single" checkbox to get the first found result.
                    By default, this gets HTML of the found element. If you want the text of the target element,
                    write "text" for attribute. You can also use any other attribute of the found element, such
                    as "src", "href"... Write your shortcodes without brackets, e.g. <b>"item-price"</b>. Next, you
                    can use it <b>in the main post template by writing [item-price]</b>')
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </td>
        <td>
            <?php echo $__env->make('form-items/multiple', [
                'include'       =>  'form-items/selector-custom-shortcode',
                'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_CUSTOM_CONTENT_SHORTCODE_SELECTORS,
                'addon'         =>  'dashicons dashicons-search',
                'data'          =>  [
                    'urlSelector'   =>  $urlSelector,
                    'testType'      =>  \WPCCrawler\Test\Test::$TEST_TYPE_SELECTOR_ATTRIBUTE,
                    'attr'          =>  'html'
                ],
                'test'          => true,
                'addKeys'       => true,
                'addonClasses'  => 'wcc-test-selector-attribute',
                'defaultAttr'   => 'html',
                'optionsBox'    => true,
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php echo $__env->make('partials/test-result-container', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </td>
    </tr>

    
    <tr>
        <td>
            <?php echo $__env->make('form-items/label', [
                'for'   => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_FIND_REPLACE_CUSTOM_SHORT_CODE,
                'title' => _wpcc("Find and replace in custom short codes"),
                'info'  => _wpcc('If you want some things to be replaced with some other things in <b>custom short code
                    contents</b>, this is the place. <b>The replacements will be applied after custom short code
                    contents are retrieved</b>.') . " " . _wpcc_trans_regex()
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </td>
        <td>
            <?php echo $__env->make('form-items/multiple', [
                'include'       =>  'form-items/find-replace-in-custom-short-code',
                'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_FIND_REPLACE_CUSTOM_SHORT_CODE,
                'addKeys'       =>  true,
                'remove'        =>  true,
                'addon'         =>  'dashicons dashicons-search',
                'data'          =>  [
                    'urlSelector'       =>  $urlSelector,
                    'subjectSelector'   =>  $testFindReplaceFirstLoadSelector,
                    'testType'          =>  \WPCCrawler\Test\Test::$TEST_TYPE_FIND_REPLACE_IN_CUSTOM_SHORT_CODE,
                    'requiredSelectors' =>  "{$urlSelector} | {$testFindReplaceFirstLoadSelector}", // One of them is enough

                ],
                'test'          => true,
                'addonClasses'  => 'wcc-test-find-replace-in-custom-short-code'
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php echo $__env->make('partials/test-result-container', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </td>
    </tr>

    
    <?php echo $__env->make('partials.table-section-title', ['title' => _wpcc("List Type Posts")], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_IS_LIST_TYPE,
        'class' =>  'label-list',
        'title' =>  _wpcc('Posts are list type?'),
        'info'  =>  _wpcc('If the target post is list type, and you want to import it as a list, check this.'),
        'dependants'    => '[
                "#list-title-selector",
                "#list-content-selector",
                "#list-item-number-selectors",
                "#list-items-start-after-selectors",
                "#list-insert-reversed",
                "#list-item-auto-number"
            ]'
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-selector', [
        'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_LIST_ITEM_STARTS_AFTER_SELECTORS,
        'class'         =>  'label-list',
        'title'         =>  _wpcc('List Items Start After Selectors'),
        'info'          =>  _wpcc("CSS selectors for the elements coming just before the first list item. This will be
            used to detect list item contents accurately. The position of the first match of any given selector will be
            compared to others and the greatest position will be used. You can give a selector for the first item. It'll
            do the job. E.g. <span class='highlight selector'>.entry > .list-item</span>"),
        'urlSelector'   =>  $urlSelector,
        'data'          =>  [
            'testType'               =>  \WPCCrawler\Test\Test::$TEST_TYPE_FIRST_POSITION,
            'selectorFinderBehavior' =>  \WPCCrawler\Objects\Enums\SelectorFinderBehavior::UNIQUE,
        ],
        'inputClass'    => 'css-selector',
        'showDevTools'  => true,
        'id'    => 'list-items-start-after-selectors',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-selector-with-attribute', [
        'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_LIST_ITEM_NUMBER_SELECTORS,
        'class'         =>  'label-list',
        'title'         =>  _wpcc('List Item Number Selectors'),
        'info'          =>  _wpcc("CSS selectors for each list item's number, if the target post is list type. This gets
            the text of specified element. If you give more than one selector, the first match will
            be used."),
        'urlSelector'   =>  $urlSelector,
        'data'          =>  [
            'selectorFinderBehavior' =>  \WPCCrawler\Objects\Enums\SelectorFinderBehavior::SIMILAR,
        ],
        'inputClass'    => 'css-selector',
        'defaultAttr'   => 'text',
        'showDevTools'  => true,
        'id'            => 'list-item-number-selectors'
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_LIST_ITEM_AUTO_NUMBER,
        'class' =>  'label-list',
        'title' =>  _wpcc('Insert list item numbers automatically?'),
        'info'  =>  _wpcc('If you want to insert list item numbers automatically when there is no item number,
            then check this. The items will be numbered starting from 1.'),
        'id'    =>  'list-item-auto-number',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-selector-with-attribute', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_LIST_TITLE_SELECTORS,
        'class' =>  'label-list',
        'title' =>  _wpcc('List Item Title Selectors'),
        'info'  =>  _wpcc("CSS selectors for each list item's title, if the target post is list type. This gets
            the text of specified element. If you give more than one selector, the first match will
            be used."),
        'urlSelector'   =>  $urlSelector,
        'data'          =>  [
            'selectorFinderBehavior' =>  \WPCCrawler\Objects\Enums\SelectorFinderBehavior::SIMILAR,
        ],
        'inputClass'    => 'css-selector',
        'defaultAttr'   => 'text',
        'showDevTools'  => true,
        'id'            => 'list-title-selector'
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-selector-with-attribute', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_LIST_CONTENT_SELECTORS,
        'class' =>  'label-list',
        'title' =>  _wpcc('List Item Content Selectors'),
        'info'  =>  _wpcc("CSS selector for each list item's content, if the target post is list type. This gets
            the HTML of specified element. If you give more than one selector, the results will be
            combined when creating each list item's content."),
         'urlSelector'   =>  $urlSelector,
         'data'          =>  [
             'selectorFinderBehavior' =>  \WPCCrawler\Objects\Enums\SelectorFinderBehavior::SIMILAR,
         ],
         'inputClass'    => 'css-selector',
         'defaultAttr'   => 'html',
         'showDevTools'  => true,
         'id'            => 'list-content-selector'
     ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_LIST_INSERT_REVERSED,
        'class' =>  'label-list',
        'title' =>  _wpcc('Insert list in reverse order?'),
        'info'  =>  _wpcc('If you want to insert the list into the post in reverse order, then check this.'),
        'id'    =>  'list-insert-reversed',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('partials.table-section-title', ['title' => _wpcc("Pagination")], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_PAGINATE,
        'title'         =>  _wpcc('Paginate posts?'),
        'info'          =>  _wpcc('If the target post is paginated, and you want it to be imported as paginated, check this.'),
        'class'         =>  'label-paginate',
        'dependants'    => '["#post-next-page-url-selector", "#post-all-page-urls-selectors", "#post-save-as-single-page"]'
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-selector-with-attribute', [
        'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_NEXT_PAGE_URL_SELECTORS,
        'title'         =>  _wpcc('Post Next Page URL Selectors'),
        'info'          =>  _wpcc('CSS selector for next page URL, used to get "href" attribute of "a" tag. E.g.
            <span class="highlight selector">.pagination > a.next</span>. If you give more than one selector,
            the first match will be used.'),
        'class'         =>  'label-paginate',
        'urlSelector'   =>  $urlSelector,
        'data'          =>  [
            'targetTag'              =>  'a',
            'targetCssSelectors'     => ['link[rel="next"]'],
            'selectorFinderBehavior' =>  \WPCCrawler\Objects\Enums\SelectorFinderBehavior::UNIQUE,
        ],
        'defaultAttr'   => 'href',
        'optionsBox'    => true,
        'id'            => 'post-next-page-url-selector'
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-selector-with-attribute', [
        'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_NEXT_PAGE_ALL_PAGES_URL_SELECTORS,
        'title'         =>  _wpcc('Post All Page URLs Selectors'),
        'info'          =>  _wpcc('CSS selectors for all page URLs. Sometimes there is no next page URL. Instead, the
            post page has all of the post pages (or parts) in one place. If this is the case, you should
            use this. This is used to get "href" attribute of "a" tag. E.g. <span class="highlight selector">.post > .parts > a</span>.
            If you give more than one selector, then the first match will be used.'),
        'class'         =>  'label-paginate',
        'urlSelector'   =>  $urlSelector,
        'data'          =>  [
            'targetTag'              =>  'a',
            'selectorFinderBehavior' =>  \WPCCrawler\Objects\Enums\SelectorFinderBehavior::SIMILAR,
        ],
        'defaultAttr'   => 'href',
        'optionsBox'    => true,
        'id'            => 'post-all-page-urls-selectors'
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_SAVE_AS_SINGLE_PAGE,
        'title'         =>  _wpcc('Save as single page?'),
        'info'          =>  _wpcc('If you want to collect all pages of the target post into a single page, check this.
            When this is checked, the saved post will have only one page containing the content of all of the pages of
            the target post.'),
        'class'         =>  'label-paginate',
        'id'            =>  'post-save-as-single-page',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('partials.table-section-title', ['title' => _wpcc("Post Meta")], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <tr>
        <td>
            <?php echo $__env->make('form-items/label', [
                'for'   => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_CUSTOM_META_SELECTORS,
                'title' => _wpcc('Custom Meta Selectors'),
                'info'  => _wpcc('CSS selectors for custom meta values. You can use this to save anything from
                    target post as post meta of to-be-saved post. You can write "html", "text", or an attribute
                    of the target element for attribute input.')
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </td>
        <td>
            <?php echo $__env->make('form-items/multiple', [
                'include'       => 'form-items/selector-custom-post-meta',
                'name'          => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_CUSTOM_META_SELECTORS,
                'addon'         =>  'dashicons dashicons-search',
                'data'          =>  [
                    'urlSelector'   =>  $urlSelector,
                    'testType'      =>  \WPCCrawler\Test\Test::$TEST_TYPE_SELECTOR_ATTRIBUTE,
                    'attr'          =>  'text'
                ],
                'test'          => true,
                'addKeys'       => true,
                'addonClasses'  => 'wcc-test-selector-attribute',
                'defaultAttr'   => 'text',
                'optionsBox'    => true,
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php echo $__env->make('partials/test-result-container', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </td>
    </tr>

    
    <tr>
        <td>
            <?php echo $__env->make('form-items/label', [
                'for'   => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_CUSTOM_META,
                'title' => _wpcc('Custom Meta'),
                'info'  => _wpcc('You can save any value as a post meta. Just write a post meta key and its value.')
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </td>
        <td>
            <?php echo $__env->make('form-items/multiple', [
                'include'       => 'form-items/custom-post-meta',
                'name'          => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_CUSTOM_META,
                'addKeys'       => true,
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php echo $__env->make('partials/test-result-container', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </td>
    </tr>

    
    <tr>
        <td>
            <?php echo $__env->make('form-items/label', [
                'for'   => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_FIND_REPLACE_CUSTOM_META,
                'title' => _wpcc("Find and replace in custom meta"),
                'info'  => _wpcc('If you want some things to be replaced with some other things in <b>custom meta
                    values</b>, this is the place. <b>The replacements will be applied after
                    find-and-replaces for element HTMLs are applied</b>.') . " " . _wpcc_trans_regex()
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </td>
        <td>
            <?php echo $__env->make('form-items/multiple', [
                'include'       =>  'form-items/find-replace-in-custom-meta',
                'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_FIND_REPLACE_CUSTOM_META,
                'addKeys'       =>  true,
                'remove'        =>  true,
                'addon'         =>  'dashicons dashicons-search',
                'data'          =>  [
                    'urlSelector'       =>  $urlSelector,
                    'subjectSelector'   =>  $testFindReplaceFirstLoadSelector,
                    'testType'          =>  \WPCCrawler\Test\Test::$TEST_TYPE_FIND_REPLACE_IN_CUSTOM_META,
                    'requiredSelectors' =>  "{$urlSelector} | {$testFindReplaceFirstLoadSelector}"
                ],
                'test'          => true,
                'addonClasses'  => 'wcc-test-find-replace-in-custom-meta'
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php echo $__env->make('partials/test-result-container', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </td>
    </tr>

    
    <?php echo $__env->make('partials.table-section-title', ['title' => _wpcc("Taxonomies")], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <tr>
        <td>
            <?php echo $__env->make('form-items/label', [
                'for'   => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_CUSTOM_TAXONOMY_SELECTORS,
                'title' => _wpcc('Custom Taxonomy Value Selectors'),
                'info'  => _wpcc('CSS selectors for custom taxonomy values. You can use this to save anything from
                    target post as taxonomy value of to-be-saved post. You can write "html", "text", or an attribute
                    of the target element for attribute input. By default, the first found values will be used. If you
                    want to use all values found by a CSS selector, check the multiple checkbox. If you want to append
                    to any previously-existing values, check the append checkbox. Otherwise, the given value will
                    remove all of the previously-existing values of its taxonomy.')
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </td>
        <td>
            <?php echo $__env->make('form-items/multiple', [
                'include'       => 'form-items/selector-custom-post-taxonomy',
                'name'          => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_CUSTOM_TAXONOMY_SELECTORS,
                'addon'         =>  'dashicons dashicons-search',
                'data'          =>  [
                    'urlSelector'   =>  $urlSelector,
                    'testType'      =>  \WPCCrawler\Test\Test::$TEST_TYPE_SELECTOR_ATTRIBUTE,
                    'attr'          =>  'text'
                ],
                'test'          => true,
                'addKeys'       => true,
                'addonClasses'  => 'wcc-test-selector-attribute',
                'defaultAttr'   => 'text',
                'optionsBox'    => true,
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php echo $__env->make('partials/test-result-container', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </td>
    </tr>

    
    <tr>
        <td>
            <?php echo $__env->make('form-items/label', [
                'for'   => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_CUSTOM_TAXONOMY,
                'title' => _wpcc('Custom Taxonomy Value'),
                'info'  => _wpcc('You can save any value as a value for a taxonomy. Just write a taxonomy and its value.
                    If you want to append to any previously-existing values, check the append checkbox. Otherwise,
                    the given value will remove all of the previously-existing values of its taxonomy.')
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </td>
        <td>
            <?php echo $__env->make('form-items/multiple', [
                'include'       => 'form-items/custom-post-taxonomy',
                'name'          => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_CUSTOM_TAXONOMY,
                'addKeys'       => true,
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php echo $__env->make('partials/test-result-container', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </td>
    </tr>

    
    <?php echo $__env->make('site-settings.partial.html-manipulation-inputs', [
        "keyTestUrl" => \WPCCrawler\Objects\Settings\Enums\SettingKey::TEST_URL_POST,
        "keyFactory" => \WPCCrawler\Objects\Settings\Factory\HtmlManip\PostHtmlManipKeyFactory::getInstance(),
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('partials.table-section-title', ['title' => _wpcc("Unnecessary Elements")], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-selector', [
        'name'          => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_UNNECESSARY_ELEMENT_SELECTORS,
        'title'         =>  _wpcc('Unnecessary Element Selectors'),
        'info'          =>  _wpcc('CSS selectors for unwanted elements in the post page. Specified elements will be
            removed from the HTML of the page. Content extraction will be done after the page is cleared
            from unnecessary elements.'),
        'urlSelector'   =>  $urlSelector,
        'inputClass'    =>  'css-selector',
        'showDevTools'  =>  true,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('partials.table-section-title', ['title' => _wpcc("Filters")], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.filter-with-label', [
        'name'        => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_REQUEST_FILTERS,
        'title'       => _wpcc('Post request filters'),
        'info'        => _wpcc('Define filters that are related to the requests made to crawl post pages.') . _wpcc_filter(true),
        'eventGroup'  => \WPCCrawler\Objects\Events\Enums\EventGroupKey::POST_REQUEST,
        'filterClass' => 'request-filter',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.filter-with-label', [
        'name'        => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_PAGE_FILTERS,
        'title'       => _wpcc('Post page filters'),
        'info'        => _wpcc('Define filters that will be applied in the post pages.') . _wpcc_filter(true),
        'eventGroup'  => \WPCCrawler\Objects\Events\Enums\EventGroupKey::POST_PAGE,
        'filterClass' => 'page-filter',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('partials.table-section-title', ['title' => _wpcc("Notifications")], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.multiple-selector-with-attribute', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_NOTIFY_EMPTY_VALUE_SELECTORS,
        'title' => _wpcc('CSS selectors for empty value notification'),
        'info'  => _wpcc('Write CSS selectors and their attributes you want to retrieve. If the retrieved value
                is empty, you will be notified via email. These CSS selectors will be tried to be retrieved
                after all replacements are applied.'),
        'urlSelector'   =>  $urlSelector,
        'defaultAttr'   => 'text',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('partials.table-section-title', ['title' => _wpcc("Other")], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::POST_TRIGGER_SAVE_POST_HOOK,
        'title' => sprintf(_wpcc('Trigger %1$s hook'), 'save_post'),
        'info'  => sprintf(
           _wpcc('Check this if %1$s hook of WordPress must be triggered after a post is completely saved.'
                . ' Normally, after a post is saved, more information, such as post meta, taxonomies, and attachments,'
                . ' is added to the post, but the hook is not triggered. However, other plugins or the theme might be'
                . ' listening to the post updates to do certain things. If you check this, they will be notified'
                . ' after the post and all the related information are completely saved.'),
            "<span class='highlight'>save_post</span>"
        ),
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php

    /** @var int $postId */
    /** @var array $settings */
    /**
     * Fires before closing table tag in post tab of site settings page.
     *
     * @param array $settings   Existing settings and their values saved by user before
     * @param int $postId       ID of the site
     * @since 1.6.3
     */
    do_action('wpcc/view/site-settings/tab/post', $settings, $postId);

    ?>

</table>
<?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/site-settings/tab-post.blade.php ENDPATH**/ ?>