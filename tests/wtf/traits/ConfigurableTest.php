<?php

namespace Wtf\Traits;

class ConfigurableMock {

	use Configurable;
}

class ConfigurableNamedMock {

	use Configurable;
}

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-06-27 at 10:15:56.
 */
class ConfigurableTest extends \PHPUnit_Framework_TestCase {

	protected $object;

    public static function setUpBeforeClass()
    {
		// init basic singleton
		var_dump(\Wtf\Core\Config::singleton(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'config'));
    }
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {
		
	}

	/**
	 * @covers Wtf\Traits\Configurable::configure
	 */
	public function testConfigureNamed() {
		$object = new ConfigurableNamedMock();
		var_dump($object);
		$php = $object::configure('php');
		
		$this->assertInstanceOf('\\Wtf\\Core\\Config',$php);
		$this->assertEquals('ONE',$php['string']);
	}

	/**
	 * @covers Wtf\Traits\Configurable::configure
	 */
	public function testConfigureUnnamed() {
		$object = new ConfigurableMock();
		$def = $object::configure();
		
		$this->assertInstanceOf('\\Wtf\\Core\\Config',$def);
		$this->assertEquals('IuriiP <hardwork.mouse@gmail.com>',$def['name']);
	}

	/**
	 * @covers Wtf\Traits\Configurable::config
	 * @depends testConfigureUnnamed
	 */
	public function testConfig() {
		$object = new ConfigurableMock();
		
		$this->assertEquals('json',$object->config('format'));
		$this->assertNull($object->config('nothing'));
	}
}