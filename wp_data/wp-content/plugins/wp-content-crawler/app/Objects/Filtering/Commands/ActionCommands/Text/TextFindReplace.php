<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 31/03/2020
 * Time: 13:30
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ActionCommands\Text;


use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Base\AbstractTextActionCommand;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinition;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinitionList;
use WPCCrawler\Objects\Filtering\Enums\CommandKey;
use WPCCrawler\Objects\Filtering\Enums\InputName;
use WPCCrawler\Objects\Traits\FindAndReplaceTrait;
use WPCCrawler\Objects\Views\Enums\ViewVariableName;
use WPCCrawler\Objects\Views\MultipleFindReplaceWithLabelForCmd;

class TextFindReplace extends AbstractTextActionCommand {

    use FindAndReplaceTrait;

    public function getKey(): string {
        return CommandKey::TEXT_FIND_REPLACE;
    }

    public function getName(): string {
        return _wpcc('Find and replace');
    }

    protected function hasTreatAsHtmlOption(): bool {
        return false;
    }

    protected function createViewDefinitionList(): ?ViewDefinitionList {
        return (new ViewDefinitionList())
            ->add((new ViewDefinition(MultipleFindReplaceWithLabelForCmd::class))
                ->setVariable(ViewVariableName::TITLE, _wpcc('Find and replace'))
                ->setVariable(ViewVariableName::INFO,  _wpcc('Find and replace anything in the value.'))
                ->setVariable(ViewVariableName::NAME,  InputName::FIND_REPLACE))
            ;
    }

    protected function onModifyText(string $text): ?string {
        // Get the options
        $findReplaces = $this->getArrayOption(InputName::FIND_REPLACE);

        // Apply the find-replace rules
        return $this->findAndReplace($findReplaces, $text);
    }

}