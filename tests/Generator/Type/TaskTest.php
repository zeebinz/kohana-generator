<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Test case for Generator_Type_Task.
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
class Generator_Type_TaskTest extends Unittest_TestCase 
{
	/**
	 * Tests that all type options are applied correctly.
	 */
	public function test_type_options()
	{
		$type = new Generator_Type_Task('Foo');
		$type->no_help();

		$this->assertSame('Task_Foo', $type->name());
		$type->name('Task_Foo');
		$this->assertSame('Task_Foo', $type->name());
		$type->name('Bar_Task_Foo');
		$this->assertSame('Bar_Task_Foo', $type->name());

		$type->render();
		$params = $type->params();
		$this->assertFalse($params['help']);
		$this->assertSame('Minion_Task', $params['extends']);
		$this->assertSame('Tasks', $params['category']);
	}

} // End Generator_Type_TaskTest 
