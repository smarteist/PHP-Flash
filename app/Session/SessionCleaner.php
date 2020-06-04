<?php


namespace Hexbit\Flash\Session;


interface SessionCleaner
{
    function init();

    function getFlashKeys();

    function sessionCleanup();
}