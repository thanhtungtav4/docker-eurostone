<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 02/12/2018
 * Time: 17:06
 *
 * @since 1.8.0
 */

namespace WPCCrawler\PostDetail\Base;


use Illuminate\Contracts\View\View;
use WPCCrawler\Objects\Crawling\Bot\PostBot;
use WPCCrawler\Objects\Crawling\Data\PostData;

abstract class BasePostDetailTester {

    /**
     * @var null|View
     */
    private $testerView;

    /** @var PostBot */
    private $postBot;

    /** @var BasePostDetailData */
    private $detailData;

    /**
     * @param PostBot            $postBot
     * @param BasePostDetailData $detailData
     * @since 1.8.0
     */
    public function __construct($postBot, $detailData) {
        $this->postBot = $postBot;
        $this->detailData = $detailData;
    }


    /**
     * Create tester view. This view will be shown in the test results in the Tester page. The view can be created
     * by using {@link Utils::view()} method. If the view is outside of the plugin, it can be created using a custom
     * implementation of {@link Utils::view()}. In that case, check the source code of the method. Variables available
     * for the general post test result view are available for this view as well. See {@link GeneralPostTest::createView()}
     * for available variables. '$detailData' variable that is the data for this factory will be injected to the view.
     * '$postData' variable that is an instance of {@link PostData} and can be used to reach main post data will also
     * be injected to the view.
     *
     * @return null|View Not-rendered blade view
     * @since 1.8.0
     */
    abstract protected function createTesterView();

    /**
     * @return View|null
     * @since 1.8.0
     */
    public function getTesterView() {
        if (!$this->testerView) $this->testerView = $this->createTesterView();

        return $this->testerView;
    }

    /*
     * PROTECTED GETTERS
     */

    /**
     * @return PostBot
     * @since 1.8.0
     */
    protected function getPostBot() {
        return $this->postBot;
    }

    /**
     * @return BasePostDetailData
     * @since 1.8.0
     */
    protected function getDetailData() {
        return $this->detailData;
    }

    /**
     * @return PostData
     * @since 1.8.0
     */
    protected function getPostData() {
        return $this->postBot->getPostData();
    }
}