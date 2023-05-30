<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 19/02/2021
 * Time: 11:36
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Crawling\Data\Taxonomy;


class TaxonomyItem {

    /**
     * @var string Name of the taxonomy. This must be registered to WordPress before a value can be saved under this
     *      name.
     */
    private $taxonomy;

    /** @var string|string[] Value of the taxonomy. This is either a string or an array of strings. */
    private $data;

    /**
     * @var bool True if this value must be appended to any previously-existing values of the same taxonomy name. If
     *      false, previously-existing values of this taxonomy will be deleted before setting the value of this taxonomy
     *      name.
     */
    private $append;

    /**
     * @param string          $taxonomy See {@link taxonomy}
     * @param string|string[] $data     See {@link data}
     * @param bool            $append   See {@link append}
     * @since 1.11.0
     */
    public function __construct(string $taxonomy, $data, bool $append = false) {
        $this
            ->setTaxonomy($taxonomy)
            ->setData($data)
            ->setAppend($append);
    }


    /**
     * @return string See {@link taxonomy}
     * @since 1.11.0
     */
    public function getTaxonomy(): string {
        return $this->taxonomy;
    }

    /**
     * @param string $taxonomy See {@link taxonomy}
     * @return TaxonomyItem
     * @since 1.11.0
     */
    public function setTaxonomy(string $taxonomy): self {
        $this->taxonomy = $taxonomy;
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
     * @param string|string[] $data See {@link data}
     * @return TaxonomyItem
     * @since 1.11.0
     */
    public function setData($data): self {
        $this->data = $data;
        return $this;
    }

    /**
     * @return bool See {@link append}
     * @since 1.11.0
     */
    public function isAppend(): bool {
        return $this->append;
    }

    /**
     * This method exists, because {@link ValueExtractor} needs this method to exist for it to be able to extract the
     * value of {@link append} variable.
     *
     * @return bool Returns what {@link isAppend()} returns.
     * @since 1.11.0
     */
    public function getAppend(): bool {
        return $this->isAppend();
    }

    /**
     * @param bool $append See {@link append}
     * @return TaxonomyItem
     * @since 1.11.0
     */
    public function setAppend(bool $append): self {
        $this->append = $append;
        return $this;
    }


}