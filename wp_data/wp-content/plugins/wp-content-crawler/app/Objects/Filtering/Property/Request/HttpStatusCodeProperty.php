<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 18/11/2020
 * Time: 10:36
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Property\Request;


use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\Filtering\Commands\Base\AbstractBaseCommand;
use WPCCrawler\Objects\Filtering\Enums\PropertyKey;
use WPCCrawler\Objects\Filtering\Property\Base\AbstractProperty;
use WPCCrawler\Objects\Filtering\Property\Objects\CalculationResult;
use WPCCrawler\Objects\Transformation\Objects\Special\RequestTransformableField;

class HttpStatusCodeProperty extends AbstractProperty {

    public function getKey(): string {
        return PropertyKey::HTTP_STATUS_CODE;
    }

    public function getName(): string {
        return _wpcc('HTTP status code');
    }

    public function getDescription(): ?string {
        /** @noinspection HtmlUnknownTarget */
        return sprintf(
            _wpcc('<a href="%1$s" target="_blank">HTTP status code</a> of the response retrieved for the request 
                made to the target site. E.g. <b>%2$d</b> is for not-found pages.'),
            'https://developer.mozilla.org/en-US/docs/Web/HTTP/Status',
            404
        );
    }

    public function getInputDataTypes(): array {
        return [ValueType::T_REQUEST];
    }

    public function getOutputDataTypes(): array {
        return [ValueType::T_NUMERIC, ValueType::T_INTEGER];
    }

    protected function onCalculate($key, $source, AbstractBaseCommand $cmd): ?CalculationResult {
        if ($key !== RequestTransformableField::KEY_STATUS || !is_int($source)) return null;

        return new CalculationResult($key, $source);
    }
}