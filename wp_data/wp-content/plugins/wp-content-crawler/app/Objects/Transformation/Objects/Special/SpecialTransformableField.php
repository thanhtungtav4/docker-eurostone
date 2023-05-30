<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 12/04/2020
 * Time: 14:14
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Transformation\Objects\Special;


use WPCCrawler\Objects\Transformation\Objects\TransformableField;
use WPCCrawler\Objects\Value\ValueExtractorOptions;

class SpecialTransformableField extends TransformableField {

    /**
     * @var string|null Stores the identifier of a data source so that the command having this field can be injected
     *                  with the data source having the defined identifier. If this is null, no data source will be
     *                  injected to the command. See {@link SpecialFieldService::getDataSourceIdentifier()} and
     *                  {@link FilterDependencyProvider::injectCommandDependencies()}.
     */
    private $dataSourceIdentifier = null;

    /**
     * @return string|null See {@link dataSourceIdentifier}
     * @since 1.11.0
     */
    public function getDataSourceIdentifier(): ?string {
        return $this->dataSourceIdentifier;
    }

    /**
     * @param string|null $dataSourceIdentifier See {@link dataSourceIdentifier}
     * @return SpecialTransformableField
     * @since 1.11.0
     */
    public function setDataSourceIdentifier(?string $dataSourceIdentifier): SpecialTransformableField {
        $this->dataSourceIdentifier = $dataSourceIdentifier;
        return $this;
    }

    protected function createExtractorOptions(): ValueExtractorOptions {
        return (new ValueExtractorOptions())->setAllowAll(true);
    }

}