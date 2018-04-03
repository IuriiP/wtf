<?php

namespace Wtf\Core;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2017-03-02 at 15:12:52.
 */
class InputTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var Input
	 */
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
	 * @covers Wtf\Core\Input::__construct
	 */
	public function test__constructGet() {
		$object = new Input('get', [
			'foo' => 42,
			'bar' => 'lorem ipsum',
		]);
		$this->assertInstanceOf(Input::class, $object);
		$this->assertAttributeEquals([
			'foo' => 42,
			'bar' => 'lorem ipsum',
			], '_data', $object);
	}

	/**
	 * @covers Wtf\Core\Input::__construct
	 */
	public function test__constructPost() {
		$object = new Input('post', [
			'foo' => 42,
			'bar' => 'lorem ipsum',
			], [
			'baz' => [
				'tmp_name' => 'temp.file',
				'name' => 'original.name',
				'type' => 'text/plain',
				'size' => 42,
				'error' => 0,
			]
		]);
		$this->assertInstanceOf(Input::class, $object);
		$this->assertAttributeEquals([
			'foo' => 42,
			'bar' => 'lorem ipsum',
			'baz' => new InputFile([
				'tmp_name' => 'temp.file',
				'name' => 'original.name',
				'type' => 'text/plain',
				'size' => 42,
				'error' => 0,
				])
			], '_data', $object);
	}

	/**
	 * @covers Wtf\Core\Input::__construct
	 */
	public function test__constructMultipart() {
		$_SERVER['CONTENT_TYPE'] = 'multipart/form-data; boundary=tearline';
		$stream = <<<'EOT'
--tearline
Content-Disposition: form-data; name="foo"

text default
--tearline
Content-Disposition: form-data; name="bar"


text with CRLF

--tearline
Content-Disposition: form-data; name="baz"

42
--tearline
Content-Disposition: form-data; name="file[]"; filename="a.txt"
Content-Type: text/plain

Content of a.txt.

--tearline
Content-Disposition: form-data; name="file[]"; filename="a.html"
Content-Type: text/html

<!DOCTYPE html><title>Content of a.html.</title>

--tearline--
EOT;
		$object = new Input('put', $stream);
		$this->assertInstanceOf(Input::class, $object);

		return $object;
		$this->assertAttributeArraySubset([
			'foo' => 'text default',
			'bar' => '
text with CRLF
',
			'baz' => '42',
			], '_data', $object);
	}

	/**
	 * @depends test__constructMultipart
	 * @covers Wtf\Core\Input::offsetExists
	 */
	public function testOffsetExists($object) {
		$this->assertTrue(isset($object['foo']));
		$this->assertTrue(isset($object['bar']));
		$this->assertTrue(isset($object['baz']));
		$this->assertTrue(isset($object['file']));
		$this->assertFalse(isset($object['unknown']));
	}

	/**
	 * @depends test__constructMultipart
	 * @covers Wtf\Core\Input::offsetGet
	 */
	public function testOffsetGet($object) {
		$this->assertEquals('text default', $object['foo']);
		$this->assertEquals('
text with CRLF
', $object['bar']);
		$this->assertEquals('42', $object['baz']);
		$this->assertNull($object['unknown']);

		$files = $object['file'];
		$this->assertInternalType('array', $files);
		$this->assertContainsOnlyInstancesOf(InputFile::class, $files);
		$file = $files[0];
		$this->assertEquals(0, $file->error);
		$this->assertEquals('a.txt', $file->name);
		$this->assertEquals('text/plain', $file->type);
		$this->assertEquals(18, $file->size);
		$this->assertTrue(is_file($file->tmp_name));
		$this->assertEquals('Content of a.txt.
', (string) $file);
	}

	/**
	 * @depends test__constructMultipart
	 * @covers Wtf\Core\Input::__invoke
	 */
	public function test__invoke($object) {
		$this->assertEquals([
			'foo' => 'text default',
			'baz' => '42'
			], $object('foo', 'baz', 'unknown'));
		$this->assertEquals([
			'foo' => 'text default',
			'baz' => '42'
			], $object(['foo', 'baz', 'unknown']));
		$this->assertEquals([
			'foo' => 'text default',
			'baz' => '42'
			], $object('foo', ['baz', 'unknown']));
	}

	/**
	 * @depends test__constructMultipart
	 * @covers Wtf\Core\Input::rewind
	 * @covers Wtf\Core\Input::current
	 * @covers Wtf\Core\Input::key
	 * @covers Wtf\Core\Input::next
	 * @covers Wtf\Core\Input::valid
	 */
	public function testIterator($object) {
		foreach($object as $key => $value) {
			$this->assertInternalType('string', $key);
			if('file' === $key) {
				$this->assertInternalType('array', $value);
			} else {
				$this->assertInternalType('string', $value);
			}
		}
	}

	/**
	 * @depends test__constructMultipart
	 * @covers Wtf\Core\Input::offsetSet
	 * @expectedException ErrorException
	 * @expectedExceptionMessage readonly
	 */
	public function testOffsetSet($object) {
		$object['file'] = 'some';
	}

	/**
	 * @depends test__constructMultipart
	 * @covers Wtf\Core\Input::offsetUnset
	 * @expectedException ErrorException
	 * @expectedExceptionMessage readonly
	 */
	public function testOffsetUnset($object) {
		unset($object['foo']);
	}

	/**
	 * @depends test__constructMultipart
	 * @covers Wtf\Core\Input::isFile
	 */
	public function testIsFile($object) {
		$this->assertTrue(Input::isFile($object['file/0']));
	}
}