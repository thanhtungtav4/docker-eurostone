<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 12/07/2020
 * Time: 18:30
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Property\Strings\Base;


use WPCCrawler\Objects\Chunk\LengthStrategy\AbstractLengthStrategy;
use WPCCrawler\Objects\Crawling\Bot\DummyBot;
use WPCCrawler\Objects\Enums\ValueType;
use WPCCrawler\Objects\Filtering\Commands\Base\AbstractBaseCommand;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinitionFactory;
use WPCCrawler\Objects\Filtering\Commands\Views\ViewDefinitionList;
use WPCCrawler\Objects\Filtering\Enums\InputName;
use WPCCrawler\Objects\Filtering\Property\Base\AbstractProperty;
use WPCCrawler\Objects\Filtering\Property\Objects\CalculationResult;

abstract class AbstractStringLengthProperty extends AbstractProperty {

    /** @var AbstractLengthStrategy|null */
    private $lengthStrategy = null;

    public function getInputDataTypes(): array {
        return [ValueType::T_STRING];
    }

    public function getOutputDataTypes(): array {
        return [ValueType::T_INTEGER, ValueType::T_NUMERIC];
    }

    /**
     * @return AbstractLengthStrategy A length strategy that will be used to calculate the length of the text. E.g.
     *                                {@link CharLengthStrategy}
     * @since 1.11.0
     */
    abstract protected function createLengthStrategy(): AbstractLengthStrategy;

    /**
     * @return AbstractLengthStrategy A length strategy that will be used to calculate the length of a string
     * @since 1.11.0
     */
    protected function getLengthStrategy(): AbstractLengthStrategy {
        if ($this->lengthStrategy === null) {
            $this->lengthStrategy = $this->createLengthStrategy();
        }

        return $this->lengthStrategy;
    }

    protected function createViews(): ?ViewDefinitionList {
        return (new ViewDefinitionList())
            ->add(ViewDefinitionFactory::getInstance()->createTreatAsHtmlInput());
    }

    protected function onCalculate($key, $source, AbstractBaseCommand $cmd): CalculationResult {
        $source = is_scalar($source)
            ? (string) $source
            : null;

        if ($source === null) {
            return new CalculationResult($key, null);
        }

        $length = $this->shouldTreatAsHtml($cmd)
            ? $this->calculateHtmlTextLength($source, $cmd)
            : $this->calculateTextLength($source, $cmd);

        return new CalculationResult($key, $length);
    }

    /*
     *
     */

    /**
     * Calculate the length of a text
     *
     * @param string              $source The text
     * @param AbstractBaseCommand $cmd    Current command
     * @return int The length of the text
     * @since 1.11.0
     */
    protected function calculateTextLength(string $source, AbstractBaseCommand $cmd): int {
        $length = $this->getLengthStrategy()->getLengthFor($source);

        $logger = $cmd->getLogger();
        if ($logger) $logger->addMessage(sprintf(
            _wpcc('The length of the text is %1$d.'),
            $length
        ));

        return $length;
    }

    /**
     * Calculate the length of the text inside an HTML code
     *
     * @param string              $source Source HTML code
     * @param AbstractBaseCommand $cmd    Current command
     * @return int The length of the text inside the HTML code
     * @since 1.11.0
     */
    protected function calculateHtmlTextLength(string $source, AbstractBaseCommand $cmd): int {
        $bot = new DummyBot([]);
        $crawler = $bot->createDummyCrawler($source);

        // Get the text. The text inside the HTML code might be returned as surrounded with new lines. Trim it so that
        // the whitespace characters at the beginning and at the end of the text are not counted.
        $text = trim($crawler->text());
        $length = $this->getLengthStrategy()->getLengthFor($text);

        $logger = $cmd->getLogger();
        if ($logger) $logger->addMessage(sprintf(
            _wpcc('The length of the text inside the HTML code is %1$d.'),
            $length
        ));

        return $length;
    }

    /*
     *
     */

    /**
     * @param AbstractBaseCommand $cmd
     * @return bool True if the subject should be treated as HTML and only its text should be considered when
     *              calculating the length.
     * @since 1.11.0
     */
    protected function shouldTreatAsHtml(AbstractBaseCommand $cmd): bool {
        return $cmd->getCheckboxOption(InputName::TREAT_AS_HTML);
    }

}