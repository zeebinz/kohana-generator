<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Test case for Generator_Type_Class.
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
class Generator_Type_ClassTest extends Unittest_TestCase
{
	/**
	 * Tests that all type options are applied correctly for abstract classes.
	 */
	public function test_abstract_class_options()
	{
		$module = basename(dirname(dirname(dirname(dirname(__FILE__)))));
		$type = new Generator_Type_Class('Foo');

		$type->as_abstract()
			->implement('TestCountable')
			->implement('TestSortable')
			->module($module)
			->extend('TestClassParent')
			->blank(FALSE);

		$params = $type->params();
		$this->assertSame($module, $type->module());
		$this->assertTrue($params['abstract']);
		$this->assertSame('TestClassParent', $params['extends']);
		$this->assertContains('TestCountable', $params['implements']);
		$this->assertContains('TestSortable', $params['implements']);
		$this->assertArrayNotHasKey('methods', $params);

		$type->render();
		$params = $type->params();

		$this->assertSame('TestCountable, TestSortable', $params['implements']);
		$this->assertSame('TestClassParent', $params['extends']);

		// Abstract classes should not implement inherited abstract methods
		$this->assertCount(0, $params['methods']);
	}

	/**
	 * Tests that all type options are applied correctly for concrete classes.
	 *
	 * @depends test_abstract_class_options
	 */
	public function test_concrete_class_options()
	{
		$module = basename(dirname(dirname(dirname(dirname(__FILE__)))));
		$type = new Generator_Type_Class('Foo');

		$type->extend('TestClassParent')
			->implement('TestCountable')
			->implement('TestSortable')
			->module($module)
			->blank(FALSE);

		$params = $type->params();
		$this->assertSame($module, $type->module());
		$this->assertArrayNotHasKey('abstract', $params);
		$this->assertSame('TestClassParent', $params['extends']);
		$this->assertContains('TestCountable', $params['implements']);
		$this->assertContains('TestSortable', $params['implements']);
		$this->assertArrayNotHasKey('methods', $params);

		$type->render();
		$params = $type->params();

		$this->assertCount(1, $params['methods']);
		$this->assertCount(3, $params['methods']['public']);
		$this->assertArrayNotHasKey('abstract', $params['methods']);
		$this->assertArrayNotHasKey('public_method', $params['methods']['public']);

		// Concrete classes should implement a parent's abstract methods
		$this->assertArrayHasKey('abstract_method', $params['methods']['public']);
		$this->assertNotEmpty($params['methods']['public']['abstract_method']['doccomment']);
		$this->assertFalse($params['methods']['public']['abstract_method']['abstract']);
		$this->assertArrayHasKey('body', $params['methods']['public']['abstract_method']);
		$this->assertNotRegExp('/parent::/', $params['methods']['public']['abstract_method']['body']);

		// Concrete classes should implement all interface methods
		$this->assertSame('TestCountable', $params['methods']['public']['count']['class']);
		$this->assertNotEmpty($params['methods']['public']['count']['doccomment']);
		$this->assertFalse($params['methods']['public']['count']['abstract']);
		$this->assertArrayHasKey('body', $params['methods']['public']['count']);
		$this->assertNotRegExp('/parent::/', $params['methods']['public']['count']['body']);

		$this->assertArrayHasKey('sort', $params['methods']['public']);
		$this->assertSame('TestSortable', $params['methods']['public']['sort']['class']);
		$this->assertNotEmpty($params['methods']['public']['sort']['doccomment']);
		$this->assertFalse($params['methods']['public']['sort']['abstract']);
		$this->assertArrayHasKey('body', $params['methods']['public']['sort']);
		$this->assertNotRegExp('/parent::/', $params['methods']['public']['sort']['body']);
	}

} // End Generator_Type_ClassTest

interface TestCountable
{
	public function count();
}

interface TestSortable
{
	public function sort();
}

abstract class TestClassParent
{
	public function public_method() {}
	abstract public function abstract_method();
}
