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
		'module'   => '',
		'template' => '',
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
	 * @param  array  $params  The task parameters to use
	 * @return void
	 */
	public function run(Generator_Builder $builder, array $params)
	{
		if ($params['inspect'])
		{
			// Output debug info for each generator item
			$i = 1;
			foreach ($builder->inspect() as $num => $item)
			{
				$this->_write('');
				$this->_write("[ File $i ] ".Debug::path($item['file']));
				$this->_write('');
				$this->_write($item['rendered']);
				$i++;
			}
			return;
		}

		// Choose which command to run
		$command = $params['remove'] ? Generator::REMOVE : Generator::CREATE;

		if ( ! $params['quiet'] AND ! $params['pretend'] AND ! $params['no-ask'])
		{
			// Run once in pretend mode to get a list of expected actions,
			// and don't continue if there's nothing to do
			if ( ! $this->run_command($command, $builder->with_pretend(TRUE)))
				return;

			$this->_write('');

			// Ask for user confirmation
			$read = $this->_read('Do you want to continue?', array('y', 'n'));
			if ($read == 'n')
				return;
		}

		// Run the chosen command on the generators
		$this->run_command($command, $builder->with_pretend($params['pretend']));
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
	 * Outputs the common help message by default.
	 *
	 * @return void
	 */
	protected function _execute(array $params)
	{
		$this->_help($params);
	}

	/**
	 * Writes a message directly to STDOUT.
	 *
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
	 * @return void
	 */	
	protected function _write_log($status, $item)
	{
		$this->_write(sprintf("%10s  %s", $status, $item));
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
	 * @param  array  $params  the current task parameters
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
