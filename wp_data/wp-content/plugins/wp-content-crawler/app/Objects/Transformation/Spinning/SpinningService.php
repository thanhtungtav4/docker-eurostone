<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 16/02/2019
 * Time: 10:10
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Transformation\Spinning;


use Exception;
use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\Objects\Transformation\Base\AbstractTransformAPIClient;
use WPCCrawler\Objects\Transformation\Base\AbstractTransformationService;
use WPCCrawler\Objects\Transformation\Exceptions\TransformationFailedException;
use WPCCrawler\Objects\Transformation\Spinning\Clients\AbstractSpinningAPIClient;
use WPCCrawler\Objects\Transformation\Spinning\Clients\SpinRewriterClient;
use WPCCrawler\Objects\Transformation\Spinning\Clients\TurkceSpinClient;

class SpinningService extends AbstractTransformationService {

    // Done?  API                                    URL                                 HTML   Should implement?
    //   √    Chimp Rewriter API implementation      https://chimprewriter.com/           √     Yes but it seems that HTML handling is not quite right

    /*
     * TODO For the settings:
     *  ? Add "Send plain text" option. When this is checked, the plugin should send only plain text instead of HTML to
     *  the selected API service.
     *  . An option to enter protected HTML elements. The contents of these elements should not be spun. The HTML elements
     *  can be specified using CSS selectors.
     */
    
    /** @var SpinningService|null */
    private static $instance = null;

    const SERVICE_KEY_SPIN_REWRITER     = 'spin_rewriter';
//    const SERVICE_KEY_CHIMP_REWRITER    = 'chimp_rewriter';
    const SERVICE_KEY_TURKCE_SPIN       = 'turkce_spin';

    /*
     * == HOW TO ADD A NEW SPINNING API TO THE PLUGIN ==
     * See README
     */

    /**
     * @return SpinningService
     * @since 1.9.0
     */
    public static function getInstance(): SpinningService {
        if (static::$instance === null) {
            static::$instance = new SpinningService();
        }
        
        return static::$instance;
    }

    protected function __construct() {
        parent::__construct();

        // Register the clients here
        $this
            ->registerAPIClient(SpinRewriterClient::class,  SpinningService::SERVICE_KEY_SPIN_REWRITER,'Spin Rewriter')
//            ->registerAPIClient(ChimpRewriterClient::class, SpinningService::SERVICE_KEY_CHIMP_REWRITER,    'Chimp Rewriter')
            ->registerAPIClient(TurkceSpinClient::class,  SpinningService::SERVICE_KEY_TURKCE_SPIN, 'Türkçe Spin')
        ;
    }

    /**
     * @return string Name of the base API client class. This class is used to check if an API that is wanted to be
     * registered is valid or not.
     *
     * @return class-string
     * @since 1.9.0
     */
    protected function getBaseAPIClientClassName(): string {
        return AbstractSpinningAPIClient::class;
    }

    /**
     * @return string Prefix to be used when creating an option key for the transformation APIs. For example, for
     *                translation, this prefix can be 'translation'. To understand how this is used, see
     *                {@link optionKeyFormat}
     * @since 1.9.0
     */
    protected function getOptionKeyPrefix(): string {
        return 'spinning';
    }

    /**
     * @return string Option key (post meta key) that stores the selected transformation service. This is basically the
     *                name of the input field (select field) that the user interacts with to select a transformation
     *                service. For example '_wpcc_selected_translation_service'
     * @since 1.9.0
     */
    protected function getOptionKeyForSelectedService(): string {
        return SettingKey::WPCC_SELECTED_SPINNING_SERVICE;
    }

    /**
     * @param AbstractTransformAPIClient $client   The API client that will be used to transform the given texts. The
     *                                             client is an instance of the class returned from
     *                                             {@link getBaseAPIClientClassName()}
     * @param array                      $texts    A flat array of texts, probably retrieved from
     *                                             {@link ValueExtractor::fillAndFlatten()}.
     * @param bool                       $dryRun   If true, the texts will not be transformed. Instead, they will be
     *                                             appended dummy values to mock the transformation.
     *
     * @return null|array If the selected transformation service does not exist, returns null. Otherwise, transformed
     *                    $texts.
     * @throws TransformationFailedException If required parameters for the transformation service selected in the
     *                                       settings are not valid, or there is a transformation error.
     * @since 1.9.0
     */
    protected function applyTransformation(AbstractTransformAPIClient $client, $texts, $dryRun = false) {
        /** @var AbstractSpinningAPIClient $client */
        // Translate the texts considering user's settings
        $textSpinner = new TextSpinner($texts, $dryRun);

        try {
            return $client->spin($textSpinner);

        } catch (Exception $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
