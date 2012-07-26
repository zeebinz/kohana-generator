<?php defined('SYSPATH') OR die('No direct script access.');

if (function_exists('trait_exists'))
{
	require_once dirname(dirname(dirname(__FILE__))).'/fixtures/_test_traits.php';
}

/**
 * Test case for Generator_Type_Trait.
 *
 * @group      generator
 * @group      generator.types
 * @group      generator.traits
 *
 * @package    Generator
 * @category   Tests
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Generator_Type_TraitTest extends Unittest_TestCase
{
	/**
	 * This method is called before each test is run.
	 */
	public function setUp()
	{
		parent::setUp();

		if ( ! function_exists('trait_exists'))
		{
			$this->markTestSkipped('PHP >= 5.4.0 is required');
		}
	}

	/**
	 * Tests that all type options are applied correctly.
	 */
	public function test_type_options()
	{
		$module = basename(dirname(dirname(dirname(dirname(__FILE__)))));
		$type = new Generator_Type_Trait('Foo');

		$type->module($module);
		$this->assertSame($module, $type->module());

		$rendered = $type->render();
		$this->assertRegExp('/@category\s+Traits/', $rendered);
	}

	/**
	 * Traits can inherit other traits by using them. The limitation here is that
	 * properties defined by traits can't be re-declared or overridden.
	 *
	 * @link http://php.net/manual/en/language.oop5.traits.php
	 */
	public function test_can_use_other_traits()
	{
		$type = new Generator_Type_Trait('Foo');
		$type->using('Bar, Baz');

		$params = $type->params();
		$this->assertArrayHasKey('traits', $params);
		$this->assertCount(2, $params['traits']);
		$this->assertContains('Bar', $params['traits']);
		$this->assertContains('Baz', $params['traits']);

		$rendered = $type->render();
		$this->assertRegExp('/use Bar;/', $rendered);
		$this->assertRegExp('/use Baz;/', $rendered);

		$type = new Generator_Type_Trait('Foo');
		$type->using('Fx_Trait_Selector');

		$rendered = $type->render();
		$params = $type->params();

		$this->assertArrayHasKey('traits', $params);
		$this->assertCount(1, $params['traits']);
		$this->assertContains('Fx_Trait_Selector', $params['traits']);
		$this->assertRegExp('/use Fx_Trait_Selector;/', $rendered);

		// Abstract methods of inherited traits should not be implemented
		$this->assertArrayNotHasKey('methods', $params);
	}

	/**
	 * Although traits can't be extended, we can fake transparent extension by
	 * creating a stub that uses the trait.
	 */
	public function test_can_fake_transparent_extension()
	{
		$type = new Generator_Type_Trait('Foo');

		$type->template('generator/type/stub')
			->set('source', 'Foo_Bar');

		$this->assertRegExp('/trait Foo {use Foo_Bar;}/', $type->render());
	}

} // End Generator_Type_TraitTest
