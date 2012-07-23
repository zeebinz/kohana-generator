<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Test case for Generator_Type_Interface.
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
class Generator_Type_InterfaceTest extends Unittest_TestCase
{
	/**
	 * Tests that all type options are applied correctly.
	 */
	public function test_type_options()
	{
		$module = basename(dirname(dirname(dirname(dirname(__FILE__)))));
		$type = new Generator_Type_Interface('Foo');

		$type
			->extend('Bar, Boom')
			->extend('Bang')
			->module($module);

		$params = $type->params();
		$this->assertSame($module, $type->module());
		$this->assertSame(array('Bar', 'Boom', 'Bang'), $params['extends']);

		$rendered = $type->render();
		$params = $type->params();
		$this->assertSame('Bar, Boom, Bang', $params['extends']);
		$this->assertRegExp('/@category\s+Interfaces/', $rendered);
	}

} // End Generator_Type_InterfaceTest
