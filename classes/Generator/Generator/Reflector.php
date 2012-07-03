<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * This class provides some shortcuts for handling basic Reflection details
 * from sources such as classes, interfaces, etc.
 *
 * Thanks to simshaun for the pointers and samples.
 *
 * @package    Generator
 * @category   Reflectors
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Generator_Generator_Reflector
{
	// The supported source types
	const TYPE_CLASS     = 'class';
	const TYPE_INTERFACE = 'interface';

	/**
	 * The source class, interface etc. to inspect
	 * @var  string
	 */
	protected $_source;

	/**
	 * The current source type
	 * @var  string
	 */
	protected $_type;

	/**
	 * The parsed reflection info for the source
	 * @var  string
	 */
	protected $_info = array();

	/**
	 * Instantiates the reflector and stores the name of the source that is
	 * being inspected.
	 *
	 * @param  string  $source  The source name
	 * @param  string  $type    The source type
	 * @return void
	 */
	public function __construct($source = NULL, $type = Generator_Reflector::TYPE_CLASS)
	{
		$this->source($source);
		$this->type($type);
	}

	/**
	 * Setter/getter for the source class, interface etc. being inspected.
	 *
	 * @param   string  $source  The source name
	 * @return  string|Generator_Reflector  The current source name or this instance
	 */
	public function source($source = NULL)
	{
		if ($source === NULL)
			return $this->_source;

		$this->_source = $source;
		$this->_info = array();

		return $this;
	}

	/**
	 * Setter/getter for the current source type.
	 *
	 * @param   string  $type    The source type
	 * @return  string|Generator_Reflector  The current source type or this instance
	 */
	public function type($type = Generator_Reflector::TYPE_CLASS)
	{
		if ($type === NULL)
			return $this->_type;

		$this->_type = $type;

		return $this;
	}

	/**
	 * Determines whether the current source exists, based on its given type.
	 *
	 * @return  bool
	 */
	public function exists()
	{
		return call_user_func($this->_type.'_exists', $this->_source);
	}

	/**
	 * Gathers basic reflection info on the given source and stores it locally.
	 *
	 * @throws  Generator_Exception  If a source is not set
	 * @return  Generator_Reflector  This instance
	 */
	public function inspect()
	{
		// We need a source to work with
		if ( ! $this->_source)
			throw new Generator_Exception('No source is available to inspect');

		// Start the new reflection
		$reflection = new ReflectionClass($this->_source);
		$this->_info = array();

		foreach ($reflection->getMethods() as $method)
		{
			$info = array();

			// Add any doccomment
			$info['doccomment'] = $method->getDocComment();

			// Add the modifiers string
			$info['modifiers'] = implode(' ', Reflection::getModifierNames($method->getModifiers()));

			// Add the returns by reference flag
			$info['by_ref'] = $method->returnsReference();

			// Add the parsed parameters list
			foreach ($method->getParameters() as $param)
			{
				$info['params'][$param->getName()] = $this->parse_method_param($param);
			}

			// Store the method info locally
			$this->_info['methods'][$method->getName()] = $info;
		}

		return $this;
	}

	/**
	 * Parses method parameters for information such as type hints and any
	 * default values, etc.
	 *
	 * @param   ReflectionParameter  $param  The parameter to parse
	 * @return  array  The parsed info
	 */
	public function parse_method_param(ReflectionParameter $param)
	{
		// Get any type hint without needing to load any classes
		preg_match('/\[\s\<\w+?>\s([\w]+)/s', $param->__toString(), $matches);
		$type = isset($matches[1]) ? $matches[1] : '';

		// Do we have a type hint to use?
		$hint = (bool) $type;

		// Get the param properties
		$by_ref = $param->isPassedByReference();
		$default = NULL;

		if ($param->isOptional())
		{
			// Add any default values
			$default = $this->export_variable($param->getDefaultValue());

			if ($type == '')
			{
				// Set the type info based on the default value
				$type = gettype($param->getDefaultValue());
				$type = str_replace(array('NULL', 'boolean'), array('mixed', 'bool'), $type);
			}
		}

		// Use 'mixed' as the default type
		$type = $type == '' ? 'mixed' : $type;

		return array(
			'type'    => $type,
			'hint'    => $hint,
			'default' => $default,
			'by_ref'  => $by_ref
		);
	}

	/**
	 * Exports a variable value to a parsable string representation. Array
	 * variables can be processed recursively, and indentation may optionally
	 * be included with these.
	 *
	 * @param   mixed   $variable  The variable to export
	 * @param   bool    $indent    Should indentation be included?
	 * @param   bool    $level     The indentation level
	 * @return  string  The exported string
	 */
	public function export_variable($variable, $indent = FALSE, $level = 1)
	{
		if ( ! is_array($variable))
		{
			// Return the exported value
			$val = var_export($variable, TRUE);
			return in_array($val, array('true', 'false', 'null')) ? strtoupper($val) : $val;
		}

		// Convert arrays to comma-separated lists
		$list = array();

		foreach ($variable as $key => $value)
		{
			// Array values may be exported recursively
			$entry = $this->export_variable($value, $indent, is_array($value) ? ($level + 1) : $level);

			if ( ! is_integer($key))
			{
				// Expand string keys to 'key' => val
				$entry = "'{$key}' => ".$entry;
			}

			// Add the new entry
			$list[] = $entry;
		}

		if ($indent)
		{
			// Return an indented array definition
			return 'array('.PHP_EOL
				.str_repeat("\t", $level)
				.implode(",\n".str_repeat("\t", $level), $list).','.PHP_EOL
				.str_repeat("\t", $level - 1).')';
		}

		// Return a flat array definition
		return 'array('.implode(', ', $list).')';
	}

	/**
	 * Determines whether the current source has been inspected yet.
	 *
	 * @return  bool
	 */
	public function is_inspected()
	{
		return ! empty($this->_info);
	}

	/**
	 * Returns the list of methods with their parsed info from the current
	 * source.
	 *
	 * @return  array  The methods list
	 */
	public function get_methods()
	{
		$this->is_inspected() OR $this->inspect();

		return isset($this->_info['methods']) ? $this->_info['methods'] : array();
	}

	/**
	 * Returns the signature for a given method parameter as a parsable string
	 * representation from the current source.
	 *
	 * @throws  Generator_Exception  If the parameter does not exist
	 * @param   string  $method  The method name
	 * @param   string  $param   The parameter name
	 * @return  string  The parameter signature
	 */
	public function get_param_signature($method, $param)
	{
		if (empty($this->_info['methods'][$method]['params'][$param]))
			throw new Generator_Exception('Param :param does not exist for method :method',
				array(':param' => $param, ':method' => $method));

		$p = $this->_info['methods'][$method]['params'][$param];

		// Build the signature from the stored info
		$type    = ($p['hint'] AND $p['type']) ? ($p['type'].' ') : '';
		$ref     = $p['by_ref'] ? '& ' : '';
		$default = $p['default'] ? (' = '.$p['default']) : '';

		// Return the parsed signature
		return $type.$ref.'$'.$param.$default;
	}

	/**
	 * Returns the full signature for the given method parameters as a parsable
	 * string representation from the current source.
	 *
	 * @throws  Generator_Exception  If the method does not exist
	 * @param   string  $method  The method name
	 * @return  string  The full signature for the parameters
	 */
	public function get_method_param_signatures($method)
	{
		if (empty($this->_info['methods'][$method]))
			throw new Generator_Exception('Method :method does not exist', array(':method' => $method));

		// Start the list of signatures
		$sigs = array();

		if ( ! empty($this->_info['methods'][$method]['params']))
		{
			foreach (array_keys($this->_info['methods'][$method]['params']) as $param)
			{
				// Add each parameter signature to the list
				$sigs[] = $this->get_param_signature($method, $param);
			}
		}

		// Return the imploded list
		return implode(', ', $sigs);
	}

	/**
	 * Returns a full method signature as a parsable string representation from
	 * the current source.
	 *
	 * @throws  Generator_Exception  If the method does not exist
	 * @param   string  $method  The method name
	 * @return  string  The method signature
	 */
	public function get_method_signature($method)
	{
		$this->is_inspected() OR $this->inspect();

		if (empty($this->_info['methods'][$method]))
			throw new Generator_Exception('Method :method does not exist', array(':method' => $method));

		$m = $this->_info['methods'][$method];

		// Get the method parameter signatures
		$params = $this->get_method_param_signatures($method);

		// Return the full signature
		$ref = $m['by_ref'] ? '& ' : '';
		return $m['modifiers'].' function '.$ref.$method.'('.$params.')';
	}

} // End Generator_Generator_Reflector
