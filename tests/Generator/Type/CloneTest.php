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
	public function test_class_inheritance()
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
	 * Tests that all type options are applied correctly when cloning classes.
	 *
	 * @depends test_class_inheritance
	 */
	public function test_cloning_classes()
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

		$this->assertSame('TestCloneClassFour', $params['extends']);
		$this->assertSame('TestCloneInterfaceCountable', $params['implements']);

		// Constants
		$this->assertCount(4, $params['constants']);

		$this->assertSame('// Declared in TestCloneClassThree',
			$params['constants']['CONSTANT_ONE']['comment']);
		$this->assertSame('const CONSTANT_ONE = \'foo\'',
			$params['constants']['CONSTANT_ONE']['declaration']);

		$this->assertSame('// Declared in TestCloneClassThree',
			$params['constants']['CONSTANT_THREE']['comment']);
		$this->assertSame('const CONSTANT_THREE = \'three\'',
			$params['constants']['CONSTANT_THREE']['declaration']);

		$this->assertSame('// Declared in TestCloneClassFour',
			$params['constants']['CONSTANT_FOUR']['comment']);
		$this->assertSame('const CONSTANT_FOUR = 4',
			$params['constants']['CONSTANT_FOUR']['declaration']);

		// Implemented interface constants can't be re-declared, so shouldn't be included
		$this->assertArrayNotHasKey('CONSTANT_COUNTABLE', $params['constants']);

		// Properties
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

		// Methods
		$this->assertCount(2, $params['methods']['static']);
		$this->assertCount(2, $params['methods']['public']);
		$this->assertCount(2, $params['methods']['abstract']);
		$this->assertCount(1, $params['methods']['other']);

		$this->assertSame('TestCloneClassThree',
			$params['methods']['static']['method_one']['class']);
		$this->assertSame('TestCloneInterfaceCountable',
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

	/**
	 * When classes implement interfaces that extend other interfaces, all of the
	 * interface methods should be inherited, but the class declaration should
	 * only use the interfaces that aren't inherited by another.
	 *
	 * @depends test_cloning_classes
	 */
	public function test_cloning_classes_with_inherited_interfaces()
	{
		$type = new Generator_Type_Clone('Foo');
		$type->source('TestCloneClassFive')
			->type(Generator_Reflector::TYPE_CLASS);

		$type->render();
		$params = $type->params();

		$this->assertSame('TestCloneInterface', $params['implements']);
		$this->assertCount(4, $params['methods']['public']);
		$this->assertArrayHasKey('method_one', $params['methods']['public']);
		$this->assertArrayHasKey('method_two', $params['methods']['public']);
		$this->assertArrayHasKey('method_three', $params['methods']['public']);
		$this->assertArrayHasKey('count', $params['methods']['public']);

		$type = new Generator_Type_Clone('Foo');
		$type->source('TestCloneClassSix')
			->type(Generator_Reflector::TYPE_CLASS);

		$type->render();
		$params = $type->params();

		$this->assertSame('TestCloneInterface, TestCloneInterfaceSortable',
			$params['implements']);
		$this->assertCount(6, $params['methods']['public']);
		$this->assertArrayHasKey('method_one', $params['methods']['public']);
		$this->assertArrayHasKey('method_two', $params['methods']['public']);
		$this->assertArrayHasKey('method_three', $params['methods']['public']);
		$this->assertArrayHasKey('sort', $params['methods']['public']);
		$this->assertArrayHasKey('iter', $params['methods']['public']);
		$this->assertArrayHasKey('count', $params['methods']['public']);
	}

	/**
	 * Interface methods should be treated as abstract without the modifier, and
	 * should use multiple inheritance for extending other interfaces instead
	 * of via the 'implements' keyword.
	 *
	 * @depends test_cloning_classes_with_inherited_interfaces
	 */
	public function test_cloning_interfaces()
	{
		$type = new Generator_Type_Clone('Foo');
		$type->source('TestCloneInterface')
			->type(Generator_Reflector::TYPE_INTERFACE)
			->inherit(TRUE)
			->render();

		$params = $type->params();
		$this->assertArrayNotHasKey('implements', $params);
		$this->assertSame('TestCloneInterfaceTwo, TestCloneInterfaceCountable',
			$params['extends']);

		$this->assertCount(1, $params['constants']);

		// Inherited interface constants can't be re-declared, so shouldn't be included
		$this->assertArrayNotHasKey('CONSTANT_COUNTABLE', $params['constants']);

		$this->assertCount(4, $params['methods']['public']);
		$this->assertTrue($params['methods']['public']['method_one']['abstract']);
		$this->assertTrue($params['methods']['public']['method_two']['abstract']);
		$this->assertTrue($params['methods']['public']['method_three']['abstract']);
		$this->assertTrue($params['methods']['public']['count']['abstract']);
		$this->assertArrAyNotHasKey('body', $params['methods']['public']['method_one']);
		$this->assertArrAyNotHasKey('body', $params['methods']['public']['method_two']);
		$this->assertArrAyNotHasKey('body', $params['methods']['public']['method_three']);
		$this->assertArrAyNotHasKey('body', $params['methods']['public']['count']);
	}

} // End Generator_Type_CloneTest

// Test classes

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

abstract class TestCloneClassThree extends TestCloneClassFour implements TestCloneInterfaceCountable
{
	const CONSTANT_ONE = 'foo';
	const CONSTANT_TWO = 2;
	const CONSTANT_THREE = 'three';

	public static $prop_one = 'bar';
	public static function method_one() {}
	protected static function _method_two() {}

	/**
	 * A public property
	 * @var  string
	 */
	public $prop_two;

	public function count() {}

	public function method_three() {}

	abstract public function method_four();
	abstract public function method_five();

	protected $_prop_three;

	/**
	 * A protected method
	 */
	protected function _method_six($foo = 'foo') {}
}

class TestCloneClassFour
{
	const CONSTANT_THREE = 3;
	const CONSTANT_FOUR = 4;
}

// Test interfaces

interface TestCloneInterface extends TestCloneInterfaceTwo, TestCloneInterfaceCountable
{
	const TEST_CLONE = 1;

	public function method_one();
	public function method_two($foo = 1);
}

interface TestCloneInterfaceTwo
{
	public function method_three();
}

interface TestCloneInterfaceCountable
{
	const CONSTANT_COUNTABLE = 1;

	public function count();
}

interface TestCloneInterfaceIterable extends TestCloneInterfaceCountable
{
	const CONSTANT_ITERABLE = 2;

	public function iter();
}

interface TestCloneInterfaceSortable extends TestCloneInterfaceIterable
{
	public function sort();
}

abstract class TestCloneClassFive implements TestCloneInterface {}

abstract class TestCloneClassSix implements TestCloneInterface, TestCloneInterfaceSortable {}
