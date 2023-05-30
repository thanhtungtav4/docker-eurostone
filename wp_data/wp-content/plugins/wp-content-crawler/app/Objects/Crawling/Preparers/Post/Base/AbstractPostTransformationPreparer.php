<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 21/02/2019
 * Time: 18:55
 *
 * @since 1.9.0
 */

namespace WPCCrawler\Objects\Crawling\Preparers\Post\Base;


use Exception;
use Symfony\Component\DomCrawler\Crawler;
use WPCCrawler\Objects\Crawling\Bot\PostBot;
use WPCCrawler\Objects\Crawling\Data\PostData;
use WPCCrawler\Objects\Enums\InformationType;
use WPCCrawler\Objects\Informing\Information;
use WPCCrawler\Objects\Informing\Informer;
use WPCCrawler\Objects\Transformation\Base\AbstractTransformer;
use WPCCrawler\PostDetail\PostDetailsService;

abstract class AbstractPostTransformationPreparer extends AbstractPostBotPreparer {

    /**
     * Prepare the post bot
     *
     * @return void
     */
    public function prepare() {
        // Stop if we should not prepare.
        if(!$this->shouldPrepare()) return;

        $identifier = $this->getActionIdentifier();

        /**
         * Fires just before the post data is transformed according to the settings.
         *
         * @param int      $siteId   ID of the site
         * @param string   $postUrl  URL of the post
         * @param PostBot  $bot      The bot itself
         * @param PostData $postData The data retrieved from the target site by using the settings configured by the user.
         * @param Crawler  $crawler  Crawler containing the target post page's source code. The crawler was manipulated
         *                           according to the settings.
         * @since 1.6.3
         * @since 1.9.0 Fixes: $this should return the bot.
         */
        do_action("wpcc/post/data/before_{$identifier}", $this->bot->getSiteId(), $this->bot->getPostUrl(), $this->bot, $this->bot->getPostData(), $this->bot->getCrawler());

        // Transform by handling the errors.
        try {
            // Create a transformer and transform
            $transformer = $this->createTransformer();
            if (!$transformer) {
                throw new Exception(_wpcc('Transformer does not exist. The transformation could not be done.'));
            }

            $transformer->transform();

            // Transform registered post details
            PostDetailsService::getInstance()->transform($this->bot, $transformer);

        } catch(Exception $e) {
            $this->bot->addError($this->getErrorType(), $e->getMessage());

            Informer::add(Information::fromInformationMessage(
                $this->getInformationMessageType(),
                $e->getMessage(),
                InformationType::ERROR
            )->setException($e)->addAsLog());
        }

        /**
         * Fires just after the post data is transformed according to the settings.
         *
         * @param int      $siteId   ID of the site
         * @param string   $postUrl  URL of the post
         * @param PostBot  $bot      The bot itself
         * @param PostData $postData Transformed post data.
         * @param Crawler  $crawler  Crawler containing the target post page's source code. The crawler was manipulated
         *                           according to the settings.
         * @since 1.6.3
         * @since 1.9.0 Fixes: $this should return the bot.
         */
        do_action("wpcc/post/data/after_{$identifier}", $this->bot->getSiteId(), $this->bot->getPostUrl(), $this->bot, $this->bot->getPostData(), $this->bot->getCrawler());
    }

    /**
     * @return bool True if the preparation should be done. Otherwise, false.
     * @since 1.9.0
     */
    protected abstract function shouldPrepare();

    /**
     * @return string An string that identifies this transformation preparer. E.g. if 'translate' is returned, an action
     *                to fire for this preparer will be named, e.g., as 'wpcc/post/data/before_translate'
     * @since 1.9.0
     */
    protected abstract function getActionIdentifier();

    /**
     * Create the transformer to be used to transform the post.
     *
     * @return AbstractTransformer|null
     * @since 1.9.0
     */
    protected abstract function createTransformer(): ?AbstractTransformer;

    /**
     * @return string One of the constants defined in {@link ErrorType}. This will be used to retrieve an error message
     *                text for this transformation preparer.
     * @since 1.9.0
     */
    protected abstract function getErrorType();

    /**
     * @return string One of the constants defined in {@link InformationMessage}. This will be used to retrieve an
     *                information message text for this transformation preparer.
     * @since 1.9.0
     */
    protected abstract function getInformationMessageType();

}