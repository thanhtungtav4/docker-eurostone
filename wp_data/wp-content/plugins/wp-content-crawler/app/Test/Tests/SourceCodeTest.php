<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 25/10/2018
 * Time: 17:32
 */

namespace WPCCrawler\Test\Tests;


use Illuminate\Contracts\View\View;
use WPCCrawler\Objects\Crawling\Bot\DummyBot;
use WPCCrawler\Objects\Html\ScriptRemover;
use WPCCrawler\Objects\Traits\FindAndReplaceTrait;
use WPCCrawler\Test\Base\AbstractTest;
use WPCCrawler\Test\Data\TestData;
use WPCCrawler\Utils;

class SourceCodeTest extends AbstractTest {

    use FindAndReplaceTrait;

    protected $responseResultsKey = 'html';

    /**
     * Conduct the test and return an array of results.
     *
     * @param TestData $data Information required for the test
     * @return array|null
     */
    protected function createResults($data): ?array {
        $url                        = $data->get("url");
        $applyManipulationOptions   = $data->get("applyManipulationOptions");
        $removeScripts              = $data->get("removeScripts");
        $removeStyles               = $data->get("removeStyles");

        if(!$url) return null;

        $bot = new DummyBot($data->getPostSettings(), null, $data->getUseUtf8(), $data->getConvertEncodingToUtf8());

        $crawler = $bot->request($url, "GET", $applyManipulationOptions ? $data->getRawHtmlFindReplaces() : null);

        if(!$crawler) return null;

        // Remove the scripts in the page
        if($removeScripts) {
            $crawler = (new ScriptRemover($crawler))->removeScripts()->getCrawler();
        }

        // Remove the styles
        if($removeStyles) {
            // Remove style elements
            $bot->removeElementsFromCrawler($crawler, ["style", "[rel=stylesheet]"]);

            // Remove style attributes
            $bot->removeElementAttributes($crawler, ['[style]'], 'style');
        }

        // Apply manipulation options
        if ($applyManipulationOptions) {
            $this->applyHtmlManipulationOptions($bot, $crawler, null, $url);
        }

        // Get the HTML to be manipulated
        $html = Utils::getNodeHTML($crawler);

        // Remove empty attributes. This is important for CSS selector finder script. It fails when there is an attribute
        // whose attribute consists of only spaces.
        $html = $this->findAndReplaceSingle(
            '<.*?[a-zA-Z-]+=["\']\s+["\'].*?>',
            '',
            $html,
            true
        );

        $parts = parse_url($url);
        $base = is_array($parts) && isset($parts['host']) 
            ? ($parts['scheme'] ?? 'http') . '://' . $parts['host']
            : null;

        // Set the base URL like this. By this way, relative URLs will be handled correctly.
        if ($base !== null) {
            $html = $this->findAndReplaceSingle(
                '(<head>|<head\s[^>]+>)',
                '$1 <base href="' . $base . '">',
                $html,
                true
            );
        }

        return [$html];
    }

    /**
     * Create the view of the response
     *
     * @return View|null
     */
    protected function createView() {
        return null;
    }
}