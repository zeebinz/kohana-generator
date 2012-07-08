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
	 * Inherited methods and properties may optionally be included in any
	 * cloned classes.
	 */
	public function test_inheritance()
	{
		$type = new Generator_Type_Clone('Foo');
		$type->source('TestCloneClassOne')
			->type(Generator_Reflector::TYPE_CLASS)
			->inherit(FALSE);

		$type->render();
		$params = $type->params();

		$this->assertCount(2, $params['properties']['public']);
		$this->assertArrayNotHasKey('public', $params['methods']);
		$this->assertCount(1, $params['methods']['other']);
		$this->assertArrayNotHasKey('_inherited_method', $params['methods']['other']);

		$this->assertSame('TestCloneClassOne',
			$params['methods']['other']['_overridden_method']['class']);

		$type = new Generator_Type_Clone('Foo');
		$type->source('TestCloneClassOne')
			->type(Generator_Reflector::TYPE_CLASS)
			->inherit(TRUE);

		$type->render();
		$params = $type->params();

		$this->assertCount(3, $params['properties']['public']);
		$this->assertCount(1, $params['methods']['public']);
		$this->assertCount(2, $params['methods']['other']);
		$this->assertArrayHasKey('_inherited_method', $params['methods']['other']);

		// Final and private methods shouldn't be inherited
		$this->assertArrayNotHasKey('final_method', $params['methods']['public']);
		$this->assertArrayNotHasKey('_private_method', $params['methods']['other']);

		$this->assertSame('TestCloneClassOne',
			$params['methods']['other']['_overridden_method']['class']);

		// Inherited methods should invoke their parent
		$this->assertRegExp('/Some inherited method/',
			$params['methods']['other']['_inherited_method']['doccomment']);
		$this->assertRegExp('/Defined in TestCloneClassTwo/',
			$params['methods']['other']['_inherited_method']['body']);
		$this->assertRegExp('/parent::_inherited_method\(\$foo\);/',
			$params['methods']['other']['_inherited_method']['body']);
	}

	/**
	 * Tests that all type options are applied correctly.
	 *
	 * @depends test_inheritance
	 */
	public function test_type_options()
	{
		$type = new Generator_Type_Clone('Foo');
		$type->source('TestCloneClassThree')
			->type(Generator_Reflector::TYPE_CLASS)
			->inherit(TRUE);

		$params = $type->params();
		$this->assertArrayNotHasKey('implements', $params);
		$this->assertArrayNotHasKey('methods', $params);

		$type->render();
		$params = $type->params();

		$this->assertSame('Countable', $params['implements']);

		$this->assertCount(2, $params['constants']);
		$this->assertSame('// Declared in TestCloneClassThree',
			$params['constants']['CONSTANT_ONE']['comment']);
		$this->assertSame('const CONSTANT_ONE = \'foo\'',
			$params['constants']['CONSTANT_ONE']['declaration']);

		$this->assertCount(1, $params['properties']['static']);
		$this->assertCount(1, $params['properties']['public']);
		$this->assertCount(1, $params['properties']['other']);

		$prop = $params['properties']['static']['prop_one'];
		$this->assertSame('TestCloneClassThree', $prop['class']);
		$this->assertSame('string', $prop['type']);
		$this->assertRegExp('/Declared in TestCloneClassThree/', $prop['doccomment']);
		$this->assertSame('public static $prop_one = \'bar\'', $prop['declaration']);

		$prop = $params['properties']['public']['prop_two'];
		$this->assertSame('TestCloneClassThree', $prop['class']);
		$this->assertSame('mixed', $prop['type']);
		$this->assertRegExp('/A public property/', $prop['doccomment']);
		$this->assertSame('public $prop_two', $prop['declaration']);

		$this->assertCount(2, $params['methods']['static']);
		$this->assertCount(2, $params['methods']['public']);
		$this->assertCount(2, $params['methods']['abstract']);
		$this->assertCount(1, $params['methods']['other']);

		$this->assertSame('TestCloneClassThree',
			$params['methods']['static']['method_one']['class']);
		$this->assertSame('Countable',
			$params['methods']['public']['count']['class']);

		$this->assertRegExp('/Implementation of TestCloneClassThree::method_three/',
			$params['methods']['public']['method_three']['doccomment']);
		$this->assertRegExp('/Method implementation/',
			$params['methods']['public']['method_three']['body']);

		$this->assertRegExp('/Declaration of TestCloneClassThree::method_four/',
			$params['methods']['abstract']['method_four']['doccomment']);
		$this->assertArrayNotHasKey('body', $params['methods']['abstract']['method_four']);

		$this->assertRegExp('/A protected method/',
			$params['methods']['other']['_method_six']['doccomment']);
		$this->assertRegExp('/Implementation of TestCloneClassThree::_method_six/',
			$params['methods']['other']['_method_six']['body']);
	}

} // End Generator_Type_CloneTest

class TestCloneClassOne extends TestCloneClassTwo
{
	public $foo;
	public $bar;

	protected function _overridden_method() {}
}

class TestCloneClassTwo
{
	public $foo;
	public $moo;

	public function __construct() {}

	final public function final_method() {}
	private function _private_method() {}

	protected function _overridden_method() {}

	/**
	 * Some inherited method
	 */
	protected function _inherited_method($foo = 1) {}
}

abstract class TestCloneClassThree implements Countable
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
