<?php

namespace Wtf\Traits;

class ContainerMock implements \Wtf\Interfaces\Container {

	use Container;
}

class ContainerSingletonMock implements \Wtf\Interfaces\Container, \Wtf\Interfaces\Singleton {

	use Container,
	 Singleton;
}

/**
 * @group Traits
 */
class ContainerTest extends \PHPUnit_Framework_TestCase {

	protected $object;

	protected $fixture = [
		'one' => 'string',
		'two' => 'other string',
		'complex' => [
			'sub0' => 'sub zero',
			'sub1' => 'sub one',
		],
		'toocomplex' => [
			'sub0' => [
				'sub0' => 'sub zero',
				'sub1' => 'sub one',
			],
			'sub1' => [
				'sub0' => 'sub zero',
				'sub1' => 'sub one',
			],
		],
	];

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = new ContainerMock;
		$this->object->set($this->fixture);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {
		
	}

	/**
	 * @covers Wtf\Traits\Container::set
	 */
	public function testSet() {
		$this->assertEmpty($this->object->set([]));
		$this->assertEquals($this->fixture, $this->object->set($this->fixture));
	}

	/**
	 * @covers Wtf\Traits\Container::getIterator
	 */
	public function testGetIterator() {
		$iterator = $this->object->getIterator();
		$this->assertInstanceOf('\\Traversable',$iterator);
		$this->assertEquals($this->fixture, (array)$iterator);
		foreach($this->object as $key => $value) {
			$this->assertEquals($this->fixture[$key], $value);
		}
	}

	/**
	 * @covers Wtf\Traits\Container::get
	 */
	public function testGet() {
		$array = $this->fixture;

		$this->assertEquals($array['one'], $this->object->get('oNe'));
		$this->assertNull($this->object->get('three'));
		$this->assertEquals('nothing', $this->object->get('three', 'nothing'));
		$this->assertEquals($array['complex'], $this->object->get('CompleX'));
		$this->assertEquals($array['complex']['sub0'], $this->object->get('Complex/Sub0'));
		$this->assertEquals($array['toocomplex']['sub0']['sub1'], $this->object->get('TooComplex/Sub0/Sub1'));
	}

	/**
	 * @covers Wtf\Traits\Container::eliminate
	 */
	public function testEliminate() {
		$array = $this->fixture;

		$this->assertEquals($array['one'], $this->object->eliminate('oNe'));
		$this->assertEquals($array['complex'], $this->object->eliminate('CompleX'));
		$this->assertEquals($array['toocomplex']['sub0']['sub1'], $this->object->eliminate('TooComplex/Sub0/Sub1'));

		unset($array['one']);
		unset($array['complex']);
		unset($array['toocomplex']['sub0']['sub1']);

		$this->assertEquals($array, (array)$this->object->getIterator());
		$this->assertEquals('nothing', $this->object->eliminate('OnE', 'nothing'));
	}

	/**
	 * @covers Wtf\Traits\Container::__get
	 */
	public function test__get() {
		$array = $this->fixture;

		$this->assertEquals($array['two'], $this->object->Two);
		$this->assertNull($this->object->nothing);
		$this->assertEquals($array['toocomplex'], $this->object->TooComplex);
	}

	/**
	 * @covers Wtf\Traits\Container::__set
	 */
	public function test__set() {
		$this->object->One = 'ONE';
		$this->assertEquals('ONE', $this->object->one);

		$array = [
			'first' => 1,
			'second' => 2,
		];
		$this->object->Complex = $array;
		$this->assertEquals($array, $this->object->compleX);
	}

	/**
	 * @covers Wtf\Traits\Container::__call
	 */
	public function test__call() {
		$array = $this->fixture;

		$this->assertEquals($array['two'], $this->object->Two());
		$this->assertNull($this->object->nothing());
		$this->assertNull($this->object->complex('four'));
		$this->assertEquals($array['complex']['sub0'], $this->object->Complex('Sub0'));
		$this->assertEquals($array['toocomplex']['sub0']['sub0'], $this->object->TooComplex('Sub0/Sub0'));
		$this->assertEquals($array['toocomplex']['sub1']['sub1'], $this->object->TooComplex('Sub1', 'Sub1'));
	}

	/**
	 * @covers Wtf\Traits\Container::__callStatic
	 */
	public function test__callStatic() {
		$obj = ContainerSingletonMock::singleton();
		$obj->set($this->fixture);
		$array = $this->fixture;

		$this->assertEquals($array['two'], ContainerSingletonMock::Two());
		$this->assertNull(ContainerSingletonMock::nothing());
		$this->assertNull(ContainerSingletonMock::three('four'));
		$this->assertEquals($array['complex']['sub0'], ContainerSingletonMock::Complex('Sub0'));
		$this->assertEquals($array['toocomplex']['sub0']['sub0'], ContainerSingletonMock::TooComplex('Sub0', 'Sub0'));
		$this->assertEquals($array['toocomplex']['sub1']['sub1'], ContainerSingletonMock::TooComplex('Sub1/Sub1'));
	}

	/**
	 * @covers Wtf\Traits\Container::__invoke
	 */
	public function test__invoke() {
		$array = $this->fixture;
		$obj = $this->object;

		$this->assertEquals($array['two'], $obj('Two'));
		$this->assertNull($obj('nothing'));
		$this->assertNull($obj('three', 'four'));
		$this->assertEquals($array['complex']['sub0'], $obj('Complex', 'Sub0'));
		$this->assertEquals($array['toocomplex']['sub0']['sub0'], $obj('TooComplex', 'Sub0', 'Sub0'));
		$this->assertEquals($array['toocomplex']['sub1']['sub1'], $obj('TooComplex', 'Sub1/Sub1'));
	}

	/**
	 * @covers Wtf\Traits\Container::offsetExists
	 * @depends test__invoke
	 */
	public function testOffsetExists() {
		$this->assertTrue($this->object->offsetExists('onE'));
		$this->assertTrue($this->object->offsetExists('comPleX/sub0'));
		$this->assertTrue($this->object->offsetExists('tOOcomPleX/sub0/sub1'));
		$this->assertFalse($this->object->offsetExists('nothing'));
		$this->assertFalse($this->object->offsetExists('nothing/else'));
		$this->assertFalse($this->object->offsetExists('toocomplex/sub2'));
		$this->assertFalse($this->object->offsetExists('toocomplex/sub0/sub3'));
	}

	/**
	 * @covers Wtf\Traits\Container::offsetGet
	 */
	public function testOffsetGet() {
		$array = $this->fixture;

		$this->assertEquals($array['one'], $this->object->offsetGet('oNe'));
		$this->assertNull($this->object->offsetGet('three'));
		$this->assertEquals($array['complex'], $this->object->offsetGet('CompleX'));
		$this->assertEquals($array['complex']['sub0'], $this->object->offsetGet('Complex/suB0'));
		$this->assertEquals($array['toocomplex']['sub1']['sub1'], $this->object->offsetGet('TooComplex/Sub1/Sub1'));
	}

	/**
	 * @covers Wtf\Traits\Container::offsetSet
	 */
	public function testOffsetSet() {
		$this->assertEquals('oNE', $this->object->offsetSet('oNe', 'oNE'));
		$this->assertEquals('nothing', $this->object->offsetSet('thRee', 'nothing'));

		$complex = [
			'sub0' => 'sub zero NEW',
			'sub1' => 'sub one NEW',
		];

		$this->assertEquals($complex, $this->object->offsetSet('CompleX', $complex));
	}

	/**
	 * @covers Wtf\Traits\Container::offsetUnset
	 */
	public function testOffsetUnset() {
		$this->object->offsetUnset('two');
		$this->assertNull($this->object->two);

		$complex = $this->object->complex;
		$this->object->offsetUnset('complex/Sub1');
		unset($complex['sub1']);
		$this->assertEquals($complex, $this->object->complex);

		$toocomplex = $this->object->toocomplex;
		$this->object->offsetUnset('TOOcomplex/Sub1/Some0');
		unset($toocomplex['sub1']['some0']);
		$this->assertEquals($toocomplex, $this->object->toocomplex);
	}

}
