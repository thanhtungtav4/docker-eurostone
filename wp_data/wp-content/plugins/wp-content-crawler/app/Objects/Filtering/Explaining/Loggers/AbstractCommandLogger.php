<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 11/05/2020
 * Time: 11:19
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Explaining\Loggers;


abstract class AbstractCommandLogger extends Logger {

    /** @var array */
    private $managedStringArrays = [];

    /** @var int Maximum number of items that can be in an array inside {@link managedStringArrays} */
    protected $maxItemCount = 20;

    /** @var int Maximum length of items that can be in an array inside {@link managedStringArrays} */
    protected $maxItemLength = 1000;

    /** @var string The key used to store subject items in {@link managedStringArrays} */
    const KEY_SUBJECTS = 'subjects';

    /** @var string The key used to store denied subject items in {@link managedStringArrays} */
    const KEY_DENIED_SUBJECTS = 'deniedSubjects';

    /**
     * Add a subject item
     *
     * @param string|null $item A string that will be shown to the user so that he/she can understand the subject's
     *                          value
     * @return AbstractCommandLogger
     * @since 1.11.0
     */
    public function addSubjectItem(?string $item): self {
        $this->addItem(static::KEY_SUBJECTS, $item);

        return $this;
    }

    /**
     * @param string|null $subjectItem
     * @return AbstractCommandLogger
     * @since 1.11.0
     */
    public function addDeniedSubjectItem(?string $subjectItem): self {
        $this->addItem(static::KEY_DENIED_SUBJECTS, $subjectItem);

        return $this;
    }

    /*
     *
     */

    /**
     * Add an item to an array inside {@link managedStringArrays}. The item will be added only if the maximum item limit
     * was not reached
     *
     * @param string      $arrayKey Key of the managed string array. If the key does not exist in
     *                              {@link managedStringArrays}, then it will be created.
     * @param string|null $item     The item that will be added to the array
     * @since 1.11.0
     */
    protected function addItem(string $arrayKey, ?string $item): void {
        // If the key does not exist, create an array with the key
        if (!isset($this->managedStringArrays[$arrayKey])) {
            $this->managedStringArrays[$arrayKey] = [
                'items'        => [],
                'limitReached' => false,
                'itemCount'    => 0
            ];
        }

        // Get the managed array
        $arr = $this->managedStringArrays[$arrayKey];

        // If there is an item
        if ($item !== null) {

            // If the maximum item limit is not reached yet
            if (!$arr['limitReached']) {
                $arr['items'][] = $this->maybeShorten($item);
                $arr['itemCount']++;

                // If the limit is reached, mark the array as it has reached its limit.
                if (sizeof($arr['items']) >= $this->maxItemCount) {
                    $arr['limitReached'] = true;
                }

            } else {
                // If the limit has been reached, instead of adding the item into the array, just increase the number
                // of items so that we can show the user the total number of items in the array.
                $arr['itemCount']++;
            }
        }

        // Assign the modified array into the instance variable
        $this->managedStringArrays[$arrayKey] = $arr;
    }

    /**
     * Get items from {@link managedStringArrays} in a structure that can be directly used in the response
     *
     * @param string $arrayKey The key of the managed string array
     * @return array An array that has these keys:
     *                  $arrayKey           This key contains the actual items in the array
     *                  $arrayKey . 'Count' The number of items that would be in the array if there was no limitation
     * @since 1.11.0
     */
    protected function getItemsForResponse(string $arrayKey): array {
        $arr = $this->managedStringArrays[$arrayKey] ?? null;
        if ($arr === null) return [];

        $items        = $arr['items'];
        $actualCount  = $arr['itemCount'];
        $limitedCount = sizeof($items);

        // Add an item that shows how many additional elements exist in the list
        if ($actualCount > $limitedCount) {
            $items[] = sprintf(_wpcc('+%d item(s)...'), $actualCount - $limitedCount);
        }

        return [
            $arrayKey           => $items,
            $arrayKey . 'Count' => $actualCount,
        ];
    }

    /**
     * @param string|null $item An item that might be shortened
     * @return string|null If the length of the item is greater than {@link maxItemLength}, the item that is shortened
     *                     to {@link maxItemLength}. Otherwise, the item itself.
     * @since 1.11.0
     */
    protected function maybeShorten(?string $item): ?string {
        if ($item === null) return null;

        $itemLength = mb_strlen($item);
        return $itemLength <= $this->maxItemLength
            ? $item
            : mb_substr($item, 0, $this->maxItemLength)
                . '... ('
                . sprintf(_wpcc('+%1$d char(s)'), $itemLength - $this->maxItemLength)
                . ')';
    }

    /*
     *
     */

    public function toArray(): array {
        return array_merge(
            parent::toArray(),
            $this->getItemsForResponse(static::KEY_SUBJECTS),
            $this->getItemsForResponse(static::KEY_DENIED_SUBJECTS)
        );
    }

}