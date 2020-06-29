<?php


class CapabilityCheck
{
    protected $function;

    function __construct($function)
    {
        $this->function = $function;
    }

    function checkCapability(){return 1;}

    function jobOwnerShipCheck($jobid){return 1;}

}