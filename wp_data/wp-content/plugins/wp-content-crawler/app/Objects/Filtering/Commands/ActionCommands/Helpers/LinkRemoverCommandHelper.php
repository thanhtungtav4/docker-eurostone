<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 23/11/2020
 * Time: 18:30
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ActionCommands\Helpers;


use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Base\AbstractActionCommand;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinition;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinitionList;
use WPCCrawler\Objects\Filtering\Enums\InputName;
use WPCCrawler\Objects\Html\LinkRemover;
use WPCCrawler\Objects\Views\Enums\ViewVariableName;
use WPCCrawler\Objects\Views\MultipleInputWithLabel;

class LinkRemoverCommandHelper {

    /** @var AbstractActionCommand The command that this helper helps */
    private $command;

    /**
     * @param AbstractActionCommand $command See {@link command}
     * @since 1.11.0
     */
    public function __construct(AbstractActionCommand $command) {
        $this->command = $command;
    }

    public function createViewDefinitionList(): ViewDefinitionList {
        $wildcardInfo = sprintf(
            _wpcc('You can use %1$s to indicate the variable parts. For example, %2$s rule does not match %3$s, 
            but %4$s does.'),
            '<b>*</b>',
            '<b>domain.com</b>',
            '<b>sub.domain.com</b>',
            '<b>*.domain.com</b>'
        );

        return (new ViewDefinitionList())
            ->add((new ViewDefinition(MultipleInputWithLabel::class))
                ->setVariable(ViewVariableName::TITLE,       _wpcc('Blocked domains'))
                ->setVariable(ViewVariableName::INFO,        _wpcc('Enter the blocked domain rules. The links 
                    targeting one of the given domains will be removed.') . ' ' . $wildcardInfo)
                ->setVariable(ViewVariableName::NAME,        InputName::INVALID_DOMAIN)
                ->setVariable(ViewVariableName::PLACEHOLDER, _wpcc('Blocked domain'))
                ->setVariable(ViewVariableName::TYPE,        'text'))

            ->add((new ViewDefinition(MultipleInputWithLabel::class))
                ->setVariable(ViewVariableName::TITLE,       _wpcc('Allowed domains'))
                ->setVariable(ViewVariableName::INFO,        _wpcc('Enter the allowed domain rules. The links 
                    targeting a domain outside of the given domains will be removed.') . ' ' . $wildcardInfo)
                ->setVariable(ViewVariableName::NAME,        InputName::VALID_DOMAIN)
                ->setVariable(ViewVariableName::PLACEHOLDER, _wpcc('Allowed domain'))
                ->setVariable(ViewVariableName::TYPE,        'text'))
            ;
    }

    public function createLinkRemover(): LinkRemover {
        return new LinkRemover(
            $this->getDomainRules(InputName::INVALID_DOMAIN),
            $this->getDomainRules(InputName::VALID_DOMAIN)
        );
    }

    /*
     *
     */

    /**
     * @return AbstractActionCommand See {@link command}
     * @since 1.11.0
     */
    public function getCommand(): AbstractActionCommand {
        return $this->command;
    }

    /**
     * Get the domain rules defined in an input
     *
     * @param string $inputName Name of the input that stores the domain rules
     * @return string[] The domain rules defined in the input
     * @since 1.11.0
     */
    protected function getDomainRules(string $inputName): array {
        $value = $this->getCommand()->getOption($inputName);
        if (!$value || !is_array($value)) return [];

        return array_filter(array_map(function($rule) {
            $trimmed = trim($rule);
            return $trimmed !== '' ? $trimmed : null;
        }, $value));
    }
}