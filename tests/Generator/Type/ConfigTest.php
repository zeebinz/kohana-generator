<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Test case for Generator_Type_Config.
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
class Generator_Type_ConfigTest extends Unittest_TestCase 
{
	/**
	 * Tests that all type options are applied correctly.
	 */
	public function test_type_options()
	{
		$ds = DIRECTORY_SEPARATOR;

		$type = new Generator_Type_Config();

		$type->value('a.b.c|some string');
		$type->value('a.b.d|2, e | 3');

		$type->render();
		$params = $type->params();

		$this->assertSame(
			array(
				'a' => array(
					'b' => array(
						'c' => 'some string',
						'd' => 2,
					),
				),
				'e' => 3,
			),
			$params['values']
		);
	}

} // End Generator_Type_ConfigTest 
