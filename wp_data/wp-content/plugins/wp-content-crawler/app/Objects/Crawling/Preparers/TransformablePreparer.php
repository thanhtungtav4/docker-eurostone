<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 17/11/2019
 * Time: 09:52
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Crawling\Preparers;


use Exception;
use WPCCrawler\Exceptions\MethodNotExistException;
use WPCCrawler\Objects\Crawling\Preparers\Interfaces\Preparer;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Objects\Transformation\Interfaces\Transformable;
use WPCCrawler\Objects\Transformation\Objects\TransformableFieldList;
use WPCCrawler\Objects\Value\ValueExtractor;
use WPCCrawler\Objects\Value\ValueSetter;

class TransformablePreparer implements Preparer {

    /** @var Transformable An instance of {@link Transformable} that will be transformed */
    private $transformable;

    /**
     * @var string[] Fields that should be transformed. See {@link Transformable::getTransformableFields()} for more
     *      information about the fields. While {@link Transformable::getTransformableFields()} returns a
     *      {@link TransformableFieldList} array, this must be a sequential string array of field names.
     */
    private $fields;

    /**
     * @var callable The callback that will be used to prepare each value retrieved from {@link fields} of
     *               {@link transformable}. This takes only one argument, which is the text that should be transformed.
     *               This must return the transformed string. E.g. <b>func(string $text) { return $transformedText; }</b>
     *
     */
    private $cbPrepare;

    /**
     * @param Transformable $transformable See {@link transformable}
     * @param string[]      $fields        See {@link fields}
     * @param callable      $cbPrepare     See {@link cbPrepare}
     * @since 1.9.0
     */
    public function __construct(Transformable $transformable, array $fields, callable $cbPrepare) {
        $this->transformable = $transformable;
        $this->fields        = $fields;
        $this->cbPrepare     = $cbPrepare;
    }

    /**
     * Prepare.
     *
     * @return mixed
     * @throws MethodNotExistException See {@link ValueExtractor::fillAndFlatten()} and {@link ValueSetter::set()}
     */
    public function prepare() {
        // Extract the values of the fields from the transformable
        $extractor = new ValueExtractor();
        $texts = $extractor->fillAndFlatten($this->transformable, $this->prepareFields($this->fields));

        // If there are no texts to transform, stop.
        if(!$texts) return;

        // Transform each text and store the transformation
        $transformedTexts = [];
        foreach($texts as $key => $text) {
            try {
                $transformedTexts[$key] = call_user_func($this->cbPrepare, $text);

            } catch(Exception $e) {
                Informer::addInfo(sprintf(_wpcc('Transformation error for %1$s'), $key))
                    ->setException($e)
                    ->addAsLog();
            }
        }

        // Assign transformed texts to the Transformable instance.
        $setter = new ValueSetter();
        $setter->set($this->transformable, $transformedTexts);

        return;
    }

    /*
     * PRIVATE METHODS
     */

    /**
     * Prepare the fields so that they can be directly used as a parameter in {@link ValueExtractor::fillAndFlatten()}
     * method.
     *
     * @param string[] $fields
     * @return array Associative array where keys are field names and the values are empty
     * @since 1.9.0
     */
    private function prepareFields(array $fields): array {
        $results = [];
        foreach($fields as $field) {
            $results[$field] = '';
        }

        return $results;
    }
}