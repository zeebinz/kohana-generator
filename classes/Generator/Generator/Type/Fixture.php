<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generator Fixture type.
 *
 * @package    Generator
 * @category   Generator/Types
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Generator_Generator_Type_Fixture extends Generator_Type
{
	protected $_template = 'generator/type_fixture';
	protected $_folder   = 'tests/fixtures';
	protected $_security = FALSE;

	protected $_defaults = array(
		'summary'  => '',
		'command'  => '',
		'expected' => '',
	);

	/**
	 * Sets/gets the fixture name.
	 *
	 * @param   string  $name  The fixture name
	 * @return  string|Generator_Type_Fixture  The name or this instance
	 */
	public function name($name = NULL)
	{
		if ($name === NULL)
			return $this->_name;

		// Append '.test' to the name if it's not aleady present
		$this->_name = (strpos($name, '.test') === FALSE) ? ($name.'.test') : $name;

		return $this;
	}

	/**
	 * Sets/gets the fixture summary.
	 *
	 * @param   string  $class  The fixture summary
	 * @return  string|Generator_Type_Fixture  The summary or this instance
	 */
	public function summary($summary = NULL)
	{
		if ($summary === NULL)
			return $this->_params['summary'];

		$this->_params['summary'] = (string) $summary;
		return $this;
	}

	/**
	 * Sets/gets the fixture command.
	 *
	 * @param   string  $class  The fixture command
	 * @return  string|Generator_Type_Fixture  The command or this instance
	 */
	public function command($command = NULL)
	{
		if ($command === NULL)
			return $this->_params['command'];

		$this->_params['command'] = (string) $command;
		return $this;
	}

	/**
	 * Sets/gets the fixture test expectation.
	 *
	 * @param   string  $class  The fixture expectation
	 * @return  string|Generator_Type_Fixture  The expectation or this instance
	 */
	public function expect($expect = NULL)
	{
		if ($expect === NULL)
			return $this->_params['expected'];

		$this->_params['expected'] = (string) $expect;
		return $this;
	}

	/**
	 * Ensures that the filename is not guessed by converting the name to
	 * a path, replacing underscores, etc.
	 *
	 * @param   boolean  $convert  Should the name be converted to a file path?
	 * @return  string   The guessed filename
	 * @throws  Generator_Exception  On invalid name or base path
	 */
	public function guess_filename($convert = TRUE)
	{
		return parent::guess_filename(FALSE);
	}

	/**
	 * Finalizes parameters and renders the template.
	 *
	 * @return  string  The rendered output
	 */
	public function render()
	{
		return parent::render();
	}

	/**
	 * Loads an existing fixture file based on the current fixture name,
	 * parses the contents and stores the values in the current instance.
	 *
	 * @return  boolean  FALSE if the file contents couldn't be parsed
	 * @throws  Generator_Exception  On missing fixture name or file path
	 */
	public function load_from_file()
	{
		// Get the fixture filename
		$file = $this->_file ?: $this->guess_filename();

		if ( ! is_file($file))
		{
			throw new Generator_Exception('The fixture file could not be found at :path:',
				array(':path' => $file));
		}

		// Get the file contents
		$contents = file_get_contents($file);

		// Parse the file contents
		preg_match('/^
			---SUMMARY---\s*
			(?P<summary>.*?)\s*
			---COMMAND---\s*
			(?P<command>.*?)\s*
			---EXPECTED---\s*
			(?P<expected>.*?)\s*
			---END---
			/sx',
			$contents, $matches);

		if ($matches AND isset($matches['expected']))
		{
			// Store the parsed values as parameters
			$this->_params['summary']  = $matches['summary'];
			$this->_params['command']  = $matches['command'];
			$this->_params['expected'] = $matches['expected'];

			return TRUE;
		}

		// Failed to match anything
		return FALSE;
	}

} // End Generator_Generator_Type_Fixture
