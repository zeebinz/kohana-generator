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
	public function test_toggle_on_boolean_options()
	{
		$task = Minion_Task::factory(array('task' => 'generate', 'inspect' => NULL));

		$options = $task->get_options();
		$this->assertTrue($options['inspect']);
	}

	//
	// More task tests to follow when Minion testing is a bit more practical
	//

} // End Generator_Task_GenerateTest
