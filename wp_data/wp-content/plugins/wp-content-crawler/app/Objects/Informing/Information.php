<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 26/10/2018
 * Time: 18:36
 */

namespace WPCCrawler\Objects\Informing;


use Exception;
use WPCCrawler\Objects\Enums\InformationMessage;
use WPCCrawler\Objects\Enums\InformationType;
use WPCCrawler\WPCCrawler;

class Information {

    /** @var string */
    private $message;

    /** @var string */
    private $details;

    /** @var string One of the constants of InformationType class */
    private $type;

    /** @var string One of the constants of InformationMessage class */
    private $informationMessage;

    /** @var string|null */
    private $file;

    /** @var Exception|null */
    private $exception;

    /** @var bool True if the message should be written to PHP's log file. */
    private $addAsLog;

    /**
     * Information constructor.
     * @param string $message The message
     * @param string $details Details about the information
     * @param string $type A constant of InformationType class
     */
    public function __construct($message, $details, $type) {
        $this->message = $message;
        $this->details = $details;
        $this->type = InformationType::isValidName($type) ? $type : InformationType::ERROR;
    }

    /**
     * @param string $details
     * @return Information
     */
    public function setDetails($details) {
        $this->details = $details;
        return $this;
    }

    /**
     * @param string $informationMessage See {@link $informationMessage}. If the message is not valid, it won't be set.
     * @return Information
     */
    public function setInformationMessage($informationMessage) {
        if (!InformationMessage::isValidValue($informationMessage)) {
            $this->informationMessage = $informationMessage;
        }

        return $this;
    }

    /**
     * @param null|string $file
     * @return Information
     */
    public function setFile($file) {
        $this->file = $file;
        return $this;
    }

    /**
     * @param Exception|null $exception
     * @return Information
     */
    public function setException($exception) {
        $this->exception = $exception;
        return $this;
    }

    /**
     * When called, a log with the details of this information will be created in PHP's log file. So, call it when
     * everything about this information is set up.
     *
     * @return Information
     */
    public function addAsLog() {
        $this->addAsLog = true;

        if (!WPCCrawler::isDoingPhpUnitTest()) {
            error_log($this->__toString());
        }

        return $this;
    }

    /*
     * GETTERS
     */

    /**
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getDetails() {
        return $this->details;
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @return null|string
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * @return Exception|null
     */
    public function getException() {
        return $this->exception;
    }

    /**
     * @return bool
     */
    public function isAddAsLog() {
        return $this->addAsLog;
    }

    /*
     * MAGIC METHODS
     */

    public function __toString() {
        $msg = "WPCC ({$this->type}): {$this->message}";

        if ($this->details)     $msg .= " (" . _wpcc("Details") . ": {$this->details})";
        if ($this->file)        $msg .= " (" . _wpcc("File") . ": {$this->file})";
        if ($this->exception)   $msg .= "\n(" . _wpcc("Exception") . ": {$this->exception->getTraceAsString()})";

        return $msg;
    }

    /*
     * STATIC CONSTRUCTORS
     */

    /**
     * @param string $messageType A constant of {@link InformationMessage}
     * @param string $details Details about the information
     * @param string $type A constant of {@link InformationType} class
     * @return Information
     */
    public static function fromInformationMessage($messageType, $details, $type) {
        return (new Information(InformationMessage::getDescription($messageType), $details, $type))->setInformationMessage($messageType);
    }

}