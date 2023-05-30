<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 10/05/2020
 * Time: 14:13
 *
 * @since 1.11.0
 */

namespace WPCCrawler\Objects\Filtering\Explaining\Loggers;


use WPCCrawler\Interfaces\Arrayable;

class Logger implements Arrayable {

    /** @var float */
    private $timeStart = 0;

    /** @var float */
    private $timeEnd = 0;

    /** @var float */
    private $memoryStart = 0;

    /** @var float */
    private $memoryEnd = 0;

    /** @var string[] Stores log messages */
    private $messages = [];

    /**
     * Record memory and time
     *
     * @since 1.11.0
     */
    public function tick(): self {
        $this->timeStart   = microtime(true);
        $this->memoryStart = memory_get_usage();

        $this->timeEnd   = 0;
        $this->memoryEnd = 0;

        return $this;
    }

    /**
     * Finish recording memory and time that were started to be recorded via {@link tick()}
     *
     * @since 1.11.0
     */
    public function tock(): self {
        $this->timeEnd   = microtime(true);
        $this->memoryEnd = memory_get_usage();

        return $this;
    }

    /**
     * @param string $message The message that will be added to {@link messages}
     * @return $this
     * @since 1.11.0
     */
    public function addMessage(string $message): self {
        $this->messages[] = $message;
        return $this;
    }

    /**
     * @return string[] See {@link messages}
     * @since 1.11.0
     */
    public function getMessages(): array {
        return $this->messages;
    }

    /*
     *
     */

    /**
     * @return float The time passed between {@link tick()} and {@link tock()} in milliseconds
     * @since 1.11.0
     */
    public function getElapsedTime(): float {
        $elapsed = (float) number_format(($this->timeEnd - $this->timeStart) * 1000, 2, '.', '');
        return $elapsed < 0 ? 0 : $elapsed;
    }

    /**
     * @return float The memory usage between {@link tick()} and {@link tock()} in kilobytes
     * @since 1.11.0
     */
    public function getUsedMemory(): float {
        $memory = (float) number_format(($this->memoryEnd - $this->memoryStart) / 1000, 2, '.', '');
        return $memory < 0 ? 0 : $memory;
    }

    /*
     *
     */

    public function toArray(): array {
        return [
            'elapsedTimeMs' => $this->getElapsedTime(),
            'memoryUsageKb' => $this->getUsedMemory(),
            'messages'      => $this->getMessages(),
        ];
    }

}