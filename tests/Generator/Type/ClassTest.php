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

	/**
	 * All the tests involving traits are included here for convenience due to the
	 * PHP version requirement.
	 *
	 * @group  generator.traits
	 */
	public function test_can_use_traits()
	{
		if ( ! function_exists('trait_exists'))
		{
			$this->markTestSkipped('PHP >= 5.4.0 is required');
		}

		// We'll use the fixtures dummies for these tests
		require_once dirname(dirname(dirname(__FILE__))).'/fixtures/_test_traits.php';

		$type = new Generator_Type_Class('Foo');
		$type->using('Bar, Baz');

		$params = $type->params();
		$this->assertArrayHasKey('traits', $params);
		$this->assertCount(2, $params['traits']);
		$this->assertContains('Bar', $params['traits']);
		$this->assertContains('Baz', $params['traits']);

		$rendered = $type->render();
		$this->assertRegExp('/use Bar;/', $rendered);
		$this->assertRegExp('/use Baz;/', $rendered);

		// Concrete classes should implement any abstract methods in inherited traits
		$type = new Generator_Type_Class('Foo');
		$type->using('Fx_Trait_Selector')->render();

		$params = $type->params();
		$this->assertArrayHasKey('public', $params['methods']);
		$this->assertArrayHasKey('select', $params['methods']['public']);
		$this->assertSame('Fx_Trait_Selector', $params['methods']['public']['select']['class']);
		$this->assertSame('Fx_Trait_Selector', $params['methods']['public']['select']['trait']);
		$this->assertSame('public', $params['methods']['public']['select']['modifiers']);
		$this->assertRegExp('/Implementation of Fx_Trait_Selector::select/',
			$params['methods']['public']['select']['doccomment']);
		$this->assertNotRegExp('/parent::/',
			$params['methods']['public']['select']['body']);


		// The same goes if abstract parents include abstract methods from traits
		$type = new Generator_Type_Class('Foo');
		$type->extend('Fx_AbstractClassWithTraits')->render();

		$params = $type->params();
		$this->assertArrayHasKey('public', $params['methods']);
		$this->assertArrayHasKey('select', $params['methods']['public']);
		$this->assertSame('Fx_AbstractClassWithTraits', $params['methods']['public']['select']['class']);
		$this->assertSame('Fx_Trait_Selector', $params['methods']['public']['select']['trait']);
		$this->assertSame('public', $params['methods']['public']['select']['modifiers']);
		$this->assertRegExp('/Implementation of Fx_AbstractClassWithTraits::select/',
			$params['methods']['public']['select']['doccomment']);
		$this->assertRegExp('/First defined in trait: Fx_Trait_Selector/',
			$params['methods']['public']['select']['doccomment']);
		$this->assertNotRegExp('/parent::/',
			$params['methods']['public']['select']['body']);
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
