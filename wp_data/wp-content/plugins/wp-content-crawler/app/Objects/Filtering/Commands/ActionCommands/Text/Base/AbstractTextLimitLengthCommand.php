<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 11/07/2020
 * Time: 18:01
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Commands\ActionCommands\Text\Base;


use WPCCrawler\Objects\Chunk\LengthStrategy\AbstractLengthStrategy;
use WPCCrawler\Objects\Filtering\Commands\ActionCommands\Base\AbstractTextActionCommand;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinition;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinitionList;
use WPCCrawler\Objects\Filtering\Enums\InputName;
use WPCCrawler\Objects\Html\HtmlTextModifier;
use WPCCrawler\Objects\Views\Enums\ViewVariableName;
use WPCCrawler\Objects\Views\InputWithLabel;
use WPCCrawler\Objects\Views\NumericInputWithLabel;

abstract class AbstractTextLimitLengthCommand extends AbstractTextActionCommand {

    /** @var null|AbstractLengthStrategy A length strategy that will be used to find out the length of a text */
    private $lengthStrategy = null;

    /**
     * @return string Name of the option into which the user should enter the maximum length of the text
     * @since 1.11.0
     */
    abstract protected function getLengthOptionName(): string;

    /**
     * @return string Description of the option whose name is returned by {@link getLengthOptionName()}
     * @since 1.11.0
     */
    abstract protected function getLengthOptionDescription(): string;

    /**
     * @return int The minimum length that the cut texts can have. If the length input by the user is smaller than this,
     *             the texts will not be cut.
     * @since 1.11.0
     */
    protected function getMinimumLength(): int {
        return 1;
    }

    /**
     * @return bool True if the text should be trimmed before its length is measured while processing HTML. Defaults to
     *              false, meaning that the text's length should be measured without trimming the text. If the spaces
     *              before and after a text should not be counted, then this should return true.
     * @since 1.11.0
     */
    protected function trimBeforeMeasuringLength(): bool {
        return false;
    }

    /*
     *
     */

    /**
     * @param string $text    The text that should be cut
     * @param int    $length  The length of the cut text. The text should be cut to this length.
     * @param string $endText The text that should be appended to the cut text. If the text is not cut, then this must
     *                        not be appended.
     * @return string The cut text. If there is no text when the original text is cut, then returns null.
     * @since 1.11.0
     */
    abstract protected function onCutText(string $text, int $length, string $endText): string;

    /**
     * Create an {@link AbstractLengthStrategy} that will be used to retrieve the length of a text when modifying HTML.
     *
     * @return AbstractLengthStrategy The length strategy, e.g. {@link WordLengthStrategy}
     * @since 1.11.0
     */
    abstract protected function createLengthStrategy(): AbstractLengthStrategy;

    /*
     *
     */

    /**
     * @return AbstractLengthStrategy See {@link lengthStrategy}
     * @since 1.11.0
     */
    protected function getLengthStrategy(): AbstractLengthStrategy {
        if ($this->lengthStrategy === null) {
            $this->lengthStrategy = $this->createLengthStrategy();
        }

        return $this->lengthStrategy;
    }

    protected function createViewDefinitionList(): ?ViewDefinitionList {
        return new ViewDefinitionList([
            (new ViewDefinition(NumericInputWithLabel::class))
                ->setVariable(ViewVariableName::TITLE, $this->getLengthOptionName())
                ->setVariable(ViewVariableName::INFO,  $this->getLengthOptionDescription() . ' ' .
                    sprintf(_wpcc('This must be at least %d.'), $this->getMinimumLength())
                )
                ->setVariable(ViewVariableName::NAME,  InputName::NUMBER)
                ->setVariable(ViewVariableName::STEP,  'any'),

            (new ViewDefinition(InputWithLabel::class))
                ->setVariable(ViewVariableName::TITLE, _wpcc('End text'))
                ->setVariable(ViewVariableName::INFO,  _wpcc('A text that will be added to the end of the text when the text is cut.'))
                ->setVariable(ViewVariableName::NAME,  InputName::TEXT)
                ->setVariable(ViewVariableName::TYPE,  'text')
        ]);
    }

    protected function onModifyText(string $text): ?string {
        $maxLength = $this->getMaxLength();
        if ($maxLength === null) return $text;

        // Cut the text. Use the end text defined by the user.
        return $this->onCutText($text, $maxLength, $this->getEndText());
    }

    protected function onModifyHtml(string $html): string {
        // Get the maximum length
        $maxLength = $this->getMaxLength();
        if ($maxLength === null) return $html;

        // Get the end text defined by the user
        $endText = $this->getEndText();

        // We will modify only the texts inside the HTML and keep the length of the text in each text node so that we
        // can limit the length globally in the HTML.
        $textModifier = new HtmlTextModifier($html);

        $lengthStrategy = $this->getLengthStrategy();
        $processedLength = 0;
        $modifiedHtml = $textModifier
            ->modify(function($text) use ($endText, &$processedLength, $maxLength, $lengthStrategy) {
                // Get the current length limit by subtracting the processed count from the maximum
                $currentLengthLimit = $maxLength - $processedLength;

                // If there is no limit, remove the text.
                if ($currentLengthLimit <= 0) {
                    // If the current length limit is exactly 0, it means that the end text was not added before. Make
                    // sure the end text is added and make sure the end text will not be added again by ensuring that
                    // the currentLengthLimit will be less than zero the next time this function is called.
                    if ($currentLengthLimit === 0) {
                        $processedLength = $maxLength + 1;
                        if ($endText) return $endText;
                    }

                    return '';
                }

                // Remove extra length
                $newText = $this->onCutText($text, $currentLengthLimit, $endText);

                // Get the text that should be measured. If it needs to be trimmed, trim it.
                $textToMeasure = $this->trimBeforeMeasuringLength() ? trim($text) : $text;

                // If nothing is removed, increase the processed length
                if (mb_strlen($text) === mb_strlen($newText)) {
                    // If the text is empty when it is trimmed, do not count it. This happens in case the text is
                    // composed of all new lines or spaces. We do not want to count new lines.
                    $processedLength += trim($textToMeasure) === '' ? 0 : $lengthStrategy->getLengthFor($textToMeasure);

                    // If this is the last piece and it was not added the end text, trim it to get rid of existing
                    // spaces in the end of the text.
                    if ($processedLength === $maxLength) {
                        $newText = rtrim($newText);
                    }

                } else {
                    // If the new text does not have the same length as the old text, then we reached the limit.
                    $processedLength += $lengthStrategy->getLengthFor($textToMeasure);

                    // If the processed length is less than the maximum length, make sure it is not. It might happen if
                    // we cannot get the measurement right. This will probably not occur, but we want to be on the safe
                    // side.
                    if ($processedLength < $maxLength) $processedLength = $maxLength;
                }

                return $newText;
            });

        return $modifiedHtml ?: $html;
    }

    /*
     *
     */

    /**
     * @return string The end text defined by the user. If it is not defined, an empty string is returned.
     * @since 1.11.0
     */
    protected function getEndText(): string {
        $endText = $this->getOption(InputName::TEXT);
        return $endText === null 
            ? '' 
            : (string) $endText;
    }

    /**
     * @return int|null The maximum length defined by the user. If it is not defined, null.
     * @since 1.11.0
     */
    protected function getMaxLength(): ?int {
        // Get the maximum length defined by the user. If there is none, return the original text.
        $maxLength = $this->getOption(InputName::NUMBER);
        if ($maxLength === null || $maxLength === '') return null;

        // Make the length an integer. If the length is less than the minimum, return the original text.
        $maxLength = (int) $maxLength;
        return $maxLength < $this->getMinimumLength() ? null : $maxLength;
    }

}