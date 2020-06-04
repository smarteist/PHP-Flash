<?php


namespace Hexbit\Flash\Session;


class FlashSessionHandler implements SessionCleaner, SessionHandler
{
    /**
     * Unique id for flash keys.
     */
    const FLASH_KEYS = 'flash_keys_f3740dce19e03367d9';

    /**
     * Unique id for flash headers.
     */
    const FLASH_HEADER = 'flash_header_f3740dce19e03367d9';

    /**
     * @var bool determines session cleanup should skipped or not
     */
    private $skipCleanup;

    /**
     * @var array $messageKeys for cleanup job on shutdown function
     * @linked to $_SESSION super global array
     */
    private $messageKeys;

    public function __construct()
    {
        $this->init();
    }

    /**
     * Initialization jobs for this handler
     */
    function init()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        if (isset($_SESSION[self::FLASH_HEADER])) {
            @header($_SESSION[self::FLASH_HEADER]);
            unset($_SESSION[self::FLASH_HEADER]);
        }
        // linked field to sessions as a pointer
        $this->messageKeys =& $_SESSION[self::FLASH_KEYS];
        if (!isset($this->messageKeys)) {
            $this->messageKeys = [];
        }
        $this->skipCleanup = false;
        // cleanup session when script reached to the endpoint.
        register_shutdown_function([$this, 'sessionCleanup']);
    }

    function getSkipCleanup(): bool
    {
        return $this->skipCleanup;
    }

    function setSkipCleanup(bool $skip)
    {
        $this->skipCleanup = $skip;
    }

    function sessionCleanup()
    {
        if (!self::getSkipCleanup()) {
            //clean flash messages by using stored keys
            foreach ($this->messageKeys as $messageKey) {
                unset($_SESSION[$messageKey]);
            }
            //clean stored keys itself
            $this->messageKeys = [];
        }
    }

    function setMessage($key, $value)
    {
        $_SESSION[$key] = $value;
        $this->messageKeys[] = $key;
    }

    function setFlashHeader($header)
    {
        $_SESSION[self::FLASH_HEADER] = $header;
    }

    function getMessage($key)
    {
        return $_SESSION[$key];
    }

    function removeMessage($key)
    {
        unset($this->messageKeys[array_search($key, $this->messageKeys)]);
        if (empty($this->messageKeys)) {
            $this->skipCleanup = false;
        }
    }

    function getFlashKeys()
    {
        return $this->messageKeys;
    }
}
