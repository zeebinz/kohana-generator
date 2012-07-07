<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Test case for Generator_Type_Clone.
 *
 * @group      generator
 * @group      generator.types
 *
 * @package    Generator
 * @category   Tests
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Generator_Type_CloneTest extends Unittest_TestCase
{
	/**
	 * Tests that all type options are applied correctly.
	 */
	public function test_type_options()
	{
		$type = new Generator_Type_Clone('Foo');

		$type->source('TestCloneClass')->type(Generator_Reflector::TYPE_CLASS);

		$params = $type->params();
		$this->assertArrayNotHasKey('implements', $params);
		$this->assertArrayNotHasKey('methods', $params);

		$type->render();
		$params = $type->params();

		$this->assertSame('Countable', $params['implements']);

		$this->assertCount(2, $params['constants']);
		$this->assertSame('// Declared in TestCloneClass',
			$params['constants']['CONSTANT_ONE']['comment']);
		$this->assertSame('const CONSTANT_ONE = \'foo\'',
			$params['constants']['CONSTANT_ONE']['declaration']);

		$this->assertCount(1, $params['properties']['static']);
		$this->assertCount(1, $params['properties']['public']);
		$this->assertCount(1, $params['properties']['other']);

		$prop = $params['properties']['static']['prop_one'];
		$this->assertSame('TestCloneClass', $prop['class']);
		$this->assertSame('string', $prop['type']);
		$this->assertRegExp('/Declared in TestCloneClass/', $prop['doccomment']);
		$this->assertSame('public static $prop_one = \'bar\'', $prop['declaration']);

		$prop = $params['properties']['public']['prop_two'];
		$this->assertSame('TestCloneClass', $prop['class']);
		$this->assertSame('mixed', $prop['type']);
		$this->assertRegExp('/A public property/', $prop['doccomment']);
		$this->assertSame('public $prop_two', $prop['declaration']);

		$this->assertCount(2, $params['methods']['static']);
		$this->assertCount(2, $params['methods']['public']);
		$this->assertCount(2, $params['methods']['abstract']);
		$this->assertCount(1, $params['methods']['other']);

		$this->assertSame('TestCloneClass',
			$params['methods']['static']['method_one']['class']);
		$this->assertSame('Countable',
			$params['methods']['public']['count']['class']);
		$this->assertRegExp('/Implementation of TestCloneClass::method_four/',
			$params['methods']['abstract']['method_four']['doccomment']);
		$this->assertRegExp('/A protected method/',
			$params['methods']['other']['_method_six']['doccomment']);
	}

} // End Generator_Type_CloneTest

abstract class TestCloneClass implements Countable
{
	const CONSTANT_ONE = 'foo';
	const CONSTANT_TWO = 2;

	public static $prop_one = 'bar';
	public static function method_one() {}
	protected static function _method_two() {}

	/**
	 * A public property
	 * @var  string
	 */
	public $prop_two;

	public function count()	{}
	public function method_three() {}

	abstract public function method_four();
	abstract public function method_five();

	protected $_prop_three;

	/**
	 * A protected method
	 */
	protected function _method_six($foo = 'foo') {}
}
