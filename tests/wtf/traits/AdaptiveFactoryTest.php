<?php

namespace Wtf\Traits;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-06-27 at 10:15:56.
 */
class AdaptiveFactoryTest extends \PHPUnit_Framework_TestCase {

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
	 * @covers Wtf\Traits\AdaptiveFactory::produce
	 */
	public function testAdaptiveFactoryEmpty() {
		$mock = $this->getMockForTrait('\\Wtf\\Traits\\AdaptiveFactory');
		$mock->expects($this->any())
			->method('guess')
			->will($this->returnValue('empty'));

		$this->assertEquals('empty', $mock::produce());
	}

	/**
	 * @covers Wtf\Traits\AdaptiveFactory::produce
	 */
	public function testAdaptiveFactoryOne() {
		$mock = $this->getMockForTrait(AdaptiveFactory::class);
		$mock->expects($this->any())
			->method('guess_string')
			->will($this->returnValue('the string'));

		$this->assertEquals('the string', $mock::produce('sssstringggg'));
	}

	/**
	 * @covers Wtf\Traits\AdaptiveFactory::produce
	 */
	public function testAdaptiveFactorySome() {
		$mock = $this->getMockForTrait(AdaptiveFactory::class);
		$mock->expects($this->any())
			->method('guess_string_int_bool')
			->will($this->returnValue('the string, int and bool'));

		$this->assertEquals('the string, int and bool', $mock::produce('sssstringggg', 5, true));
	}

	/**
	 * @covers Wtf\Traits\AdaptiveFactory::produce
	 */
	public function testAdaptiveFactoryReduced() {
		$mock = $this->getMockForTrait(AdaptiveFactoryMock::class);
		$mock->expects($this->any())
			->method('guess_string')
			->will($this->returnValue('the string only'));
		$mock->expects($this->any())
			->method('guess_string_int')
			->will($this->returnValue('the string and int'));
		$mock->expects($this->any())
			->method('guess_string_bool')
			->will($this->returnValue('the string and bool'));

		$this->assertEquals('the string and int', $mock::produce('sssstringggg', 5, true));
		$this->assertEquals('the string and bool', $mock::produce('sssstringggg', true, 999));
		$this->assertEquals('the string only', $mock::produce('sssstringggg', 'true', 999));
		$this->assertInstanceOf($mock, $mock::produce(123, 'sssstringggg', true));
	}

}