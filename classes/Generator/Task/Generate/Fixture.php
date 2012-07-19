<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Task for generating test fixtures, see Task_Generate_Fixture for usage.
 *
 * @package    Generator
 * @category   Generator/Tasks
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Generator_Task_Generate_Fixture extends Task_Generate
{
	/**
	 * @var  array  The task options
	 */
	protected $_options = array(
		'name'    => '',
		'command' => '',
		'refresh' => FALSE,
	);

	/**
	 * @var  array  Arguments mapped to options
	 */
	protected $_arguments = array(
		1 => 'name',
		2 => 'command',
	);

	/**
	 * Validates the task options.
	 *
	 * @param   Validation  $validation  The validation object to add rules to
	 * @return  Validation
	 */
	public function build_validation(Validation $validation)
	{
		if ( ! $this->_options['refresh'])
		{
			$validation->rule('name', 'not_empty');
			$validation->rule('command', 'not_empty');
		}

		return parent::build_validation($validation);
	}

	/**
	 * Creates a generator builder with the given configuration options.
	 *
	 * @param   array  $options  The selected task options
	 * @return  Generator_Builder
	 * @throws  Generator_Exception  On failure to parse command
	 */
	public function get_builder(array $options)
	{
		if ($options['refresh'])
		{
			// Get the values from an existing fixture file
			$fixture = new Generator_Type_Fixture;
			$fixture->name($options['name'])
				->module($options['module'])
				->load_from_file();

			// We want only the stored command and summary
			$options['command'] = $fixture->command();
			$summary = $fixture->summary();
		}

		// Parse the command into arguments
		$options['command'] = str_replace("'", '"', trim($options['command']));
		$args = $this->parse_task_command($options['command']);

		// Reverse it to guarantee that command and output match
		$command = $this->create_task_command($args);

		if ($command != $options['command'])
		{
			// This shouldn't happen, but it's worth checking
			throw new Generator_Exception(
				"Couldn't parse the command [':com1' doesn't match ':com2']",
				array(':com1' => $command, ':com2' => $options['command']));
		}

		// Get the test summary
		$summary = isset($summary) ? $summary : $this->get_fixture_summary($args);

		// Get the test expectation
		$expected = $this->get_fixture_expectation($args);

		return Generator::build()
			->add_fixture($options['name'])
				->summary($summary)
				->command($command)
				->expect($expected)
			->with_module($options['module'])
			->with_pretend($options['pretend'])
			->with_force($options['force']);
	}

	/**
	 * Returns the test summary/description for the fixture.
	 *
	 * @param   array   $args  The arguments for running the given task
	 * @return  string  The test summary
	 */
	public function get_fixture_summary(array $args)
	{
		return 'Test fixture for the '.strtoupper($args['task']).' generator.';
	}

	/**
	 * Generates the expected output for the task with the given arguments.
	 *
	 * As we're creating a fixture file and so need reproducible output, we
	 * should use the fixtures configuration and dummy classes or interfaces
	 * defined for use only by the fixtures.
	 *
	 * @param   array   $args  The arguments for running the given task
	 * @return  string  The expected output
	 * @throws  Generator_Exception  On unsupported command
	 */
	public function get_fixture_expectation(array $args)
	{
		if (strpos($args['task'], 'generate') !== 0)
		{
			// Any other commands should use a different task
			throw new Generator_Exception('Only generator commands are supported');
		}

		if (array_key_exists('remove', $args['options']))
		{
			// Handle the --remove command
			return $this->_get_remove_expectation($args);
		}

		// Get the task instance
		$task = $this->get_fixture_task($args);

		// Get the fixtures directory
		$fixtures = $this->get_fixtures_directory();

		// Include any test dummies
		$this->include_fixture_dummies($fixtures);

		// Add the test config, if any
		$task->set_options(array('config' => $fixtures.'_test_config.php'));

		// Get the builder and override its options
		$builder = $task->get_builder($task->get_options())
			->with_verify(FALSE)
			->with_force(FALSE)
			->with_pretend(TRUE)
			->prepare();

		// Create the expectation string
		$expected = ''; $i = 1;

		foreach ($builder->inspect() as $item)
		{
			// Add the normalized file path info
			$expected .= str_replace(DIRECTORY_SEPARATOR, '/',
				"[ File $i ] ".Debug::path($item['file'])).PHP_EOL.PHP_EOL;

			// Add the rendered output
			$expected .= $item['rendered'].PHP_EOL;
			$i++;
		}

		return trim($expected);
	}

	/**
	 * Includes any test dummy files required by the fixture, such as dummy classes
	 * or interfaces.
	 *
	 * @param   string  $directory  The directory to search
	 * @return  void
	 */
	public function include_fixture_dummies($directory)
	{
		require_once $directory.'_test_interfaces.php';
		require_once $directory.'_test_classes.php';
	}

	/**
	 * Returns a valid task instance to be used for creating a fixture
	 * expectation.
	 *
	 * @param   array  $args  The arguments for running the given task
	 * @return  Minion_Task   The task instance
	 * @throws  Generator_Exception  On failed validation
	 */
	public function get_fixture_task(array $args)
	{
		// Create the task instance
		$class = $this->convert_task_to_class_name($args['task']);
		$task = new $class;

		// Set the task options
		$task->set_options($args['options']);

		// Validate the task options
		$validation = Validation::factory($task->get_options());
		$validation = $task->build_validation($validation);
		if ( ! $validation->check())
		{
			// Get any parameter errors
			$errors = implode(', ', $validation->errors($this->get_errors_file()));

			// Throw the errors so we can test for them later
			throw new Generator_Exception('Parameter Errors for [ '.$args['task'].' ]: :errors',
				array(':errors' => $errors));
		}

		return $task;
	}

	/**
	 * Returns the full path to the directory in which the fixtures are stored.
	 *
	 * @return  string  The path to the fixtures directory
	 */
	public function get_fixtures_directory()
	{
		$ds = DIRECTORY_SEPARATOR;

		$path = $this->_options['module'] ? MODPATH.$this->_options['module'].$ds : APPPATH;

		return $path.'tests'.$ds.'fixtures'.$ds;
	}

	/**
	 * Returns the names of any currently stored fixtures, optionally limited to
	 * fixture names that match a given pattern (with wildcards).
	 *
	 * @return  string  $pattern  A pattern for the fixture names to match
	 * @return  array   The list of fixture names
	 */
	public function get_fixtures_list($pattern = NULL)
	{
		$dir = $this->get_fixtures_directory();
		$pattern = $pattern ?: '*test';
		$fixtures = array();

		foreach (glob($dir.$pattern) as $file)
		{
			// Skip names starting with underscores
			$name = basename($file);
			if ($name[0] == '_')
				continue;

			// Add the fixture name to the list
			$fixtures[] = $name;
		}

		return $fixtures;
	}

	/**
	 * Returns the expected output of generator tasks that are run with the
	 * remove option.
	 *
	 * @todo  Implement this in full, or just forget about supporting it
	 *
	 * @param   array   $args  The arguments for running the given task
	 * @return  string  The expected output
	 * @throws  Generator_Exception
	 */
	protected function _get_remove_expectation(array $args)
	{
		throw new Generator_Exception('The :opt option is not currently supported',
			array(':opt' => '--remove'));
	}

	/**
	 * Loads a builder, or merges different builders when refreshing a set of
	 * fixtures, and runs the task.
	 *
	 * @param   array  $params  The current task parameters
	 * @return  void
	 * @throws  Generator_Exception  On empty fixtures list
	 */
	protected function _execute(array $params)
	{
		if ($params['name'] AND (strpos($params['name'], '*') === FALSE))
		{
			// We only want a single fixture
			$builder = $this->get_builder($params);
		}
		elseif ($params['refresh'])
		{
			// Get the list of fixtures to refresh
			if ( ! ($fixtures = $this->get_fixtures_list($params['name'])))
			{
				throw new Generator_Exception('No fixtures were found at: :path',
					array(':path' => $this->get_fixtures_directory()));
			}

			// Start with a base builder
			$builder = new Generator_Builder;

			foreach ($fixtures as $name)
			{
				// Set the options for the new builder
				$options = $params;
				$options['name'] = $name;

				// Merge the new builder into the base
				$builder->merge($this->get_builder($options));
			}
		}

		// Run the task
		$this->run($builder->prepare(), $params);
	}

} // End Generator_Task_Generate_Fixture
