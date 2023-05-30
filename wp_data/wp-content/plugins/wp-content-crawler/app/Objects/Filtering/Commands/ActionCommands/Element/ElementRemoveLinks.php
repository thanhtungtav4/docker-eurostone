<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 11/04/2020
 * Time: 17:23
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ActionCommands\Element;


use Symfony\Component\DomCrawler\Crawler;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Base\AbstractBotActionCommand;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Helpers\LinkRemoverCommandHelper;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinitionList;
use WPCCrawler\Objects\Filtering\Enums\CommandKey;

/**
 * Used to remove elements from the crawler by using the given CSS selectors
 *
 * @since 1.11.0
 */
class ElementRemoveLinks extends AbstractBotActionCommand {

    /** @var LinkRemoverCommandHelper|null A helper that helps with the link removal */
    private $helper;

    public function getKey(): string {
        return CommandKey::ELEMENT_REMOVE_LINKS;
    }

    public function getName(): string {
        return _wpcc('Remove links');
    }

    protected function createViews(): ?ViewDefinitionList {
        return $this->getHelper()->createViewDefinitionList();
    }

    protected function onExecuteCommand($node): void {
        if (!$node || !($node instanceof Crawler)) return;

        $this->getHelper()->createLinkRemover()->removeLinksFromCrawler($node);
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