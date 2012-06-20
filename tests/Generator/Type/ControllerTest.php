<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Test case for Generator_Type_Controller.
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
class Generator_Type_ControllerTest extends Unittest_TestCase 
{
	/**
	 * Tests that all type options are applied correctly.
	 */
	public function test_type_options()
	{
		$type = new Generator_Type_Controller('Foo');

		$this->assertSame('Controller_Foo', $type->name());
		$type->name('Controller_Foo');
		$this->assertSame('Controller_Foo', $type->name());
		$type->name('Bar_Controller_Foo');
		$this->assertSame('Bar_Controller_Foo', $type->name());

		$type->action('');
		$type->action(NULL);
		$params = $type->params();
		$this->assertTrue(empty($params['actions']));
		
		$type->action('first');
		$type->action('second, third');
		$type->blank();

		$params = $type->params();
		$this->assertTrue($params['blank']);
		$this->assertContains('first', $params['actions']);
		$this->assertContains('second', $params['actions']);
		$this->assertContains('third', $params['actions']);		
		
		$type->render();
		$params = $type->params();
		$this->assertSame('Controller', $params['extends']);
		$this->assertSame('Controllers', $params['category']);
	}

} // End Generator_Type_ControllerTest 
