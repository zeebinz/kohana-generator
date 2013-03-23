<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generator Config type.
 *
 * @package    Generator
 * @category   Generator/Types
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Generator_Generator_Type_Config extends Generator_Type
{
	protected $_template = 'generator/type/config';
	protected $_folder   = 'config';

	/**
	 * Adds any values for this config file.
	 *
	 * The values should be passed in the form "key|value", where "key" can be an
	 * array path, and may be in an array or comma-separated list.
	 *
	 * @param   string|array  $values  The value definitions
	 * @return  Generator_Type_Config  This instance
	 */
	public function value($values)
	{
		$this->param_to_array($values, 'values');
		return $this;
	}

	/**
	 * Imports existing config values into this config file.
	 *
	 * The values should be passed in the form "source|path", where "source" is
	 * either a config group name or an absolute path to a config file, and "path"
	 * is an array path to the config value to be imported. If only "source" is
	 * specified, the whole contents of the source will be imported.
	 *
	 * Multiple value definitions may be passed as an array or a comma-separated
	 * list, and stored values will take precedence over imported values.
	 *
	 * @param   string|array  $imports  The value definitions to be imported
	 * @return  Generator_Type_Config  This instance
	 */
	public function import($imports)
	{
		$this->param_to_array($imports, 'imports');
		return $this;
	}

	/**
	 * Finalizes parameters and renders the template.
	 *
	 * @return  string  The rendered output
	 */
	public function render()
	{
		if ( ! empty($this->_params['values']))
		{
			// Parse the stored values
			$this->_params['values'] = $this->_parse_values($this->_params['values']);
		}
		if ( ! empty($this->_params['imports']))
		{
			// Parse the imported values
			$this->_params['imports'] = $this->_parse_imports($this->_params['imports']);

			// Merge any stored and imported values
			$values = empty($this->_params['values']) ? array() : $this->_params['values'];
			$this->_params['values'] = Arr::merge($this->_params['imports'], $values);
		}

		return parent::render();
	}

	/**
	 * Converts an array of value definition strings into a final array of
	 * value items.
	 *
	 * @param   array  $values  The list of value definitions
	 * @return  array  The parsed list
	 */
	protected function _parse_values(array $values)
	{
		$ret = array();

		foreach ($values as $value)
		{
			list($key, $val) = explode('|', $value);
			$key = is_integer($key) ? (trim($key) + 0) : trim($key);
			$val = is_numeric($val) ? (trim($val) + 0) : trim($val);

			if (is_numeric($key))
			{
				// Numeric keys should be set directly
				$ret[$key] = $val;
			}
			else
			{
				// Otherwise treat key as an array path
				Arr::set_path($ret, $key, $val);
			}
		}

		return $ret;
	}

	/**
	 * Converts an array of imported value definition strings into a final array of
	 * value items ready for merging.
	 *
	 * @param   array  $imports  The list of imported value definitions
	 * @return  array  The parsed list
	 */
	protected function _parse_imports(array $imports)
	{
		$ret = array();

		foreach ($imports as $import)
		{
			if (strpos($import, '|') === FALSE)
			{
				// No array path is specified, so include the whole result
				$ret = Arr::merge($ret, $this->_import_source($import));
				continue;
			}

			// Include the array path with the original array structure
			list($source, $path) = explode('|', $import);
			Arr::set_path($ret, $path, $this->_import_source($source, $path));
		}

		return $ret;
	}

	/**
	 * Imports values from a given source, either in whole or by array path.
	 *
	 * @param   array  $source  Source for the values
	 * @param   array  $path    Array path for the values
	 * @return  mixed  The imported values
	 */
	protected function _import_source($source, $path = NULL)
	{
		return Generator::get_config($source, $path);
	}

} // End Generator_Generator_Type_Config
