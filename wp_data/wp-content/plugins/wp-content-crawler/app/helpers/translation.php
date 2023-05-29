<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 13/04/16
 * Time: 20:49
 */

use WPCCrawler\Environment;
use WPCCrawler\Objects\Enums\ShortCodeName;

/**
 * A replacement function for translatable texts
 *
 * @param string $string Text to be translated
 * @return string Translated text
 */
function _wpcc($string) {
    return __($string, Environment::appDomain());
}

/**
 * Returns a translated string that informs about regular expressions
 *
 * @return string
 */
function _wpcc_trans_regex() {
    return _wpcc('You can write plain text or regular expressions.') . ' ' . _wpcc_trans_regex_base();
}

/**
 * @return string A translated string that explains how the regular expression should be structured and can be tested.
 * @since 1.11.0
 */
function _wpcc_trans_regex_base() {
    return _wpcc('If you want to use delimiters, you can use "/". If the expression does not start with "/", it 
        is considered as it does not have delimiters. In that case, forward slashes will be automatically added. You 
        can test your regex <a href="https://regex101.com/" target="_blank">here</a>.');
}

/**
 * Returns a translated string indicating that in case of multiple selector usage, the first match will be used.
 *
 * @return string
 */
function _wpcc_trans_multiple_selectors_first_match() {
    return _wpcc('If you give more than one selector, the first match will be used.');
}

/**
 * Returns a translated string indicating that in case of multiple selector usage, all matches will be used.
 *
 * @return string
 */
function _wpcc_trans_multiple_selectors_all_matches() {
    return _wpcc('If you give more than one selector, all matches will be used.');
}

/**
 * Returns a translated string that informs that saving images increase the memory usage.
 *
 * @return string
 */
function _wpcc_trans_save_image_note() {
    return _wpcc('<b>Note that</b> saving images will increase the time  to save the posts, and hence memory usage.');
}

/**
 * @param string $url Optional URL that will be opened when the text is clicked.
 * @param bool   $openInNewWindow True if the URL should be opened in new window.
 * @return string 'How to get it?' text
 */
function _wpcc_trans_how_to_get_it($url = '', $openInNewWindow = true) {
    $text = _wpcc('How to get it?');

    if($url) {
        $text = sprintf('<a href="%1$s"%3$s>%2$s</a>', $url, $text, !$openInNewWindow ? '' : ' target="_blank"');
    }

    return $text;
}

/**
 * Returns a translated string indicating that if there are more than one, a random one among them will be used.
 *
 * @return string
 * @since 1.8.0
 */
function _wpcc_trans_more_than_one_random_one() {
    return _wpcc('If you define more than one, a random one will be used.');
}

/**
 * @return string Explanation of using [wcc-item] short code with dot key in case of treating the item as JSON.
 */
function _wpcc_wcc_item_short_code_dot_key_for_json() {
    return sprintf(
        _wpcc('You can use <b>[%1$s]</b> short code with a dot key when the item is treated as JSON. A dot key
            shows the value\'s path in the JSON. For example, in <i>%2$s</i>, the dot key for <b>%3$s</b> is
            <b>%4$s</b>. To get <b>%3$s</b> using <i>[%1$s]</i>, you can write <b>[%1$s %4$s]</b>. After this, the short code
            will be replaced by <b>%3$s</b>. In case of array values, you can use the target value\'s index as well. For example,
            in <i>%5$s</i>, you can get <b>%6$s</b> with <b>[%1$s %7$s]</b>.'
        ),
        ShortCodeName::WCC_ITEM,
        '{"item": {"inner": "value"}}',
        'value',
        'item.inner',
        '{"item": {"inner1": [{"key": "15"}]}}',
        '15',
        'item.inner1.0.key'
    );
}

/**
 * @return string Explanation about the tests conducted in the file options box
 * @since 1.8.0
 */
function _wpcc_file_options_box_tests_note() {
    return _wpcc('The tests are conducted by creating a 1-byte temporary file that has the same name and extension
        with the found item. After the tests have been done, while the temporary files will be deleted, created folders 
        will <b>not</b> be deleted.');
}

/**
 * @return string Explanation about what can be written in attribute input of a selector-attribute input group.
 * @since 1.8.0
 */
function _wpcc_selector_attribute_info() {
    return _wpcc("You can write 'text', 'html', or the name of another HTML attribute for the attribute input. 
    'text' will get the text content of the element, while 'html' will get the HTML of the element.");
}

/**
 * @return string Explanation about using wildcard characters in the domain names.
 * @since 1.8.0
 */
function _wpcc_domain_wildcard_info() {
    return sprintf(
        _wpcc('You can use wildcard character, which is %1$s, to indicate variable parts. For example,
            when you enter %2$s as domain, it will not allow %3$s. However, if you write it as %4$s or %5$s, it will 
            be allowed. You can use the wildcard character anywhere in the domain name.'
        ),
        '<b>*</b>',
        '<b>domain.com</b>',
        '<b>subdomain.domain.com</b>',
        '<b>*.domain.com</b>',
        '<b>*domain.com</b>'
    );
}

/**
 * @param bool $withLineBreaks True if two line breaks (<code><br><br></code>) must be added at the beginning of the
 *                             text.
 * @return string Explanation of a filter
 * @since 1.11.0
 */
function _wpcc_filter(bool $withLineBreaks = false): string {
    $result = $withLineBreaks ? "<br><br>" : "";
    $result .= _wpcc('A filter is composed of "if" and "then" parts that contain commands. The "if" part defines the 
        conditions that should be met. If those conditions are met, the commands defined in the "then" part will be 
        executed. With a filter, you basically say "if this condition is met, then do this". You can define many 
        commands for both the "if" and "then" parts to create very complex rules to achieve your goal. The commands 
        defined in the "if" part are condition commands, while the ones defined in the "then" part are action commands. 
        The condition commands check if something is true, while the action commands perform some action. If you do not
        define a condition, the action command will be executed no matter what.');

    return $result;
}

/**
 * @return string Explains how the date should be entered, i.e. in what format, into an input that stores a date. Also,
 *                explains acceptable date formats by referring to the constructor of DateTime class.
 * @since 1.11.0
 */
function _wpcc_enter_date_with_format() {
    return sprintf(
        _wpcc('Enter a date formatted as "%1$s" (without quotes), i.e. "%2$s". For example: %3$s. You can also 
            enter any time value supported by %4$s. If the date is not a fixed date, then the time of your server is 
            used as now, not the time of WordPress. It is recommended to enter a fixed date for reliability.'),
        '<b>' . Environment::mysqlDateFormat() . '</b>',
        sprintf(
            '<b>(%1$s)-(%2$s)-(%3$s) (%4$s):(%5$s):(%6$s)</b>',
            _wpcc('year'),
            _wpcc('month'),
            _wpcc('day'),
            _wpcc('hour'),
            _wpcc('minute'),
            _wpcc('second')
        ),
        '<span class="highlight date">2020-05-27 13:37:15</span>',
        '<a href="https://www.php.net/manual/en/datetime.construct.php" target="_blank">DateTime</a>'
    );
}