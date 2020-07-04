<?php


namespace Hexbit\Flash\Session;


/**
 * Class Script implements php script life cycle
 * @package Hexbit\Flash\Session
 */
abstract class Script
{

    /**
     * script starting point
     */
    public function init()
    {
        // will be called when the script reached to the endpoint.
        register_shutdown_function([$this, 'teardown']);
    }


    /**
     * script end point.
     */
    public abstract function teardown();

}