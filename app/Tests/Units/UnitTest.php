<?php
namespace tests\Units;

use PHPUnit\Framework\TestCase;
use Core\Config;

abstract class UnitTest extends TestCase
{
    public function __construct()
    {
        define ('ROOT', realpath(dirname(__FILE__)) . "/../../../");
        new Config();    
    }
}
