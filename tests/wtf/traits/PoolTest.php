<?php

namespace Wtf\Traits;

class PoolMock implements \Wtf\Interfaces\Pool {

	use Pool;
	
}

/**
 * @group Traits
 */
class PoolTest extends \PHPUnit_Framework_TestCase {

	protected $object;

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
	 * @covers Wtf\Traits\Pool::instance
	 */
	public function testInstance() {
		$first = PoolMock::instance('first');
		$this->assertInstanceOf('\\Wtf\\Traits\\PoolMock',$first);
		$this->assertSame($first, PoolMock::instance('first'));
		
		$second = PoolMock::instance('second');
		$this->assertInstanceOf('\\Wtf\\Traits\\PoolMock',$second);
		$this->assertSame($second, PoolMock::instance('second'));
		$this->assertNotSame($first, $second);
	}


}
