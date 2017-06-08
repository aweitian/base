<?php
class BaseTest extends PHPUnit_Framework_TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testArr()
    {
    	$taw = [];
    	\Tian\Arr::set($taw, "/taw/xpath/v", 'ann','/');
    	//var_dump($taw);
    	$this->assertEquals("ann", \Tian\Arr::get($taw, "/taw/xpath/v",null,'/'));
    	$c = & \Tian\Arr::ref($taw, "/taw/xpath/v",'/');
    	$c = "balabala";
    	$this->assertEquals("balabala", \Tian\Arr::get($taw, "/taw/xpath/v",null,'/'));
    	$c = & \Tian\Arr::ref($taw, "/taw/xpath",'/');
    	$c['v'] = "gg";
    	$this->assertEquals("gg", \Tian\Arr::get($taw, "/taw/xpath/v",null,'/'));
    	\Tian\Arr::set($taw, "taw.xpath.vv", 'garri');
    	$this->assertEquals("garri", $c['vv']);
    }
 
}

