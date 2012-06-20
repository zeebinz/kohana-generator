<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Test case for Generator_Type_Generator.
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
class Generator_Type_GeneratorTest extends Unittest_TestCase 
{
	/**
	 * Tests that all type options are applied correctly.
	 */
	public function test_type_options()
	{
		$type = new Generator_Type_Generator('Foo');

		$this->assertSame('Generator_Type_Foo', $type->name());
		$type->name('Generator_Type_Foo');
		$this->assertSame('Generator_Type_Foo', $type->name());
		$type->name('Bar_Generator_Type_Foo');
		$this->assertSame('Bar_Generator_Type_Foo', $type->name());
		
		$type->render();
		$params = $type->params();
		$this->assertSame('Generator/Types', $params['category']);
		$this->assertSame('Generator_Type', $params['extends']);
		$this->assertSame('Foo', $params['type']);
		$this->assertSame('type_foo', $params['type_template']);
	}

} // End Generator_Type_GeneratorTest 
