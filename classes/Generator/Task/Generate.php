<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Base task for generating application and module files from templates, see
 * Task_Generate for usage.
 *
 * All generator tasks should extend this class, but it's also used as is to
 * output the help for the 'minion generate --help' command.
 *
 * @package    Generator
 * @category   Generator/Tasks
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Generator_Task_Generate extends Minion_Task
{
	/**
	 * The task options that apply to all generator tasks
	 * @var array
	 */
	protected $_common_options = array(
		'pretend'  => FALSE,
		'force'    => FALSE,
		'quiet'    => FALSE,
		'inspect'  => FALSE,
		'no-ask'   => FALSE,
		'remove'   => FALSE,
		'verbose'  => FALSE,
		'no-ansi'  => FALSE,
		'module'   => '',
		'template' => '',
		'config'   => '',
	);

	/**
	 * Instantiates the task, and merges common and task options.We make this
	 * public here so we can handle different instances more easily.
	 *
	 * @return void
	 */
	public function __construct()
	{
		// Merge any task options with the common options
		$this->_options = array_merge($this->_common_options, $this->_options);

		parent::__construct();
	}

	/**
	 * Sets the task options passed as parameters.
	 *
	 * Boolean parameters (i.e. switches without values) are handled here by
	 * toggling their associated options on.
	 *
	 * @param   array  $options  The options to set
	 * @return  Minion_Task  This instance
	 */
	public function set_options(array $options)
	{
		foreach ($options as $key => $value)
		{
			if ($value === NULL AND isset($this->_options[$key])
				AND $this->_options[$key] === FALSE)
			{
				// Switch on the boolean option
				$options[$key] = TRUE;
			}
		}

		return parent::set_options($options);
	}

	/**
	 * Runs the current task with the given generator builder.
	 *
	 * By default, this method runs in interactive mode, requesting user
	 * confirmation before any destructive changes, allowing inspection, etc.
	 *
	 * @param   Generator_Builder  $builder  The builder to execute
	 * @param   array  $options  The task options to use
	 * @return  void
	 */
	public function run(Generator_Builder $builder, array $options)
	{
		if ($options['inspect'])
		{
			// Output debug info for each generator item
			$i = 1;
			foreach ($builder->inspect() as $item)
			{
				$this->_write('');
				$this->_write($this->_color("[ File $i ] ".Debug::path($item['file']), 'brown'));
				$this->_write('');
				$this->_write($item['rendered']);
				$i++;
			}
			return;
		}

		// Choose which command to run
		$command = $options['remove'] ? Generator::REMOVE : Generator::CREATE;

		if ( ! $options['quiet'] AND ! $options['pretend'] AND ! $options['no-ask'])
		{
			// Run once in pretend mode to get a list of expected actions,
			// and don't continue if there's nothing to do
			if ( ! $this->run_command($command, $builder->with_pretend(TRUE)))
				return;

			$this->_write('');

			// Ask for user confirmation
			if ('y' !== $this->_read('Do you want to continue?', array('y', 'n')))
				return;
		}

		// Run the chosen command on the generators
		$this->run_command($command, $builder->with_pretend($options['pretend']));
	}

	/**
	 * Runs the given command on the generators and outputs the log, or only
	 * lists the expected actions if we're in pretend mode.
	 *
	 * @param   string   $command  The command to be run
	 * @param   Generator_Builder  $builder  The builder holding the generators
	 * @return  boolean  TRUE if some action has been logged
	 */
	public function run_command($command, Generator_Builder $builder)
	{
		if ($builder->is_pretend())
		{
			$this->_write('');
			$this->_write('The result of running this task will be:');
		}

		// Track the logged messages
		$messages = array();
		$this->_write('');

		foreach ($builder->generators() as $generator)
		{
			$generator->$command();

			foreach ($generator->log() as $msg)
			{
				if (($this->_options['verbose'] AND $msg != end($messages))
					OR ! in_array($msg, $messages))
				{
					// We only want unique messages, unless in verbose mode and
					// the last message isn't being repeated
					$this->_write_log($msg['status'], Debug::path($msg['item']));
					$messages[] = $msg;
				}
			}
		}

		if (empty($messages))
		{
			// No actions have been logged
			$this->_write('    Nothing to do, no changes made.');
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Convenience method for loading configuration values, optionally from
	 * a given config group or an absolute file path.
	 *
	 * @param   array  $path   Array path to the config values
	 * @param   array  $group  The config group of file to load
	 * @return  mixed  The config values or NULL
	 */
	public function get_config($path, $group = NULL)
	{
		if ($group !== NULL AND is_file($group))
		{
			// Return the values from the file
			$config = include $group;
			return Arr::path($config, $path);
		}

		// Otherwise load the CFS config values
		$group = $group ?: 'generator';
		return Kohana::$config->load($group.'.'.$path);
	}

	/**
	 * Returns a generator builder created with the given configuration options.
	 * This method should be implemented in full by child classes.
	 *
	 * @param   array  $options  The selected task options
	 * @return  boolean|Generator_Builder  FALSE if no builder is available
	 */
	public function get_builder(array $options)
	{
		return FALSE;
	}

	/**
	 * Loads a builder and runs the task, or outputs the common help message
	 * by default.
	 *
	 * @param   array  $params  The current task parameters
	 * @return  void
	 */
	protected function _execute(array $params)
	{
		if ($builder = $this->get_builder($params))
		{
			$this->run($builder->prepare(), $params);
			return;
		}

		$this->_help($params);
	}

	/**
	 * Writes a message directly to STDOUT.
	 *
	 * @param   string   $text  The message to write
	 * @param   boolean  $eol   Should EOL be added?
	 * @return  void
	 */
	protected function _write($text, $eol = TRUE)
	{
		if ($this->_options['quiet'])
			return;

		echo ($eol ? ($text.PHP_EOL) : $text);
	}

	/**
	 * Writes a formatted log message directly to STDOUT.
	 *
	 * @param   string  $status  The status message
	 * @param   string  $item    The item affected
	 * @return  void
	 */
	protected function _write_log($status, $item)
	{
		$color = in_array($status, array(Generator::CREATE, Generator::REMOVE)) ? 'green' : 'red';

		$this->_write($this->_color(sprintf('%10s  %s', $status, $item), $color));
	}

	/**
	 * Returns the given text with the correct color codes for a foreground and
	 * optionally a background color.
	 *
	 * @param   string  $text        The text to color
	 * @param   string  $foreground  The foreground color
	 * @param   string  $background  The background color
	 * @return  string  The color coded string
	 */
	public function _color($text, $foreground, $background = NULL)
	{
		if ($this->_options['no-ansi'])
			return $text;

		return Minion_CLI::color($text, $foreground, $background);
	}

	/**
	 * Reads user input from STDIN.
	 *
	 * @param   string  $text     text to show user before waiting for input
	 * @param   array   $options  array of options the user is shown
	 * @return  string  the user input
	 */
	protected function _read($text, array $options = NULL)
	{
		return Minion_CLI::read($text, $options);
	}

	/**
	 * Outputs the help message for the given generator task.
	 *
	 * A list of available generators will be appended to the help only if the
	 * current instance is the base generator task.
	 *
	 * @param   array  $params  The current task parameters
	 * @return  void
	 */
	protected function _help(array $params)
	{
		parent::_help($params);

		if ( ! is_subclass_of($this, 'Task_Generate'))
		{
			// Get the list of available generators
			$generators = $this->_compile_task_list(Kohana::list_files('classes/task/generate'));

			// Append the list to the help output
			$this->_write('Available generators:');
			$this->_write('');
			foreach ($generators as $generator)
			{
				$this->_write('  * '.$generator);
			}
			$this->_write('');
		}
	}

	/**
	 * Parses a task command string, returning an array of arguments and
	 * option values.
	 *
	 * @param   string  $command  The task command
	 * @return  array   The parsed command arguments
	 */
	public function parse_task_command($command)
	{
		$command = explode(' ', trim($command), 2);

		// Set the task name and initialize options
		$result['task'] = $command[0];
		$result['options'] = array();

		if (isset($command[1]))
		{
			// Get the options arguments
			preg_match_all("/--(?P<option>[\w\-]+)(=(?P<value>.*?))?(?:\s|\-|$)/",
				$command[1], $matches, PREG_SET_ORDER);

			foreach ($matches as $match)
			{
				// Set the options with or without values
				$value = isset($match['value']) ? $match['value'] : NULL;
				$result['options'][$match['option']] = $value;
			}
		}

		return $result;
	}

	/**
	 * Creates a task command string from an array of arguments, reversing
	 * the results of parse_task_command().
	 *
	 * @param   array   $args  The command arguments
	 * @return  string  The task command
	 */
	public function create_task_command(array $args)
	{
		$command = $args['task'];

		foreach ($args['options'] as $key => $value)
		{
			$command .= ' --'.$key.($value ? ('='.$value) : '');
		}

		return $command;
	}

} // End Generator_Task_Generate
