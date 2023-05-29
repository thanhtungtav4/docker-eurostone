<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 09/05/2020
 * Time: 19:43
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Explaining\Base;


use Illuminate\Support\Arr;
use WPCCrawler\Exceptions\PropertyNotExistException;
use WPCCrawler\Objects\Filtering\Commands\Base\AbstractBaseCommand;
use WPCCrawler\Objects\Filtering\FilteringUtils;
use WPCCrawler\Objects\Filtering\Property\PropertyService;

abstract class AbstractCommandExplainer extends AbstractExplainer {

    /** @var AbstractBaseCommand The command that will be explained */
    private $cmd;

    /**
     * @param AbstractBaseCommand $cmd
     * @since 1.11.0
     */
    public function __construct(AbstractBaseCommand $cmd) {
        $this->cmd = $cmd;
    }

    public function explain(): array {
        $cmd    = $this->getCommand();
        $logger = $cmd->getLogger();

        return [
            'type'     => 'command',
            'subject'  => $this->getFieldName(),
            'property' => $this->getPropertyName(),
            'command'  => $cmd->getName(),
            'details'  => array_merge(
                $logger ? $logger->toArray() : [],
                [
                    'executed'         => $cmd->isExecuted(),
                    'needSubjectValue' => $cmd->doesNeedSubjectValue(),
                ]
            )
        ];
    }

    /*
     * PROTECTED METHODS
     */

    /**
     * @return string|null If found, name of the subject's property. Otherwise, null.
     * @since 1.11.0
     */
    protected function getPropertyName(): ?string {
        $propertyKey = $this->getCommand()->getPropertyKey();
        if (!$propertyKey) return null;

        try {
            $property = PropertyService::getInstance()->getProperty($propertyKey);
            return $property 
                ? $property->getName() 
                : null;

        } catch (PropertyNotExistException $e) {
            return null;
        }
    }

    /**
     * @return string|null If found, name of the field (subject) of this command. Otherwise, null.
     * @since 1.11.0
     */
    protected function getFieldName(): ?string {
        $fieldKey = $this->getCommand()->getFieldKey();
        if ($fieldKey === null) {
            return null;
        }
        
        return Arr::get(FilteringUtils::getFilteringService()->getSubjectTitleMap(), $fieldKey);
    }

    /*
     *
     */

    /**
     * @return AbstractBaseCommand
     * @since 1.11.0
     */
    public function getCommand(): AbstractBaseCommand {
        return $this->cmd;
    }

}