<?php


namespace Hexbit\Flash\Session;


class FlashSessionHandler extends Script implements SessionHandler
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

    /**
     * @var array of flash headers who will be stored in session storage
     * @linked to $_SESSION super global array
     */
    private $flashHeaders;

    public function __construct()
    {
        parent::init();

        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }

        // linked sessions to fields as a pointer
        $this->messageKeys =& $_SESSION[self::FLASH_KEYS];
        $this->flashHeaders =& $_SESSION[self::FLASH_HEADER];

        if (isset($this->flashHeaders)) {
            foreach ($this->flashHeaders as $header) {
                @header($header);
            }
        }
        // clear flash headers
        $this->flashHeaders = [];

        if (!isset($this->messageKeys)) {
            $this->messageKeys = [];
        }

        $this->skipCleanup = false;
    }

    /**
     * @return bool indicates that script skips current session data
     * cleaning or not.
     */
    function getSkipCleanup()
    {
        return $this->skipCleanup;
    }

    /**
     * @param $skip bool changes session data cleaning indicator.
     */
    function setSkipCleanup($skip)
    {
        $this->skipCleanup = $skip;
    }

    /**
     * @param $header string add a flash http header for next script running.
     */
    function setFlashHeader($header)
    {
        $this->flashHeaders[] = $header;
    }

    /**
     * removes http flash header by key.
     * @param $key
     */
    function removeFlashHeader($key)
    {
        unset($this->flashHeaders[array_search($key, $this->flashHeaders)]);
    }

    /**
     * adds a new flash message in sessions.
     * @param $key
     * @param $value
     */
    function setSessionData($key, $value)
    {
        $_SESSION[$key] = $value;
        $this->messageKeys[] = $key;
    }

    /**
     * @param $key
     * @return string|null flash data if exists.
     */
    function getSessionData($key)
    {
        if (in_array($key, $this->getFlashKeys())) {
            return $_SESSION[$key];
        }
        return null;
    }

    /**
     * removes data from flash sessions by key.
     * @param $key
     */
    function removeSessionData($key)
    {
        unset($_SESSION[$key]);
        unset($this->messageKeys[array_search($key, $this->messageKeys)]);
        if (empty($this->messageKeys)) {
            $this->skipCleanup = false;
        }
    }

    /**
     * @return array of current flash message keys.
     */
    function getFlashKeys()
    {
        return $this->messageKeys;
    }

    /**
     * script end point.
     */
    public function teardown()
    {
        $this->sessionCleanup();
    }

    /**
     * This method clears the session on script endpoint
     * when script reached on shut down function , it means all messages
     * added to the output on that point, and we can cleanup the flashes.
     */
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
}
