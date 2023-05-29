<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 11/12/2018
 * Time: 09:08
 *
 * @since 1.8.0
 */

namespace WPCCrawler\Objects\OptionsBox;


use WPCCrawler\Objects\OptionsBox\Boxes\Base\BaseOptionsBoxApplier;
use WPCCrawler\Objects\OptionsBox\Boxes\Base\BaseOptionsBoxApplierFactory;
use WPCCrawler\Objects\OptionsBox\Boxes\Def\DefaultOptionsBoxApplierFactory;
use WPCCrawler\Objects\OptionsBox\Boxes\File\FileOptionsBoxApplierFactory;
use WPCCrawler\Objects\OptionsBox\Enums\OptionsBoxType;
use WPCCrawler\Utils;

class OptionsBoxService {

    /** @var OptionsBoxService */
    private static $instance = null;

    /**
     * Get the instance
     *
     * @return OptionsBoxService
     * @since 1.8.0
     */
    public static function getInstance(): OptionsBoxService {
        if (static::$instance === null) static::$instance = new OptionsBoxService();
        return static::$instance;
    }

    /** This is a singleton */
    private function __construct() { }

    /*
     *
     */

    /**
     * Creates an applier considering the given options box configuration.
     *
     * @param string|array $rawData Options box settings
     * @param bool         $unslash
     * @return null|BaseOptionsBoxApplier
     * @since 1.8.0
     */
    public function createApplierFromRawData($rawData, bool $unslash = false): ?BaseOptionsBoxApplier {
        return $this->createApplierFromArrayConfig($this->getArrayConfig($rawData, $unslash));
    }

    /**
     * @param array|null $selectorData Selector data. This typically contains 'selector', 'attr' and 'options_box' keys.
     *                                 If this data has options box options, it will be used to create an options box
     *                                 applier.
     * @param bool $unslash
     * @return null|BaseOptionsBoxApplier
     * @since 1.8.0
     * @uses OptionsBoxService::createApplierFromRawData()
     */
    public function createApplierFromSelectorData(?array $selectorData, bool $unslash = false): ?BaseOptionsBoxApplier {
        if (!$selectorData) return null;

        $options = Utils::array_get($selectorData, 'options_box');
        if (!$options) return null;

        return $this->createApplierFromRawData($options, $unslash);
    }

    /*
     * PRIVATE METHODS
     */

    /**
     * Creates an options box applier from an array configuration.
     *
     * @param array|null $config
     * @return BaseOptionsBoxApplier|null
     * @since 1.8.0
     */
    private function createApplierFromArrayConfig(?array $config): ?BaseOptionsBoxApplier {
        // If there is no config, return null.
        if (!$config) return null;

        // Get the factory
        $factory = $this->getApplierFactoryFromArrayConfig($config);

        // Create the data
        $data = $factory->createData($config);

        // Create the applier
        return $factory->createApplier($data);
    }

    /**
     * Returns an applier factory considering the given options box configuration.
     *
     * @param array $config Options box configuration array
     * @return BaseOptionsBoxApplierFactory
     * @since 1.8.0
     */
    private function getApplierFactoryFromArrayConfig(array $config): BaseOptionsBoxApplierFactory {
        // Find the factory class for the box type
        switch ($this->getBoxTypeFromArrayConfig($config)) {
            case OptionsBoxType::FILE:
                $factoryCls = FileOptionsBoxApplierFactory::class;
                break;

            case OptionsBoxType::DEF:
            default:
                $factoryCls = DefaultOptionsBoxApplierFactory::class;
                break;
        }

        return BaseOptionsBoxApplierFactory::getFactoryInstance($factoryCls);
    }

    /**
     * Get box type from an options box configuration.
     *
     * @param array $config
     * @return string Options box type
     * @since 1.8.0
     */
    private function getBoxTypeFromArrayConfig(array $config): string {
        return Utils::array_get($config, OptionsBoxConfiguration::KEY_BOX . '.' . OptionsBoxConfiguration::KEY_TYPE, OptionsBoxType::DEF);
    }

    /**
     * Get array configuration
     *
     * @param string|array $rawData Raw configuration. Either an array or a JSON.
     * @param bool $unslash
     * @return array
     * @since 1.8.0
     */
    private function getArrayConfig($rawData, bool $unslash = true): array {
        // If the data is an array, return it since there is no need to parse it.
        if (is_array($rawData)) return $rawData;

        // If the raw data should be unslashed
        if ($unslash) {
            // Unslash and reslash backward slashes to create a valid JSON. Unescaped backslashes are not valid in JSON.
            $rawData = str_replace('\\', '\\\\', wp_unslash($rawData));
        }

        $result = is_string($rawData) ? json_decode($rawData, true) : [];
        return is_array($result)
            ? $result
            : [];
    }
}