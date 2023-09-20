<div class="wcc-settings-title">
    <h3><?php echo e(_wpcc('Advanced')); ?></h3>
    <span><?php echo e(_wpcc('Advanced settings for crawler')); ?></span>
</div>

<table class="wcc-settings">

    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'          => \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_MAKE_SURE_ENCODING_UTF8,
        'title'         =>  _wpcc('Always use UTF8 encoding?'),
        'info'          =>  _wpcc('If you want to crawl all pages in UTF-8 encoding, check this.'),
        'dependants'    => '["#convert-encoding"]',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_CONVERT_CHARSET_TO_UTF8,
        'title' => _wpcc('Convert encoding to UTF8 when it is not UTF8'),
        'info'  => _wpcc('If you want to convert the encoding of the HTML retrieved from target sites to UTF8 when
            it has a different encoding, check this.'),
        'id'    => 'convert-encoding',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.input-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_HTTP_USER_AGENT,
        'title' =>  _wpcc('HTTP User Agent'),
        'info'  =>  _wpcc('The user agent to be used when crawling, i.e.
            <span class="highlight variable">HTTP_USER_AGENT</span>. If you leave this empty, the default value
            will be used. You can find user agent strings
            <a target="_blank" href="http://www.useragentstring.com/pages/useragentstring.php">here</a>.')
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.input-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_HTTP_ACCEPT,
        'title' =>  _wpcc('HTTP Accept'),
        'info'  =>  _wpcc('HTTP accept value to be used when crawling, i.e.
            <span class="highlight variable">HTTP_ACCEPT</span>. If you leave this empty, the default value
            will be used.')
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_HTTP_ALLOW_COOKIES,
        'title' =>  _wpcc('Allow cookies?'),
        'info'  =>  _wpcc('If you want to allow cookies when crawling, check this.')
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_DISABLE_SSL_VERIFICATION,
        'title' =>  _wpcc('Disable SSL verification?'),
        'info'  =>  _wpcc('If you do not want to verify the SSL certificate of the target site, check this. Please'
            . ' note that, <b>if you check this, you can no longer trust that the responses are retrieved from the'
            . ' target site</b>. Before you check this, it is recommended that you research on what SSL certificates are'
            . ' used for, to better understand the implications of disabling the SSL verification.')
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.input-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_CONNECTION_TIMEOUT,
        'title' =>  _wpcc('Connection timeout (in seconds)'),
        'info'  =>  _wpcc('Maximum number of seconds in which target server should response. Write 0 to disable.
                Default: 0'),
        'type'  =>  'number',
        'min'   =>  0
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('partials.table-section-title', ['title' => _wpcc("Proxies")], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_USE_PROXY,
        'title'         =>  _wpcc('Use proxy?'),
        'info'          =>  _wpcc('If you want to use a proxy when crawling the target site, check this.'),
        'dependants'    =>  '["#proxy-test-url", "#proxies", "#proxy-try-limit", "#proxy-randomize"]',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.input-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_TEST_URL_PROXY,
        'title' =>  _wpcc('URL for proxy testing'),
        'info'  =>  _wpcc('A URL to be used to perform the proxy test.'),
        'type'  => 'url',
        'id'    => 'proxy-test-url',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <tr id="proxies">
        <td>
            <?php echo $__env->make('form-items/label', [
                'for'   =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_PROXIES,
                'title' =>  _wpcc('Proxies'),
                'info'  =>  _wpcc('You can write your proxies here. Write every proxy in a new line. If you want to
                        use a proxy specifically with a protocol, write the proxy with its protocol. E.g.
                        <span class="highlight proxy">https://192.168.16.1:10</span>, or
                        <span class="highlight proxy">http://192.168.16.1:10</span>. You can also provide proxies
                        that contain a scheme, username and password. E.g.
                        <span class="highlight proxy">http://username:password@192.168.16.1:10</span>. If you do not
                        specify the protocol, TCP will be used. SOCKS is not supported.')
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </td>
        <td>
            <?php echo $__env->make('form-items/textarea', [
                'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_PROXIES,
                'placeholder'   =>  _wpcc('New line-separated proxies...'),
                'data'          =>  [
                    'urlSelector'   =>  sprintf('#%s', \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_TEST_URL_PROXY),
                    'testType'      =>  \WPCCrawler\Test\Test::$TEST_TYPE_PROXY,
                ],
                'addon'         =>  'dashicons dashicons-search',
                'test'          =>  true,
                'addonClasses'  => 'wcc-test-proxy',
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php echo $__env->make('partials/test-result-container', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </td>
    </tr>

    
    <?php echo $__env->make('form-items.combined.input-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_PROXY_TRY_LIMIT,
        'title' =>  _wpcc('Proxy try limit'),
        'info'  =>  _wpcc('Maximum number of proxies that can be tried for one request. Write 0 for no limitation.
                Default: 0'),
        'type'  =>  'number',
        'min'   =>  0,
        'id'    =>  'proxy-try-limit',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('form-items.combined.checkbox-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_PROXY_RANDOMIZE,
        'title' =>  _wpcc('Randomize proxies'),
        'info'  =>  _wpcc('When you check this, the proxies you entered will be randomized. This means, the order
            of the proxies will be changed every time before a new request is made. If you do not check this,
            the proxies will be tried in the order you entered them.'),
        'id'    =>  'proxy-randomize',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php if($isGeneralPage): ?>

        
        <?php echo $__env->make('partials.table-section-title', ['title' => _wpcc("Other")], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        
        <?php echo $__env->make('form-items.combined.checkbox-with-label', [
            'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_DISABLE_TOOLTIP,
            'title' =>  _wpcc('Disable Tooltip'),
            'info'  =>  _wpcc("Tooltip is used to show you certain messages when you, for example, hover over a button
                or a form item, such as a checkbox. Because other plugins or themes sometimes load their own files into
                the plugin's pages, they break the functionality of the tooltip used by the plugin. To overcome this
                situation, you can just disable the tooltip used in the plugin. Disabling the tooltip will not cause any
                problems. You will see the messages but not as a tooltip. It might take a few moments for the messages
                to be displayed when tooltip is disabled. Also, certain messages will not be properly formatted."),
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        
        <?php echo $__env->make('form-items.combined.button-with-label', [
            'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_REFRESH_DOCS_LABEL_INDEX,
            'title' => _wpcc('Refresh documentation links'),
            'info'  => sprintf(_wpcc('To refresh the documentation links shown in the description of the settings, click
                the button. You can use this button if certain documentation links do not work. When clicked, fresh URLs
                will be retrieved from %1$s.'),
                sprintf('<a href="%1$s" target="_blank">%1$s</a>', \WPCCrawler\Environment::getDocumentationUrl())
            ),
            'text' => _wpcc('Refresh documentation links'),
            'buttonClass' => 'wcc-test refresh-doc-links',
            'data'          =>  [
                'testType'      =>  \WPCCrawler\Test\Test::$TEST_TYPE_REFRESH_DOC_LINKS,
            ],
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php endif; ?>

    <?php

    /**
     * Fires before closing table tag in advanced tab of general settings page.
     *
     * @param array $settings       Existing settings and their values saved by user before
     * @param bool  $isGeneralPage  True if this is called from a general settings page.
     * @param bool  $isOption       True if this is an option, instead of a setting. A setting is a post meta, while
     *                              an option is a WordPress option. This is true when this is fired from general
     *                              settings page.
     * @since 1.6.3
     */
    do_action('wpcc/view/general-settings/tab/advanced', $settings, $isGeneralPage, $isOption);

    ?>

</table>
<?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/general-settings/tab-advanced.blade.php ENDPATH**/ ?>