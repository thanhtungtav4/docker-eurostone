<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 21/12/2018
 * Time: 11:37
 *
 * @since 1.8.0
 */

namespace WPCCrawler\Objects\Traits;


use WPCCrawler\Objects\File\FileService;

trait ShortCodeReplacerAndFindAndReplace {

    use FindAndReplaceTrait;
    use ShortCodeReplacer;

    /**
     * Replaces short codes considering the existence of a file name with a short code in it. Basically, since short
     * codes in the file name have different opening and closing brackets, in addition to replacing short codes with
     * regular opening and closing brackets, performs another replacement considering the brackets in the file name as
     * well.
     *
     * @param array $map        See {@link ShortCodeReplacer::replaceShortCodes()}
     * @param array $templates  See {@link ShortCodeReplacer::replaceShortCodes()}
     * @param array $frForMedia Find and replaces for media URLs. See
     *                          {@link PostTemplatePreparer::applyShortCodesAndFindReplaces()}
     * @return array
     * @uses  ShortCodeReplacer::replaceShortCodes()
     * @uses  PostTemplatePreparer::applyShortCodesAndFindReplaces()
     * @since 1.8.0
     */
    protected function applyShortCodesConsideringFileName(&$map, $templates, $frForMedia) {
        $templates = $this->applyShortCodesAndFindReplaces($templates, $map, $frForMedia);
        return $this->replaceShortCodes($map, $templates, null, 
            FileService::SC_OPENING_BRACKETS, FileService::SC_CLOSING_BRACKETS);
    }

    /**
     * Replaces short codes considering the existence of a file name with a short code in it. Basically, since short
     * codes in the file name have different opening and closing brackets, in addition to replacing short codes with
     * regular opening and closing brackets, performs another replacement considering the brackets in the file name as
     * well.
     * 
     * @param array  $map        See {@link ShortCodeReplacer::replaceShortCodesSingle()}
     * @param string $template   See {@link ShortCodeReplacer::replaceShortCodesSingle()}
     * @param array  $frForMedia Find and replaces for media URLs. See
     *                           {@link PostTemplatePreparer::applyShortCodesAndFindReplaces()}
     * @return string
     * @since 1.11.1
     */
    protected function applyShortCodesConsideringFileNameSingle(array &$map, string $template, array $frForMedia): string {
        $template = $this->applyShortCodesAndFindReplacesSingle($template, $map, $frForMedia);
        return $this->replaceShortCodesSingle($map, $template, null, 
            FileService::SC_OPENING_BRACKETS, FileService::SC_CLOSING_BRACKETS);
    }

    /**
     * Applies find-replace options to the single subject and then replaces the short codes in the subject
     *
     * @param string      $subject       See {@link applyShortCodesAndFindReplaces()}
     * @param array       $shortCodeMap  See {@link applyShortCodesAndFindReplaces()}
     * @param array       $findReplaces  See {@link applyShortCodesAndFindReplaces()}
     * @param null|string $innerKey      See {@link applyShortCodesAndFindReplaces()}
     * @return string The subject with all replacements applied
     * @uses  applyShortCodesAndFindReplaces()
     * @since 1.11.1
     */
    protected function applyShortCodesAndFindReplacesSingle(string $subject, array &$shortCodeMap, array &$findReplaces, 
                                                            ?string $innerKey = null): string {
        $result = $this->applyShortCodesAndFindReplaces([$subject], $shortCodeMap, $findReplaces, $innerKey);
        
        // If the result is a non-empty array, return its first item. Otherwise, return the original subject.
        return $result
            ? array_values($result)[0]
            : $subject;
    }

    /**
     * Applies find-replace options to the subject and then replaces the short codes in the subject
     *
     * @param array       $subjects      See {@link FindAndReplaceTrait::applyFindAndReplaces()} and
     *                                   {@link ShortCodeReplacer::replaceShortCodes()}
     * @param array       $shortCodeMap  See {@link ShortCodeReplacer::replaceShortCodes()}
     * @param array       $findReplaces  See {@link FindAndReplaceTrait::applyFindAndReplaces()}
     * @param null|string $innerKey      See {@link FindAndReplaceTrait::applyFindAndReplaces()} and
     *                                   {@link ShortCodeReplacer::replaceShortCodes()}
     * @return array The subject with all replacements applied
     * @uses  FindAndReplaceTrait::applyFindAndReplaces()
     * @uses  ShortCodeReplacer::replaceShortCodes()
     * @since 1.8.0
     */
    protected function applyShortCodesAndFindReplaces($subjects, &$shortCodeMap, &$findReplaces, $innerKey = null) {
        return $this->replaceShortCodes($shortCodeMap,
            $this->applyFindAndReplaces($findReplaces, $subjects, $innerKey),
            $innerKey
        );
    }

}