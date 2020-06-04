<?php


namespace Hexbit\Flash;


use Hexbit\Flash\Exceptions\FlashHandlerNotInitialized;
use Hexbit\Flash\Session\FlashSessionHandler;

class Flash
{

    /**
     * @var null|FlashSessionHandler
     */
    static $sessionHandler = null;
    /**
     * @var string
     */
    private $xRedirectBy = 'flash';
    /**
     * @var int
     */
    private $statusCode = 302;
    /**
     * @var string
     */
    private $redirectUrl;

    /**
     * Initializing Flash Handler
     */
    public static function init()
    {
        if (!isset(static::$sessionHandler)) {
            static::$sessionHandler = new FlashSessionHandler();
        }
    }


    /**
     * @param string $key flash message key in session storage
     * @param string $message message value
     *
     * @return Flash
     * @throws FlashHandlerNotInitialized
     */
    public function message($key, $message)
    {
        if (!isset(self::$sessionHandler)) {
            throw new FlashHandlerNotInitialized();
        }
        self::$sessionHandler->setMessage($key, $message);
        return $this;
    }

    /**
     * @param $url
     *
     * @return Flash
     */
    public function redirectLocation($url)
    {
        $this->redirectUrl = $url;
        return $this;
    }

    /**
     * @param $status
     *
     * @return Flash
     */
    public function withStatus($status = 302)
    {
        $this->statusCode = $status;
        return $this;
    }

    /**
     * @param $xRedirectBy
     *
     * @return Flash
     */
    public function redirectBy($xRedirectBy = 'flash')
    {
        $this->xRedirectBy = $xRedirectBy;
        return $this;
    }

    public function redirect()
    {
        if (!isset($this->redirectUrl)) {
            $this->redirectBack();
            return;
        }
        self::$sessionHandler->setSkipCleanup(true);
        @header("X-Redirect-By: $this->xRedirectBy", true, $this->statusCode);
        @header("Location: $this->redirectUrl", true, $this->statusCode);

        exit();
    }

    /**
     * Redirects to previous url
     */
    public function redirectBack()
    {
        $this->redirectUrl = $_SERVER['HTTP_REFERER'];
        $this->redirect();
    }


    /**
     * @param $url string
     * @param int $delaysInSec
     * @return $this
     */
    public function redirectAfter($delaysInSec, $url)
    {
        @header("Refresh: {$delaysInSec}; URL={$url}", true, $this->statusCode);
        return $this;
    }

}