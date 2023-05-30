<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 18/11/2020
 * Time: 11:21
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Transformation\Objects\Special;


use Exception;
use WPCCrawler\Objects\Crawling\Bot\AbstractBot;
use WPCCrawler\Objects\Crawling\Interfaces\MakesCrawlRequest;
use WPCCrawler\Objects\Filtering\Interfaces\NeedsBot;
use WPCCrawler\Objects\Transformation\Interfaces\Transformable;

class RequestTransformableField extends SpecialTransformableField implements NeedsBot {

    const KEY_EXCEPTION = 'exception';
    const KEY_STATUS    = 'status';

    /** @var AbstractBot|null */
    private $bot;

    protected function onExtractSubjectValues(?Transformable $dataSource): ?array {
        $bot = $this->getBot();
        if (!$bot || !($bot instanceof MakesCrawlRequest)) return null;

        return [
            static::KEY_EXCEPTION => $bot->getLatestRequestException(),
            static::KEY_STATUS    => $bot->getResponseHttpStatusCode(),
        ];
    }

    protected function getSubjectItemAsString($subject): ?string {
        // If this is an exception, return its message.
        if ($subject instanceof Exception) {
            return $subject->getMessage();
        }

        // Otherwise, let the parent handle it.
        return parent::getSubjectItemAsString($subject);
    }

    /*
     * INTERFACE METHODS
     */

    public function setBot(?AbstractBot $bot): void {
        $this->bot = $bot;
    }

    public function getBot(): ?AbstractBot {
        return $this->bot;
    }
}