<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 17/02/2019
 * Time: 18:45
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Transformation\Base;


use Exception;
use WPCCrawler\Objects\Settings\SettingsImpl;
use WPCCrawler\Objects\Transformation\Interfaces\Transformable;
use WPCCrawler\Objects\Transformation\Objects\TransformableFieldList;
use WPCCrawler\Objects\Value\ValueExtractor;
use WPCCrawler\Objects\Value\ValueSetter;

/**
 * Transforms a {@link Transformable} instance
 *
 * @package WPCCrawler\Objects\Translation
 * @since 1.9.0 Moved to AbstractTransformer class.
 */
abstract class AbstractTransformer {

    /** @var SettingsImpl */
    private $settings;

    /** @var Transformable A Transformable instance to be transformed */
    private $transformable;

    /**
     * @var array An array of transformable fields that will be used instead of the value retrieved from
     *            {@link Transformable::getTransformableFields()} by calling
     *            {@link TransformableFieldList::toAssociativeArray()}. If this is empty or not valid, transformable
     *            fields will be retrieved from the {@link Transformable} instance.
     */
    private $customTransformableFields = [];

    /**
     * @var null|string Prefix to be used when retrieving custom transformable fields from the settings. One of the
     *      prefixes used in {@link AbstractTransformationService::prepareTransformableFieldForSelect()}
     */
    private $customTransformableFieldsPrefix = null;

    /** @var bool True if custom fields should be used instead of the defaults provided by Transformable instance. */
    private $useCustomFields = false;

    /**
     * @var bool If true, the texts will not be transformed. Instead, they will be appended dummy values to mock the
     *      transformation.
     */
    private $dryRun;

    /**
     * @param SettingsImpl  $settings                        Post meta of a Site type post. (get_post_meta($siteId))
     * @param Transformable $transformable                   See {@link $transformable}
     * @param string|null   $customTransformableFieldsPrefix See {@link $customTransformableFieldsPrefix}
     * @param bool          $dryRun                          See {@link $dryRun}
     *
     * @since 1.9.0 $customTransformableFieldsPrefix parameter is added.
     * @since 1.8.0
     */
    public function __construct(SettingsImpl $settings, Transformable $transformable, $customTransformableFieldsPrefix = null, $dryRun = false) {
        $this->settings = $settings;
        $this->transformable = $transformable;
        $this->setCustomTransformableFields($customTransformableFieldsPrefix);
        $this->dryRun = $dryRun;
    }

    /**
     * Translates the transformable according to the settings
     *
     * @return null|Transformable Translated data or null.
     * @throws Exception If the transformation has failed
     * @since 1.8.0
     */
    public function transform(): ?Transformable {
        // Prepare the texts to be transformed, and create a TextTranslator.
        // Any separator would be OK except "." because we do not want TextTranslator to remap the texts into their
        // positions using the dot notation. We will handle remapping using ValueSetter.
        $separator = '|';
        $extractor = new ValueExtractor();

        // Get which fields should be transformed
        $transformableFields = $this->useCustomFields ? $this->customTransformableFields : $this->transformable->getTransformableFields()->toAssociativeArray();

        $texts = $extractor->fillAndFlatten($this->transformable, $transformableFields, $separator);

        // If there are no texts to transform, no need to proceed.
        if(!$texts) return $this->transformable;

        // Translate the prepared texts
        $transformedTexts = $this->getTransformationService()->transform($this->settings, $texts, $this->dryRun);
        if (!$transformedTexts) return null;

        // Assign transformed texts to the Transformable instance.
        $setter = new ValueSetter();
        $setter->set($this->transformable, $transformedTexts, $separator);

        return $this->transformable;
    }

    /*
     * ABSTRACT METHODS
     */

    /**
     * @return AbstractTransformationService
     * @since 1.9.0
     */
    protected abstract function getTransformationService();

    /**
     * @return string The option key (post meta key) that stores the values of custom transformable fields selected by
     *                the user. E.g. '_transformable_fields'
     * @since 1.9.0
     */
    protected abstract function getTransformableFieldsOptionKey();

    /*
     * SETTERS
     */

    /**
     * Set a transformable and its transformable fields so that these values are used when transforming, instead of the
     * values provided in the constructor.
     *
     * @param Transformable $transformable                   See {@link $transformable}
     * @param string|null   $customTransformableFieldsPrefix See {@link $customTransformableFields}
     *
     * @since 1.9.0 $customTransformableFieldsPrefix parameter is added.
     * @since 1.8.0
     */
    public function setTransformable(Transformable $transformable, $customTransformableFieldsPrefix = null): void {
        $this->transformable = $transformable;
        $this->setCustomTransformableFields($customTransformableFieldsPrefix);
    }

    /**
     * @return null|string See {@link $customTransformableFieldsPrefix}
     * @since 1.9.0
     * @noinspection PhpUnused
     */
    public function getCustomTransformableFieldsPrefix(): ?string {
        return $this->customTransformableFieldsPrefix;
    }

    /*
     * HELPERS
     */

    /**
     * Assign the value {@link $customTransformableFields} after preparing custom transformable fields such that the
     * custom fields can only contain the keys that exist in the {@link $transformable}'s transformable fields.
     *
     * @param string|null $customTransformableFieldsPrefix A prefix used in {@link TranslationService::prepareTransformableFieldForSelect()}
     * @since 1.9.0
     */
    private function setCustomTransformableFields($customTransformableFieldsPrefix): void {
        $this->customTransformableFieldsPrefix = $customTransformableFieldsPrefix;

        // Get custom transformable fields from the settings using the given prefix.
        $transformableFieldSettings = $this->settings->getSetting($this->getTransformableFieldsOptionKey(), []);

        // If there are no custom fields defined in the settings or there is no prefix, stop.
        if (!$transformableFieldSettings || !$customTransformableFieldsPrefix) {
            $this->useCustomFields = false;
            $this->customTransformableFields = [];
            return;
        }

        // Custom fields should be used.
        $this->useCustomFields = true;

        // Get the original fields
        $originalFields = $this->transformable->getTransformableFields()->toAssociativeArray();
        $result = [];

        // Get the transformable fields defined in the settings.
        $customFields = $this->getTransformationService()->getTransformableFieldsFromSelect(
            $transformableFieldSettings,
            $customTransformableFieldsPrefix
        );

        foreach($customFields as $field) {
            // If this field does not exist in the transformable's fields, continue with the next one.
            if (!isset($originalFields[$field])) continue;

            // This is a valid field. Add this field to the result. The result should be structured the same as the
            // Transformable::getTransformableFields() method's structure
            $result[$field] = '';
        }

        // Set the custom transformable fields as the result
        $this->customTransformableFields = $result;
    }
}