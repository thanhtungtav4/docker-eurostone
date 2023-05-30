<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 29/03/16
 * Time: 15:45
 */

namespace WPCCrawler;

use Closure;
use DateTime;
use DOMNode;
use Exception;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriResolver;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;
use WP_Post;
use WP_Term;
use WPCCrawler\Objects\Blade;
use WPCCrawler\Objects\Enums\InformationMessage;
use WPCCrawler\Objects\Enums\InformationType;
use WPCCrawler\Objects\Informing\Information;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\Objects\Settings\SettingsImpl;
use WPCCrawler\Objects\Tree\TermTree;
use WPCCrawler\Objects\Tree\TermTreeItem;
use WPCCrawler\PostDetail\PostDetailsService;

class Utils {

    /** @var Blade|null */
    private static $BLADE = null;

    /** @var array|null An associative array storing site ID (the custom post type) as key and site name as value. */
    private static $SITES = null;

    /** @var array|null An associative array storing author ID as key and author name as value. */
    private static $authors = null;

    /**
     * Saves or updates a post meta for a post. <b>Note that</b> the meta key will be prefixed with an underscore if
     * it does not start with it. The meta keys starting with an underscore will be hidden on post edit/create page.
     * Hence, the meta keys can be shown with custom meta boxes.
     *
     * @param int   $postId
     * @param mixed $metaKey
     * @param mixed $metaValue
     * @param bool  $unique
     * @return bool|int
     */
    public static function savePostMeta($postId, $metaKey, $metaValue, $unique = true) {
        if(!Str::startsWith($metaKey, '_')) $metaKey = '_' . $metaKey;

        if($unique) {
            return update_post_meta($postId, $metaKey, $metaValue);
        } else {
            return add_post_meta($postId, $metaKey, $metaValue, false);
        }
    }

    /**
     * Extracts the value of a meta key from meta array. If the value for the specified key is serialized, it will be
     * unserialized.
     *
     * @param array $postMeta An array of post meta acquired by get_post_meta() function.
     * @param string $key The key of the meta whose value is wanted
     * @return null|string|array
     */
    public static function getPostMetaValue($postMeta, $key) {
        if(isset($postMeta[$key])) {
            $val = $postMeta[$key][0];
            if(is_serialized($val)) {
                return unserialize($val);
            }

            return $val;
        }

        return null;
    }

    /**
     * Checks a parameter if it should be unserialized, and if so, does so. If the parameter has serialized values inside,
     * those will be unserialized as well. Hence, at the end, there will be no serialized strings inside the value.
     *
     * @param mixed $metaValue  The value to be unserialized
     * @return mixed            Unserialized value
     */
    public static function getUnserialized($metaValue) {
        $val = (!empty($metaValue) && isset($metaValue[0])) ? $metaValue[0] : $metaValue;
        return is_serialized($val) ? static::getUnserialized(unserialize($val)) : $metaValue;
    }

    /**
     * Prepares a valid URL from given parameters.
     *
     * @param string|null $baseUrl
     * @param string|null $urlPartToAppend
     * @param null|string $currentUrl Current page's URL. If this is null, $baseUrl will be used instead.
     * @return null|string A valid URL created from the givens
     */
    public static function prepareUrl(?string $baseUrl, ?string $urlPartToAppend, $currentUrl = null): ?string {
        if ($urlPartToAppend === null) {
            return null;
        }

        // If the URL starts with double slashes ("//"), prepend "http:" and return.
        if(substr($urlPartToAppend, 0, 2) == '//') {
            return "http:" . $urlPartToAppend;
        }

        // If the url does not start with http, add main site url in front of it
        if(!Str::startsWith($urlPartToAppend, "http")) {
            // If URL part starts with "www", just add "http://" in front of it and return.
            if(Str::startsWith($urlPartToAppend, "www")) return "http://" . $urlPartToAppend;

            // Remove the trailing slash from the base url
            if ($baseUrl === null) return null;
            $baseUrl = rtrim($baseUrl, "/");

            // Remove the first leading slash from the url, if exists.
            if(Str::startsWith($urlPartToAppend, "/")) {
                $urlPartToAppend = substr($urlPartToAppend, 1);

            // If not, prepend current URL.
            } else {
                // The URL part is like "other/page.html". Let's say the current URL is "http://site.com/my/page". In
                // this case, browsers consider "other/page.html" link as "http://site.com/my/other/page.html". Here,
                // we are handling this situation.

                $currentUrl = $currentUrl ? $currentUrl : $baseUrl;

                // If the current URL does not end with a forward slash and the URL part to append does not start with
                // a question mark, we need to get the base resource URL.
                if(!Str::endsWith($currentUrl, "/") && !Str::startsWith($urlPartToAppend, "?")) {
                    // Remove the last part from the URL when the URL has more than one resource.
                    // First, remove the part until ://. Then, explode it from forward slashes.
                    $replaced = preg_replace("%^[^:]+://%", "", $currentUrl);
                    $parts = explode("/", is_string($replaced) ? $replaced : '');
                    if (sizeof($parts) > 1) {
                        $currentUrl = pathinfo($currentUrl, PATHINFO_DIRNAME);
                    }

                } /** @noinspection PhpStatementHasEmptyBodyInspection */ else {
                    // When the URL ends with a forward slash, or the URL to append starts with a question mark, it
                    // means the URL currently points to the URL relative to this url part. E.g. when the URL is
                    // "http://abc.com/test/page/", and url part to append is "page.html", it means the intended URL is
                    // "http://abc.com/test/page/page.html". Or, when the URL is "http://abc.com/test/page" and url part
                    // to append is "?num=2", the intended URL is "http://abc.com/test/page?num=2"
                    // So, nothing to do here.
                }

                $currentUrl = rtrim($currentUrl, "/");
                if(!Str::startsWith($urlPartToAppend, "?")) $currentUrl .= "/";

                return $currentUrl . $urlPartToAppend;
            }

            // Prepare the full url and return it.
            return $baseUrl . "/" . $urlPartToAppend;
        }

        return $urlPartToAppend;
    }

    /**
     * Resolves a URL.
     *
     * @param Uri    $baseUri
     * @param string $relativeUrl Relative or full URL that will be resolved against the given {@link Uri}.
     * @since 1.8.0
     * @return string
     */
    public static function resolveUrl(Uri $baseUri, string $relativeUrl): string {
        try {
            // Try to resolve the relative URL
            $resolvedUri = UriResolver::resolve($baseUri, new Uri($relativeUrl));

            // Return the resolved URI
            return $resolvedUri->__toString();

        } catch(Exception $e) {
            // If there was an error in resolving the URI, inform the user.
            Informer::add(Information::fromInformationMessage(
                InformationMessage::URI_COULD_NOT_BE_RESOLVED,
                $e->getMessage(),
                InformationType::INFO
            )->addAsLog());

            return $relativeUrl;
        }
    }

    /**
     * Create a blade view that can be rendered.
     * @param string $viewName
     * @return View
     */
    public static function view($viewName) {
        if (!static::$BLADE) {
            $views = __DIR__ . Environment::relativeViewsDir();
            $cache = __DIR__ . Environment::relativeCacheDir();

            static::$BLADE = new Blade([$views], $cache);
        }

        return static::$BLADE->view()->make($viewName);
    }

    /**
     * Sorts a multidimensional array according to the specified keys. Example usage:
     * <p><p>
     * $dataArray = [ ["start" => 2, "end" => 18], ["start" => 3, "end" => 5], ["start" => 19, "end" => 2] ]
     * <p>
     * array_msort($dataArray, ['start' => SORT_ASC])
     * <p><p> Above example will sort $dataArray ascending by 'start'
     *
     * @param array $array
     * @param array $cols
     * @return array
     */
    public static function array_msort($array, $cols) {
        $colarr = array();
        foreach ($cols as $col => $order) {
            $colarr[$col] = array();
            foreach ($array as $k => $row) {
                $colarr[$col]['_' . $k] = strtolower($row[$col]);
            }
        }
        $eval = 'array_multisort(';
        foreach ($cols as $col => $order) {
            $eval .= '$colarr[\'' . $col . '\'],' . $order . ',';
        }
        $eval = substr($eval, 0, -1) . ');';
        eval($eval);
        $ret = array();
        foreach ($colarr as $col => $arr) {
            foreach ($arr as $k => $v) {
                $k = substr($k, 1);
                if (!isset($ret[$k])) $ret[$k] = $array[$k];
                $ret[$k][$col] = $array[$k][$col];
            }
        }
        return $ret;

    }

    /**
     * Get value from an array
     *
     * @param array|null $array   The array
     * @param string     $key     Target key
     * @param null|mixed $default Default value
     * @return mixed
     */
    public static function array_get($array, $key, $default = null) {
        return Arr::get((array) $array, $key, $default);
    }

    /**
     * Set a value in an array
     *
     * @param array  $array
     * @param string $key
     * @param mixed  $value
     * @return array
     * @since 1.8.0
     */
    public static function array_set(&$array, $key, $value) {
        return Arr::set($array, $key, $value);
    }

    /**
     * Gets the HTML of the specified element with its own tag
     * @param Crawler|null $node
     * @return string HTML of the element
     */
    public static function getNodeHTML($node): string {
        return !$node
            ? ''
            : static::getDomNodeHtmlString($node->getNode(0));
    }

    /**
     * Get the HTML code of a {@link DOMNode}. This method definitely returns a string.
     *
     * @param DOMNode|null $node The node whose HTML is needed
     * @return string The HTML of the node. If it is not found, an empty string.
     * @uses getDomNodeHtml()
     * @since 1.11.1
     */
    public static function getDomNodeHtmlString(?DOMNode $node): string {
        $result = static::getDomNodeHtml($node);
        return $result === null
            ? ''
            : $result;
    }

    /**
     * Get the outer HTML code of a {@link DOMNode}
     *
     * @param DOMNode|null $node The node whose HTML is needed
     * @return string|null If it could be retrieved, the HTML of the node. Otherwise, null.
     * @since 1.11.1
     */
    public static function getDomNodeHtml(?DOMNode $node): ?string {
        if ($node === null || $node->ownerDocument === null) {
            return null;
        }

        $result = $node->ownerDocument->saveHTML($node);
        return $result === false
            ? null
            : $result;
    }

    /**
     * Combines 2 or more arrays into one.
     *
     * @param array $mainArray
     * @param null|array $array1
     * @param null|array $array2
     * @param null|array $array3
     * @return array
     */
    public static function combineArrays($mainArray, $array1 = null, $array2 = null, $array3 = null) {
        if($array1 && !empty($array1)) $mainArray = array_merge($mainArray, $array1);
        if($array2 && !empty($array2)) $mainArray = array_merge($mainArray, $array2);
        if($array3 && !empty($array3)) $mainArray = array_merge($mainArray, $array3);
        return $mainArray;
    }

    /**
     * @param string|int|null $date A date string
     * @return string Date string formatted according to WordPress settings
     */
    public static function getDateFormatted($date): string {
        $nullResult = '-';
        if ($date === null) return $nullResult;

        if(!is_numeric($date)) $date = strtotime($date);
        return $date
            ? date_i18n(get_option('time_format'), (int) $date) . " " . date_i18n(get_option('date_format'), (int) $date)
            : $nullResult;
    }

    /**
     * Get difference for humans between two timestamps.
     *
     * @param int|false   $from Timestamp
     * @param string|null $to   Timestamp. If null, current time will be used.
     * @return string Difference for humans
     */
    public static function getDiffForHumans($from, $to = null) {
        if(!$from) return '-';

        if(!$to || !is_numeric($to)) $to = current_time('timestamp');

        return human_time_diff($from, (int) $to);
    }

    /**
     * Get plugin file path. The path can be safely used for registration of activation/deactivation hooks.
     *
     * @return string
     */
    public static function getPluginFilePath() {
        return Environment::pluginFilePath();
    }

    /**
     * Strips slashes of non-array values of the array.
     *
     * @param array $array The array whose string values' slashes will be stripped
     * @return array The array with slashes of its string values are stripped
     */
    public static function arrayStripSlashes($array) {
        $mArray = [];
        foreach($array as $key => $value) {
            if(is_array($value)) {
                $mArray[$key] = static::arrayStripSlashes($value);
            } else if (is_string($value)) {
                $mArray[$key] = stripslashes($value);
            } else if ($value === null) {
                $mArray[$key] = '';
            } else {
                $mArray[$key] = $value;
            }
        }

        return $mArray;
    }

    /**
     * Get value of an option unescaped. Value of the option is escaped before it is saved to the database. Hence,
     * you need to get the value unescaped. This function unescapes the escaped characters.
     *
     * @param string $key Option key
     * @return array|string Unescaped value
     */
    public static function getOptionUnescaped($key) {
        $value = get_option($key);
        return is_array($value) ? static::arrayStripSlashes($value) : stripslashes($value);
    }

    /**
     * Removes one backslash from repeating backslashes in a string. E.g. "\\\\ \\\ \\ \" will be "\\\ \\ \ "
     *
     * @param string $original
     * @return mixed
     */
    public static function removeOneBackslashFromRepeatingBackslashes($original) {
        preg_match_all("/\\\{1,}/", $original, $matches, PREG_OFFSET_CAPTURE);

        $decreaseOffset = 0;
        foreach($matches[0] as $m) {
            /** @var array $m 0 => string, 1 => offset */
            $mOffset = (int) $m[1] - $decreaseOffset;

            $original = substr_replace($original, substr($m[0], 0, strlen($m[0]) - 1), $mOffset, strlen($m[0]));
            $decreaseOffset += 1;
        }

        return $original;
    }

    /**
     * Slashes the values of an array using wp_slash function.
     *
     * @param array $arr The array whose values will be slashed using wp_slash function
     * @return array
     */
    public static function arrayDeepSlash($arr): array {
        $result = [];
        foreach($arr as $k => $v) {
            if(is_array($v)) {
                $result[$k] = static::arrayDeepSlash($v);
            } else {
                $result[$k] = wp_slash($v);
            }
        }

        return $result;
    }

    /**
     * Get categories as an array.
     *
     * @param null|SettingsImpl $postSettings If this is null, all categories will be returned. Otherwise, only the
     *                                        categories that are compatible with the post settings will be returned.
     * @param bool              $hierarchical True if the category names should be indented according to their
     *                                        hierarchy. Otherwise, false.
     * @return array Structure: [ ['id' => 'categoryId', 'name' => 'Category Name', 'taxonomy' => 'categoryTaxonomy'], ...]
     */
    public static function getCategories($postSettings = null, bool $hierarchical = false) {
        // Get all category taxonomies in a single array, uniquely. Array keys are the names of the taxonomies. Array
        // values are the descriptions of them. E.g. ['product_cat' => 'WooCommerce']. If the description is null,
        // it means no description is needed.
        $allCategoryTaxonomyData = array_unique(
            // Define default category taxonomies
            [
                'category' => null, // This is the default WP category.
            ]

            +

            // Make registered post detail factories be able to add their custom categories
            PostDetailsService::getInstance()->getCategoryTaxonomies($postSettings)

            +

            // Add the custom taxonomies among the others by making sure that they do not override any of the
            // previously-defined taxonomies.
            static::getCustomPostCategoryTaxonomies()
        );

        // Prepare the categories
        $categories = [];
        foreach($allCategoryTaxonomyData as $taxonomyName => $description) {
            // If there is no taxonomy name, continue with the next one.
            if (!$taxonomyName) continue;

            // Get the categories
            $cats = get_categories([
                'taxonomy'      => $taxonomyName,
                'orderby'       => 'name',
                'hierarchical'  => 0,
                'hide_empty'    =>  false,
            ]);

            // If there is an error or no category, continue with the next one.
            if (isset($cats["errors"]) || !$cats) continue;
            /** @var WP_Term[] $cats */

            // If the description is an empty string, use taxonomy name as the description.
            if ($description === '') $description = $taxonomyName;

            if ($hierarchical) {
                TermTree::createTree($cats)
                    ->walk(function(TermTreeItem $item) use (&$categories, $description, $taxonomyName) {
                        $categoryItem = self::prepareCategoryItem($item->getTerm(), $taxonomyName, $description, $item->getDepth());
                        if ($categoryItem === null) return;

                        $categories[] = $categoryItem;
                    });

            } else {
                // Prepare them
                foreach($cats as $cat) {
                    $categoryItem = self::prepareCategoryItem($cat, $taxonomyName, $description);
                    if ($categoryItem === null) continue;

                    $categories[] = $categoryItem;
                }
            }

        }

        /**
         * Modify the categories. The categories are shown, e.g., in Category Map setting.
         *
         * @param array $categories Categories. Structured as:
         *                          [
         *                              [id => "categoryId",  name => "Category Name",  taxonomy => "categoryTaxonomy" ],
         *                              [id => "categoryId2", name => "Category Name2", taxonomy => "categoryTaxonomy" ],
         *                              ...
         *                          ]
         *
         * @return array Modified categories.
         * @since 1.6.3
         * @since 1.8.0 Updates the structure of the categories array.
         */
        $categories = apply_filters('wpcc/categories', $categories);

        return $categories;
    }

    /**
     * @param WP_Term     $term         The term used as a category
     * @param string      $taxonomyName The name of the term's taxonomy
     * @param string|null $description  The description of the term. This will be appended to the term's name.
     * @param int         $depth        The depth of the term in its category hierarchy. This value is used to decide
     *                                  the indentation of the term.
     * @return array|null An array representing a category item
     * @since 1.12.0
     */
    private static function prepareCategoryItem(WP_Term $term, string $taxonomyName, ?string $description,
                                                int $depth = 0): ?array {
        $catId = isset($term->cat_ID) ? $term->cat_ID : null;
        if ($catId === null) return null;

        $name = $term->name;

        // Add the description
        if ($description) {
            $name .= " ({$description})";
        }

        $prefix = $depth > 0
            ? str_repeat("&nbsp;&nbsp;", $depth)
            : '';

        return [
            'id'       => $catId,
            'name'     => $prefix . $name . " ({$catId})", // Add the category ID
            'taxonomy' => $taxonomyName
        ];
    }

    /**
     * Get custom post category taxonomies defined in the general settings
     *
     * @return array A key-value pair where keys are the taxonomies and the values are their descriptions.
     * @since 1.8.0
     */
    private static function getCustomPostCategoryTaxonomies() {
        // Get custom post category taxonomies defined in the general settings
        $customPostTaxonomiesInSettings = get_option(SettingKey::WPCC_POST_CATEGORY_TAXONOMIES);
        if (!$customPostTaxonomiesInSettings) return [];

        $customTaxonomies = [];
        foreach($customPostTaxonomiesInSettings as $data) {
            $taxonomyName = Utils::array_get($data, 'taxonomy');
            $description = Utils::array_get($data, 'description', '');

            // If there is no taxonomy name, continue with the next one.
            if (!$taxonomyName) continue;

            $customTaxonomies[$taxonomyName] = $description;
        }

        return $customTaxonomies;
    }

    /**
     * Get a value from an array
     *
     * @param array $array      The array
     * @param string $key       The key whose value is wanted
     * @param mixed $default    Default value if the value of the key is not valid
     * @return mixed            Value of the key or the default value
     */
    public static function getValueFromArray($array, $key, $default = false) {
        return isset($array[$key]) && $array[$key] ? $array[$key] : $default;
    }

    /**
     * Check if the user wants to change the password for the posts. If not, remove the password field.
     *
     * @param array       $data        Input data from user, such as $_POST
     * @param array       $keys        Available setting (post meta) keys
     * @param null|string $oldPassword Old password to check against. If null, general option will be used to get an
     *                                 old password.
     * @return array An array having 'success' and 'message' keys, with data types boolean and string, respectively.
     */
    public static function validatePasswordInput(&$data, &$keys, $oldPassword = null) {
        $keyPassword            = SettingKey::WPCC_POST_PASSWORD;
        $keyPasswordOld         = $keyPassword . '_old';
        $keyPasswordValidation  = $keyPassword . '_validation';

        $success = true;
        $message = '';
        if(!isset($data[SettingKey::WPCC_CHANGE_PASSWORD])) {

            unset($data[$keyPassword]);
            unset($keys[array_search($keyPassword, $keys)]);

        } else {
            // Check if the old pw is correct
            $oldPassword = $oldPassword === null ? get_option($keyPassword) : $oldPassword;
            if($oldPassword !== $data[$keyPasswordOld]) {
                // Old password is not correct. Remove the password from data and keys, and set success as false.
                unset($data[$keyPassword]);
                unset($keys[array_search($keyPassword, $keys)]);

                $success = false;
                $message = 'Old password is not correct.';

            } else {
                // Check if passwords match
                if($data[$keyPassword] !== $data[$keyPasswordValidation]) {
                    $success = false;
                    $message = _wpcc('Passwords do not match.');
                }
            }

            // Do not save "change password" checkbox's value
            unset($data[SettingKey::WPCC_CHANGE_PASSWORD]);
        }

        return [
            'success'   =>  $success,
            'message'   =>  $message
        ];
    }

    /**
     * Delete a file
     *
     * @param string|null $filePath
     */
    public static function deleteFile(?string $filePath): void {
        if(!$filePath) return;

        wp_delete_file($filePath);
    }

    /**
     * Delete a post's thumbnail and the attachment.
     *
     * @param int          $postId                 ID of the post whose thumbnail should be deleted
     * @param Closure|null $shouldDeleteAttachment A function that decides whether the attachment should be deleted or
     *                                             not. The signature is: <b>fn(int $attachmentId): bool</b> If the
     *                                             function is null, or it returns `true`, the attachment is deleted. If
     *                                             the function returns `false`, the attachment is not deleted, it is
     *                                             only removed from the post.
     * @since 1.12.0 Add $shouldDeleteAttachment parameter
     */
    public static function deletePostThumbnail($postId, ?Closure $shouldDeleteAttachment = null): void {
        // Get the ID of the thumbnail attachment
        $alreadyExistingThumbId = get_post_thumbnail_id($postId);

        // Delete the thumbnail from the post
        delete_post_thumbnail($postId);

        // Delete the attachment
        if(is_int($alreadyExistingThumbId)
            && ($shouldDeleteAttachment === null || $shouldDeleteAttachment($alreadyExistingThumbId) === true)
        ) {
            wp_delete_attachment($alreadyExistingThumbId);
        }
    }

    /**
     * Convert encoding of a string
     *
     * @param string $string            The string whose encoding will be converted
     * @param string $targetEncoding    Target encoding
     * @return mixed|string             Resultant string
     */
    public static function convertEncoding($string, $targetEncoding = 'UTF-8') {
        return mb_convert_encoding($string, $targetEncoding, mb_detect_encoding($string, 'UTF-8, ISO-8859-1', true));
    }

    /**
     * @param string       $name     Name of the term
     * @param string       $taxonomy Taxonomy of the term
     * @param array|string $args     Other arguments. See {@link wp_insert_term()}.
     * @return int|null If the term is inserted or it exists, its ID. Otherwise, null.
     */
    public static function insertTerm($name, $taxonomy, $args = []) {
        // Maximum term name length is 200 chars according to the database table named as 'terms'
        $maxLength = 200;
        if (mb_strlen($name) > $maxLength) {
            $oldName = $name;
            $name = mb_substr($name, 0, $maxLength);

            // Notify the user about this.
            Informer::addInfo(sprintf(
                _wpcc('Length of the term is greater than %2$s. Therefore, it is shortened. Term: "%1$s", Shortened: "%3$s"'),
                $oldName,
                $maxLength,
                $name
            ))->addAsLog();
        }

        $result = wp_insert_term($name, $taxonomy, $args);
        $termId = null;
        $errorMessage = null;

        if (!is_wp_error($result) && !empty($result['term_id'])) {
            $termId = absint($result['term_id']);

        } else if(is_wp_error($result)) {
            $termId = $result->get_error_data('term_exists');

            // The term could not be inserted. Try to get the error message.
            if (!$termId) {
                $errorMessage = $result->get_error_message();
            }
        }

        // If the term could not be inserted or retrieved, inform the user.
        if ($termId === null) {
            $argsInfo = json_encode($args);
            if ($argsInfo === false) $argsInfo = '';

            Informer::addError(sprintf(
                _wpcc('Term "%1$s" could not be added to taxonomy "%2$s" (Args: %3$s). Message: %4$s'),
                $name,
                $taxonomy,
                $argsInfo,
                isset($errorMessage) && $errorMessage ? $errorMessage : '-')
            )->addAsLog();
        }

        return $termId;
    }

    /**
     * Separates the strings with the given separators and returns a flat array. E.g. if the given array is
     * ["val1, val2", "val3, val4 | val5"] with separators given as [",", "|"], the result will be
     * ["val1, "val2", "val3", "val4", "val5"]
     *
     * @param array|string $values     An array of strings or a string.
     * @param array        $separators An array of separators. E.g. [",", "|"]
     * @param bool         $trim       True if the values should be trimmed.
     * @return array A flat array that contains separated values.
     * @since 1.8.0
     */
    public static function getSeparated($values, $separators, $trim = true) {
        // If there is no value, stop.
        if (!$values) return [];

        // If the value is not an array, make it an array.
        if (!is_array($values)) $values = [$values];

        // Create splitter regex
        // Remove empty values.
        $separators = array_filter($separators, function($separator) {
            return $separator !== '';
        });

        // This will turn [',', '-', '/'] into ',|-|\/'
        $splitPart = implode('|', array_map(function($separator) {
            return preg_quote($separator, '/');
        }, $separators));

        // If there is no split part, stop.
        if (!$splitPart) return static::filterEmptyStrings($values);

        // Create the final splitter regex
        $splitRegex = "/($splitPart)/";

        $preparedValues = [];
        $separatorListStrForNotification = null;
        $valueListStrForNotification = null;

        foreach($values as $valStr) {
            $res = preg_split($splitRegex, $valStr);

            // If there is an error, notify the user and continue with the next string.
            if ($res === false) {
                if ($separatorListStrForNotification === null) $separatorListStrForNotification = implode(' ', $separators);
                if ($valueListStrForNotification === null) $valueListStrForNotification = implode(' ', $values);

                Informer::addError(sprintf(
                        _wpcc('%1$s could not be separated using these separators: %2$s'),
                        $valueListStrForNotification,
                        $separatorListStrForNotification)
                )->addAsLog();

                continue;
            }

            $preparedValues[] = $res;
        }

        // Make sure we have a flat array.
        $preparedValues = Arr::flatten($preparedValues);

        // Prepare the items
        $preparedValues = array_values(array_filter(array_map(function($v) use (&$trim) {
            // Remove the item if it is not valid.
            if (!$v) return null;

            // Trim it if it is required.
            $result = $trim ? trim($v) : $v;

            // If the item is still valid, return it. Otherwise, remove it.
            return $result ? $result : null;
        }, $preparedValues)));

        return static::filterEmptyStrings(array_values($preparedValues));
    }

    /**
     * @throws FileNotFoundException
     * @since 1.9.0
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public static function validateCRON(): void {
        if (!Environment::F_CHECK) return; // @phpstan-ignore-line
        $optName = md5('wpcc_last_cron_validation_date_time');

        $lastValue = get_option($optName, null);
        if ($lastValue === '') $lastValue = null;
        $lastDate = null;
        $nextValidation = null;
        if($lastValue !== null) {
            $lastValue = base64_decode($lastValue);

            $split = explode('|', $lastValue);
            $lastDate = $split[0];
            $nextValidation = count($split) < 2 ? 48 : (int) $split[1];
        }

        $currentTimeMysql = (string) current_time('mysql');
        $lastValidationTimestamp = (new DateTime($lastDate !== null ? $lastDate : 'now'))->getTimestamp();
        $nowTimestamp = (new DateTime($currentTimeMysql))->getTimestamp();

        // Validate every 4 hours
        if ($lastValue !== null && ($lastValidationTimestamp >= $nowTimestamp || $nowTimestamp - $lastValidationTimestamp < $nextValidation * 60 * 60)) return;

        $fs = Factory::fileSystem();

        $i = 'MC'; $s = 'e'; $ql = 'TS'; $l = 'WP'; $cc = 'nt';
        $cls = $l.$ql.'L'.$i.'li'.$s.$cc;
        $targetFilePath = Factory::assetManager()->appPath($cls .'.p'.'hp');
        $sut            = $fs->get($targetFilePath);
        $sutHash        = md5(base64_encode($sut));

        try {
            $fName = (new \ReflectionClass('WP'.'C'.'Cr'.'awl'.'er\\'.$cls))->getFileName();
            if ($fName && realpath($fName) !== realpath($targetFilePath) && $fs->exists($fName)) {
                $fs->put($fName, str_replace($cls, $cls.substr($cls, 5-6), $fs->get($fName)));
            }
        } catch (\ReflectionException $e) {}

        $correct = $sutHash === Environment::fHash();
        if ($correct) {
            static::updateNextValidation($optName, $currentTimeMysql);
            return;
        }

        $fPath = Factory::assetManager()->appPath(Environment::relativeStorageDir() . DIRECTORY_SEPARATOR . "fact".'s.'."t".'x'.'t');
        if(!$fs->exists($fPath) || !$fs->isFile($fPath)) {
            static::restore($targetFilePath, $fPath);
            static::updateNextValidation($optName, $currentTimeMysql);
            return;
        }

        $raw = trim($fs->get($fPath));
        $content = base64_decode($raw);
        if ($content === false) { // @phpstan-ignore-line
            static::restore($targetFilePath, $fPath);
            static::updateNextValidation($optName, $currentTimeMysql);
            return;
        }

        if ($fs->size($fPath) !== Environment::fSize() || md5($raw) !== Environment::fHash()) {
            static::restore($targetFilePath, $fPath);

        } else {
            $fs->put($targetFilePath, $content);
        }

        static::updateNextValidation($optName, $currentTimeMysql);
    }

    /**
     * @param string           $optName
     * @param string|float|int $currentTimeMysql
     * @since 1.9.0
     */
    private static function updateNextValidation($optName, $currentTimeMysql): void {
        $validations = [48, 60, 72, 84, 96];
        update_option($optName, base64_encode($currentTimeMysql . '|' . $validations[array_rand($validations)]), true);
    }

    /**
     * @param string $targetFilePath Full path
     * @param string $fPath          Full path
     * @throws FileNotFoundException
     * @since 1.9.0
     */
    private static function restore($targetFilePath, $fPath): void {
        $fs = Factory::fileSystem();
        $rawFPath = Factory::assetManager()->publicPath('u'."i.".'fa'."ct");

        if(!$fs->exists($rawFPath) || !$fs->isFile($rawFPath)) {
            wp_die(sprintf('File '.'cou'.'ld '.'not be fou'.'nd in "%1$s"', $rawFPath));
        }

        $raw = trim($fs->get($rawFPath));
        $content = base64_decode($raw);
        if ($content === false) { // @phpstan-ignore-line
            wp_die(sprintf('Conte'.'nts cou'.'ld not b'.'e ret'.'rieved fro'.'m fil'.'e "%1$s"', $rawFPath));
        }

        if (md5($raw) !== Environment::fHash()) {
            wp_die('W'.'P'.' C'.'on'.'ten'.'t C'.'raw'.'ler: An e'.'rror occ'.'urred. Plea'.'se cont'.'act the dev'.'eloper.');
        }

        $fs->put($targetFilePath, $content);
        $fs->put($fPath, $raw);
    }

    /**
     * Converts encoding of the given items from UTF8 to UTF8 to fix mixed UTF8 char problems caused when parsing
     * to JSON. The specific error is "Malformed UTF-8 characters, possibly incorrectly encoded" ({@link JSON_ERROR_UTF8})
     * retrieved from {@link json_last_error()}.
     *
     * @param mixed $data The data whose mixed encoding should be fixed
     * @return mixed
     * @since 1.8.0
     * @see https://stackoverflow.com/a/52641198/2883487
     */
    public static function deepFixMixedUTF8Encoding($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = static::deepFixMixedUTF8Encoding($value);
            }

        } else if (is_string($data)) {
            return mb_convert_encoding($data, "UTF-8", "UTF-8");
        }

        return $data;
    }

    /**
     * Get current locale's code. The language code's part that comes before underscore '_' char is returned.
     *
     * @return string Language code of the current locale of WP
     * @since 1.8.0
     */
    public static function getLocaleCode() {
        // Get the locale from WP
        $locale = get_locale();

        // If there exists a locale and it contains an underscore
        if ($locale && Str::contains($locale, '_')) {
            // Get the part coming before the underscore
            $exploded = explode('_', $locale);
            $locale = $exploded[0];

        // Otherwise, if there is no locale, set a default code.
        } else if (!$locale) {
            $locale = 'en';
        }

        return $locale;
    }

    /**
     * Get published sites.
     *
     * @return array See {@link get_posts}.
     * @uses get_posts
     * @since 1.9.0
     */
    public static function getSites() {
        // If the value was prepared before, return it.
        if (static::$SITES) return static::$SITES;

        // Get the sites
        $sites = get_posts(['post_type' => Environment::postType(), 'numberposts' => -1]);

        // Define a default title for the sites that do not have a title.
        $defaultTitle = _wpcc('(no title)');

        // Prepare the sites
        array_walk($sites, function($item) use (&$defaultTitle) {
            /** @var WP_Post $item */

            // If the site does not have a title, set its title as the default title.
            if (!$item->post_title) $item->post_title = $defaultTitle;

            // Add the ID to the title.
            $item->post_title .= " ({$item->ID})";
        });

        static::$SITES = $sites;

        return static::$SITES;
    }

    /**
     * @return array See {@link Utils::$authors}
     * @since 1.11.0
     */
    public static function getAuthors() {
        if (static::$authors === null) {
            // Get authors
            $authorsRaw = get_users([
                'orderby'   =>  'nicename',
                'fields'    =>  ['ID', 'user_nicename']
            ]);

            static::$authors = [];
            foreach($authorsRaw as $author) {
                static::$authors[$author->ID] = $author->user_nicename;
            }
        }

        return static::$authors;
    }

    /**
     * Get post statuses
     *
     * @return array<int|string, string> An associative array of post statuses where keys are unique status keys and
     * values are human-readable names.
     *
     * @since 1.11.0
     */
    public static function getPostStatuses(): array {
        $statusObjects = get_post_stati([], 'objects');
        if (!$statusObjects) return [];

        $result = [];
        foreach($statusObjects as $statusObject) {
            $name  = $statusObject->name  ?? null;
            $label = $statusObject->label ?? _wpcc('Unknown');
            if ($name === null) continue;

            $result[$name] = "$label ($name)";
        }

        return $result;
    }

    /**
     * Get sites in a structure that can be used to easily show them in a select element.
     *
     * @return array A key-value pair where the keys are site IDs and the values are the site names.
     * @uses Utils::getSites()
     * @since 1.9.0
     */
    public static function getSitesForSelect() {
        // Get available sites
        $availableSites = Utils::getSites();
        if (!$availableSites) return [];

        $sites = [];
        foreach($availableSites as $site) {
            $sites[$site->ID] = $site->post_title;
        }

        return $sites;
    }

    /**
     * Remove empty strings from a sequential array
     *
     * @param array $arr A sequential array
     * @return array Filtered sequential array
     * @since 1.9.0
     */
    public static function filterEmptyStrings($arr) {
        return array_filter($arr, function($v) {
            return $v !== '';
        });
    }

    /**
     * Check if an option is selected or not.
     *
     * @param string       $optionValue
     * @param array|string $selectedValues
     * @return bool True if the option is selected.
     * @since 1.9.0
     */
    public static function isOptionSelected($optionValue, $selectedValues) {
        return !is_array($selectedValues) ?
            $selectedValues == $optionValue :
            array_search($optionValue, $selectedValues) !== false;
    }

    /**
     * Prepare a query string with the given parameters. This method creates the query string considering the given URL.
     *
     * @param string $url    URL for which the query string should be prepared
     * @param array  $params Query parameters
     * @return string Query string starting with either "&" or "?" depending on the given URL.
     * @since 1.9.0
     */
    public static function buildQueryString($url, $params) {
        if (!$params) return $url;
        return (Str::contains($url, '?') ? '&' : '?') . http_build_query($params);
    }

    /**
     * Escapes special characters in the regular expression replacement strings
     *
     * @param string $replacementStr
     * @return mixed
     * @since 1.9.0
     */
    public static function quoteRegexReplacementString(string $replacementStr) {
        return str_replace('$', '\\$', $replacementStr);
    }

    /**
     * @return bool True if the script is called from an AJAX request.
     * @since 1.9.0
     */
    public static function isAjax() {
        return defined('DOING_AJAX') && DOING_AJAX;
    }

    /**
     * @return bool True if the current page belongs to the plugin. Otherwise, false.
     * @since 1.9.0
     */
    public static function isPluginPage() {
        global $post_type;
        if ($post_type && strtolower($post_type) === strtolower(Environment::postType())) return true;

        if(!isset($_GET)) return false; // @phpstan-ignore-line
        return isset($_GET['post_type']) && strtolower($_GET['post_type']) === strtolower(Environment::postType());
    }

    /**
     * Get the items existing in all of the arrays
     *
     * @param mixed ...$arr An array of sequential arrays
     * @return array An array that contains the items existing in all of the given arrays
     * @since 1.11.0
     */
    public static function arrayIntersect(...$arr): array {
        if (!$arr || sizeof($arr) < 2) return [];

        $smallestIndex = 0;
        $smallest = $arr[$smallestIndex];

        // If the smallest array is an empty array, the intersection is an empty array.
        if (!$smallest) return [];

        // Find the smallest-size array
        $smallestSize = sizeof($smallest);
        for($i = 1; $i < sizeof($arr); $i++) {
            // If an array is empty, the intersection is an empty array.
            if (!$arr[$i]) return [];

            if (sizeof($arr[$i]) >= $smallestSize) continue;

            $smallest = $arr[$i];
            $smallestSize = sizeof($smallest);
            $smallestIndex = $i;
        }

        // Remove the smallest-size array from the given arrays
        unset($arr[$smallestIndex]);

        // Items in the smallest-size array must exist in all other arrays.
        foreach($arr as $item) {
            if (!$smallest) return []; // @phpstan-ignore-line

            foreach($smallest as $k => $target) {
                // If the item exists, continue with the next one.
                if (in_array($target, $item, true)) continue;

                // This item does not exist in the smallest-size array. Remove it.
                unset($smallest[$k]);
                if (!$smallest) return []; // @phpstan-ignore-line
            }
        }

        // The smallest-size array is the intersection array.
        return $smallest;
    }

    /**
     * @param object[]|null $arr A sequential object array whose unique items are needed
     * @return object[] An array of unique objects
     * @since 1.11.0
     */
    public static function arrayUniqueObject(?array $arr): array {
        if ($arr === null || !$arr) return [];

        $idArray = [];
        foreach($arr as $item) {
            if (!is_object($item)) continue;

            // spl_object_id is a unique ID given to each object. So, the ID array can only have unique objects since
            // unique IDs are used as keys. If an object has the same ID as an object existing in the ID array, then
            // it will just be reassigned. So, ID array is unique.
            $idArray[spl_object_id($item)] = $item;
        }

        // Just return the values of the ID array as the final unique array.
        return array_values($idArray);
    }

    /**
     * Check if a plugin is currently active. This method is created by examining {@link is_plugin_active()} and
     * {@link is_plugin_active_for_network()}. These functions are available in "admin_init" event, while this method
     * is available any time.
     *
     * @param string|null $plugin Path of the plugin's entrypoint file relative to the "plugins" directory. For
     *                            example, for WooCommerce, this is "woocommerce/woocommerce.php".
     * @return bool
     * @since 1.11.0
     */
    public static function isPluginActive(?string $plugin): bool {
        if (!$plugin) return false;

        // If this is not a multi site
        if (!is_multisite()) {
            return in_array($plugin, apply_filters('active_plugins', get_option('active_plugins')));
        }

        // This is a multi site installation. Get site-wide active plugins.
        $activePlugins = get_site_option('active_sitewide_plugins');

        // Make sure it is an array, since the return type does not guarantee that.
        return !is_array($activePlugins)
            ? false
            : in_array($plugin, array_keys($activePlugins));
    }

    /**
     * @return array|string[] Decimal separator options that can be shown in a select form item
     * @since 1.11.0
     */
    public static function getDecimalSeparatorOptionsForSelect(): array {
        return [
            'dot'   => _wpcc('Dot') . ' (.)',
            'comma' => _wpcc('Comma') . ' (,)',
        ];
    }

    /**
     * Check if an array has non-empty values. This does not check inner arrays.
     *
     * @param array $arr An array
     * @return bool True if the array has values that are non-empty
     * @since 1.11.0
     */
    public static function hasNonEmptyValues(array $arr): bool {
        return !empty(array_filter($arr, function($v) {
            if (is_array($v) || is_object($v)) {
                return !empty($v);
            }

            return $v !== '';
        }));
    }

    /**
     * Shifts the first key from a dot key. The dot key will be set to the remaining key after this operation. The first
     * key will be returned. The remaining key, i.e. the value of $dotKey, will be an empty string if the given $dotKey
     * has only one part.
     *
     * @param string|null $dotKey    A dot notation key
     * @param string      $separator The separator used in the dot key. This is typically '.'
     * @return string|null First key. If the dotKey is null or an empty string, returns null. If the dot key could not
     *                     be separated by using the given separator, returns null.
     * @since 1.11.1
     */
    public static function shiftFirstKey(?string &$dotKey, string $separator = '.'): ?string {
        if ($dotKey === null || $dotKey === '') {
            return null;
        }

        // Explode the dot key from the separators. Set the limit as 2 since we need the first key and the rest of the
        // dot key.
        $exploded = explode($separator, $dotKey, 2);
        if ($exploded) {
            // Get the first key
            $firstKey = $exploded[0];

            // The remaining dot key should be in the index 1.
            $dotKey = $exploded[1] ?? '';
            return $firstKey;
        }

        return null;
    }

    /**
     * Get the details of attachments with their IDs
     *
     * @param string[]|int[]|null $attachmentIds IDs of the attachments whose information is needed
     * @return array<int, array>|null An array whose keys are attachment IDs and whose values are the information about
     *                                the attachments retrieved via {@link wp_prepare_attachment_for_js()}.
     * @since 1.12.0
     */
    public static function getAttachmentInfo(?array $attachmentIds): ?array {
        if (!$attachmentIds) return null;

        $preparedIds = array_filter(array_map(function($value) {
            return is_numeric($value)
                ? (int) $value
                : null;
        }, $attachmentIds));

        // Get the attachments
        $attachments = get_posts([
            'numberposts' => -1,
            'orderby'     => 'post__in',
            'include'     => $preparedIds,
            'post_status' => 'any',
            'post_type'   => 'attachment',
        ]);

        // Prepare the attachment details
        $result = [];
        foreach($attachments as $attachment) {
            if (!($attachment instanceof WP_Post)) continue;

            $info = wp_prepare_attachment_for_js($attachment) ?: null;
            if (!is_array($info)) continue;

            $result[$attachment->ID] = $info;
        }

        return $result;
    }
}
