<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Test case for Generator_Type_Model.
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
class Generator_Type_ModelTest extends Unittest_TestCase 
{
/**
	 * Tests that all type options are applied correctly.
	 */
	public function test_type_options()
	{
		$type = new Generator_Type_Model('Foo');

		$this->assertSame('Model_Foo', $type->name());
		$type->name('Model_Foo');
		$this->assertSame('Model_Foo', $type->name());
		$type->name('Bar_Model_Foo');
		$this->assertSame('Bar_Model_Foo', $type->name());

		$type->render();
		$params = $type->params();
		$this->assertSame('Model', $params['extends']);
		$this->assertSame('Models', $params['category']);
	}

} // End Generator_Type_ModelTest 
