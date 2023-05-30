<?php

/**
 * @param string $shortCodeName Name of the short code
 * @param string $transElement Singular element name
 * @param string $transElements Plural element name
 * @return string Information as a translated string
 * @since 1.8.0
 */
function _wpcc_trans_domain_for_short_code($shortCodeName, $transElement, $transElements) {
    return sprintf(
            _wpcc('Define domains from which %2$s source can be retrieved. %1$s short code will only show %3$s whose
                source URL is from one of these domains.'),
            '<b>' . $shortCodeName . '</b>',
            $transElement,
            $transElements
        ) . ' ' . _wpcc_domain_wildcard_info();
}

?>

<div class="wcc-settings-title">
    <h3>{{ _wpcc('Post Settings') }}</h3>
    <span>{{ _wpcc('Set post settings') }}</span>
</div>

<table class="wcc-settings">
    {{-- ALLOW COMMENTS --}}
    @include('form-items.combined.checkbox-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_ALLOW_COMMENTS,
        'title' =>  _wpcc('Allow Comments'),
        'info'  =>  _wpcc('If you want to allow comments for automatically inserted posts, check this.')
    ])

    {{-- POST STATUS --}}
    @include('form-items.combined.select-with-label', [
        'name'      =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_POST_STATUS,
        'title'     =>  _wpcc('Post Status'),
        'info'      =>  _wpcc('Set the status of automatically inserted posts.'),
        'options'   =>  $postStatuses,
        'isOption'  =>  $isOption,
    ])

    {{-- POST TYPE --}}
    @include('form-items.combined.select-with-label', [
        'name'      =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_POST_TYPE,
        'title'     =>  _wpcc('Post Type'),
        'info'      =>  _wpcc('Set the type of automatically inserted posts.'),
        'options'   =>  $postTypes,
        'isOption'  =>  $isOption,
    ])

    @if($isGeneralPage)

        {{-- CUSTOM CATEGORY TAXONOMIES --}}
        @include('form-items.combined.multiple-custom-category-taxonomy-with-label', [
            'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_POST_CATEGORY_TAXONOMIES,
            'title' => _wpcc('Post Category Taxonomies'),
            'info'  => _wpcc("Set custom post category taxonomies registered into your WordPress installation so that
                you can set a custom post category in the site settings. For taxonomy field, write the name of the
                taxonomy. The description you write in the description field will be shown when selecting a category."),
        ])

    @endif

    {{-- POST AUTHOR --}}
    @include('form-items.combined.select-with-label', [
        'name'      =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_POST_AUTHOR,
        'title'     =>  _wpcc('Post Author'),
        'info'      =>  _wpcc('Set the author of automatically inserted posts.'),
        'options'   =>  $authors,
        'isOption'  =>  $isOption,
    ])

    {{-- POST TAG LIMIT --}}
    @include('form-items.combined.input-with-label', [
        'name'      =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_POST_TAG_LIMIT,
        'title'     =>  _wpcc('Maximum number of tags'),
        'info'      =>  _wpcc('How many tags at maximum can be added to a post? Set this <b>0</b> if you do not
            want to set a limit and get all available tags. The default value is 0.'),
        'isOption'  =>  $isOption,
        'type'      =>  'number',
        'min'       =>  0,
    ])

    {{-- CHANGE POST PASSWORD --}}
    @include('form-items.combined.checkbox-with-label', [
        'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_CHANGE_PASSWORD,
        'title'         =>  _wpcc('Change Password'),
        'info'          =>  _wpcc('If you want to change post password, check this.'),
        'dependants'    =>  '["#post-password"]'
    ])

    {{-- POST PASSWORD --}}
    <tr id="post-password">
        <td>
            @include('form-items/label', [
                'for'   =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_POST_PASSWORD,
                'title' =>  _wpcc('Post Password'),
                'info'  =>  _wpcc('Set the password for automatically inserted posts. The value you
                    enter here will be stored as raw text in the database, without encryption.
                    If anyone accesses your database, he/she will be able to see your password.
                    <br /><br />
                    If you want to delete the password, just leave the new password fields empty.
                    When you change the password, new password will be effective for new posts,
                    and passwords for old posts will not be changed.
                    <br /><br />
                    <b>Leave old password field empty if you did not set any password before.</b>')
            ])
        </td>
        <td>
            @include('form-items/password-with-validation', [
                'name'      =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_POST_PASSWORD,
            ])
        </td>
    </tr>

    {{-- SECTION: MEDIA --}}
    @include('partials.table-section-title', ['title' => _wpcc("Media")])

    {{-- SET SRCSET ATTRIBUTE --}}
    @include('form-items.combined.checkbox-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_POST_SET_SRCSET,
        'title' =>  _wpcc('Set srcset attributes of saved media when possible'),
        'info'  =>  _wpcc('If you want to set srcset attributes of the saved media using alternative sizes
            of the media, check this. The srcset attributes will be set if they are available.')
    ])

    @if ($isGeneralPage)

        {{-- PROTECTED ATTACHMENTS --}}
        @include('form-items.combined.multiple-media-item-with-label', [
            'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_PROTECTED_ATTACHMENTS,
            'title' => _wpcc('Protected attachments'),
            'info'  => _wpcc('When recrawling or deleting a post, the plugin deletes the images of the existing post.'
                . ' This setting is used to exclude certain images from deletion. The images whose IDs are added to'
                . ' this setting will not be deleted when a post is being recrawled or deleted. This setting is also'
                . ' automatically updated, when changes made to certain site settings, such as filter commands that'
                . ' define attachments.'),
        ])

        {{-- SECTION: SHORT CODES --}}
        @include('partials.table-section-title', ['title' => _wpcc("Short Codes")])

        {{-- ALLOWED IFRAME SHORT CODE DOMAINS --}}
        @include('form-items.combined.multiple-domain-with-label', [
            'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_ALLOWED_IFRAME_SHORT_CODE_DOMAINS,
            'title' => _wpcc('Allowed domains for iframe short code'),
            'info'  => _wpcc_trans_domain_for_short_code(
                \WPCCrawler\Objects\GlobalShortCodes\GlobalShortCodeService::getShortCodeTagName(\WPCCrawler\Objects\GlobalShortCodes\ShortCodes\IFrameGlobalShortCode::class),
                _wpcc('iframe'),
                _wpcc('iframes')
            )
        ])

        {{-- ALLOWED SCRIPT SHORT CODE DOMAINS --}}
        @include('form-items.combined.multiple-domain-with-label', [
            'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_ALLOWED_SCRIPT_SHORT_CODE_DOMAINS,
            'title' => _wpcc('Allowed domains for script short code'),
            'info'  => _wpcc_trans_domain_for_short_code(
                \WPCCrawler\Objects\GlobalShortCodes\GlobalShortCodeService::getShortCodeTagName(\WPCCrawler\Objects\GlobalShortCodes\ShortCodes\ScriptGlobalShortCode::class),
                _wpcc('script'),
                _wpcc('scripts')
            )
        ])

    @endif

    <?php

    /** @var array $settings */
    /** @var bool  $isGeneralPage */
    /** @var bool  $isOption */
    /**
     * Fires before closing table tag in post tab of general settings page.
     *
     * @param array $settings       Existing settings and their values saved by user before
     * @param bool  $isGeneralPage  True if this is called from a general settings page.
     * @param bool  $isOption       True if this is an option, instead of a setting. A setting is a post meta, while
     *                              an option is a WordPress option. This is true when this is fired from general
     *                              settings page.
     * @since 1.6.3
     */
    do_action('wpcc/view/general-settings/tab/post', $settings, $isGeneralPage, $isOption);

    ?>

</table>
