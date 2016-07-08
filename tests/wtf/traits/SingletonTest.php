<?php

namespace Wtf\Traits;

class SingletonMock implements \Wtf\Interfaces\Singleton{

	use Singleton;
}

class SingletonMockSimple implements \Wtf\Interfaces\Singleton {

	use Singleton;
}

class SingletonMockComplex implements \Wtf\Interfaces\Singleton {

	use Singleton;

	private function __construct($param0, $param1) {
		$this->foo = $param0;
		$this->bar = $param1;
	}

}

/**
 * @group Traits
 */
class SingletonTest extends \PHPUnit_Framework_TestCase {

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
	 * @covers Wtf\Traits\Singleton::__construct
	 */
	public function testSingletonConstruct() {
		$mock = SingletonMock::singleton();

		$this->assertInstanceOf(SingletonMock::class, $mock);

		$method = new \ReflectionMethod($mock, '__construct');
		$this->assertTrue($method->isConstructor());
		$this->assertTrue($method->isPrivate());
	}

	/**
	 * @covers Wtf\Traits\Singleton::__clone
	 * @expectedException \ErrorException
	 * @expectedExceptionMessage not allowed
	 */
	public function testSingletonClone() {
		$mock = SingletonMock::singleton();

		$method = new \ReflectionMethod($mock, '__clone');
		$this->assertTrue($method->isPrivate());
		$method->setAccessible(true);
		$method->invoke($mock);
	}

	/**
	 * @covers Wtf\Traits\Singleton::__wakeup
	 * @expectedException \ErrorException
	 * @expectedExceptionMessage not allowed
	 */
	public function testSingletonWakeup() {
		$mock = SingletonMock::singleton();

		$method = new \ReflectionMethod($mock, '__wakeup');
		$this->assertTrue($method->isPrivate());
		$method->setAccessible(true);
		$method->invoke($mock);
	}

	/**
	 * @covers Wtf\Traits\Singleton::singleton
	 */
	public function testSingletonSimple() {
		$mock = SingletonMockSimple::singleton();

		$this->assertInstanceOf(SingletonMockSimple::class, $mock);
		$this->assertSame($mock, SingletonMockSimple::singleton());
	}

	/**
	 * @covers Wtf\Traits\Singleton::singleton
	 * @todo   Implement testSingleton().
	 */
	public function testSingletonComplex() {
		$mock = SingletonMockComplex::singleton('foo', 'bar');

		$this->assertInstanceOf(SingletonMockComplex::class, $mock);
		$this->assertEquals('foo', $mock->foo);
		$this->assertEquals('bar', $mock->bar);
		$this->assertSame($mock, SingletonMockComplex::singleton());
	}

}
