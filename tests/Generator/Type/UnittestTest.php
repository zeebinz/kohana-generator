<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Test case for Generator_Type_Unittest.
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
class Generator_Type_UnittestTest extends Unittest_TestCase 
{
	/**
	 * Tests that all type options are applied correctly.
	 */
	public function test_type_options()
	{
		$type = new Generator_Type_Unittest('Foo');

		$this->assertSame('FooTest', $type->name());
		$type->name('FooTest');
		$this->assertSame('FooTest', $type->name());

		$type->group('');
		$type->group(NULL);
		$params = $type->params();
		$this->assertTrue(empty($params['groups']));

		$type->group('tester');
		$type->group('tester.tests, tester.foo');
		$type->blank();

		$params = $type->params();
		$this->assertTrue($params['blank']);
		$this->assertSame('Foo', $params['class_name']);
		$this->assertContains('tester', $params['groups']);
		$this->assertContains('tester.tests', $params['groups']);
		$this->assertContains('tester.foo', $params['groups']);
		
		$type->render();
		$params = $type->params();
		$this->assertSame('Tests', $params['category']);
		$this->assertSame('Unittest_TestCase', $params['extends']);
	}

} // End Generator_Type_UnittestTest 
