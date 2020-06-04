<?php


namespace Hexbit\Flash\Session;


interface SessionHandler
{
    function setMessage($key, $value);

    function getMessage($key);

    function removeMessage($key);
}