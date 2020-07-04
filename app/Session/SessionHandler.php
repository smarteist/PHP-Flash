<?php


namespace Hexbit\Flash\Session;


interface SessionHandler
{
    function getFlashKeys();

    function sessionCleanup();

    function setSessionData($key, $value);

    function getSessionData($key);

    function removeSessionData($key);
}