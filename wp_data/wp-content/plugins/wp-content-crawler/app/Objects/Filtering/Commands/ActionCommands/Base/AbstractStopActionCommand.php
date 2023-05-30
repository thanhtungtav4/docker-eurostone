<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 01/04/2022
 * Time: 14:27
 *
 * @since 1.12.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ActionCommands\Base;

use Exception;
use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinition;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinitionList;
use WPCCrawler\Objects\Filtering\Enums\InputName;
use WPCCrawler\Objects\Views\Enums\ViewVariableName;
use WPCCrawler\Objects\Views\TextAreaWithLabel;

abstract class AbstractStopActionCommand extends AbstractActionCommand {

    /**
     * @return string The description of the "reason" setting. In the "reason" setting, the user enters a text that
     *                explains why the stop action is executed.
     * @since 1.12.0
     */
    abstract protected function getReasonSettingDescription(): string;

    /**
     * @return string The main reason that will always be included, regardless of whether the user defined a reason or
     *                not. This should include the name of the command as well. The name can be retrieved via
     *                {@link getName()}.
     * @since 1.12.0
     */
    abstract protected function getMainReasonMessage(): string;

    /**
     * Stop the crawling operation by throwing an exception.
     *
     * @param string $message The reason for why the crawling is stopping
     * @throws Exception
     * @since 1.12.0
     */
    abstract protected function onStop(string $message): void;

    /*
     *
     */

    public function getInputDataTypes(): array {
        return [ValueType::T_CRAWLING];
    }

    protected function isOutputTypeSameAsInputType(): bool {
        return true;
    }

    public function doesNeedSubjectValue(): bool {
        return false;
    }

    protected function isTestable(): bool {
        return false;
    }

    protected function createViews(): ViewDefinitionList {
        return (new ViewDefinitionList())
            ->add((new ViewDefinition(TextAreaWithLabel::class))
                ->setVariable(ViewVariableName::TITLE, _wpcc('Reason'))
                ->setVariable(ViewVariableName::INFO, $this->getReasonSettingDescription()
                    . ' '
                    .  _wpcc('This will be added to the log message and to the information messages that you'
                        . ' will see when this command is run. This is optional. If you define this, you will be able'
                        . ' to understand why this command is run when it is run.')

                )
                ->setVariable(ViewVariableName::NAME, InputName::REASON)
                ->setVariable(ViewVariableName::ROWS, 2)
            );
    }

    /**
     * @param int|string|null $key
     * @param mixed|null      $subjectValue
     * @return void
     * @throws Exception
     * @since 1.12.0
     */
    protected function onExecute($key, $subjectValue): void {
        $message = $this->getMainReasonMessage();

        // If there is a reason, append it to the message.
        $reasonPart = $this->getReasonPart();
        if ($reasonPart !== null) {
            $message .= ' ' . $reasonPart;
        }

        $this->onStop($message);
    }

    /*
     * HELPERS
     */

    /**
     * @return string|null The reason part of the message that will be logged and/or shown to the user. If there is no
     *                     reason text, returns null.
     * @since 1.12.0
     */
    protected function getReasonPart(): ?string {
        $reason = $this->getOption(InputName::REASON);
        if (!is_string($reason)) return null;

        // Trim the reason text so that it looks nice.
        $reasonPrepared = trim($reason);
        if ($reasonPrepared === '') return null;

        $reasonPart = _wpcc('Reason') . ': "' . $reasonPrepared . '"';

        // If there is a logger, add the reason as a message so that the user can see it when debugging.
        $logger = $this->getLogger();
        if ($logger) $logger->addMessage($reasonPart);

        return $reasonPart;
    }

}