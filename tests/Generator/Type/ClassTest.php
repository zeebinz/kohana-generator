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
	 * Tests that all type options are applied correctly.
	 */
	public function test_type_options()
	{
		$module = basename(dirname(dirname(dirname(dirname(__FILE__)))));
		$type = new Generator_Type_Class('Foo');

		$type->as_abstract()
			->implement('Countable')
			->implement('ArrayAccess')
			->implement('TestCountable')
			->module($module)
			->extend('Bar');

		$params = $type->params();
		$this->assertSame($module, $type->module());
		$this->assertTrue($params['abstract']);
		$this->assertSame('Bar', $params['extends']);
		$this->assertContains('Countable', $params['implements']);
		$this->assertContains('ArrayAccess', $params['implements']);
		$this->assertContains('TestCountable', $params['implements']);
		$this->assertArrayNotHasKey('methods', $params);

		$type->render();
		$params = $type->params();
		$this->assertSame('Countable, ArrayAccess, TestCountable', $params['implements']);
		$this->assertCount(1, $params['methods']);
		$this->assertCount(5, $params['methods']['public']);
		$this->assertSame('TestCountable', $params['methods']['public']['count']['class']);
		$this->assertNotEmpty($params['methods']['public']['count']['doccomment']);
		$this->assertFalse($params['methods']['public']['count']['abstract']);
		$this->assertArrayHasKey('body', $params['methods']['public']['count']);
	}

} // End Generator_Type_ClassTest 

interface TestCountable
{
	public function count();
}
