<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 10/05/2020
 * Time: 14:28
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Explaining\Loggers;


class ActionCommandLogger extends AbstractCommandLogger {

    /** @var string The key used to store new values of the subject items in {@link managedStringArrays} */
    const KEY_MODIFIED_SUBJECTS = 'modifiedSubjects';

    /** @var bool */
    private $onlyAllowed = false;

    /**
     * @return bool
     * @since 1.11.0
     */
    public function isOnlyAllowed(): bool {
        return $this->onlyAllowed;
    }

    /**
     * Add a subject item's new value
     *
     * @param string|null $item A string that will be shown to the user so that he/she can understand the subject's new
     *                          value
     * @return ActionCommandLogger
     * @since 1.11.0
     */
    public function addModifiedSubjectItem(?string $item): self {
        $this->addItem(static::KEY_MODIFIED_SUBJECTS, $item);

        return $this;
    }

    /**
     * @param bool $onlyAllowed
     * @return ActionCommandLogger
     * @since 1.11.0
     */
    public function setOnlyAllowed(bool $onlyAllowed): self {
        $this->onlyAllowed = $onlyAllowed;
        return $this;
    }

    /*
     *
     */

    public function toArray(): array {
        return array_merge(parent::toArray(),
            $this->getItemsForResponse(static::KEY_MODIFIED_SUBJECTS),
            [
                'onlyAllowedSubjects' => $this->isOnlyAllowed(),
            ]
        );
    }


}