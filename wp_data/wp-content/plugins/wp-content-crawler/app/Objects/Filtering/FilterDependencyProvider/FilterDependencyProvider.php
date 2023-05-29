<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 05/04/2020
 * Time: 21:25
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\FilterDependencyProvider;


use Illuminate\Support\Str;
use WPCCrawler\Objects\Filtering\Commands\Base\AbstractBaseCommand;
use WPCCrawler\Objects\Filtering\Interfaces\HasBot;
use WPCCrawler\Objects\Filtering\Interfaces\HasCommand;
use WPCCrawler\Objects\Filtering\Interfaces\NeedsBot;
use WPCCrawler\Objects\Filtering\Interfaces\NeedsCommand;
use WPCCrawler\Objects\Filtering\Interfaces\NeedsProvider;
use WPCCrawler\Objects\Filtering\SpecialFieldService;
use WPCCrawler\Objects\Transformation\Interfaces\Transformable;

/**
 * Injects dependencies to objects that have a class existing in Filtering package
 *
 * @since 1.11.0
 */
class FilterDependencyProvider implements NeedsCommand, HasCommand {

    /**
     * @var array $dataSourceMap An associative array stores identifiers of data sources as keys and the data
     *      sources, which should be {@link Transformable}s, as values. Structured as:
     *
     *      <b>['identifier1' => {@link Transformable}, 'identifier2' => {@link Transformable}]</b>
     */
    private $dataSourceMap;

    /** @var AbstractBaseCommand|null */
    private $command;

    /**
     * @param array|null $dataSourceMap See {@link dataSourceMap}
     * @since 1.11.0
     */
    public function __construct(?array $dataSourceMap) {
        $this->dataSourceMap = $dataSourceMap ?: [];
    }

    /**
     * @return array See {@link dataSourceMap}
     * @since 1.11.0
     */
    public function getDataSourceMap(): array {
        return $this->dataSourceMap;
    }

    /**
     * Inject dependencies of an object
     *
     * @param object $item
     * @since 1.11.0
     */
    public function injectDependencies($item): void {
        if (!is_object($item)) return;

        $this->injectProvider($item);
        $this->injectCommandDependencies($item);
        $this->injectBot($item);
        $this->injectCommand($item);
    }

    /**
     * Invalidate the dependencies of an object
     *
     * @param object $item
     * @since 1.11.0
     */
    public function invalidateDependencies($item): void {
        if (!is_object($item)) return;

        $this->invalidateProvider($item);
        $this->invalidateCommandDependencies($item);
        $this->invalidateBot($item);
        $this->invalidateCommand($item);
    }

    /*
     * PROTECTED METHODS
     */

    /**
     * Inject a provider to the item if the item {@link NeedsProvider}
     *
     * @param mixed|NeedsProvider $item
     * @since 1.11.0
     */
    protected function injectProvider($item): void {
        if ($item instanceof NeedsProvider) {
            $item->setProvider($this);
        }
    }

    /**
     * Invalidate the item's provider if the item {@link NeedsProvider}
     *
     * @param mixed|NeedsProvider $item
     * @since 1.11.0
     */
    protected function invalidateProvider($item): void {
        if (!($item instanceof NeedsProvider)) return;
        $item->setProvider(null);
    }

    /*
     *
     */

    /**
     * Inject a suitable data source into the command by using its field key (See
     * {@link AbstractBaseCommand::getFieldKey()}) so that it can do its job.
     *
     * @param mixed|AbstractBaseCommand $command An object. If this is not a command, no injection is done.
     * @since 1.11.0
     */
    protected function injectCommandDependencies($command): void {
        if (!($command instanceof AbstractBaseCommand)) return;

        // Inject the command into this instance as well
        $this->setCommand($command);

        $fieldKey = $command->getFieldKey();
        if (!$fieldKey) return;

        // First, get the data source identifier.
        $identifier = null;

        // If this is a special field, try to get the special field's identifier
        $specialFieldIdentifier = SpecialFieldService::SPECIAL_FIELD_IDENTIFIER . '.';
        if (Str::startsWith($fieldKey, $specialFieldIdentifier)) {
            $specialFieldKey = substr($fieldKey, strlen($specialFieldIdentifier));
            $identifier = SpecialFieldService::getInstance()->getDataSourceIdentifier($specialFieldKey);

        } else {
            // This is not a special field. Get the first part until the dot as the data source identifier.
            $identifier = explode('.', $fieldKey, 2)[0];
        }

        // If there is no identifier, stop. We cannot find the data source to inject.
        if (!$identifier) return;

        // Try to get a data source with the found data source identifier. If there is no data source with that
        // identifier, stop.
        $dataSource = $this->getDataSourceMap()[$identifier] ?? null;
        if (!$dataSource || !is_a($dataSource, Transformable::class)) return;

        // We have the data source. Inject it to the command.
        $command
            ->setDataSource($dataSource)
            ->setDataSourceIdentifier($identifier);
    }

    /**
     * @param mixed|AbstractBaseCommand $command An object. If this is not a command, nothing is done.
     * @since 1.11.0
     */
    protected function invalidateCommandDependencies($command): void {
        if (!($command instanceof AbstractBaseCommand)) return;

        $command
            ->setDataSource(null)
            ->setDataSourceIdentifier(null);

        // Invalidate the command stored in this instance as well
        $this->setCommand(null);
    }

    /*
     *
     */

    /**
     * Inject a bot to the item if the item {@link NeedsBot} and this class {@link HasBot}
     *
     * @param mixed|NeedsBot $item
     * @since 1.11.0
     */
    protected function injectBot($item): void {
        // If the command needs a bot and the this provider has a bot, get the bot from data provider and assign it to
        // the command.
        if ($item instanceof NeedsBot && $this instanceof HasBot) {
            $item->setBot($this->getBot());
        }
    }

    /**
     * Invalidate the item's bot if the item {@link NeedsBot}
     *
     * @param mixed|NeedsBot $item
     * @since 1.11.0
     */
    protected function invalidateBot($item): void {
        if (!($item instanceof NeedsBot)) return;
        $item->setBot(null);
    }

    /*
     *
     */

    /**
     * Inject a command to the item if the item {@link NeedsCommand}
     *
     * @param mixed|NeedsCommand $item
     * @since 1.11.0
     */
    protected function injectCommand($item): void {
        if ($item instanceof NeedsCommand) {
            $item->setCommand($this->getCommand());
        }
    }

    /**
     * Invalidate the item's bot if the item {@link NeedsCommand}
     *
     * @param mixed|NeedsCommand $item
     * @since 1.11.0
     */
    protected function invalidateCommand($item): void {
        if (!($item instanceof NeedsCommand)) return;
        $item->setCommand(null);
    }

    /*
     *
     */

    public function getCommand(): ?AbstractBaseCommand {
        return $this->command;
    }

    public function setCommand(?AbstractBaseCommand $command): void {
        $this->command = $command;
    }

}