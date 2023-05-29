<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 10/01/17
 * Time: 13:28
 */

namespace WPCCrawler\Objects\Crawling\Bot;

use Symfony\Component\DomCrawler\Crawler;
use WPCCrawler\Objects\Crawling\Interfaces\MakesCrawlRequest;

class DummyBot extends AbstractBot implements MakesCrawlRequest {

    /** @var Crawler|null */
    private $crawler;

    /** @var string|null */
    private $url = null;

    /** @var int|null */
    private $responseHttpStatusCode = null;

    public function request($url, $method = "GET", $findAndReplaces = null): ?Crawler {
        $this->setCrawlingUrl($url);
        $this->setResponseHttpStatusCode(null);

        // Let the parent handle the request
        $result = parent::request($url, $method, $findAndReplaces);

        // Store the status code
        $latestResponse = $this->getLatestResponse();
        $this->setResponseHttpStatusCode($latestResponse
            ? $latestResponse->getStatusCode()
            : null
        );

        return $result;
    }

    public function getCrawler(): ?Crawler {
        return $this->crawler;
    }

    public function setCrawler(?Crawler $crawler): void {
        $this->crawler = $crawler;
    }

    public function getCrawlingUrl(): ?string {
        return $this->url;
    }

    public function setCrawlingUrl(?string $url): self {
        $this->url = $url;
        return $this;
    }

    public function getResponseHttpStatusCode(): ?int {
        return $this->responseHttpStatusCode;
    }

    public function setResponseHttpStatusCode(?int $code): self {
        $this->responseHttpStatusCode = $code;
        return $this;
    }

}