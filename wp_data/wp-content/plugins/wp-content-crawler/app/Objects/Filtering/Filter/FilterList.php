<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 02/04/2020
 * Time: 12:21
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Filter;


use WPCCrawler\Objects\Events\Base\AbstractEvent;
use WPCCrawler\Objects\Filtering\Enums\FilterSettingOptionKey;
use WPCCrawler\Objects\Filtering\FilterDependencyProvider\FilterDependencyProvider;
use WPCCrawler\Objects\Filtering\FilteringUtils;

class FilterList {

    /** @var Filter[] */
    private $items;

    /** @var bool True if this list is enabled for execution. Otherwise, false. */
    private $enabled;

    /**
     * @param Filter[]|null $items
     * @param bool          $enabled See {@link enabled}
     * @since 1.11.0
     */
    public function __construct(?array $items, bool $enabled = true) {
        $this->items   = $items ?: [];
        $this->enabled = $enabled;
    }

    /**
     * @return Filter[]
     * @since 1.11.0
     */
    public function getItems(): array {
        return $this->items;
    }

    /**
     * @return bool See {@link enabled}
     * @since 1.11.0
     */
    public function isEnabled(): bool {
        return $this->enabled;
    }

    /**
     * Apply all filters in this list
     *
     * @param FilterDependencyProvider $provider See {@link FilterApplier::__construct()}
     * @since 1.11.0
     */
    public function applyAll(FilterDependencyProvider $provider): void {
        // If the list is not enabled, do not apply the filters.
        if (!$this->isEnabled()) return;

        $this->iterateWithAppliers($provider, function(FilterApplier $applier) {
            $applier->apply();
        });
    }

    /**
     * Subscribe the filters to the events specified by the filters so that their conditions and actions are run when
     * the specified events occur.
     *
     * @param FilterDependencyProvider $provider              See {@link FilterApplier::__construct()}
     * @param AbstractEvent            $defaultConditionEvent See {@link FilterApplier::subscribeEvents()}
     * @param AbstractEvent|null       $defaultActionEvent    See {@link FilterApplier::subscribeEvents()}
     * @since 1.11.0
     */
    public function subscribeAll(FilterDependencyProvider $provider, AbstractEvent $defaultConditionEvent,
                                 ?AbstractEvent $defaultActionEvent = null): void {
        // If the list is not enabled, do not subscribe to the events so that the filters are not executed.
        if (!$this->isEnabled()) return;

        $this->iterateWithAppliers(
            $provider,
            function(FilterApplier $applier) use ($defaultConditionEvent, $defaultActionEvent) {
                $applier->subscribeEvents($defaultConditionEvent, $defaultActionEvent);
            });
    }

    /*
     *
     */

    /**
     * Iterate the items and create a filter applier for each. Do anything with the applier by using the callback
     * function.
     *
     * @param FilterDependencyProvider $provider The data provider that will be used to create {@link FilterApplier}s.
     * @param callable                 $callback Takes one parameter, which is a filter applier created for the
     *                                           current {@link Filter}.
     *                                           E.g. function(FilterApplier $applier) { return; }
     * @since 1.11.0
     */
    protected function iterateWithAppliers(FilterDependencyProvider $provider, callable $callback): void {
        foreach($this->getItems() as $filter) {
            // If this is not a filter or it does not have any actions, continue with the next one. A filter without
            // actions is of no use.
            if (!is_a($filter, Filter::class) || !$filter->getActions()) continue;

            $callback(new FilterApplier($filter, $provider));
        }
    }

    /*
     * STATIC METHODS
     */

    /**
     * Create a new instance of this class
     * 
     * @param Filter[]|null $items
     * @param bool          $enabled See {@link enabled}
     * @return FilterList
     * @since 1.11.1
     */
    public static function newInstance(?array $items, bool $enabled = true): FilterList {
        return new FilterList($items, $enabled);
    }

    /**
     * @param array|null $listOptions An array of filter options
     * @param bool|null  $verbose     See {@link FilteringUtils::getVerbose()}
     * @return FilterList|null If the options are valid, a new list. Otherwise, null.
     * @since 1.11.0
     */
    public static function fromOptions(?array $listOptions, ?bool $verbose = null): ?FilterList {
        if (!$listOptions) return null;

        $verbose = FilteringUtils::getVerbose($verbose);

        $options = $listOptions[FilterSettingOptionKey::OPTIONS] ?? [];
        $enabled = (bool) ($options[FilterSettingOptionKey::ENABLED] ?? true);

        $items = [];
        $filters = $listOptions[FilterSettingOptionKey::FILTERS] ?? [];
        foreach($filters as $filter) {
            $item = Filter::fromOptions($filter, $verbose);
            if ($item === null) continue;

            $items[] = $item;
        }

        return static::newInstance($items, $enabled);
    }

    /**
     * @param string|null $json    JSON string representing the filter setting. The same format as the one returned by
     *                             the filter serializer in the UI.
     * @param bool|null  $verbose  See {@link FilteringUtils::getVerbose()}
     * @return FilterList|null If the JSON is valid, a new list. Otherwise, null.
     * @since 1.11.0
     */
    public static function fromJson(?string $json, ?bool $verbose = null): ?FilterList {
        if (!$json) return null;

        $arr = json_decode($json, true);
        if ($arr === null) return null;

        return static::fromOptions($arr, FilteringUtils::getVerbose($verbose));
    }
}