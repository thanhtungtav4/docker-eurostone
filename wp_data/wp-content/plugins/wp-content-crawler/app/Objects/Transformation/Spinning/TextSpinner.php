<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 21/02/2019
 * Time: 18:18
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Transformation\Spinning;


use Exception;
use Illuminate\Support\Arr;
use WPCCrawler\Objects\Crawling\Bot\DummyBot;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Objects\Traits\FindAndReplaceTrait;
use WPCCrawler\Objects\Transformation\Spinning\Clients\AbstractSpinningAPIClient;
use WPCCrawler\Utils;

class TextSpinner {

    use FindAndReplaceTrait;

    /** @var array Sequential array of texts that should be spun. */
    private $texts = [];

    /** @var bool True if no request should be made, if a test spinning operation should be carried out. */
    private $dryRun;

    /** @var string */
    private $textElementTag                     = 'ts';
    /** @var string */
    private $textElementIdFormat                = 'ts-%1$s';
    /** @var string */
    private $textElementTagOpeningRegexFormat   = '^<%1$s[^>]+>';

    /**
     * @var string Format that will be used to wrap a text to mark it
     *             %1$s: Tag name. E.g. "wpcc"
     *             %2$s: ID attribute's value. E.g. "my-el-3"
     *             %3$s: The content of the HTML element. The text itself should be passed for this.
     */
    private $elementFormat = '<%1$s id="%2$s">%3$s</%1$s>';

    /**
     * @param array $texts  See {@link $texts}
     * @param bool  $dryRun See {@link $dryRun}
     */
    public function __construct($texts, $dryRun = false) {
        $this->texts  = $texts;
        $this->dryRun = $dryRun;
    }

    /**
     * @param AbstractSpinningAPIClient $client
     * @param array                     $spinningOptions Extra options that the client should use.
     * @return array Spun texts
     * @throws Exception See {@link mapTextsFromSpinResult()}
     * @since 1.9.0
     */
    public function spin(AbstractSpinningAPIClient $client, $spinningOptions = []) {
        // Extract some more protected strings and append them to the existing ones
        $client->setProtectedStrings(array_unique(array_merge($client->getProtectedStrings(), $this->extractProtectedStrings())));

        // To combine the texts, each text is wrapped in an HTML tag having an ID attribute. So, the size of the texts
        // will increase in case of combining. The length constraints defined in the chunker should consider this. The
        // limits must be assigned considering that an HTML tag with an ID attribute will be added to each text.
        $chunker = $client->createChunker($this->texts);
        $chunks = $chunker->chunk();

        $results = [];
        foreach($chunks as $chunk) {
            // Get the texts to be spun
            $texts = $client->isSendOneRequest()
                ? [$this->combineTextsIntoSingleString($chunk)]
                : $chunk;

            // Spin the texts
            $result = $this->dryRun
                ? $this->spinForDryRun($texts)
                : $client->spinBatch($texts, $spinningOptions);

            // Assign the current chunk as the final result as a fallback.
            $finalResultForChunk = $chunk;

            // Now, check if the spinning operation was successful. It was not successful if one of these true:
            //  . There is no result
            //  . There should be only one result but there are more than 1 results
            // If the operation was not successful, inform the user.
            if (!$result || ($client->isSendOneRequest() && count($result) > 1)) {
                Informer::addError(_wpcc('Spinning was not successful. Original value of the texts will be used.'))
                    ->addAsLog();

            } else {
                // Spinning was successful. Assign the spun texts as the final result for current chunk.
                $finalResultForChunk = $client->isSendOneRequest()
                    ? $this->extractTextsFromSingleString($chunk, $result[0])
                    : $result;
            }

            // Add the current results
            $results = array_merge($results, $finalResultForChunk);
        }

        // Unchunk the results
        $finalResults = $chunker->unchunk(Arr::flatten($results));
        return $finalResults;
    }

    /*
     * PRIVATE METHODS
     */

    /**
     * Create dummy spinning results for testing
     *
     * @param string[] $chunk A string array
     * @return array Dummy spinning results
     * @since 1.9.0
     */
    private function spinForDryRun(array $chunk) {
        $regex = sprintf('/(<%1$s[^>]+>)/u', $this->textElementTag);
        $mark  = '(SPINNING TEST)';

        return array_map(function($text) use (&$regex, &$mark) {
            // If there are tagged elements, add a text to all
            if (strpos($text, "<{$this->textElementTag}") !== false) {
                return $this->findAndReplaceSingle($regex, '$1' . $mark, $text, true, false);

            // Otherwise
            } else {
                $needle = '>';
                $pos = strpos($text, $needle);

                if ($pos !== false) {
                    return substr_replace($text, $needle . $mark, $pos, strlen($needle));

                } else {
                    return $mark . $text;
                }
            }

        }, $chunk);
    }

    /**
     * Combines all texts into a single string. Sending a single text containing all given texts to the API will reduce
     * the number of requests made.
     *
     * @param array $texts A one-dimensional key-value pair where keys are the identifiers and the values are strings.
     *                     The keys of this array will be used in the ID attribute to uniquely tag each item.
     * @return string
     * @since 1.9.0
     */
    private function combineTextsIntoSingleString(array $texts) {
        $preparedTexts = [];
        foreach($texts as $index => $text) {
            $preparedTexts[] = $this->wrapTextWithHtmlTag($text, $index);
        }

        return implode("\n\n", $preparedTexts);
    }

    /**
     * @param string $text The text that should be wrapped
     * @param int    $index Index of the text or an integer that uniquely identifies the text
     * @return string The text wrapped in the HTML tag specified in {@link textElementTag}, using {@link elementFormat}
     *                and {@link textElementIdFormat}.
     * @since 1.9.0
     */
    private function wrapTextWithHtmlTag(string $text, int $index) {
        return sprintf($this->elementFormat, $this->textElementTag, sprintf($this->textElementIdFormat, $index), $text);
    }

    /**
     * Extracts the texts from a single text created by {@link combineTextsIntoSingleString()}.
     *
     * @param array  $texts      A one-dimensional key-value pair where keys are the identifiers and the values are
     *                           strings. The keys will be used to find the HTML elements created for that key. So, IDs
     *                           of HTML elements should match the keys of this array. In other words, this array must
     *                           be the same array used in {@link combineTextsIntoSingleString()} to combine the texts
     *                           into single string.
     * @param string $singleText Single text created by {@link combineTextsIntoSingleString()}.
     * @return array An array of texts in the order of original {@link $texts}
     * @since 1.9.0
     */
    private function extractTextsFromSingleString(array $texts, string $singleText) {
        // Create a dummy crawler from the text so that we can extract the texts using their ID
        $dummyBot = new DummyBot([]);
        $crawler = $dummyBot->createDummyCrawler($singleText);

        $results = [];
        foreach($texts as $index => $text) {
            // Create the ID for this text
            $id = sprintf($this->textElementIdFormat, $index);

            // Find this text in the given text
            $node = $crawler->filter($this->textElementTag . '[id="' . $id . '"]')->first();

            // If the node is not found, a node object with no elements is returned. So, if the count of elements in the
            // node is not 1, it means the target text is not found. We need exactly 1 element.
            if ($node->count() !== 1) {
                $results[$index] = $text;
                continue;
            }

            // Get the HTML
            $html = Utils::getNodeHTML($node);

            // Now, we need to remove the surrounding tag we created previously.
            $html = $this->findAndReplaceSingle(
                sprintf($this->textElementTagOpeningRegexFormat, $this->textElementTag),
                '',
                $html,
                true
            );

            $html = $this->findAndReplaceSingle("</{$this->textElementTag}>", '', $html);

            // When a crawler is created, HTML special chars in the source HTML are escaped. Here, we revert it back.
            $html = htmlspecialchars_decode($html, ENT_QUOTES);

            $results[$index] = $html;
        }

        return $results;
    }

    /**
     * Get the words that should not be spun. This method extracts short codes existing in the given texts.
     *
     * @return array An array of strings that should not be spun
     * @since 1.9.0
     */
    private function extractProtectedStrings() {
        // Get the registered short code tags
        global $shortcode_tags;
        if (!$shortcode_tags || !is_array($shortcode_tags)) return [];

        // Get all tag names
        $tags = array_keys($shortcode_tags);

        // Prepare a regex that matches the opening and closing tags registered to WordPress globally
        $tagsForRegex = join('|', array_map(function ($tag) {
            return preg_quote($tag, '/');
        }, $tags));

        $regex = sprintf('/\[\/?(?:%1$s)(?:\s[^\]]*)?\]/i', $tagsForRegex);

        $results = [];
        $matches = [];
        foreach($this->texts as $text) {
            // Match all possible opening and closing tags in this text
            preg_match_all($regex, $text, $matches, PREG_PATTERN_ORDER);

            // We need the full matches. They are stored in index 0 as an array.
            if (!$matches || !isset($matches[0]) || !$matches[0]) continue;

            // Add the matches to the result.
            $results = array_merge($results, $matches[0]);
        }

        return array_unique($results);
    }

}