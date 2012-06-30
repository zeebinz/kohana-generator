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
	 * Instantiates the task, and merges common and task options.
	 *
	 * @return void
	 */
	protected function __construct()
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
	 * @param  Generator_Builder  $builder  The builder to execute
	 * @param  array  $options  The task options to use
	 * @return void
	 */
	public function run(Generator_Builder $builder, array $options)
	{
		if ($options['inspect'])
		{
			// Output debug info for each generator item
			$i = 1;
			foreach ($builder->inspect() as $num => $item)
			{
				$this->_write('');
				$this->_write($this->_color("[ File $i ] ".Debug::path($item['file']), 'brown'));
				$this->_write('');
				$this->_write($item['rendered']);
				$i++;
			}
			return;
		}

		// Set verbosity level
		$this->_options['verbose'] = $options['remove'] ?: $this->_options['verbose'];

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
			if ('n' == $this->_read('Do you want to continue?', array('y', 'n')))
				return;
		}

		// Run the chosen command on the generators
		$this->run_command($command, $builder->with_pretend($options['pretend']));
	}

	/**
	 * Runs the given command on the generators and outputs the log, or only
	 * lists the expected actions if we're in pretend mode.
	 *
	 * @param  string   $command  The command to be run
	 * @param  Generator_Builder  $builder  The builder holding the generators
	 * @return bool  TRUE if some action has been logged
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
				if ($this->_options['verbose'] OR ! in_array($msg, $messages))
				{
					// We only want unique messages, unless in verbose mode
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
	 * Convenience method for loading configuration values.
	 *
	 * @param  array  $path   Array path to the config values
	 * @param  array  $group  The config group to load
	 * @return mixed  The config values or NULL
	 */
	public function get_config($path, $group = NULL)
	{
		$group = $group ?: 'generator';

		return Kohana::$config->load($group.'.'.$path);
	}
	 *
	 * @param  array  $params  The current task parameters	 
	 * @return void
	 */
	protected function _execute(array $params)
	{
		$this->_help($params);
	}

	/**
	 * Writes a message directly to STDOUT.
	 *
	 * @param  string  $text  The message to write
	 * @param  bool    $eol   Should EOL be added?
	 * @return void
	 */	
	protected function _write($text, $eol = TRUE)
	{
		if ($this->_options['quiet'])
			return;

		echo ($eol ? ($text.PHP_EOL) : $text);

		// Minion_CLI::write($text, $eol);
	}

	/**
	 * Writes a formatted log message directly to STDOUT.
	 *
	 * @param  string  $status  The status message
	 * @param  string  $item    The item affected
	 * @return void
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
	 * @param  string  $text        The text to color
	 * @param  atring  $foreground  The foreground color
	 * @param  string  $background  The background color
	 * @return string  The color coded string
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
	 * @param  string  $text     text to show user before waiting for input
	 * @param  array   $options  array of options the user is shown
	 * @return string  the user input
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
	 * @param  array  $params  The current task parameters
	 * @return void
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

} // End Generator_Task_Generate
