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
	 * This uses values from fixture files created by Generator_Type_Fixture
	 * to test the expected output of the given generator commands. It's quite
	 * basic functional testing, since all it does is repeat the process by
	 * which the fixture was first created, but it does the job.
	 *
	 * A data provider isn't used here because of the bugs with how exceptions
	 * are handled when --group is specified.
	 *
	 * @param  Generator_Type_Fixture  $fixture
	 */
	public function test_generated_output_with_fixtures()
	{
		foreach ($this->_get_test_fixtures() as $fixture)
		{
			$task = new Task_Generate_Fixture;
			$args = $task->parse_task_command($fixture->command());

			$expected = $fixture->expect();
			$actual   = $task->get_fixture_expectation($args);

			$this->assertSame($expected, $actual,
				"Error matching the expectation of: '".$fixture->name()."'"
			);
		}
	}

	/**
	 * Returns the values of the stored fixtures for functional testing.
	 * This should be a data provider, but it isn't because of this bug:
	 *
	 * @link https://github.com/sebastianbergmann/phpunit/issues/498
	 */
	protected function _get_test_fixtures()
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

			// Load the fixture file
			$fixt = new Generator_Type_Fixture();
			$fixt->name($name)->module($module);

			// It doesn't hurt to use an assertion here
			$this->assertTrue($fixt->load_from_file(),
				"Couldn't load the fixture file: '$name'");

			// Add the fixture to the list
			$fixtures[] = $fixt;
		}

		return $fixtures;
	}

	//
	// More task tests to follow when Minion testing is a bit more practical
	//

} // End Generator_Task_GenerateTest
