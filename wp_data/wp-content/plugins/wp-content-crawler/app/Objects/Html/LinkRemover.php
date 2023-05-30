<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 22/11/2020
 * Time: 21:54
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Html;


use InvalidArgumentException;
use Symfony\Component\DomCrawler\Crawler;
use WPCCrawler\Objects\Crawling\Bot\DummyBot;
use WPCCrawler\Objects\DomainValidator;

class LinkRemover {

    /** @var string[] Defines the invalid domains. The links whose href value matches one of the rules will be removed. */
    private $invalidDomainRules;

    /** @var string[]|null Regex equivalents of the {@link invalidDomainRules} */
    private $invalidDomainRegexes = null;

    /**
     * @var string[]|null Defines the valid domains. The links whose href value does not match one of the rules will be
     *      removed.
     */
    private $validDomainRules;

    /** @var string[]|null Regex equivalents of the {@link validDomainRules} */
    private $validDomainRegexes = null;

    /** @var ElementUnwrapper|null */
    private $unwrapper;

    /**
     * @param string[]      $invalidDomainRules See {@link invalidDomainRules}
     * @param string[]|null $validDomainRules   See {@link validDomainRules}
     * @since 1.11.0
     */
    public function __construct(array $invalidDomainRules, ?array $validDomainRules = null) {
        $this->invalidDomainRules = $invalidDomainRules;
        $this->validDomainRules   = $validDomainRules;
    }

    /**
     * Remove links from partial HTML code. If the HTML code contains html tag, create a {@link Crawler} for it and use
     * {@link removeLinksFromCrawler()} method instead. This method creates an HTML document and adds the given HTML
     * code into it.
     *
     * @param string $html The partial HTML code that contains the links
     * @return string The HTML without the links
     * @since 1.11.0
     */
    public function removeLinksFromHtml(string $html): string {
        $dummyBot = new DummyBot([]);
        $dummyCrawler = $dummyBot->createDummyCrawler($html);

        $this->removeLinksFromCrawler($dummyCrawler);

        return trim($dummyBot->getContentFromDummyCrawler($dummyCrawler));
    }

    /**
     * Remove links from a {@link Crawler}
     *
     * @param Crawler $crawler The crawler that contains the links
     * @since 1.11.0
     */
    public function removeLinksFromCrawler(Crawler $crawler): void {
        $crawler->filter('a[href]')->each(function($node) {
            /** @var Crawler $node */
            $this->maybeUnwrap($node);
        });
    }

    /*
     * PROTECTED METHODS
     */

    /**
     * Unwrap an anchor node if its href attribute targets an invalid domain
     *
     * @param Crawler|null $node An anchor node that has an href attribute
     * @since 1.11.0
     */
    protected function maybeUnwrap(?Crawler $node): void {
        if (!$node) return;

        try {
            $href = $node->attr('href');
            if ($href === null || $href === '' || !$this->isInvalid($href)) return;

            $this->getUnwrapper()->unwrap($node->getNode(0));

        } catch (InvalidArgumentException $e) {
            // Do nothing.
        }
    }

    /**
     * Check if a URL is invalid via {@link validDomainRules} and {@link invalidDomainRules}
     *
     * @param string $url A URL that will be checked if it is invalid
     * @return bool True if the URL is invalid, and it should be removed. False if the URL is valid, and it should NOT
     *              be removed.
     * @since 1.11.0
     */
    protected function isInvalid(string $url): bool {
        $validator = DomainValidator::getInstance();

        // If an invalid domain rule matches the current URL, then the URL is invalid.
        $matchesInvalidDomain = $validator->validateWithRegexes($this->getInvalidDomainRegexes(), $url);
        if ($matchesInvalidDomain) return true;

        // Get the valid domain regexes. If there is none, since the URL did not match an invalid domain, the URL is
        // valid.
        $validDomainRegexes = $this->getValidDomainRegexes();
        if (!$validDomainRegexes) return false;

        // If the URL matches one of the valid domain rules, then the URL is valid.
        $matchesValidDomain = $validator->validateWithRegexes($validDomainRegexes, $url);
        return !$matchesValidDomain;
    }

    /**
     * @return string[]|null See {@link invalidDomainRegexes}
     * @since 1.11.0
     */
    protected function getInvalidDomainRegexes(): ?array {
        if ($this->invalidDomainRegexes === null) {
            $this->invalidDomainRegexes = DomainValidator::getInstance()
                ->convertRulesToRegexes($this->getInvalidDomainRules());
        }

        return $this->invalidDomainRegexes;
    }

    /**
     * @return string[]|null See {@link validDomainRegexes}
     * @since 1.11.0
     */
    public function getValidDomainRegexes(): ?array {
        if ($this->validDomainRegexes === null) {
            $this->validDomainRegexes = DomainValidator::getInstance()
                ->convertRulesToRegexes($this->getValidDomainRules());
        }

        return $this->validDomainRegexes;
    }

    /**
     * @return string[] See {@link invalidDomainRules}
     * @since 1.11.0
     */
    protected function getInvalidDomainRules(): array {
        return $this->invalidDomainRules;
    }

    /**
     * @return string[]|null See {@link validDomainRules}
     * @since 1.11.0
     */
    public function getValidDomainRules(): ?array {
        return $this->validDomainRules;
    }

    /**
     * @return ElementUnwrapper
     * @since 1.11.0
     */
    protected function getUnwrapper(): ElementUnwrapper {
        if ($this->unwrapper === null) {
            $this->unwrapper = new ElementUnwrapper();
        }

        return $this->unwrapper;
    }
}