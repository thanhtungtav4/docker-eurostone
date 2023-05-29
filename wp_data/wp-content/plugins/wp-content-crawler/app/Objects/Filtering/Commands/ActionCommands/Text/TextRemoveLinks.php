<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 19/11/2020
 * Time: 13:23
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ActionCommands\Text;


use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Base\AbstractTextActionCommand;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Helpers\LinkRemoverCommandHelper;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinitionList;
use WPCCrawler\Objects\Filtering\Enums\CommandKey;

class TextRemoveLinks extends AbstractTextActionCommand {

    /** @var LinkRemoverCommandHelper|null The helper used to delegate some operations of this command */
    private $helper = null;

    public function getKey(): string {
        return CommandKey::TEXT_REMOVE_LINKS;
    }

    public function getName(): string {
        return _wpcc('Remove links');
    }

    protected function hasTreatAsHtmlOption(): bool {
        return false;
    }

    protected function createViewDefinitionList(): ?ViewDefinitionList {
        return $this->getHelper()->createViewDefinitionList();
    }

    protected function onModifyText(string $text): ?string {
        return $this->getHelper()->createLinkRemover()->removeLinksFromHtml($text);
    }

    /*
     *
     */

    /**
     * @return LinkRemoverCommandHelper See {@link helper}
     * @since 1.11.0
     */
    public function getHelper(): LinkRemoverCommandHelper {
        if ($this->helper === null) {
            $this->helper = new LinkRemoverCommandHelper($this);
        }

        return $this->helper;
    }

}