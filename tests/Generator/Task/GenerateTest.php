<?php
/**
 * Test case for the Generator_Task_Generate base class.
 *
 * @group generator
 * @group generator.tasks
 *
 * @package    Generator
 * @category   Tests
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Generator_Task_GenerateTest extends Unittest_TestCase
{
	/**
	 * When called without parameters, the help message should be returned
	 * with the available generators appended.
	 */
	public function test_default_output_is_help_message()
	{
		ob_start();
		Minion_Task::factory(array('task' => 'generate'))->execute();
		$output = ob_get_clean();

		$this->assertRegExp('/Usage/', $output, 'Usage details should be displayed');
		$this->assertRegExp('/Available generators:/', $output, 'Generators list should be appended');
	}

	/**
	 * Common options and task options should be merged on instantiation.
	 */
	public function test_merging_common_and_task_options()
	{
		$task = Minion_Task::factory(array('task' => 'generate'));

		$options = $task->get_options();
		$this->assertArrayHasKey('pretend', $options);
	}

	/**
	 * Any boolean options defined for the task should be toggled on when
	 * the generator is created.
	 */
	public function test_toggling_on_boolean_options()
	{
		$task = Minion_Task::factory(array('task' => 'generate', 'inspect' => NULL));

		$options = $task->get_options();
		$this->assertTrue($options['inspect']);
	}

	/**
	 * Positional arguments may be mapped to defined option names.
	 */
	public function test_mapping_positional_arguments_to_options()
	{
		$task = Minion_Task::factory(array('task' => 'generate', 'name' => 'Foo'));

		// Using manual mappings
		$options    = $task->get_options();
		$options[1] = 'baz';
		$options[2] = 'qux';
		$mapping    = array(1 => 'someopt', 2 => 'name');
		$options    = $task->convert_arguments($options, $mapping);

		$this->assertSame('Foo', $options['name']);
		$this->assertSame('baz', $options['someopt']);
		$this->assertArrayNotHasKey(1, $options);
		$this->assertArrayNotHasKey(2, $options);

		// Using the default mappings
		$task = Minion_Task::factory(array(
			'task' => 'generate',
			1 => 'Foo',
		));

		$options = $task->get_options();
		$this->assertSame('Foo', $options['name']);
		$this->assertArrayNotHasKey(1, $options);
	}

	/**
	 * Task commands should be parsable into arrays of arguments, and the process
	 * should be reversable.
	 *
	 * @group  generator.tasks.fixtures
	 *
	 * @dataProvider provider_task_commands
	 * @param  string  $command  The command string to parse
	 * @param  array   $args     The parsed arguments
	 */
	public function test_parse_and_create_task_commands($command, $args)
	{
		$task = new Task_Generate;

		// Parse the command
		$actual = $task->parse_task_command($command);
		$this->assertSame($args, $actual);

		// Reverse by creating the command
		$actual = $task->create_task_command($actual);
		$this->assertSame($command, $actual);
	}

	/**
	 * Provides test data for test_parse_and_create_task_commands
	 */
	public function provider_task_commands()
	{
		return array(
			array(
				'generate:class --name=Foo --no-test --pretend',
				array('task' => 'generate:class', 'options' => array('name' => 'Foo', 'no-test' => NULL, 'pretend' => NULL)),
			),
			array(
				'generate:class --name=Foo --implement="One, Two"',
				array('task' => 'generate:class', 'options' => array('name' => 'Foo', 'implement' => 'One, Two')),
			),
			array(
				'generate:class --name=Foo --implement=One,Two --inspect',
				array('task' => 'generate:class', 'options' => array('name' => 'Foo', 'implement' => 'One,Two', 'inspect' => NULL)),
			),
			array(
				'generate:config --name=foo --values="a.b|a, c|d"',
				array('task' => 'generate:config', 'options' => array('name' => 'foo', 'values' => 'a.b|a, c|d')),
			),
			array(
				'generate:config --name=foo --values="a.b|a"',
				array('task' => 'generate:config', 'options' => array('name' => 'foo', 'values' => 'a.b|a')),
			),
			array(
				'generate:config --name="foo/bar" --values="a.b|a,c|d"',
				array('task' => 'generate:config', 'options' => array('name' => 'foo/bar', 'values' => 'a.b|a,c|d')),
			),
			array(
				'generate:controller:view --name=Foo',
				array('task' => 'generate:controller:view', 'options' => array('name' => 'Foo')),
			),
		);
	}

	/**
	 * This uses values from fixture files created by Generator_Type_Fixture
	 * to test the expected output of the given generator commands. It's quite
	 * basic functional testing, since all it does is repeat the process by
	 * which the fixture was first created, but it does the job.
	 *
	 * @group  generator.tasks.fixtures
	 *
	 * @dataProvider  provider_test_fixtures
	 * @param    Generator_Type_Fixture  $fixture  A fixture object
	 * @depends  test_parse_and_create_task_commands
	 */
	public function test_generated_output_with_fixtures($fixture)
	{
		if ( ! function_exists('trait_exists') AND strpos($fixture->name(), 'trait') !== FALSE)
		{
			$this->markTestSkipped("PHP >= 5.4.0 is required for '".$fixture->name()."'");
		}

		// First test loading up the fixture's file
		$this->assertTrue($fixture->load_from_file(),
			"Couldn't load the fixture: '".$fixture->name()."'");

		// Create a new task with the fixture's command
		$task = new Task_Generate_Fixture;
		$task->set_options(array('module' => $fixture->module()));
		$args = $task->parse_task_command($fixture->command());

		// Get the expectations
		$expected = $fixture->expect();
		$actual   = $task->get_fixture_expectation($args);

		$this->assertSame($expected, $actual,
			"Error matching the expectation of: '".$fixture->name()."'"
		);
	}

	/**
	 * Returns representations of stored fixtures for functional testing.
	 *
	 * [!!] Must make sure when using the --group option that the tests
	 * aren't skipped silently due to this bug:
	 *
	 * @link https://github.com/sebastianbergmann/phpunit/issues/498
	 *
	 * @return  array  Instances of Generator_Type_Fixture
	 */
	public function provider_test_fixtures()
	{
		$module = basename(dirname(dirname(dirname(dirname(__FILE__)))));
		$dir = dirname(dirname(dirname(__FILE__))).'/fixtures/';
		$fixtures = array();

		foreach (glob($dir.'*test') as $file)
		{
			// Skip name starting with underscores
			$name = basename($file);
			if ($name[0] == '_')
				continue;

			// Create the fixture object
			$fixt = new Generator_Type_Fixture;
			$fixt->name($name)->module($module);

			// Add the fixture to the list
			$fixtures[] = array($fixt);
		}

		return $fixtures;
	}

	//
	// More task tests to follow when Minion testing is a bit more practical
	//

} // End Generator_Task_GenerateTest
