<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Test case for Generator_Type_Message.
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
class Generator_Type_MessageTest extends Unittest_TestCase
{
	/**
	 * Tests that all type options are applied correctly.
	 */
	public function test_type_options()
	{
		$ds = DIRECTORY_SEPARATOR;

		$type = new Generator_Type_Message();

		$type->value('a.key|a.value');
		$type->value('b.key |b.value, c.key | c.value');

		$type->render();
		$params = $type->params();

		$this->assertSame(array('a.key' => 'a.value', 'b.key' => 'b.value',
			'c.key' => 'c.value'),	$params['values']
		);
	}

} // End Generator_Type_MessageTest
