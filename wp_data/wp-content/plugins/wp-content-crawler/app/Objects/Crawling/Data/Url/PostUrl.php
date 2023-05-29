<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 19/04/2020
 * Time: 08:24
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Crawling\Data\Url;


use Illuminate\Support\Arr;
use WPCCrawler\Interfaces\Arrayable;

class PostUrl implements Arrayable {

    /** @var string */
    private $url;

    /** @var int */
    private $start;

    /** @var int */
    private $end;

    /** @var string|null */
    private $thumbnailUrl;

    /** @var array|null Stores the output of {@link toArray()} */
    private $arrayCache = null;

    /**
     * @param string      $url
     * @param int         $start
     * @param int         $end
     * @param string|null $thumbnailUrl
     * @since 1.11.0
     */
    public function __construct(string $url, int $start, int $end, ?string $thumbnailUrl = null) {
        $this->url          = $url;
        $this->start        = $start;
        $this->end          = $end;
        $this->thumbnailUrl = $thumbnailUrl;
    }

    /**
     * @return string
     * @since 1.11.0
     */
    public function getUrl(): string {
        return $this->url;
    }

    /**
     * @param string $url
     * @return PostUrl
     * @since 1.11.0
     */
    public function setUrl(string $url): PostUrl {
        $this->url = $url;
        $this->invalidateArrayCache();
        return $this;
    }

    /**
     * @return int
     * @since 1.11.0
     */
    public function getStart(): int {
        return $this->start;
    }

    /**
     * @param int $start
     * @return PostUrl
     * @since 1.11.0
     */
    public function setStart(int $start): PostUrl {
        $this->start = $start;
        $this->invalidateArrayCache();
        return $this;
    }

    /**
     * @return int
     * @since 1.11.0
     */
    public function getEnd(): int {
        return $this->end;
    }

    /**
     * @param int $end
     * @return PostUrl
     * @since 1.11.0
     */
    public function setEnd(int $end): PostUrl {
        $this->end = $end;
        $this->invalidateArrayCache();
        return $this;
    }

    /**
     * @return string|null
     * @since 1.11.0
     */
    public function getThumbnailUrl(): ?string {
        return $this->thumbnailUrl;
    }

    /**
     * @param string|null $thumbnailUrl
     * @return PostUrl
     * @since 1.11.0
     */
    public function setThumbnailUrl(?string $thumbnailUrl): PostUrl {
        $this->thumbnailUrl = $thumbnailUrl;
        $this->invalidateArrayCache();
        return $this;
    }

    /*
     *
     */

    public function toArray(): array {
        if ($this->arrayCache === null) {
            $this->arrayCache = [
                'type'  => 'url',
                'data'  => $this->getUrl(),
                'start' => $this->getStart(),
                'end'   => $this->getEnd(),
            ];

            $thumbnailUrl = $this->getThumbnailUrl();
            if ($thumbnailUrl !== null) {
                $this->arrayCache['thumbnail'] = $thumbnailUrl;
            }
        }

        return $this->arrayCache;
    }

    /*
     * PROTECTED METHODS
     */

    protected function invalidateArrayCache(): void {
        $this->arrayCache = null;
    }

    /*
     * STATIC METHODS
     */

    /**
     * Create a {@link PostUrl} from an associative array
     *
     * @param array|null $arr See the method's implementation to learn the array keys
     * @return PostUrl|null
     * @since 1.11.0
     */
    public static function fromArray(?array $arr): ?PostUrl {
        if ($arr === null) return null;

        return new PostUrl(
            Arr::get($arr, 'data'),             // URL of the post
            (int) Arr::get($arr, 'start'),      // Start position of the element that stores the post URL in the source code
            (int) Arr::get($arr, 'end'),        // End position of the element that stores the post URL in the source code
            Arr::get($arr, 'thumbnail')         // URL of the post's thumbnail image
        );
    }

}