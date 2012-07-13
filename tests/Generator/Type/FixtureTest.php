<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Test case for Generator_Type_Fixture.
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
class Generator_Type_FixtureTest extends Unittest_TestCase
{
	/**
	 * Tests that all type options are applied correctly.
	 */
	public function test_type_options()
	{
		$ds = DIRECTORY_SEPARATOR;
		$filename = 'generate_foo';
		$type = new Generator_Type_Fixture();

		$type->name($filename);
		$this->assertSame($filename.'.test', $type->name());

		$expected = APPPATH.'tests'.$ds.'fixtures'.$ds.$filename.'.test';
		$type->guess_filename();
		$type->name($filename.'.test');
		$this->assertSame($filename.'.test', $type->name());
		$this->assertSame($expected, $type->file());

		$type->summary('A summary');
		$type->command('A command');
		$type->expect('An expectation');

		$this->assertSame('A summary', $type->summary());
		$this->assertSame('A command', $type->command());
		$this->assertSame('An expectation', $type->expect());
	}

	/**
	 * Fixture values can also be loaded directly from existing files.
	 */	
	public function test_load_from_file()
	{
		$module = basename(dirname(dirname(dirname(dirname(__FILE__)))));
		$type = new Generator_Type_Fixture();

		$type->name('_test_generate.test')->module($module);
		$this->assertTrue($type->load_from_file(), "Couldn't load from file");

		$this->assertSame('Test fixture for the GENERATE:FIXTURE generator.',
			$type->summary());
		$this->assertSame('generate:class --name=Foo --module=foo',
			$type->command());
		$this->assertSame('The expectation string'.PHP_EOL,
			$type->expect());
	}

} // End Generator_Type_FixtureTest
