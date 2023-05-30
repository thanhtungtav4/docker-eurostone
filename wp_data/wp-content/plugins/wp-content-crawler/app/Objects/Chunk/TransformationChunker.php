<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 10/11/2019
 * Time: 08:58
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Chunk;

use DOMElement;
use DOMNode;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;
use WPCCrawler\Objects\Chunk\Enum\ChunkType;
use WPCCrawler\Objects\Chunk\LengthStrategy\AbstractLengthStrategy;
use WPCCrawler\Objects\Crawling\Bot\DummyBot;
use WPCCrawler\Objects\Settings\Enums\SettingInnerKey;
use WPCCrawler\Objects\Traits\FindAndReplaceTrait;
use WPCCrawler\Objects\Traits\ShortCodeReplacer;
use WPCCrawler\Utils;

/**
 * Chunks a given HTML into several parts. APIs used to translate or spin the texts mandate certain limits. For
 * example, a few of them limit the number of words, a few of them limit the number of characters, while others limit
 * the number of bytes that can be sent in one request. Additionally, APIs limit how many items can be sent in one
 * request. To be able to satisfy these limits, HTML should be divided into logical parts in a way that outputs a valid
 * HTML. We also need to hint the APIs to not change certain things, such as short codes. Moreover, many items should be
 * transformed. For instance, a post has many tags, a title, a content, post meta values, taxonomy values, and so on. We
 * cannot directly send them to the APIs, because we need to find the transformed texts from the response of APIs. They
 * should be sent in an order to the APIs and retrieved back in the same order, which the APIs guarantee. When the
 * response is retrieved, each item's transformed value should be put back in their original places. This class handles
 * all of these in a simple but a powerful way.
 *
 * The class provides a simple {@link chunk()} and a simple {@link unchunk()} methods. {@link chunk()} prepares the
 * texts and puts them in arrays each of which can be directly sent to the APIs. {@link unchunk()} method puts back the
 * transformed texts into their original places.
 *
 * @package WPCCrawler\Objects\Chunk
 * @since   1.8.1
 */
class TransformationChunker {

    use FindAndReplaceTrait;
    use ShortCodeReplacer;

    /** @var AbstractLengthStrategy */
    private $lengthStrategy;

    /**
     * @var array Flat (1-dimensional) associative array where keys are dot keys and the values are strings. Return
     *            value retrieved from {@link ValueExtractor::fillAndFlatten()} can be directly supplied.
     */
    private $texts;

    /** @var int Maximum length per item */
    private $maxLength;

    /** @var int Maximum length per chunk */
    private $maxLengthPerChunk;

    /** @var int Maximum number of items that can be in a chunk */
    private $maxItemCountPerChunk;

    /*
     *
     */

    /** @var array Stores prepared long texts. Structure: [dotKey => preparedLongText] */
    private $longTextsPrepared = [];

    /** @var array|null Flattened {@link texts} */
    private $flattened;

    /** @var DummyBot */
    private $dummyBot;

    /** @var string Format for IDs that will be set to the elements needing to be translated. */
    private $translateNodeIdFormat = "wpcct%s";

    /** @var string Tag name that will be used to encapsulate divided texts */
    private $translateElementTagName = "w";

    /**
     * @var bool If true, the texts will not be translated. Instead, they will be appended dummy values to mock the
     *      translation.
     */
    private $dryRun = false;

    /*
     * VARIABLES USED TO HANDLE SHORT CODES
     */

    /** @var string Tag of the HTML elements that store short code data */
    private $shortCodeElementTagForOpening = 'wso';

    /** @var string Tag of the HTML elements that store the closing tag of the short codes */
    private $shortCodeElementTagForClosing = 'wsc';

    /**
     * @var string The short code elements' name attribute that stores short code name E.g. for [wpcc-script
     *      src="..." async] short code, this attribute stores "wpcc-script"
     */
    private $shortCodeElementNameAttr = 'data-name';

    /**
     * @var string The short code elements' options attribute that stores short code options. E.g. for [wpcc-script
     *      src="..." async] short code, this attribute stores 'src="..." async'.
     */
    private $shortCodeElementOptionsAttr = 'data-options';

    /**
     * @var null|string|false Stores the match for short code matching regex. Basically, it is pipe-separated and
     * regex-quoted short code names.
     */
    private $shortCodeMatchPartForRegex = null;

    /**
     * @var null|string|false Stores the regular expression that matches the short codes that are defined in WordPress.
     *                        $1: Short code name
     *                        $2: Short code options
     */
    private $shortCodeOpeningTagMatchRegex = null;

    /**
     * @var null|string|false Stores the regular expression that matches the closing tags of the short codes that are
     *                        defined in WordPress.
     *                        $1: Short code name
     */
    private $shortCodeClosingTagMatchRegex = null;

    /**
     * @var string Regular expression that matches the added HTML tag's opening part. The tag is added to mark the
     *      elements to be able to find them later.
     */
    private $translateElOpenTagFindRegex;

    /**
     * @var string Regular expression that matches the added HTML tag's closing part. The tag is added to mark the
     *      elements to be able to find them later.
     */
    private $translateElCloseTagFindRegex;

    /**
     * NOTE: When specifying a max length, subtract about 100-200 chars from the max possible length. Because, the
     * texts are added IDs in order to mark them and substitute them when unchunking. E.g. If max allowed value is
     * 10000, write 9800 so that each item in the chunk will be less than 10k.
     *
     * @param int   $chunkType            One of the constants defined in {@link ChunkType}
     * @param array $texts                See {@link texts}
     * @param int   $maxLength            See {@link maxLength}
     * @param int   $maxLengthPerChunk    See {@link maxLengthPerChunk}
     * @param int   $maxItemCountPerChunk See {@link maxItemCountPerChunk}
     * @throws Exception See {@link ChunkType::getLengthStrategyForType()}
     * @since 1.9.0
     */
    public function __construct(int $chunkType, array $texts, int $maxLength, int $maxLengthPerChunk, int $maxItemCountPerChunk) {
        $this->lengthStrategy       = ChunkType::getLengthStrategyForType($chunkType);
        $this->texts                = $texts;
        $this->maxLength            = $maxLength;
        $this->maxLengthPerChunk    = $maxLengthPerChunk;
        $this->maxItemCountPerChunk = $maxItemCountPerChunk;

        $this->dummyBot = new DummyBot([]);

        $this->translateElOpenTagFindRegex  = sprintf('^<%1$s[^>]*>', $this->translateElementTagName);
        $this->translateElCloseTagFindRegex = sprintf('<\/%1$s>$', $this->translateElementTagName);
    }

    /**
     * Chunks {@link $texts} considering the length and count constraints
     *
     * @return array An array of sequential string arrays
     * @since 1.9.0
     */
    public function chunk(): array {
        // Flatten the original texts
        $this->flattened = $this->flattenTexts($this->getTexts());

        // Prepare the texts
        $values  = $this->getValuesFromFlattened();
        $chunks  = array_chunk($values, $this->getMaxItemCountPerChunk());

        // If there is a max text constraint per chunk, create chunks such that total length of the texts in each chunk
        // does not exceed the given limit.
        if ($this->getMaxLengthPerChunk() > 0) {
            $newChunks = [];
            foreach($chunks as $chunk) {
                $newChunks = array_merge($newChunks, $this->chunkChunkByTotalTextLength($chunk));
            }

            $chunks = $newChunks;
        }

        return $chunks;
    }

    /**
     * Unchunks the chunks. In other words, remaps the chunks into their original positions in {@link texts}.
     *
     * @param array $flatChunks Chunks retrieved from {@link chunk()} as a 1-dimensional array, in the same order. In
     *                          case of multiple 1-dimensional arrays, {@link Arr::flatten()} can be used to turn the
     *                          array into a 1-dimensional one.
     * @param int   $startIndex Index of flattened array item that corresponds to the 0th item of $flatChunks
     * @return array An associative array whose structure is the same as the format of {@link texts}.
     * @throws Exception When $startIndex is not valid.
     * @since 1.9.0
     */
    public function unchunk(array $flatChunks, int $startIndex = 0): array {
        // Change values of the original flattened array with the values of the given $flatChunks
        $newFlattened = $this->flattened ?: [];
        for($i = $startIndex; $i < sizeof($flatChunks); $i++) {
            if(!isset($this->flattened[$i])) throw new Exception("Item with start index {$i} does not exist in flattened array.");

            $newFlattened[$i]["value"] = $flatChunks[$i];
        }

        $texts = $this->expandFlattenedTexts($newFlattened);

        return $texts;
    }

    /*
     * PRIVATE METHODS
     */

    /**
     * Extract values from the flattened array
     *
     * @return array A sequential array of texts extracted from {@link $flattened}
     */
    private function getValuesFromFlattened(): array {
        if ($this->flattened === null) return [];
        return array_column($this->flattened, "value");
    }

    /**
     * Chunks the given chunk (flat array) such that total length of texts in a chunk does not exceed the total
     * length returned by {@link getMaxLengthPerChunk()}. The given array should not have keys. The array must contain
     * only strings as values. The array must not contain inner arrays.
     *
     * Note: This method does not change the indices of the given array. It chunks the array by checking every next
     * item. So, this does not chunk the array in an optimal way.
     *
     * Note: If there is an item whose length exceeds the defined max length per chunk, a chunk will be created for
     * that item itself. This method does not cut the texts.
     *
     * @param array $chunk A flat array (i.e. no inner arrays) containing text as values
     * @return array An array of chunks
     */
    private function chunkChunkByTotalTextLength($chunk): array {
        $maxLengthPerChunk = $this->getMaxLengthPerChunk();

        // Stores the final chunks
        $chunks = [];

        // Stores current total character count for the next chunk.
        $currentTotal = 0;

        // Stores the index of the item that was last included into a chunk.
        $lastUsedIndex = -1;

        $arrLength = sizeof($chunk);
        $lastItemIndex = $arrLength - 1;

        for($i = 0; $i < $arrLength; $i++) {
            $currentTotal += $this->getLengthFor($chunk[$i]);

            // If the current total is greater than or equal to max length per chunk
            if($currentTotal >= $maxLengthPerChunk) {

                // If the total length equals the max length
                if($currentTotal == $maxLengthPerChunk) {
                    // Create a chunk from last used index to the current index, including the current index.
                    $chunks[] = array_slice($chunk, $lastUsedIndex + 1, $i - $lastUsedIndex);

                // If current total is greater than the limit
                } else {

                    // If there is only one item that exceeds the total chunk length, create a chunk for that item.
                    if($i - $lastUsedIndex == 1) {
                        $chunks[] = array_slice($chunk, $lastUsedIndex + 1, 1);

                    // Otherwise
                    } else {
                        // Create chunk from last used index to the current index, excluding the current index.
                        $chunks[] = array_slice($chunk, $lastUsedIndex + 1, $i - $lastUsedIndex - 1);

                        // Decrease the index by 1, since we did not include the current index into a chunk. So,
                        // start counting the total from the current index.
                        $i--;
                    }

                }

                // Assign the current index as the last used index.
                $lastUsedIndex = $i;

                // Reset the current total, since we already created a chunk and are starting over.
                $currentTotal = 0;

            // If the current total is not greater than or equal to max text length per chunk and this is the last item
            // in the given array, create a chunk including all left-out items.
            } else if ($i == $lastItemIndex) {
                $chunks[] = array_slice($chunk, $lastUsedIndex + 1, $i - $lastUsedIndex);
            }
        }

        return $chunks;
    }

    /**
     * Flattens a multidimensional texts array. Divides the texts into several pieces if they are longer than the
     * maximum allowed length.
     *
     * @param array       $texts               An array. It can have inner arrays.
     * @param null|array  $flattened           No need to pass a value to this. Used in recursion.
     * @param null|string $parentDotNotatedKey No need to pass a value to this. Used in recursion.
     * @param int         $depth               No need to pass a value to this. Used in recursion.
     * @return array
     */
    private function flattenTexts(array $texts, ?array $flattened = null, ?string $parentDotNotatedKey = null,
                                  int $depth = 0): array {
        if(!$flattened) $flattened = [];

        $maxValueLength = $this->getMaxLength();

        foreach($texts as $key => $value) {
            // Prepare the dot key.
            $dotKey = $parentDotNotatedKey ? ($parentDotNotatedKey . "." . $key) : $key;

            // If value is an array, recursively repeat the operation.
            if(is_array($value)) {
                $flattened = $this->flattenTexts($value, $flattened, $dotKey, $depth + 1);

            // If we finally reached a non-array value, we can add it to the flattened array.
            } else {
                // Replace short codes with HTML elements so that they won't be changed by translation service
                $this->replaceShortCodesWithHtmlElement($value);

                $length = $this->getLengthFor($value);

                // If there is no need to divide the value into several substrings, we can directly add it.
                if($maxValueLength < 1 || $length <= $maxValueLength) {
                    $flattened[] = [
                        "key"       => $dotKey,
                        "value"     => $value,
                        "length"    => $length,
                    ];

                // Otherwise, let's divide the value into small pieces.
                } else {
                    // Since we want to translate the texts, it is important that we divide the text into small pieces
                    // from the end of the paragraphs or the sentences for a better translation. After separation,
                    // add all the substrings to the flattened array.
                    //
                    // * NOTE THAT * the HTML must be valid after division.
                    //  . We can create a Crawler for this text, find the nodes that contain texts, assign them a
                    // unique ID, and then add each of them to the flattened array one by one, with their unique element
                    // ID. After that, after the translation, we can replace the text nodes with the translated HTML and
                    // remove the IDs.

                    // Create a dummy Crawler
                    $dummyCrawler = $this->dummyBot->createDummyCrawler($value);

                    // Prepare the crawler for translation. Here, the elements that need to be translated are marked with
                    // IDs and classes. This marking is done by considering the maximum length constraint.
                    $nextId = 0;
                    $dummyCrawler->filter("body > div")->each(function($node) use (&$maxValueLength, &$nextId) {
                        /** @var Crawler $node */
                        $domNode = $node->getNode(0);
                        if ($domNode === null) return;

                        $this->prepareNodeForTranslation($domNode, $maxValueLength, $nextId);
                    });

                    // Find the elements that need to be translated and add them to the flattened array with their IDs.
                    $count = 0;
                    $dummyCrawler->filter(sprintf("[id*=%s]", sprintf($this->translateNodeIdFormat, '')))->each(function($node) use (&$count, &$flattened, &$dotKey) {
                        /** @var Crawler $node */
                        /** @var DOMElement $element */
                        $element = $node->getNode(0);

                        $id = $element->getAttribute("id");
                        $html = Utils::getNodeHTML($node);
                        $length = $this->getLengthFor($html);

                        $flattened[] = [
                            "key"            => $dotKey . "." . $count,
                            "value"          => $html,
                            "length"         => $length,
                            "element_id"     => $id,
                            "parent_dot_key" => $dotKey,
                        ];
                        $count++;
                    });

                    // Store the prepared long text.
                    $text = trim($this->dummyBot->getContentFromDummyCrawler($dummyCrawler), "\n");

                    $this->longTextsPrepared[$dotKey] = $text;
                }
            }

        }

        return $flattened;
    }

    /**
     * Replaces the short codes inside the value with short code HTML elements so that they will not be translated.
     * See {@link $shortCodeElementTag}, {@link $shortCodeElementNameAttr}, and {@link $shortCodeElementOptionsAttr}.
     *
     * @param string $value
     * @since 1.9.0
     */
    private function replaceShortCodesWithHtmlElement(&$value): void {
        // If there is no value or the value does not have a short code, nothing to do.
        if (!$value || !$this->hasShortCode($value)) return;

        // Get the regex that matches the short codes' opening parts
        $openingRegex = $this->getShortCodeMatchRegexForOpeningTag();

        // If there is no regex, nothing to do.
        if (!$openingRegex) return;

        // Match the short codes
        preg_match_all($openingRegex, $value, $matches);

        // If there is no match, nothing to do.
        if (!$matches || sizeof($matches) < 2) return;

        // $matches contain each capture group under the group's index. The regex matches three groups. $0 is the entire
        // short code, $1 is the short code name, and $2 is the short code options. So, $matches must have 3 indices as
        // 0, 1, and 2.
        $matchCount = sizeof($matches[0]);
        for($i = 0; $i < $matchCount; $i++) {
            $entireShortCode    = $matches[0][$i];
            $scName             = $matches[1][$i];
            $scOptions          = isset($matches[2]) && isset($matches[2][$i]) ? $matches[2][$i] : '';

            // Create the HTML element
            $htmlElement = $this->createShortCodeHtmlElementOpeningTag($scName, $scOptions);
            if (!$htmlElement) continue;

            // Replace the short code in the value with the HTML element
            $value = $this->findAndReplaceSingle($entireShortCode, $htmlElement, $value);
        }

        // Replace the closing tags of the short codes
        $closingRegex = $this->getShortCodeMatchRegexForClosingTag();
        if (!$closingRegex) return;

        $value = $this->findAndReplaceSingle(
            $closingRegex,
            Utils::quoteRegexReplacementString($this->createShortCodeHtmlElementClosingTag('$1')),
            $value,
            true
        );
    }

    /**
     * Creates a dummy HTML element for a short code.
     *
     * @param string      $shortCodeName    Name of the short code. See {@link $shortCodeElementNameAttr}.
     * @param string|null $shortCodeOptions Options of the short code. See {@link $shortCodeElementOptionsAttr}.
     * @return null|string An HTML element string whose tag is {@link $shortCodeElementTag}. The name and options
     *                     values are stored in {@link $shortCodeElementNameAttr} and
     *                     {@link $shortCodeElementOptionsAttr}, respectively. E.g. for [wpcc-script async src="..."],
     *                     this will be returned:
     *                     &lt;wpcc-sc-opening data-name="wpcc-script" data-options="async src="...""&gt;&lt;/wpcc-sc-opening&gt;
     * @since 1.9.0
     */
    private function createShortCodeHtmlElementOpeningTag($shortCodeName, $shortCodeOptions = null): ?string {
        if (!$shortCodeName) return null;

        $optionsPart = '';
        if ($shortCodeOptions) $optionsPart = sprintf(' %1$s="%2$s"', $this->shortCodeElementOptionsAttr, htmlspecialchars($shortCodeOptions));

        return sprintf('<%1$s %2$s="%3$s"%4$s></%1$s>',
            $this->shortCodeElementTagForOpening,
            $this->shortCodeElementNameAttr,
            $shortCodeName,
            $optionsPart
        );
    }

    /**
     * Creates a dummy HTML element for a short code.
     *
     * @param string $shortCodeName Name of the short code. See {@link $shortCodeElementNameAttr}.
     * @return string An HTML element string whose tag is {@link $shortCodeElementTag}. The name is stored in
     *                {@link $shortCodeElementNameAttr}. E.g. for [/wpcc-script],
     *                this will be returned:
     *                &lt;wpcc-sc-closing data-name="wpcc-script"&gt;&lt;/wpcc-sc-closing&gt;
     * @since 1.9.0
     */
    private function createShortCodeHtmlElementClosingTag(string $shortCodeName): string {
        return sprintf('<%1$s %2$s="%3$s"></%1$s>',
            $this->shortCodeElementTagForClosing,
            $this->shortCodeElementNameAttr,
            $shortCodeName
        );
    }

    /**
     * Get the regular expression that matches any short code defined in WordPress.
     *
     * @return false|string If there is no regex, false. Otherwise, the regex. The matches are: <ul>
     *                      <li>$0: Full short code. E.g. [wpcc-script async src="..."]</li>
     *                      <li>$1: Short code name. E.g. wpcc-script</li>
     *                      <li>$2: Short code options (might not exist). E.g. async src="..."</li>
     *                      </ul>
     * @since 1.9.0
     */
    private function getShortCodeMatchRegexForOpeningTag() {
        // If the regex was prepared before, return it.
        if ($this->shortCodeOpeningTagMatchRegex !== null) return $this->shortCodeOpeningTagMatchRegex;

        // Get the match part
        $matchPart = $this->getShortCodeMatchPartForRegex();
        if (!$matchPart) {
            $this->shortCodeOpeningTagMatchRegex = false;
            return false;
        }

        // $0: Entire short code
        // $1: Short code name
        // $2: Short code options
        $regex = '/\[(' . $matchPart . ')(?:\s([^\s].*?))?\]/';

        $this->shortCodeOpeningTagMatchRegex = $regex;
        return $this->shortCodeOpeningTagMatchRegex;
    }

    /**
     * Get the regular expression that matches the closing tag of any short code defined in WordPress.
     *
     * @return false|string If there is no regex, false. Otherwise, the regex. The matches are: <ul>
     *                      <li>$0: Full closing tag. E.g. [/wpcc-script]</li>
     *                      <li>$1: Short code name. E.g. wpcc-script</li>
     *                      </ul>
     * @since 1.9.0
     */
    private function getShortCodeMatchRegexForClosingTag() {
        // If the regex was prepared before, return it.
        if ($this->shortCodeClosingTagMatchRegex !== null) return $this->shortCodeClosingTagMatchRegex;

        // Get the match part
        $matchPart = $this->getShortCodeMatchPartForRegex();
        if (!$matchPart) {
            $this->shortCodeClosingTagMatchRegex = false;
            return false;
        }

        // $0: Entire closing tag
        // $1: Short code name
        $regex = '/\[\/(' . $matchPart . ')\]/';

        $this->shortCodeClosingTagMatchRegex = $regex;
        return $this->shortCodeClosingTagMatchRegex;
    }

    /**
     * @return false|string See {@link $shortCodeMatchPartForRegex}.
     * @since 1.9.0
     */
    private function getShortCodeMatchPartForRegex() {
        if ($this->shortCodeMatchPartForRegex !== null) return $this->shortCodeMatchPartForRegex;

        // We need to change all short code tags defined in WordPress.
        global $shortcode_tags;

        if (!$shortcode_tags) {
            $this->shortCodeMatchPartForRegex = false;
            return false;
        }

        // Get the names of the short codes
        $shortCodeNames = array_keys($shortcode_tags);

        // Create the regex that matches only the shorts defined in WordPress.
        $matchPart = implode('|', array_map(function($v) {
            return preg_quote((string) $v);
        }, $shortCodeNames));

        $this->shortCodeMatchPartForRegex = $matchPart;
        return $this->shortCodeMatchPartForRegex;
    }

    /**
     * Expands a flattened texts array.
     *
     * @param array $flattened     A flattened text array retrieved from {@link flattenTexts}
     * @return array Expanded array
     */
    private function expandFlattenedTexts($flattened): array {
        // Recreate the original text array by using the dot-notation keys in flattened array
        $texts = [];
        $combinedContent = null;

        /** @var array<string, Crawler> $crawlers Stores crawlers for long texts. Structure: [string dot_key => Crawler crawler] */
        $crawlers = [];

        for($i = 0; $i < sizeof($flattened); $i++) {
            $item = $flattened[$i];

            $dotKey         = $item["key"];
            $value          = $this->revertShortCodeHtmlElements($item["value"]); // Revert the short code HTML elements back to the short codes
            $parentDotKey   = Utils::array_get($item, "parent_dot_key", null);
            $elementId      = Utils::array_get($item, "element_id");

            if($parentDotKey !== null && $elementId) {
                if(!isset($crawlers[$parentDotKey])) {
                    $longText = Utils::array_get($this->longTextsPrepared, $parentDotKey);

                    // Revert the operation that replaced short codes with dummy HTML elements
                    $longText = $this->revertShortCodeHtmlElements($longText);

                    $crawlers[$parentDotKey] = $this->dummyBot->createDummyCrawler($longText);
                }

                $crawler = $crawlers[$parentDotKey];
                $this->dummyBot->findAndReplaceInElementHTML($crawler, ['[id="'. $elementId . '"]'], '^[\S\s]*?$', Utils::quoteRegexReplacementString($value), true);

            } else {
                // Before setting the value, revert the operation that replaced short codes with dummy HTML elements
                Arr::set($texts, $dotKey, $this->revertShortCodeHtmlElements($value));
            }

        }

        // If there are crawlers, get their content and assign to the related dot key.
        if($crawlers) {
            $idSelector       = "[id*=" . sprintf($this->translateNodeIdFormat, '') . "]";
            $translateIdRegex = sprintf($this->translateNodeIdFormat, "[0-9]+");
            $unwrapSelector   = $this->translateElementTagName;

            foreach($crawlers as $dotKey => $crawler) {
                // Remove added IDs
                $this->dummyBot->findAndReplaceInElementAttribute($crawler, [$idSelector], 'id', $translateIdRegex, '', true);

                // Remove empty ID attributes
                $crawler->filter('[id]')->each(function($node) {
                    /** @var Crawler $node */
                    if ($node->attr('id') !== '') return;

                    $domNode = $node->getNode(0);
                    if (!($domNode instanceof DOMElement)) return;

                    $domNode->removeAttribute('id');
                });

                // Find elements to be unwrapped from their encapsulating tags and create an array storing what to find
                // and with what to replace to unwrap the surrounding tags. We are doing the replacement operation twice.
                // This is because we cannot directly remove surrounding tags. Hence, we need to do it in the raw text
                // instead of in Crawler. So, we find the elements to be unwrapped, get their HTML, remove the surrounding
                // tags, and after this, use this information to get rid of surrounding tags in the raw text of source HTML.
                $findAndReplaces = [];
                $crawler->filter($unwrapSelector)->each(function($node) use (&$findAndReplaces) {
                    /** @var Crawler $node */
                    $html       = Utils::getNodeHTML($node);
                    $replaced   = $this->unwrapTranslateElementTagName($html);
                    $findAndReplaces[] = [
                        SettingInnerKey::FIND    => $html,
                        SettingInnerKey::REPLACE => $replaced
                    ];
                });

                // Get the prepared content
                $content = $this->dummyBot->getContentFromDummyCrawler($crawler);

                // Remove the surrounding tags.
                $content = $this->findAndReplace($findAndReplaces, $content, false);

                // Assign the content to the related key
                Arr::set($texts, (string) $dotKey, trim($content));
            }
        }

        return $texts;
    }

    /**
     * Revert the operation that replaced short codes with dummy HTML elements, i.e.
     * {@link replaceShortCodesWithHtmlElement()}
     *
     * @param string $value
     * @return string The value whose short code elements are reverted to the short codes.
     * @since 1.9.0
     */
    private function revertShortCodeHtmlElements($value) {
        // If there is no value, no need to proceed.
        if (!$value) return $value;

        /** @var Crawler|null $crawler */
        $crawler = null;

        // Stores the find-replaces that will replace the HTMLs with actual short codes
        $findAndReplaces = [];

        // If it has opening element
        if (Str::contains($value, '<' . $this->shortCodeElementTagForOpening)) {
            if (!$crawler) $crawler = $this->dummyBot->createDummyCrawler($value);

            $crawler->filter($this->shortCodeElementTagForOpening)->each(function($node) use (&$findAndReplaces) {
                /** @var Crawler $node */
                $html = Utils::getNodeHTML($node);
                $scName = $node->attr($this->shortCodeElementNameAttr);
                $scOptions = $node->attr($this->shortCodeElementOptionsAttr);

                $shortCode = sprintf('[%1$s%2$s]', $scName, $scOptions ? ' ' . htmlspecialchars_decode($scOptions) : '');
                $findAndReplaces[] = $this->createFindReplaceConfig($html, $shortCode);
            });
        }

        // If it has closing element
        if (Str::contains($value, '<' . $this->shortCodeElementTagForClosing)) {
            if (!$crawler) $crawler = $this->dummyBot->createDummyCrawler($value);

            $crawler->filter($this->shortCodeElementTagForClosing)->each(function($node) use (&$findAndReplaces) {
                /** @var Crawler $node */
                $html = Utils::getNodeHTML($node);
                $scName = $node->attr($this->shortCodeElementNameAttr);

                $findAndReplaces[] = $this->createFindReplaceConfig($html, "[/{$scName}]");
            });
        }

        // If there are no replacements or there is no Crawler stop.
        if (!$findAndReplaces || !$crawler) return $value;

        // Get the content from the Crawler
        $content = rtrim($this->dummyBot->getContentFromDummyCrawler($crawler), "\n");

        // Replace the short code elements with actual short codes
        $value = $this->findAndReplace($findAndReplaces, $content, false);

        return $value;
    }

    /**
     * Prepares the node for text translation. The preparation is done by adding IDs and classes to the elements that
     * need to be translated.
     *
     * @param DOMNode $node
     * @param int     $maxValueLength         Maximum length a text can have.
     * @param int     $nextTranslationId      ID of the next to-be-translated element. Pass a variable for this
     *                                        parameter so that the count can be tracked.
     */
    private function prepareNodeForTranslation(DOMNode $node, int $maxValueLength = 0, int &$nextTranslationId = 0): void {
        // Get HTML of the node and find its length.
        $html = Utils::getDomNodeHtmlString($node);
        $length = $this->getLengthFor($html);
        $lengthTrimmed = $this->getLengthFor(trim($html));

        // If this node has a sibling, process it.
        if($node->nextSibling) $this->prepareNodeForTranslation($node->nextSibling, $maxValueLength, $nextTranslationId);

        // No need to proceed further if this is an empty element. No need to check its children as well.
        if($lengthTrimmed < 1) return;

        // Do not proceed further if this is a comment node. No need to check its children as well.
        if($node->nodeName == '#comment') return;

        $isLong = $length > $maxValueLength;

        // If this is a text node, divide it and wrap each part with p tag so that we can find the parts of the text
        // node after translation.
        if($node->nodeName == '#text') {
            // Divide the text so that length of each part is less than $maxTextLength. Wrap each part with
            // <p id="wpcc-translate-[number]" class="wpcc-translate-unwrap">

            $offsets = [];

            // If this is a long text, divide it.
            if($isLong) {
                $offsets = $this->getLengthStrategy()
                    ->getByteOffsetsForCuts($html, $maxValueLength, $length, (int) floor($length/$maxValueLength));

                if (!$offsets) {
                    $offsets = [null];
                }

            // Otherwise, no need to divide. Just add the maximum offset so that the text wont't be divided.
            } else {
                $offsets[] = null;
            }

            // If there are offsets that we can use to divide the text, let's divide it.
            if($offsets) { // @phpstan-ignore-line
                // Add 0 to the beginning of $offsets.
                array_unshift($offsets, 0);

                // Add a null offset in the end, to indicate the rest of the text should be retrieved.
                if($offsets[sizeof($offsets) - 1] !== null) $offsets[] = null;

                // Divide the text using the offsets.
                $modifiedText = '';
                for($i = 0; $i < sizeof($offsets) - 1; $i++) {
                    /** @var int $startOffset */
                    $startOffset    = $offsets[$i];
                    $endOffset      = $offsets[$i + 1];

                    // Cut the multi-byte string using byte numbers. The offsets are in bytes, not characters. So,
                    // mb_substr is not suitable here.
                    if ($endOffset !== null) {
                        $text = mb_strcut($html, $startOffset, $endOffset - $startOffset);

                    } else {
                        // If the end offset is null, it means get everything from start index until the end of the
                        // text. mb_strcut does this when the "length" is omitted.
                        $text = mb_strcut($html, $startOffset);
                    }

                    if($text && trim($text)) {
                        $text = $this->wrapWithTranslateElementTagName($text, $nextTranslationId++);
                    }

                    $modifiedText .= $text;
                }

                // No need to proceed if modified text is empty.
                if(!$modifiedText) return;

                // Replace the node's text with the modified version.
                // We cannot just change the nodeValue, because it strips HTML tags. To be able to successfully change it,
                // first, create a document fragment. Then, append the newValue to the fragment. Finally, replace the
                // node with the fragment.
                $doc        = $node->ownerDocument;
                $parentNode = $node->parentNode;
                if (!$doc || !$parentNode) return;

                $fragment = $doc->createDocumentFragment();

                // Suppress warnings so that the script keeps running.
                // There may be problems regarding a few characters, such as &amp;, when parsing XML. So, handle the
                // errors to keep the script running.
                if (@$fragment->appendXML($modifiedText)) {
                    $parentNode->replaceChild($fragment, $node);

                } else {
                    // Write an error to the error log file.
                    error_log("WPCC - XML is not valid for '" . mb_substr($modifiedText, 0, 250) . "'");
                }

            }

            // We are done with this node.
            return;
        }

        // If this non-text element is not long, just add an ID to it. Make sure this is a DOMElement, because attribute
        // getters and setters are only available for DOMElement.
        if(!$isLong && is_a($node, DOMElement::class)) {
            /** @var DOMElement $node */

            $prevId = $node->getAttribute("id");
            $idToAdd = sprintf($this->translateNodeIdFormat, $nextTranslationId);
            $newId = $prevId ? ($prevId . " " . $idToAdd) : $idToAdd;

            $node->setAttribute("id", $newId);

            // Increase next ID by one.
            $nextTranslationId++;

            // We are done with this node.
            return;
        }

        // If it is long and has children, process them as well.
        if($isLong && $node->hasChildNodes()) {
            $childNode = $node->childNodes->item(0);
            if (!$childNode) return;

            $this->prepareNodeForTranslation($childNode, $maxValueLength, $nextTranslationId);
        }

    }

    /**
     * @param string $html HTML element that might contain opening and closing parts of the HTML tag, whose name is
     *                     defined by {@link translateElementTagName}.
     * @return string
     * @since 1.9.0
     */
    private function unwrapTranslateElementTagName(string $html) {
        $replaced = $this->findAndReplaceSingle($this->translateElOpenTagFindRegex, '', $html, true, false);
        $replaced = $this->findAndReplaceSingle($this->translateElCloseTagFindRegex, '', $replaced, true, false);

        return $replaced;
    }

    /**
     * Wrap a text with an HTML tag whose name is defined by {@link translateElementTagName}
     *
     * @param string $value      The value that will be wrapped with an element whose tag name is defined by
     *                           {@link translateElementTagName}
     * @param string $id         ID attribute's value of the wrapping HTML tag
     * @return string The value wrapped with the HTML element
     * @since 1.9.0
     */
    private function wrapWithTranslateElementTagName(string $value, string $id): string {
        $text = sprintf('<%3$s id="%1$s">%2$s</%3$s>',
            sprintf($this->translateNodeIdFormat, $id),
            $value,
            $this->translateElementTagName
        );

        return $text;
    }

    /*
     * PUBLIC HELPERS
     */

    /**
     * See {@link AbstractLengthStrategy::getLengthFor()}
     *
     * @param string $text
     * @return int
     * @since 1.9.0
     */
    public function getLengthFor(string $text): int {
        return $this->getLengthStrategy()->getLengthFor($text);
    }

    /*
     * PUBLIC GETTERS
     */

    /**
     * @return AbstractLengthStrategy See {@link lengthStrategy}
     * @since 1.9.0
     */
    public function getLengthStrategy(): AbstractLengthStrategy {
        return $this->lengthStrategy;
    }

    /**
     * @return array See {@link texts}
     * @since 1.9.0
     */
    public function getTexts(): array {
        return $this->texts;
    }

    /**
     * @return int See {@link maxLength}
     * @since 1.9.0
     */
    public function getMaxLength(): int {
        return $this->maxLength;
    }

    /**
     * @return int See {@link maxLengthPerChunk}
     * @since 1.9.0
     */
    public function getMaxLengthPerChunk(): int {
        return $this->maxLengthPerChunk;
    }

    /**
     * @return int See {@link maxItemCountPerChunk}
     * @since 1.9.0
     */
    public function getMaxItemCountPerChunk(): int {
        return $this->maxItemCountPerChunk;
    }

}