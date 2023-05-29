<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 21/02/2019
 * Time: 20:26
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Crawling\Preparers\Post;


use WPCCrawler\Environment;
use WPCCrawler\Objects\Crawling\Preparers\Post\Base\AbstractPostTransformationPreparer;
use WPCCrawler\Objects\Enums\ErrorType;
use WPCCrawler\Objects\Enums\InformationMessage;
use WPCCrawler\Objects\Settings\Enums\SettingKey;
use WPCCrawler\Objects\Settings\SettingService;
use WPCCrawler\Objects\Transformation\Base\AbstractTransformer;
use WPCCrawler\Objects\Transformation\Spinning\Spinner;

class PostSpinningPreparer extends AbstractPostTransformationPreparer {

    /**
     * @return bool True if the preparation should be done. Otherwise, false.
     * @since 1.9.0
     */
    protected function shouldPrepare() {
        return SettingService::isSpinningActive() && $this->bot->getSettingForCheckbox(SettingKey::ACTIVE_SPINNING);
    }

    /**
     * @return string A string that identifies this transformation preparer. E.g. if 'translate' is returned, an action
     *                to fire for this preparer will be named, e.g., as 'wpcc/post/data/before_translate'
     * @since 1.9.0
     */
    protected function getActionIdentifier() {
        return 'spin';
    }

    /**
     * Create the transformer to be used to transform the post.
     *
     * @return AbstractTransformer|null
     * @since 1.9.0
     */
    protected function createTransformer(): ?AbstractTransformer {
        return new Spinner(
            $this->bot->getSettingsImpl(),
            $this->bot->getPostData(),
            Environment::defaultPostIdentifier()
        );
    }

    /**
     * @return string One of the constants defined in {@link ErrorType}. This will be used to retrieve an error message
     *                text for this transformation preparer.
     * @since 1.9.0
     */
    protected function getErrorType() {
        return ErrorType::SPINNING_ERROR;
    }

    /**
     * @return string One of the constants defined in {@link InformationMessage}. This will be used to retrieve an
     *                information message text for this transformation preparer.
     * @since 1.9.0
     */
    protected function getInformationMessageType() {
        return InformationMessage::SPINNING_ERROR;
    }
}
