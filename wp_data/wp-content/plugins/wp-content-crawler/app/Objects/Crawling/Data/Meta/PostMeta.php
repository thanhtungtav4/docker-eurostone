<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 18/02/2021
 * Time: 17:16
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Crawling\Data\Meta;


class PostMeta {

    /** @var string The post meta key */
    private $key;

    /** @var string|string[] Value of the post meta. This is either a string or an array of strings. */
    private $data;

    /**
     * @var bool True if this is a "multiple" post meta. When it is multiple, there can be multiple post meta values
     *      with the same key in the database. If the post meta is not multiple, only one value with the given post meta
     *      key can be in the database.
     */
    private $multiple;

    /**
     * @param string          $key      See {@link key}
     * @param string|string[] $data     See {@link data}
     * @param bool            $multiple See {@link multiple}
     * @since 1.11.0
     */
    public function __construct(string $key, $data, bool $multiple = false) {
        $this
            ->setKey($key)
            ->setData($data)
            ->setMultiple($multiple);
    }

    /**
     * @return string See {@link key}
     * @since 1.11.0
     */
    public function getKey(): string {
        return $this->key;
    }

    /**
     * @param string $key See {@link key}
     * @return PostMeta
     * @since 1.11.0
     */
    public function setKey(string $key): self {
        $this->key = $key;
        return $this;
    }

    /**
     * @return string|string[] See {@link data}
     * @since 1.11.0
     */
    public function getData() {
        return $this->data;
    }

    /**
     * @param string|string[]|null $data See {@link data}
     * @return PostMeta
     * @since 1.11.0
     */
    public function setData($data): self {
        $this->data = $data === null ? '' : $data;
        return $this;
    }

    /**
     * @return bool See {@link multiple}
     * @since 1.11.0
     */
    public function isMultiple(): bool {
        return $this->multiple;
    }

    /**
     * This method exists, because {@link ValueExtractor} needs this method to exist for it to be able to extract the
     * value of {@link multiple} variable.
     *
     * @return bool Returns what {@link isMultiple()} returns
     * @since 1.11.0
     */
    public function getMultiple(): bool {
        return $this->isMultiple();
    }

    /**
     * @param bool $multiple See {@link multiple}
     * @return PostMeta
     * @since 1.11.0
     */
    public function setMultiple(bool $multiple): self {
        $this->multiple = $multiple;
        return $this;
    }

}